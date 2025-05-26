<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Browse Courses";
require __DIR__ . '/includes/header.php';

// Initialize variables
$subscribedCourses = [];
$courses = [];
$error = null;

try {
    // Check subscriptions if logged in
    if (isset($_SESSION['user_id'])) {
        if ($conn->query("SHOW TABLES LIKE 'subscriptions'")->rowCount() > 0) {
            $stmt = $conn->prepare("SELECT course_id FROM subscriptions WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $subscribedCourses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    // Get published courses
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS tutor_name 
        FROM courses c
        JOIN users u ON c.tutor_id = u.user_id
        WHERE c.status = 'published'
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $courses = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    error_log($error);
}
?>

<div class="container py-5">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <h1 class="mb-4">Available Courses</h1>
    
    <?php if (empty($courses)): ?>
        <div class="alert alert-info">No courses available yet.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= htmlspecialchars($course['thumbnail_url'] ?? 'images/default-course.jpg') ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($course['title']) ?>"
                         style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                        <p class="text-muted small mb-2">
                            By <?= htmlspecialchars($course['tutor_name']) ?>
                        </p>
                        <p class="card-text flex-grow-1">
                            <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...
                        </p>
                        
                        <div class="mt-auto">
                            <?php if (isset($_SESSION['user_id']) && in_array($course['course_id'], $subscribedCourses)): ?>
                                <a href="learn.php?course_id=<?= $course['course_id'] ?>" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-play-circle"></i> Start Learning
                                </a>
                            <?php else: ?>
                                <a href="subscribe.php?course_id=<?= $course['course_id'] ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-plus-circle"></i> Subscribe
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white">
                        <a href="course-details.php?id=<?= $course['course_id'] ?>" 
                           class="text-decoration-none">
                            View full details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>