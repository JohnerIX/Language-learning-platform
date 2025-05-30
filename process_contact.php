<?php
require_once __DIR__ . '/includes/config.php'; // For session_start() and potentially other functions

// Start session if not already started by config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF token check (assuming it might be added to contact.php form later)
    // if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    //     $_SESSION['error_message'] = "Invalid CSRF token. Please try again.";
    //     header("Location: contact.php");
    //     exit();
    // }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $terms = isset($_POST['terms']); // Checkbox

    $errors = [];

    if (empty($name)) {
        $errors[] = "Full Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email Address is required.";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }
    if (!$terms) {
        $errors[] = "You must agree to the terms and conditions.";
    }

    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
    } else {
        // Placeholder for sending email
        // mail("your-email@example.com", "Contact Form: " . $subject, $message, "From: " . $email);
        $_SESSION['success_message'] = "Thank you for your message, " . htmlspecialchars($name) . "! We'll get back to you shortly. (This is a placeholder response)";
    }
    header("Location: contact.php"); // Redirect back to contact page to show the message
    exit();

} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: contact.php");
    exit();
}
?>
