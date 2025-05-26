<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid tutor specified";
    header("Location: courses.php");
    exit();
}

$tutor_id = (int)$_GET['id'];

try {
    // Get tutor details
    $stmt = $conn->prepare("
        SELECT u.*, 
               COUNT(c.course_id) AS course_count,
               COUNT(s.user_id) AS student_count
        FROM users u
        LEFT JOIN courses c ON u.user_id = c.tutor_id AND c.status = 'published'
        LEFT JOIN subscriptions s ON c.course_id = s.course_id
        WHERE u.user_id = ? AND u.role = 'tutor'
        GROUP BY u.user_id
    ");
    $stmt->execute([$tutor_id]);
    $tutor = $stmt->fetch();

    if (!$tutor) {
        $_SESSION['error'] = "Tutor not found";
        header("Location: courses.php");
        exit();
    }

    // Get tutor's published courses
    $stmt = $conn->prepare("
        SELECT c.*, COUNT(s.user_id) AS student_count
        FROM courses c
        LEFT JOIN subscriptions s ON c.course_id = s.course_id
        WHERE c.tutor_id = ? AND c.status = 'published'
        GROUP BY c.course_id
        ORDER BY c.created_at DESC
        LIMIT 6
    ");
    $stmt->execute([$tutor_id]);
    $courses = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Error loading tutor details";
    header("Location: courses.php");
    exit();
}

$pageTitle = $tutor['name'] . " | Tutor Profile";
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <!-- Tutor Profile Header -->
    <div class="row mb-5">
        <div class="col-md-3 text-center">
            <img src="<?= htmlspecialchars($tutor['profile_pic'] ?? 'assets/default-user.jpg') ?>" 
                 class="rounded-circle border border-3 border-success mb-3" 
                 width="180" 
                 height="180" 
                 alt="<?= htmlspecialchars($tutor['name']) ?>">
            
            <div class="d-flex justify-content-center gap-2 mb-3">
                <?php if (!empty($tutor['twitter'])): ?>
                    <a href="<?= htmlspecialchars($tutor['twitter']) ?>" class="text-dark">
                        <i class="fab fa-x-twitter fa-lg"></i>
                    </a>
                <?php endif; ?>
                <?php if (!empty($tutor['linkedin'])): ?>
                    <a href="<?= htmlspecialchars($tutor['linkedin']) ?>" class="text-dark">
                        <i class="fab fa-linkedin fa-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-9">
            <h1 class="mb-3"><?= htmlspecialchars($tutor['name']) ?></h1>
            
            <div class="d-flex gap-4 mb-4">
                <div class="text-center">
                    <div class="h4 text-success"><?= $tutor['course_count'] ?></div>
                    <div class="text-muted">Courses</div>
                </div>
                <div class="text-center">
                    <div class="h4 text-success"><?= $tutor['student_count'] ?></div>
                    <div class="text-muted">Students</div>
                </div>
            </div>
            
            <?php if (!empty($tutor['bio'])): ?>
                <div class="card bg-light border-success mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">About Me</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($tutor['bio'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tutor's Courses -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-success">My Courses</h2>
                <?php if ($tutor['course_count'] > 6): ?>
                    <a href="courses.php?tutor=<?= $tutor_id ?>" class="btn btn-outline-success">
                        View All Courses
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (empty($courses)): ?>
                <div class="alert alert-dark">
                    This tutor hasn't published any courses yet.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-success">
                            <img src="<?= htmlspecialchars($course['thumbnail_url'] ?? 'assets/default-course.jpg') ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($course['title']) ?>"
                                 style="height: 180px; object-fit: cover;">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                <p class="card-text flex-grow-1 small">
                                    <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="badge bg-success">
                                        <?= $course['student_count'] ?> students
                                    </span>
                                    <a href="course-details.php?id=<?= $course['course_id'] ?>" 
                                       class="btn btn-sm btn-outline-success">
                                        View Course
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>