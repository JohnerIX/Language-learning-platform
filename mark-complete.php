<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php'; // Basic auth, might need user ID

// Default redirect location
$redirect_url = 'courses.php'; // Fallback redirect

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to mark lessons.";
    // If we know the course_id or lesson_id, we could try to redirect more specifically
    // For now, redirecting to login or a general page.
    if (isset($_POST['course_id'])) {
        $redirect_url = 'learn.php?course_id=' . (int)$_POST['course_id'];
    }
    header("Location: login.php?redirect_url=" . urlencode($redirect_url));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token. Please try again.";
        if (isset($_POST['course_id'])) {
            $redirect_url = 'learn.php?course_id=' . (int)$_POST['course_id'];
            if(isset($_POST['lesson_id'])) {
                $redirect_url .= '&lesson=' . (int)$_POST['lesson_id'];
            }
        }
        header("Location: " . $redirect_url);
        exit();
    }

    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
    $lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if ($course_id && $lesson_id && $action) {
        $redirect_url = "learn.php?course_id={$course_id}&lesson={$lesson_id}";
        try {
            // Placeholder logic: In a real app, update the database here.
            // For example, update 'lesson_progress' table.
            // $stmt = $conn->prepare("UPDATE lesson_progress SET completed_at = ? WHERE user_id = ? AND lesson_id = ?");
            // $completed_at = ($action === 'complete') ? date('Y-m-d H:i:s') : null;
            // $stmt->execute([$completed_at, $_SESSION['user_id'], $lesson_id]);

            if ($action === 'complete') {
                $_SESSION['success_message'] = "Lesson marked as complete (Placeholder).";
            } elseif ($action === 'incomplete') {
                $_SESSION['success_message'] = "Lesson marked as incomplete (Placeholder).";
            } else {
                $_SESSION['error_message'] = "Invalid action.";
            }
        } catch (PDOException $e) {
            error_log("Error in mark-complete.php: " . $e->getMessage());
            $_SESSION['error_message'] = "An error occurred while updating lesson status.";
        }
    } else {
        $_SESSION['error_message'] = "Missing required information to update lesson status.";
        if ($course_id) {
            $redirect_url = "learn.php?course_id={$course_id}";
        }
    }
    header("Location: " . $redirect_url);
    exit();

} else {
    // Not a POST request, redirect away
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: " . $redirect_url);
    exit();
}
?>
