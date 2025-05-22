<?php
require_once __DIR__ . '/includes/config.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    redirect('login.php');
}

$pageTitle = "Tutor Dashboard";
require __DIR__ . '/includes/header.php';

// Fetch tutor's courses and earnings
$stmt = $conn->prepare("
    SELECT c.course_id, c.title, c.status, 
           COUNT(e.enrollment_id) as enrollments,
           SUM(p.amount) as earnings
    FROM courses c
    LEFT JOIN enrollments e ON c.course_id = e.course_id
    LEFT JOIN payments p ON e.payment_id = p.payment_id
    WHERE c.tutor_id = ?
    GROUP BY c.course_id
");
$stmt->execute([$_SESSION['user_id']]);
$courses = $stmt->fetchAll();

// Calculate total earnings
$totalEarnings = array_sum(array_column($courses, 'earnings'));
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Total Earnings</h5>
                    <h2>UGX <?= number_format($totalEarnings) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Active Courses</h5>
                    <h2><?= count($courses) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Total Students</h5>
                    <h2><?= array_sum(array_column($courses, 'enrollments')) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5>My Courses</h5>
                <a href="create-course.php" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> New Course
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Students</th>
                            <th>Earnings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['title']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $course['status'] === 'published' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($course['status']) ?>
                                    </span>
                                </td>
                                <td><?= $course['enrollments'] ?></td>
                                <td>UGX <?= number_format($course['earnings']) ?></td>
                                <td>
                                    <a href="edit-course.php?id=<?= $course['course_id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
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

<?php require __DIR__ . '/includes/footer.php'; ?>