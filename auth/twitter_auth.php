<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

if (isset($_GET['denied'])) {
    // User denied authorization
    $_SESSION['error'] = "Twitter login was cancelled.";
    redirect('signup.php');
}

if (!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])) {
    die("Invalid callback parameters");
}

// Verify tokens match
if ($_GET['oauth_token'] !== $_SESSION['oauth_token']) {
    die("Invalid OAuth token");
}

// Get access token
$connection = new TwitterOAuth(
    'YOUR_TWITTER_API_KEY',
    'YOUR_TWITTER_API_SECRET',
    $_SESSION['oauth_token'],
    $_SESSION['oauth_token_secret']
);

$accessToken = $connection->oauth('oauth/access_token', [
    'oauth_verifier' => $_GET['oauth_verifier']
]);

// Get user info
$userConnection = new TwitterOAuth(
    'YOUR_TWITTER_API_KEY',
    'YOUR_TWITTER_API_SECRET',
    $accessToken['oauth_token'],
    $accessToken['oauth_token_secret']
);

$userData = $userConnection->get('account/verify_credentials', [
    'include_email' => 'true',
    'skip_status' => 'true'
]);

// Process user data
$email = $userData->email ?? null;
$name = $userData->name;
$twitterId = $userData->id_str;

if (!$email) {
    $_SESSION['error'] = "Could not retrieve email from Twitter. Please try another method.";
    redirect('signup.php');
}

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