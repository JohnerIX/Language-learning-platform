<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-auth.php'; // Ensures admin is logged in & functions like log_admin_activity

$pageTitle = "Manage Tutors - Admin";
require __DIR__ . '/includes/header.php';

// Handle tutor actions (Approve/Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'], $_POST['csrf_token'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
    } else {
        $user_id = (int)$_POST['user_id'];
        $action = $_POST['action'];
        $rejection_reason = isset($_POST['rejection_reason']) ? sanitize_input($_POST['rejection_reason']) : null;

        try {
            if ($action === 'approve') {
                $stmt = $conn->prepare("UPDATE users SET status = 'approved', approved_at = NOW(), approved_by = ? WHERE user_id = ? AND role = 'tutor'");
                $stmt->execute([$_SESSION['user_id'], $user_id]);
                $_SESSION['success_message'] = "Tutor approved successfully.";
                // log_admin_activity("Approved tutor ID: $user_id"); // Assuming log_admin_activity is in admin-auth.php or config.php
            } elseif ($action === 'reject') {
                // In a real scenario, you might want to ensure rejection_reason is provided if it's mandatory
                // For this example, it's optional from the modal but can be logged.
                $stmt = $conn->prepare("UPDATE users SET status = 'rejected', rejection_reason = ?, reviewed_at = NOW(), reviewed_by = ? WHERE user_id = ? AND role = 'tutor'");
                $stmt->execute([$rejection_reason, $_SESSION['user_id'], $user_id]);
                $_SESSION['success_message'] = "Tutor rejected successfully.";
                // log_admin_activity("Rejected tutor ID: $user_id. Reason: $rejection_reason");
            }
            // Prevent form resubmission issues
            header("Location: admin-tutors.php" . (isset($_GET['filter']) ? "?filter=" . htmlspecialchars($_GET['filter']) : ""));
            exit();
        } catch (PDOException $e) {
            error_log("Admin tutor action failed: " . $e->getMessage());
            $_SESSION['error_message'] = "Database error during tutor action. " . $e->getMessage();
        }
    }
     // If error message was set due to CSRF or DB error, redirect to show it
    if (isset($_SESSION['error_message'])) {
        header("Location: admin-tutors.php" . (isset($_GET['filter']) ? "?filter=" . htmlspecialchars($_GET['filter']) : ""));
        exit();
    }
}


// Get filter parameter
$filter = isset($_GET['filter']) ? sanitize_input($_GET['filter']) : 'all';

// Build base query
$query = "SELECT user_id, name, email, status, created_at, approved_at, rejection_reason FROM users WHERE role = 'tutor'";
$params = [];

switch ($filter) {
    case 'pending':
        $query .= " AND status = 'pending'";
        break;
    case 'approved':
        $query .= " AND status = 'approved'";
        break;
    case 'rejected':
        $query .= " AND status = 'rejected'";
        break;
    case 'all':
    default:
        // No additional status filter for 'all'
        break;
}
$query .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching tutors: " . $e->getMessage());
    $_SESSION['error_message'] = "Error fetching tutors: " . $e->getMessage(); // Display error via SweetAlert
    $tutors = []; // Ensure $tutors is an empty array on error
}

?>

<div class="container-fluid py-4">
    <script>
    // This script is for SweetAlert if not already handled by header.php for session messages
    // It's generally better to have one central place (like header.php) for this.
    // If header.php already has this, this block can be removed.
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Tutors</h6>
            <div>
                <a href="?filter=all" class="btn btn-sm btn-outline-secondary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
                <a href="?filter=pending" class="btn btn-sm btn-outline-warning <?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="?filter=approved" class="btn btn-sm btn-outline-success <?= $filter === 'approved' ? 'active' : '' ?>">Approved</a>
                <a href="?filter=rejected" class="btn btn-sm btn-outline-danger <?= $filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableTutors" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tutors)): ?>
                            <tr><td colspan="6" class="text-center">No tutors found for this filter.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tutors as $tutor): ?>
                            <tr>
                                <td><?= htmlspecialchars($tutor['user_id']) ?></td>
                                <td><?= htmlspecialchars($tutor['name']) ?></td>
                                <td><?= htmlspecialchars($tutor['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $tutor['status'] === 'approved' ? 'success' : 
                                        ($tutor['status'] === 'rejected' ? 'danger' : 
                                        ($tutor['status'] === 'pending' ? 'warning' : 'secondary')) 
                                    ?>">
                                        <?= ucfirst(htmlspecialchars($tutor['status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($tutor['created_at']))) ?></td>
                                <td style="min-width:150px;">
                                    <?php if ($tutor['status'] === 'pending'): ?>
                                        <form method="POST" action="admin-tutors.php<?= !empty($filter) ? '?filter='.htmlspecialchars($filter) : '' ?>" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <input type="hidden" name="user_id" value="<?= $tutor['user_id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve Tutor"><i class="fas fa-check"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" title="Reject Tutor" data-bs-toggle="modal" data-bs-target="#rejectTutorModal<?= $tutor['user_id'] ?>"><i class="fas fa-times"></i></button>
                                    <?php elseif ($tutor['status'] === 'rejected' && !empty($tutor['rejection_reason'])): ?>
                                         <button type="button" class="btn btn-sm btn-secondary" title="View Rejection Reason" data-bs-toggle="modal" data-bs-target="#viewReasonModal<?= $tutor['user_id'] ?>"><i class="fas fa-eye"></i></button>
                                    <?php endif; ?>
                                    <a href="admin-edit-user.php?user_id=<?= $tutor['user_id'] ?>" class="btn btn-sm btn-info" title="Edit User Details"><i class="fas fa-edit"></i></a>
                                    
                                    <!-- Reject Tutor Modal -->
                                    <div class="modal fade" id="rejectTutorModal<?= $tutor['user_id'] ?>" tabindex="-1" aria-labelledby="rejectTutorModalLabel<?= $tutor['user_id'] ?>" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <form method="POST" action="admin-tutors.php<?= !empty($filter) ? '?filter='.htmlspecialchars($filter) : '' ?>">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="rejectTutorModalLabel<?= $tutor['user_id'] ?>">Reject Tutor: <?= htmlspecialchars($tutor['name']) ?></h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                              <input type="hidden" name="user_id" value="<?= $tutor['user_id'] ?>">
                                              <input type="hidden" name="action" value="reject">
                                              <!-- Removed rejection_required hidden input, server-side logic will decide if reason is truly mandatory -->
                                              <div class="mb-3">
                                                <label for="rejection_reason<?= $tutor['user_id'] ?>" class="form-label">Reason for Rejection (Optional but Recommended):</label>
                                                <textarea class="form-control" id="rejection_reason<?= $tutor['user_id'] ?>" name="rejection_reason" rows="3"></textarea>
                                              </div>
                                            </div>
                                            <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                              <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                            </div>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                    
                                    <!-- View Rejection Reason Modal -->
                                     <?php if ($tutor['status'] === 'rejected' && !empty($tutor['rejection_reason'])): ?>
                                    <div class="modal fade" id="viewReasonModal<?= $tutor['user_id'] ?>" tabindex="-1" aria-labelledby="viewReasonModalLabel<?= $tutor['user_id'] ?>" aria-hidden="true">
                                      <div class="modal-dialog">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="viewReasonModalLabel<?= $tutor['user_id'] ?>">Rejection Reason: <?= htmlspecialchars($tutor['name']) ?></h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                              <p><?= nl2br(htmlspecialchars($tutor['rejection_reason'])) ?></p>
                                            </div>
                                            <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                          </div>
                                      </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/includes/footer.php';
?>
