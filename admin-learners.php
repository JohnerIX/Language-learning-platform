<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-auth.php'; // Ensure admin is logged in

$pageTitle = "Manage Learners - Admin";
require __DIR__ . '/includes/header.php';

// Fetch learners - basic example, can be expanded with pagination, search, etc.
$learners = [];
$error = '';
try {
    $stmt = $conn->query("SELECT user_id, name, email, created_at FROM users WHERE role = 'learner' ORDER BY created_at DESC LIMIT 20");
    $learners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching learners: " . $e->getMessage();
    // Log error: error_log($error);
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Manage Learners</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= addslashes($_SESSION['success_message']) ?>',
                });
            });
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?= addslashes($_SESSION['error_message']) ?>',
                });
            });
        </script>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            Learner List
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($learners)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No learners found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($learners as $learner): ?>
                            <tr>
                                <td><?= htmlspecialchars($learner['user_id']) ?></td>
                                <td><?= htmlspecialchars($learner['name']) ?></td>
                                <td><?= htmlspecialchars($learner['email']) ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($learner['created_at']))) ?></td>
                                <td>
                                    <a href="admin-edit-user.php?user_id=<?= $learner['user_id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="admin-view-user.php?user_id=<?= $learner['user_id'] ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/includes/footer.php';
?>
