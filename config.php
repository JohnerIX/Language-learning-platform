<?php
// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session 
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => true,    
        'use_strict_mode' => true
    ]);
}


// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          
define('DB_PASS', '');              
define('DB_NAME', 'languagelearndb');

//  database connection
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Set the default timezone
date_default_timezone_set('Africa/Kampala');
// Set the character set to UTF-8
ini_set('default_charset', 'UTF-8');

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// CSRF token generation (for forms)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>