<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = "Login";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Role-based redirection
            switch ($user['role']) {
                case 'admin':
                    redirect('admin-dashboard.php');
                    break;
                case 'tutor':
                    redirect('tutor-dashboard.php');
                    break;
                case 'learner':
                    redirect('dashboard.php');
                    break;
                default:
                    redirect('profile.php');
            }
        } else {
            $error = "Invalid email or password";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="container-fluid bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="row g-0">
                        <!-- Left Side - Branding Panel -->
                        <div class="col-md-6 d-none d-md-flex bg-dark text-white p-5">
                            <div class="w-100 d-flex flex-column justify-content-center">
                                <div class="text-center">
                                    <h1 class="display-5 mb-4 text-success fw-bold">
                                        <i class="fas fa-language me-2"></i>Language Learning Platform
                                    </h1>
                                    <p class="lead mb-4 opacity-75">Connect with native speakers worldwide</p>
                                    <div class="border-top border-success w-50 mx-auto my-4 opacity-50"></div>
                                    <div class="d-flex justify-content-center gap-3">
                                        <div class="feature-item">
                                            <i class="fas fa-check-circle text-success mb-2"></i>
                                            <p class="small mb-0">Interactive Lessons</p>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-check-circle text-success mb-2"></i>
                                            <p class="small mb-0">Live Tutoring</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Login Form -->
                        <div class="col-md-6 bg-white p-5">
                            <div class="w-100 h-100 d-flex flex-column justify-content-center">
                                <h2 class="h3 mb-4 text-dark fw-bold">Welcome Back</h2>
                                
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger rounded-0 mb-4">
                                        <?php foreach ($errors as $error): ?>
                                            <p class="mb-1 small"><?= htmlspecialchars($error) ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Social Login Buttons -->
                                <div class="mb-4">
                                    <a href="google_auth.php" class="btn btn-outline-dark rounded-pill w-100 mb-3">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" width="20" class="me-2">
                                        Continue with Google
                                    </a>
                                    <a href="twitter_auth.php" class="btn btn-outline-dark rounded-pill w-100">
                                        <i class="fab fa-x-twitter me-2"></i>
                                        Continue with X
                                    </a>
                                </div>

                                <div class="d-flex align-items-center mb-4">
                                    <hr class="flex-grow-1 bg-light">
                                    <span class="mx-3 text-muted small">OR</span>
                                    <hr class="flex-grow-1 bg-light">
                                </div>

                                <!-- Email Login Form -->
                                <form method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label small text-uppercase fw-bold text-muted">Email Address</label>
                                        <input type="email" class="form-control border-0 border-bottom rounded-0 px-0 py-2" 
                                               id="email" name="email" required
                                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                        <div class="invalid-feedback small">Please enter a valid email</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="password" class="form-label small text-uppercase fw-bold text-muted">Password</label>
                                        <input type="password" class="form-control border-0 border-bottom rounded-0 px-0 py-2" 
                                               id="password" name="password" required>
                                        <div class="invalid-feedback small">Password is required</div>
                                        <div class="text-end mt-2">
                                            <a href="forgot-password.php" class="small text-success text-decoration-none">Forgot password?</a>
                                        </div>
                                    </div>

                                    <button type="submit" name="login" class="btn btn-success w-100 py-3 fw-bold mb-3">
                                        Log In
                                    </button>
                                    
                                    <p class="text-center text-muted small mt-4">
                                        Don't have an account? 
                                        <a href="signup.php" class="text-success fw-bold text-decoration-none">Create one</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .feature-item {
        width: 120px;
        text-align: center;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #28a745;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .card {
        border-radius: 0;
    }
</style>

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