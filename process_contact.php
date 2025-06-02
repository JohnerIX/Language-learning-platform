<?php
require_once __DIR__ . '/includes/config.php'; // For session_start()

// Start session if not already started by config.php or elsewhere
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation (assuming contact.php will have a CSRF token field)
    // Let's assume contact.php form will be updated to include:
    // <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token. Please try again.";
        header("Location: contact.php#contactForm"); // Keep user at the form
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? ''); // Optional
    $subject = trim($_POST['subject'] ?? '');
    $message_content = trim($_POST['message'] ?? ''); // Renamed to avoid conflict with $message variable for email
    $newsletter = isset($_POST['newsletter']) ? 'Yes' : 'No';
    $terms = isset($_POST['terms']);

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
    if (empty($message_content)) {
        $errors[] = "Message is required.";
    }
    if (!$terms) {
        $errors[] = "You must agree to the terms and conditions.";
    }

    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
    } else {
        $to = 'isaacvital44@gmail.com'; // Admin email address
        $email_subject = "New Contact Form Message: " . $subject;

        $email_body = "You have received a new message from your website contact form.\n\n";
        $email_body .= "Here are the details:\n";
        $email_body .= "Name: " . $name . "\n";
        $email_body .= "Email: " . $email . "\n";
        if (!empty($phone)) {
            $email_body .= "Phone: " . $phone . "\n";
        }
        $email_body .= "Subject: " . $subject . "\n";
        $email_body .= "Message:\n" . $message_content . "\n\n";
        $email_body .= "Subscribe to newsletter: " . $newsletter . "\n";

        // Basic headers
        $headers = "From: contactform@learnlugha.com\r\n"; // Replace with your domain
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


        // Attempt to send the email
        if (mail($to, $email_subject, $email_body, $headers)) {
            $_SESSION['success_message'] = "Thank you for your message, " . htmlspecialchars($name) . "! It has been sent successfully.";
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error trying to send your message. Please try again later or contact us directly via email.";
            error_log("Contact form mail() failed. To: $to, Subject: $email_subject, From header: $headers");
        }
    }
    header("Location: contact.php#contactForm"); // Redirect back to contact page, potentially to the form area
    exit();

} else {
    // Not a POST request
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: contact.php");
    exit();
}
?>
