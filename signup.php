<?php
$pageTitle = "Sign Up";
require_once __DIR__ . '/includes/config.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle social auth redirects
    if (isset($_POST['google_auth'])) {
        header("Location: auth/google_auth.php");
        exit();
    } elseif (isset($_POST['twitter_auth'])) {
        header("Location: auth/twitter_auth.php");
        exit();
    }

    // Handle regular form submission
    if (isset($_POST['signup'])) {
        // Validate CSRF token
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['error_message'] = "CSRF token validation failed.";
            redirect('signup.php'); // Redirect to show error
        }

        // Sanitize inputs
        $name = sanitize_input($_POST['name']);
        $email = filter_var(sanitize_input($_POST['email']), FILTER_SANITIZE_EMAIL);
        $phone = preg_replace('/[^0-9+]/', '', sanitize_input($_POST['phone']));
        $password = $_POST['password'];
        $role = isset($_POST['role']) ? sanitize_input($_POST['role']) : 'learner'; // Get selected role
        
        $temp_errors = []; // Temporary array for validation errors
        if (empty($name)) $temp_errors[] = "Full name is required";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $temp_errors[] = "Valid email is required";
        if (!preg_match('/^\+256\d{9}$/', $phone)) $temp_errors[] = "Valid Ugandan phone number (+256...) required";
        if (strlen($password) < 8) $temp_errors[] = "Password must be at least 8 characters";

        if (empty($temp_errors)) {
            try {
                // Check if email/phone exists
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR phone = ?");
                $stmt->execute([$email, $phone]);
                
                if ($stmt->fetch()) {
                    $_SESSION['error_message'] = "Email or phone already registered";
                } else {
                    // Hash password
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);

                    // Insert user
                    $stmt = $conn->prepare(
                        "INSERT INTO users (name, email, phone, password_hash, role, created_at) 
                         VALUES (?, ?, ?, ?, ?, NOW())"
                    );
                    
                    if ($stmt->execute([$name, $email, $phone, $password_hash, $role])) {
                        // Optionally auto-login the user or redirect to login with success
                        $_SESSION['success_message'] = "Registration successful! Please log in.";
                        redirect('login.php'); // Redirect to login page
                    } else {
                        $_SESSION['error_message'] = "Registration failed. Please try again.";
                    }
                }
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Database error: " . $e->getMessage();
                error_log("Signup PDOException: " . $e->getMessage());
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", array_map('htmlspecialchars', $temp_errors));
        }
        // Redirect back to signup page to show any messages
        if (isset($_SESSION['error_message'])) {
            redirect('signup.php');
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<?= addslashes($_SESSION['error_message']) ?>', // Use html for <br> tags
            background: '#f8d7da',
            color: '#721c24',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            html: '<?= addslashes($_SESSION['success_message']) ?>', // Use html for potential <br>
            background: '#d4edda',
            color: '#155724',
            confirmButtonColor: '#28a745'
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
});
</script>
<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow">
                <div class="row g-0">
                    <!-- Left Column - Branding -->
                    <div class="col-md-5 d-flex align-items-center bg-dark text-white p-4 p-lg-5">
                        <div class="w-100 text-center">
                            <h1 class="h2 mb-3 text-success">Language Learning Platform</h1>
                            <p class="small mb-0 opacity-75">Join our learning community today</p>
                        </div>
                    </div>
                    
                    <!-- Right Column - Form -->
                    <div class="col-md-7 p-4 p-lg-5">
                        <h2 class="h4 mb-4">Create Account</h2>
                        
                        <!-- Social Login Buttons -->
                        <form method="post" class="mb-4">
                            <div class="d-flex justify-content-center gap-3">
                                <button type="submit" name="google_auth" class="btn btn-outline-danger bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="me-2">
                                        <path fill="#DB4437" d="M12.545 10.239v3.821h5.445c-0.712 2.315-2.647 3.972-5.445 3.972-3.332 0-6.033-2.701-6.033-6.033s2.701-6.033 6.033-6.033c1.498 0 2.866 0.549 3.921 1.453l2.814-2.814c-1.784-1.664-4.155-2.675-6.735-2.675-5.522 0-10 4.478-10 10s4.478 10 10 10c8.396 0 10-7.496 10-10 0-0.671-0.069-1.325-0.189-1.955h-9.811z"/>
                                    </svg>
                                    Continue with Google
                                </button>
                                <button type="submit" name="twitter_auth" class="btn btn-outline-dark bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="me-2">
                                        <path fill="#000" d="M18.901 1.153h3.68l-8.04 9.19 9.46 12.552h-7.406l-5.8-7.584-6.638 7.584h-3.68l8.6-9.83-9.1-12.552h7.594l5.243 6.932z"/>
                                    </svg>
                                    Continue with X
                                </button>
                            </div>
                        </form>

                        <div class="d-flex align-items-center mb-4">
                            <hr class="flex-grow-1">
                            <span class="mx-3 text-muted">or</span>
                            <hr class="flex-grow-1">
                        </div>

                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            
                            <!-- Role Selection -->
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="role" id="learner" value="learner" autocomplete="off" checked>
                                <label class="btn btn-outline-dark" for="learner">
                                    <i class="fas fa-user-graduate me-2"></i>Learner
                                </label>
                                
                                <input type="radio" class="btn-check" name="role" id="tutor" value="tutor" autocomplete="off">
                                <label class="btn btn-outline-dark" for="tutor">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Tutor
                                </label>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone (+256...)</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       pattern="\+256[0-9]{9}" placeholder="+256XXXXXXXXX"
                                       value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                            </div>

                            <button type="submit" name="signup" class="btn btn-success w-100 py-2 mb-3 fw-bold">Sign Up</button>
                            
                            <p class="text-center text-muted mb-0">Already have an account? <a href="login.php" class="text-success">Log in</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
// Form validation
(function () {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>