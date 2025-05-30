<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error_message'] = "Please login to complete payment";
    header("Location: login.php");
    exit();
}

// Verify course selection exists
if (!isset($_SESSION['payment_course_id'])) {
    $_SESSION['error_message'] = "No course selected for payment";
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
        $_SESSION['error_message'] = "Course not available for payment";
        header("Location: courses.php");
        exit();
    }

    // Check if already enrolled
    $stmt = $conn->prepare("
        SELECT 1 FROM enrollments 
        WHERE user_id = ? AND course_id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    
    if ($stmt->fetch()) {
        $_SESSION['info_message'] = "You're already enrolled in this course";
        header("Location: learn.php?course_id=$course_id");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die("CSRF token validation failed.");
        }

        // Validate payment method
        $valid_methods = ['mobile_money', 'credit_card', 'bank_transfer'];
        if (!in_array($_POST['payment_method'], $valid_methods)) {
            $_SESSION['error_message'] = "Invalid payment method selected";
            header("Location: payment.php");
            exit();
        }

        // Process payment - in a real app, this would connect to your payment gateway
        try {
            $conn->beginTransaction();
            
            // Generate a unique transaction ID
            $transaction_id = 'TXN-' . time() . '-' . bin2hex(random_bytes(4));
            
            // Record payment
            $stmt = $conn->prepare("
                INSERT INTO payments 
                (user_id, course_id, amount, payment_method, transaction_id, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $course_id,
                $course['price'],
                $_POST['payment_method'],
                $transaction_id
            ]);
            $payment_id = $conn->lastInsertId();

            // For demo purposes, we'll simulate a successful payment
            // In production, you would verify payment with your gateway here
            $payment_status = 'completed'; // Simulate success
            
            // Update payment status
            $stmt = $conn->prepare("
                UPDATE payments SET status = ? 
                WHERE payment_id = ?
            ");
            $stmt->execute([$payment_status, $payment_id]);

            // Create enrollment only if payment succeeded
            if ($payment_status === 'completed') {
                $stmt = $conn->prepare("
                    INSERT INTO enrollments 
                    (user_id, course_id, payment_id, enrolled_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $course_id,
                    $payment_id
                ]);

                // Add to subscriptions
                $stmt = $conn->prepare("
                    INSERT INTO subscriptions 
                    (user_id, course_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $course_id]);
                
                $conn->commit();
                
                // Clear payment session
                unset($_SESSION['payment_course_id']);
                
                $_SESSION['success_message'] = "Payment successful! You now have access to '{$course['title']}'";
                header("Location: learn.php?course_id=$course_id");
                exit();
            } else {
                throw new Exception("Payment processing failed");
            }

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Payment Error: " . $e->getMessage());
            $_SESSION['error_message'] = "Error processing payment: " . $e->getMessage();
            // Ensure redirect to display the message
            header("Location: payment.php"); // Or appropriate page
            exit();
        }
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Database error processing payment";
    header("Location: course-details.php?id=$course_id"); // Redirect to course details or courses page
    exit();
}

$pageTitle = "Payment | " . $course['title'];
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-primary shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-lock me-2"></i> Secure Payment</h3>
                </div>
                
                <div class="card-body">
                    <?php /* Old error display removed, handled by SweetAlert in header.php
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error_message']) ?>
                            <?php unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>
                    */ ?>
                    
                    <div class="row">
                        <!-- Course Summary -->
                        <div class="col-md-5 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-body">
                                    <h5 class="card-title text-success">
                                        <i class="fas fa-book-open me-2"></i><?= htmlspecialchars($course['title']) ?>
                                    </h5>
                                    <p class="text-muted small">
                                        By <?= htmlspecialchars($course['tutor_name']) ?>
                                    </p>
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= htmlspecialchars($course['thumbnail_url'] ?? 'assets/default-course.jpg') ?>" 
                                             class="img-thumbnail me-3" 
                                             width="100" 
                                             alt="Course thumbnail">
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Course Price:</span>
                                        <strong>UGX <?= number_format($course['price'], 2) ?></strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Platform Fee:</span>
                                        <strong>UGX <?= number_format($course['price'] * 0.15, 2) ?></strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                                        <span>Total:</span>
                                        <span class="text-success">UGX <?= number_format($course['price'] * 1.15, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Form -->
                        <div class="col-md-7">
                            <form method="post" id="paymentForm">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="process_payment" value="1">
                                
                                <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i> Payment Method</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="mobileMoney" value="mobile_money" checked required>
                                        <label class="form-check-label fw-bold" for="mobileMoney">
                                            <i class="fas fa-mobile-alt me-2"></i> Mobile Money
                                        </label>
                                        <div id="mobileMoneyFields" class="mt-3">
                                            <div class="mb-3">
                                                <label for="mobileNumber" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" 
                                                       id="mobileNumber" name="mobile_number"
                                                       placeholder="+256 XXX XXX XXX" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="mobileNetwork" class="form-label">Network</label>
                                                <select class="form-select" id="mobileNetwork" name="mobile_network" required>
                                                    <option value="">Select network</option>
                                                    <option value="mtn">MTN Mobile Money</option>
                                                    <option value="airtel">Airtel Money</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="creditCard" value="credit_card" required>
                                        <label class="form-check-label fw-bold" for="creditCard">
                                            <i class="far fa-credit-card me-2"></i> Credit/Debit Card
                                        </label>
                                        <div id="creditCardFields" class="mt-3 d-none">
                                            <div class="mb-3">
                                                <label for="cardNumber" class="form-label">Card Number</label>
                                                <input type="text" class="form-control" 
                                                       id="cardNumber" name="card_number"
                                                       placeholder="1234 5678 9012 3456">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="expiryDate" class="form-label">Expiry Date</label>
                                                    <input type="text" class="form-control" 
                                                           id="expiryDate" name="expiry_date"
                                                           placeholder="MM/YY">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="cvv" class="form-label">CVV</label>
                                                    <input type="text" class="form-control" 
                                                           id="cvv" name="cvv"
                                                           placeholder="123">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check border p-3 rounded mb-4">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="bankTransfer" value="bank_transfer" required>
                                    <label class="form-check-label fw-bold" for="bankTransfer">
                                        <i class="fas fa-university me-2"></i> Bank Transfer
                                    </label>
                                    <div id="bankTransferFields" class="mt-3 d-none">
                                        <div class="alert alert-info">
                                            <p>Make transfer to:</p>
                                            <p><strong>Bank:</strong> Centenary Bank</p>
                                            <p><strong>Account:</strong> Learn Lugha Ltd</p>
                                            <p><strong>Account No:</strong> 3100000000</p>
                                            <p><strong>Reference:</strong> LL-<?= strtoupper(bin2hex(random_bytes(4))) ?></p>
                                            <p class="mb-0"><strong>Amount:</strong> UGX <?= number_format($course['price'] * 1.15, 2) ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg py-3">
                                        <i class="fas fa-lock me-2"></i> Pay UGX <?= number_format($course['price'] * 1.15, 2) ?>
                                    </button>
                                </div>
                                
                                <p class="text-muted small mt-3">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    All payments are securely processed
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
// Toggle payment method fields
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all fields first
        document.querySelectorAll('.form-check .mt-3').forEach(el => {
            el.classList.add('d-none');
        });
        
        // Show selected method's fields
        const fieldsId = this.id + 'Fields';
        document.getElementById(fieldsId)?.classList.remove('d-none');
    });
});

// Initialize default view
document.getElementById('mobileMoneyFields').classList.remove('d-none');
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>