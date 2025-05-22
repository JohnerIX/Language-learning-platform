<?php
ob_start(); // Start output buffering
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => false,    // Set to true if using HTTPS
        'use_strict_mode' => true
    ]);
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Replace with your MySQL username
define('DB_PASS', '');              // Replace with your MySQL password
define('DB_NAME', 'languageLearn');

// Create database connection
try {
    $conn = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function declarations with existence checks
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit();
    }
}

if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>