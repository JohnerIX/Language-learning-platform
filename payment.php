<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error'] = "Please login to complete payment";
    header("Location: login.php");
    exit();
}

// Check if course ID is provided
if (!isset($_SESSION['payment_course_id'])) {
    $_SESSION['error'] = "No course selected for payment";
    header("Location: courses.php");
    exit();
}

$course_id = (int)$_SESSION['payment_course_id'];

try {
    // Get course details
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS tutor_name
        FROM courses c
        JOIN users u ON c.tutor_id = u.user_id
        WHERE c.course_id = ? AND c.status = 'published'
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        $_SESSION['error'] = "Course not available for payment";
        header("Location: courses.php");
        exit();
    }

    // Check if already subscribed
    $stmt = $conn->prepare("
        SELECT 1 FROM subscriptions 
        WHERE user_id = ? AND course_id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    
    if ($stmt->fetch()) {
        $_SESSION['info'] = "You're already subscribed to this course";
        header("Location: learn.php?course_id=$course_id");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("CSRF token validation failed.");
        }

        // Process payment (simulated - integrate with your payment gateway)
        $payment_success = true; // Set to false to simulate failed payment
        
        if ($payment_success) {
            $conn->beginTransaction();
            
            // Record payment
            $stmt = $conn->prepare("
                INSERT INTO payments (user_id, course_id, amount, payment_method, transaction_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $course_id,
                $course['price'],
                $_POST['payment_method'],
                'txn_' . bin2hex(random_bytes(8))
            ]);
            
            // Add subscription
            $stmt = $conn->prepare("
                INSERT INTO subscriptions (user_id, course_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $course_id]);
            
            $conn->commit();
            
            // Clear payment session
            unset($_SESSION['payment_course_id']);
            
            $_SESSION['success'] = "Payment successful! You now have access to '{$course['title']}'";
            header("Location: learn.php?course_id=$course_id");
            exit();
        } else {
            $_SESSION['error'] = "Payment failed. Please try again or contact support";
        }
    }

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Payment error: " . $e->getMessage());
    $_SESSION['error'] = "Error processing payment";
    header("Location: course-details.php?id=$course_id");
    exit();
}

$pageTitle = "Payment | " . $course['title'];
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-success shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Complete Your Enrollment</h3>
                </div>
                
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error'] ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Course Summary -->
                            <div class="card mb-4 border-success">
                                <div class="card-body">
                                    <h5 class="card-title text-success">Order Summary</h5>
                                    
                                    <div class="d-flex mb-3">
                                        <img src="<?= htmlspecialchars($course['thumbnail_url'] ?? 'assets/default-course.jpg') ?>" 
                                             class="rounded me-3" 
                                             width="80" 
                                             height="60" 
                                             style="object-fit: cover;">
                                        <div>
                                            <h6><?= htmlspecialchars($course['title']) ?></h6>
                                            <small class="text-muted">By <?= htmlspecialchars($course['tutor_name']) ?></small>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Price:</span>
                                        <strong>UGX <?= number_format($course['price'], 2) ?></strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax:</span>
                                        <strong>UGX 0.00</strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total:</span>
                                        <strong class="text-success">UGX <?= number_format($course['price'], 2) ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Payment Form -->
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="process_payment" value="1">
                                
                                <h5 class="mb-3">Payment Method</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="mobileMoney" value="mobile_money" checked>
                                        <label class="form-check-label" for="mobileMoney">
                                            <i class="fas fa-mobile-alt me-2"></i> Mobile Money
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="creditCard" value="credit_card">
                                        <label class="form-check-label" for="creditCard">
                                            <i class="far fa-credit-card me-2"></i> Credit/Debit Card
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bankTransfer" value="bank_transfer">
                                        <label class="form-check-label" for="bankTransfer">
                                            <i class="fas fa-university me-2"></i> Bank Transfer
                                        </label>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Mobile Money Fields (shown by default) -->
                                <div id="mobileMoneyFields">
                                    <div class="mb-3">
                                        <label for="mobileNumber" class="form-label">Mobile Number</label>
                                        <input type="tel" class="form-control" id="mobileNumber" 
                                               placeholder="+256 XXX XXX XXX" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobileNetwork" class="form-label">Network</label>
                                        <select class="form-select" id="mobileNetwork" required>
                                            <option value="">Select network</option>
                                            <option value="mtn">MTN</option>
                                            <option value="airtel">Airtel</option>
                                                                                    </select>
                                    </div>
                                </div>
                                
                                <!-- Credit Card Fields (hidden by default) -->
                                <div id="creditCardFields" class="d-none">
                                    <div class="mb-3">
                                        <label for="cardNumber" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="cardNumber" 
                                               placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiryDate" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiryDate" 
                                                   placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" 
                                                   placeholder="123">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bank Transfer Fields (hidden by default) -->
                                <div id="bankTransferFields" class="d-none">
                                    <div class="alert alert-info">
                                        <p>Please transfer UGX <?= number_format($course['price'], 2) ?> to:</p>
                                        <p class="mb-1"><strong>Bank:</strong> Centenary Bank</p>
                                        <p class="mb-1"><strong>Account Name:</strong> Learn Lugha</p>
                                        <p class="mb-1"><strong>Account Number:</strong> 3100000000</p>
                                        <p class="mb-0"><strong>Reference:</strong> LL-<?= bin2hex(random_bytes(4)) ?></p>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg py-3">
                                        <i class="fas fa-lock me-2"></i> Pay UGX <?= number_format($course['price'], 2) ?>
                                    </button>
                                </div>
                                
                                <p class="text-muted small mt-3">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    Your payment is secured with SSL encryption
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide payment method fields based on selection
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('mobileMoneyFields').classList.add('d-none');
        document.getElementById('creditCardFields').classList.add('d-none');
        document.getElementById('bankTransferFields').classList.add('d-none');
        
        if (this.value === 'mobile_money') {
            document.getElementById('mobileMoneyFields').classList.remove('d-none');
        } else if (this.value === 'credit_card') {
            document.getElementById('creditCardFields').classList.remove('d-none');
        } else if (this.value === 'bank_transfer') {
            document.getElementById('bankTransferFields').classList.remove('d-none');
        }
    });
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>