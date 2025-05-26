<?php
require_once __DIR__ . '/includes/config.php';

// Verify learner access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'learner') {
    redirect('login.php');
}

$pageTitle = "My Learning Dashboard";
require __DIR__ . '/includes/header.php';

// Fetch learner's active courses
$stmt = $conn->prepare("
    SELECT c.*, 
           COUNT(up.lesson_id) as completed_lessons,
           COUNT(l.lesson_id) as total_lessons
    FROM enrollments e
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN lessons l ON c.course_id = l.course_id
    LEFT JOIN user_progress up ON up.lesson_id = l.lesson_id AND up.user_id = ? AND up.status = 'completed'
    WHERE e.user_id = ?
    GROUP BY c.course_id
    ORDER BY e.enrolled_at DESC
    LIMIT 4
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$my_courses = $stmt->fetchAll();

// Fetch recommended courses
$stmt = $conn->prepare("
    SELECT c.*, u.name as tutor_name 
    FROM courses c
    JOIN users u ON c.tutor_id = u.user_id
    WHERE c.language = (SELECT language_preference FROM user_meta WHERE user_id = ?)
    AND c.course_id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)
    ORDER BY RAND()
    LIMIT 3
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$recommended_courses = $stmt->fetchAll();
?>

<div class="container mb-5">
    <!-- Hero Welcome Section -->
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Continue learning, <?= explode(' ', $_SESSION['user_name'])[0] ?>!</h1>
            <p class="col-md-8 fs-4">Pick up where you left off or discover new courses to expand your language skills.</p>
        </div>
    </div>

    <!-- My Courses Section -->
    <section class="mb-5">
        <h3 class="mb-4">My Courses</h3>
        <div class="row g-4">
            <?php foreach ($my_courses as $course): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 course-card">
                        <div class="course-badge">In Progress</div>
                        <img src="<?= htmlspecialchars($course['thumbnail_url']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($course['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                            <div class="progress mb-2">
                                <div class="progress-bar" 
                                     style="width: <?= ($course['total_lessons'] > 0) ? round(($course['completed_lessons']/$course['total_lessons'])*100) : 0 ?>%">
                                </div>
                            </div>
                            <small class="text-muted">
                                <?= $course['completed_lessons'] ?>/<?= $course['total_lessons'] ?> lessons
                            </small>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="course.php?id=<?= $course['course_id'] ?>" 
   class="btn btn-success w-100">
    Continue
</a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-dashed">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Browse More Courses</h5>
                        <a href="courses.php" class="btn btn-outline-primary mt-2">Explore</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recommended Courses Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Recommended For You</h3>
            <a href="courses.php" class="btn btn-sm btn-outline-secondary">See All</a>
        </div>
        <div class="row g-4">
            <?php foreach ($recommended_courses as $course): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($course['thumbnail_url']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($course['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                            <p class="card-text text-muted">By <?= htmlspecialchars($course['tutor_name']) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary"><?= htmlspecialchars($course['language']) ?></span>
                                <span class="text-success fw-bold">UGX <?= number_format($course['price']) ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="course-detail.php?id=<?= $course['course_id'] ?>" 
                               class="btn btn-outline-primary w-100">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Learning Goals Section -->
    <section>
        <h3 class="mb-4">Your Learning Goals</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Daily Streak</h5>
                        <div class="d-flex align-items-center">
                            <div class="streak-circle bg-primary text-white">3</div>
                            <div class="ms-3">
                                <p class="mb-0">You're on a 3-day streak!</p>
                                <small class="text-muted">Learn today to keep it going</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Weekly Target</h5>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: 60%"></div>
                        </div>
                        <p class="mb-0">3 of 5 lessons completed this week</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .course-card {
        position: relative;
        transition: transform 0.3s;
    }
    .course-card:hover {
        transform: translateY(-5px);
    }
    .course-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #ffc107;
        color: #000;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }
    .border-dashed {
        border: 2px dashed #dee2e6;
    }
    .streak-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>