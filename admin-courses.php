<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = "Manage Courses";
require __DIR__ . '/includes/header.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= addslashes($_SESSION['error_message']) ?>',
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= addslashes($_SESSION['success_message']) ?>',
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
});
</script>
<?php
// Handle course actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }

    $course_id = (int)$_POST['course_id'];
    $action = $_POST['action'];
    
    try {
        switch ($action) {
            case 'approve':
                $stmt = $conn->prepare("
                    UPDATE courses 
                    SET status = 'published', 
                        approved_by = ?, 
                        approved_at = NOW() 
                    WHERE course_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $course_id]);
                $_SESSION['success_message'] = "Course approved successfully";
                break;
                
            case 'reject':
                $stmt = $conn->prepare("
                    UPDATE courses 
                    SET status = 'rejected',
                        rejection_reason = ?,
                        reviewed_by = ?,
                        reviewed_at = NOW()
                    WHERE course_id = ?
                ");
                $stmt->execute([
                    sanitize_input($_POST['rejection_reason']),
                    $_SESSION['user_id'],
                    $course_id
                ]);
                $_SESSION['success_message'] = "Course rejected successfully";
                break;
                
            case 'feature':
                $stmt = $conn->prepare("
                    UPDATE courses 
                    SET is_featured = ?
                    WHERE course_id = ?
                ");
                $stmt->execute([(int)$_POST['is_featured'], $course_id]);
                $_SESSION['success_message'] = "Course featured status updated";
                break;
        }
        
        header("Location: admin-courses.php" . (isset($_GET['filter']) ? "?filter=" . htmlspecialchars($_GET['filter']) : ""));
        exit();
        
    } catch (PDOException $e) {
        error_log("Admin course action failed: " . $e->getMessage());
        $_SESSION['error_message'] = "Action failed. Please try again.";
        header("Location: admin-courses.php" . (isset($_GET['filter']) ? "?filter=" . htmlspecialchars($_GET['filter']) : ""));
        exit();
    }
}

// Get filter parameter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build base query
$query = "
    SELECT c.*, u.name AS tutor_name, u.email AS tutor_email
    FROM courses c
    JOIN users u ON c.tutor_id = u.user_id
";

// Add filters
$where = [];
$params = [];

if ($filter === 'pending') {
    $where[] = "c.status = 'pending'";
} elseif ($filter === 'published') {
    $where[] = "c.status = 'published'";
} elseif ($filter === 'rejected') {
    $where[] = "c.status = 'rejected'";
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY c.created_at DESC";

// Get courses
$stmt = $conn->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();
?>

<div class="container-fluid pt-4">
    <?php /* Old Bootstrap alert display removed
    */ ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Courses</h6>
            <div>
                <a href="?filter=all" class="btn btn-sm btn-outline-secondary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
                <a href="?filter=pending" class="btn btn-sm btn-outline-warning <?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="?filter=published" class="btn btn-sm btn-outline-success <?= $filter === 'published' ? 'active' : '' ?>">Published</a>
                <a href="?filter=rejected" class="btn btn-sm btn-outline-danger <?= $filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Tutor</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= $course['course_id'] ?></td>
                            <td>
                                <a href="../course.php?id=<?= $course['course_id'] ?>" target="_blank">
                                    <?= htmlspecialchars($course['title']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="admin-tutors.php?id=<?= $course['tutor_id'] ?>">
                                    <?= htmlspecialchars($course['tutor_name']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-<?= 
                                    $course['status'] === 'published' ? 'success' : 
                                    ($course['status'] === 'rejected' ? 'danger' : 'warning')
                                ?>">
                                    <?= ucfirst($course['status']) ?>
                                </span>
                                <?php if ($course['is_featured']): ?>
                                    <span class="badge badge-info ml-1">Featured</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                            <td>
                                <?php if ($course['status'] === 'pending'): ?>
                                    <!-- Approve/Reject Forms -->
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <button class="btn btn-sm btn-danger ml-1" data-toggle="modal" 
                                            data-target="#rejectModal<?= $course['course_id'] ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    
                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal<?= $course['course_id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Course</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <div class="form-group">
                                                            <label>Reason for rejection</label>
                                                            <textarea name="rejection_reason" class="form-control" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php elseif ($course['status'] === 'published'): ?>
                                    <!-- Feature Toggle -->
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                        <input type="hidden" name="action" value="feature">
                                        <input type="hidden" name="is_featured" value="<?= $course['is_featured'] ? '0' : '1' ?>">
                                        <button type="submit" class="btn btn-sm btn-<?= $course['is_featured'] ? 'warning' : 'info' ?>">
                                            <i class="fas fa-star"></i> <?= $course['is_featured'] ? 'Unfeature' : 'Feature' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="edit-course.php?id=<?= $course['course_id'] ?>" 
                                   class="btn btn-sm btn-primary ml-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
require __DIR__ . '/includes/footer.php';