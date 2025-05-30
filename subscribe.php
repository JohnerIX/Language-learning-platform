<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error_message'] = "Please login to subscribe to courses";
    header("Location: login.php");
    exit();
}

if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    $_SESSION['error_message'] = "Invalid course specified";
    header("Location: courses.php");
    exit();
}

$course_id = (int)$_GET['course_id'];

try {
    // Verify course exists and is published
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS tutor_name
        FROM courses c
        JOIN users u ON c.tutor_id = u.user_id
        WHERE c.course_id = ? AND c.status = 'published'
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        $_SESSION['error_message'] = "Course not available for subscription";
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
        $_SESSION['info_message'] = "You're already subscribed to this course";
        header("Location: learn.php?course_id=$course_id");
        exit();
    }

    // Handle free vs paid courses
    if ($course['price'] > 0) {
        // Redirect to payment processing
        $_SESSION['payment_course_id'] = $course_id;
        header("Location: payment.php");
        exit();
    }

    // For free courses - direct subscription
    $conn->beginTransaction();
    
    // Add subscription
    $stmt = $conn->prepare("
        INSERT INTO subscriptions (user_id, course_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    
    // Record enrollment
    $stmt = $conn->prepare("
        INSERT INTO enrollments (user_id, course_id, enrolled_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    
    $conn->commit();
    
    $_SESSION['success_message'] = "Successfully enrolled in '{$course['title']}'";
    header("Location: learn.php?course_id=$course_id");
    exit();

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Subscription error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error processing subscription";
    header("Location: course-details.php?id=$course_id");
    exit();
}