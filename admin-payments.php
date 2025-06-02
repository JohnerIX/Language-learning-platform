<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-auth.php'; // Ensure admin is logged in

$pageTitle = "View Payments - Admin";
require __DIR__ . '/includes/header.php';

// Fetch payments - basic example
$payments = [];
$error = '';
try {
    // Assuming a 'payments' table and joining with users and courses for more details
    $sql = "SELECT p.payment_id, u.name AS user_name, c.title AS course_title, p.amount, p.status, p.payment_date 
            FROM payments p
            JOIN users u ON p.user_id = u.user_id
            JOIN courses c ON p.course_id = c.course_id
            ORDER BY p.payment_date DESC LIMIT 20";
    // Check if payments table exists to prevent error if not yet created
    $table_exists = $conn->query("SHOW TABLES LIKE 'payments'")->rowCount() > 0;
    if ($table_exists) {
       $stmt = $conn->query($sql);
       $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
       $error = "Notice: 'payments' table does not exist in the database.";
    }
} catch (PDOException $e) {
    $error = "Error fetching payments: " . $e->getMessage();
    // Log error: error_log($error);
}
?>

<div class="container mt-4">
    <h1 class="mb-4">View Payments</h1>

    <?php if ($error): ?>
        <div class="alert <?= strpos($error, 'Notice:') === 0 ? 'alert-info' : 'alert-danger' ?>"><?= htmlspecialchars($error) ?></div>
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
            Payment History
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>User</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments) && $table_exists): ?>
                        <tr>
                            <td colspan="7" class="text-center">No payments found.</td>
                        </tr>
                    <?php elseif (!$table_exists): ?>
                        <tr>
                            <td colspan="7" class="text-center">Payments table not set up.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                                <td><?= htmlspecialchars($payment['user_name']) ?></td>
                                <td><?= htmlspecialchars($payment['course_title']) ?></td>
                                <td><?= htmlspecialchars(number_format($payment['amount'], 2)) ?></td>
                                <td><span class="badge bg-<?= $payment['status'] === 'success' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                    <?= htmlspecialchars(ucfirst($payment['status'])) ?>
                                </span></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($payment['payment_date']))) ?></td>
                                <td>
                                    <a href="admin-view-payment.php?payment_id=<?= $payment['payment_id'] ?>" class="btn btn-sm btn-primary">View Details</a>
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
