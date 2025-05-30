<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-auth.php'; // Special admin authentication

$pageTitle = "Admin Dashboard";
require __DIR__ . '/includes/header.php';

// Get system statistics
$stats = [
    'total_learners' => $conn->query("SELECT COUNT(*) FROM users WHERE role='learner' AND is_active=1")->fetchColumn(),
    'total_tutors' => $conn->query("SELECT COUNT(*) FROM users WHERE role='tutor' AND status='approved'")->fetchColumn(),
    'pending_tutors' => $conn->query("SELECT COUNT(*) FROM users WHERE role='tutor' AND status='pending'")->fetchColumn(),
    'total_courses' => $conn->query("SELECT COUNT(*) FROM courses WHERE status='published'")->fetchColumn(),
    'pending_courses' => $conn->query("SELECT COUNT(*) FROM courses WHERE status='pending'")->fetchColumn(),
    'total_revenue' => $conn->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status='success'")->fetchColumn()
];

// Get recent activities
$activities = $conn->query("
    SELECT a.*, u.name, u.email 
    FROM admin_logs a
    JOIN users u ON a.user_id = u.user_id
    ORDER BY a.created_at DESC
    LIMIT 10
")->fetchAll();
?>

<div class="container-fluid py-4">
    <!-- System Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Learners</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_learners'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Tutors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_tutors'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approvals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pending_tutors'] ?> Tutors, <?= $stats['pending_courses'] ?> Courses</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">UGX <?= number_format($stats['total_revenue']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Pending Approvals -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Approvals</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="admin-tutors.php?filter=pending">View All Tutors</a>
                            <a class="dropdown-item" href="admin-courses.php?filter=pending">View All Courses</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $pendingApprovals = $conn->query("
                        (SELECT 'tutor' as type, user_id as id, name, created_at 
                         FROM users 
                         WHERE role='tutor' AND status='pending'
                         ORDER BY created_at DESC LIMIT 3)
                        
                        UNION ALL
                        
                        (SELECT 'course' as type, course_id as id, title as name, created_at 
                         FROM courses 
                         WHERE status='pending'
                         ORDER BY created_at DESC LIMIT 3)
                    ")->fetchAll();
                    ?>

                    <div class="list-group">
                        <?php foreach ($pendingApprovals as $item): ?>
                            <a href="<?= $item['type'] === 'tutor' ? 'admin-tutors.php?action=review&id='.$item['id'] : 'admin-courses.php?action=review&id='.$item['id'] ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small><?= time_ago($item['created_at']) ?></small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge badge-<?= $item['type'] === 'tutor' ? 'primary' : 'success' ?>">
                                        <?= ucfirst($item['type']) ?>
                                    </span>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        <?php foreach ($activities as $activity): ?>
                            <div class="feed-item">
                                <div class="feed-item-header">
                                    <span class="user-name"><?= htmlspecialchars($activity['name']) ?></span>
                                    <span class="time"><?= time_ago($activity['created_at']) ?></span>
                                </div>
                                <div class="feed-item-content">
                                    <?= htmlspecialchars($activity['action']) ?>
                                    <?php if ($activity['action_type']): ?>
                                        <span class="badge badge-secondary float-right"><?= $activity['action_type'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Management Sections -->
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="admin-tutors.php" class="btn btn-primary btn-block">
                                <i class="fas fa-chalkboard-teacher"></i> Manage Tutors
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="admin-learners.php" class="btn btn-info btn-block">
                                <i class="fas fa-users"></i> Manage Learners
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="admin-courses.php" class="btn btn-success btn-block">
                                <i class="fas fa-book"></i> Manage Courses
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="admin-payments.php" class="btn btn-warning btn-block">
                                <i class="fas fa-money-bill-wave"></i> View Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Health</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Storage Usage</h5>
                            <div class="progress mb-4">
                                <div class="progress-bar" role="progressbar" style="width: 65%"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Active Sessions</h5>
                            <p><?= $conn->query("SELECT COUNT(*) FROM user_sessions")->fetchColumn() ?> active</p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-secondary mr-2">
                            <i class="fas fa-database"></i> Backup Now
                        </button>
                        <div class="col-6 mb-3">
                            <a href="systemsettings.php" class="btn btn-dark">
                                <i class="fas fa-tools"></i> System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require __DIR__ . '/includes/footer.php';

// Helper function to display time ago
function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return "just now";
    if ($diff < 3600) return floor($diff/60) . " mins ago";
    if ($diff < 86400) return floor($diff/3600) . " hours ago";
    return floor($diff/86400) . " days ago";
}
?>