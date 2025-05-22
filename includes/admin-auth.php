<?php
// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error_message'] = "You need admin privileges to access this page";
    redirect('login.php');
}

// Log admin activity
function log_admin_activity($action, $metadata = null) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO admin_logs 
        (user_id, action, action_type, ip_address, user_agent, metadata)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $action,
        'system',
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'],
        $metadata ? json_encode($metadata) : null
    ]);
}
?>