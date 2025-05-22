<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Require Composer's autoload

$client = new Google\Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('YOUR_REDIRECT_URI'); // e.g., http://yourdomain.com/auth/google_callback.php
$client->addScope('email');
$client->addScope('profile');

if (!isset($_GET['code'])) {
    // First step - redirect to Google's OAuth screen
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
} else {
    // Second step - handle callback
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);
        
        $googleService = new Google\Service\Oauth2($client);
        $userData = $googleService->userinfo->get();
        
        // Process user data
        $email = $userData->getEmail();
        $name = $userData->getName();
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($user = $stmt->fetch()) {
            // Login existing user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
        } else {
            // Register new user
            $role = 'learner'; // Default role
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, role, created_at) 
                 VALUES (?, ?, ?, NOW())"
            );
            $stmt->execute([$name, $email, $role]);
            
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
        }
        
        redirect('profile.php');
        
    } catch (Exception $e) {
        error_log("Google Auth Error: " . $e->getMessage());
        $_SESSION['error'] = "Google login failed. Please try again.";
        redirect('signup.php');
    }
}