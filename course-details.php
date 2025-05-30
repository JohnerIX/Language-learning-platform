<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid course specified";
    header("Location: courses.php");
    exit();
}

$course_id = (int)$_GET['id'];

// Fetch course details
try {
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS tutor_name, u.bio AS tutor_bio, u.profile_pic,
               COUNT(s.user_id) AS student_count,
               AVG(r.rating) AS avg_rating,
               COUNT(r.review_id) AS review_count
        FROM courses c
        JOIN users u ON c.tutor_id = u.user_id
        LEFT JOIN subscriptions s ON c.course_id = s.course_id
        LEFT JOIN reviews r ON c.course_id = r.course_id
        WHERE c.course_id = ? AND c.status = 'published'
        GROUP BY c.course_id
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        $_SESSION['error_message'] = "Course not found or not available";
        header("Location: courses.php");
        exit();
    }

    // Check if user is subscribed
    $is_subscribed = false;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("
            SELECT 1 FROM subscriptions 
            WHERE user_id = ? AND course_id = ?
            LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id'], $course_id]);
        $is_subscribed = (bool)$stmt->fetch();
    }

    // Fetch course sections/lessons
    $stmt = $conn->prepare("
        SELECT * FROM course_sections
        WHERE course_id = ?
        ORDER BY `order` ASC
    ");
    $stmt->execute([$course_id]);
    $sections = $stmt->fetchAll();

    // Fetch recent reviews
    $stmt = $conn->prepare("
        SELECT r.*, u.name, u.profile_pic
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.course_id = ?
        ORDER BY r.created_at DESC
        LIMIT 3
    ");
    $stmt->execute([$course_id]);
    $reviews = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading course details";
    header("Location: courses.php");
    exit();
}

$pageTitle = $course['title'] . " | Learn Lugha";
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <!-- Course Header -->
    <div class="row mb-5">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-<?= 
                    $course['level'] === 'beginner' ? 'success' : 
                    ($course['level'] === 'intermediate' ? 'warning' : 'danger')
                ?> me-2">
                    <?= ucfirst($course['level']) ?>
                </span>
                <?php if ($course['is_featured']): ?>
                    <span class="badge bg-primary">Featured</span>
                <?php endif; ?>
            </div>
            
            <h1 class="mb-3"><?= htmlspecialchars($course['title']) ?></h1>
            
            <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                    <i class="fas fa-users"></i> <?= $course['student_count'] ?> students
                </div>
                <div class="me-3">
                    <i class="fas fa-star text-warning"></i> 
                    <?= number_format($course['avg_rating'] ?? 0, 1) ?> (<?= $course['review_count'] ?> reviews)
                </div>
                <div>
                    <i class="fas fa-clock"></i> 
                    <?= $course['duration'] ?? 'Self-paced' ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <?php if ($is_subscribed): ?>
                        <a href="learn.php?course_id=<?= $course_id ?>" class="btn btn-success btn-lg w-100 mb-3">
                            <i class="fas fa-play-circle"></i> Continue Learning
                        </a>
                    <?php else: ?>
                        <a href="subscribe.php?course_id=<?= $course_id ?>" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-plus-circle"></i> Enroll Now
                        </a>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Price:</span>
                        <strong><?= $course['price'] > 0 ? 'UGX ' . number_format($course['price']) : 'Free' ?></strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Last updated:</span>
                        <strong><?= date('M d, Y', strtotime($course['updated_at'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Content -->
    <div class="row">
        <div class="col-md-8">
            <!-- Course Thumbnail -->
            <div class="mb-4 ratio ratio-16x9">
                <?php
                $raw_thumbnail_url_detail = $course['thumbnail_url'] ?? null;
                $final_thumbnail_url_detail = 'images/default-course.jpg'; // Default fallback

                if (!empty($raw_thumbnail_url_detail)) {
                    if (preg_match('~^https?://~i', $raw_thumbnail_url_detail)) {
                        $final_thumbnail_url_detail = $raw_thumbnail_url_detail;
                    } elseif (strpos($raw_thumbnail_url_detail, 'uploads/course_thumbs/') === 0) {
                        $final_thumbnail_url_detail = $raw_thumbnail_url_detail;
                    } elseif (strpos($raw_thumbnail_url_detail, '/') === false) {
                        $final_thumbnail_url_detail = 'uploads/course_thumbs/' . $raw_thumbnail_url_detail;
                    } else {
                        if (strpos($raw_thumbnail_url_detail, '/') === 0) { // Starts with a slash - root relative
                             $final_thumbnail_url_detail = $raw_thumbnail_url_detail;
                        } else { // A relative path like 'folder/image.jpg' - this is unlikely.
                             $final_thumbnail_url_detail = 'uploads/course_thumbs/' . $raw_thumbnail_url_detail;
                        }
                    }
                }
                $final_thumbnail_url_detail = str_replace('//', '/', $final_thumbnail_url_detail);
                // Check if after potential construction, it's still a valid-looking path or URL
                if (strpos($final_thumbnail_url_detail, 'uploads/course_thumbs/') === 0 && $final_thumbnail_url_detail !== 'uploads/course_thumbs/') {
                    // Path is fine or constructed
                } elseif (!preg_match('~^https?://~i', $final_thumbnail_url_detail)) { // Not a URL and not a valid constructed path
                    $final_thumbnail_url_detail = 'images/default-course.jpg'; // Reset to default if construction is suspect
                }
                
                // Final check for empty or base path only
                if (empty($final_thumbnail_url_detail) || $final_thumbnail_url_detail === 'uploads/course_thumbs/') {
                    $final_thumbnail_url_detail = 'images/default-course.jpg';
                }
                ?>
                <img src="<?= htmlspecialchars($final_thumbnail_url_detail) ?>" 
                     class="img-fluid rounded" 
                     alt="<?= htmlspecialchars($course['title']) ?>"
                     style="background-color: #f0f0f0;"
                     onerror="this.onerror=null; this.src='images/default-course.jpg';">
            </div>
            
            <!-- Course Description -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">About This Course</h3>
                    <div class="course-description">
                        <?= nl2br(htmlspecialchars($course['description'])) ?>
                    </div>
                </div>
            </div>
            
            <!-- What You'll Learn -->
            <?php if (!empty($course['learning_outcomes'])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">What You'll Learn</h3>
                    <ul class="list-group list-group-flush">
                        <?php foreach (explode("\n", $course['learning_outcomes']) as $outcome): ?>
                            <?php if (trim($outcome)): ?>
                                <li class="list-group-item border-0 px-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <?= htmlspecialchars(trim($outcome)) ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Course Curriculum -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Course Content</h3>
                    <div class="accordion" id="courseSections">
                        <?php foreach ($sections as $index => $section): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $index ?>">
                                <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?= $index ?>">
                                    Section <?= $index + 1 ?>: <?= htmlspecialchars($section['title']) ?>
                                    <span class="badge bg-secondary ms-2">
                                        <?= $section['lesson_count'] ?? 0 ?> lessons
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse<?= $index ?>" 
                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                 aria-labelledby="heading<?= $index ?>">
                                <div class="accordion-body">
                                    <?php if ($is_subscribed): ?>
                                        <!-- Display lessons for enrolled students -->
                                        <?php
                                        $stmt = $conn->prepare("
                                            SELECT * FROM lessons
                                            WHERE section_id = ?
                                            ORDER BY `order` ASC
                                        ");
                                        $stmt->execute([$section['section_id']]);
                                        $lessons = $stmt->fetchAll();
                                        ?>
                                        
                                        <ul class="list-group">
                                            <?php foreach ($lessons as $lesson): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="learn.php?course_id=<?= $course_id ?>&lesson=<?= $lesson['lesson_id'] ?>" 
                                                   class="text-decoration-none">
                                                    <i class="fas fa-play-circle text-primary me-2"></i>
                                                    <?= htmlspecialchars($lesson['title']) ?>
                                                </a>
                                                <span class="badge bg-light text-dark">
                                                    <?= gmdate("i:s", $lesson['duration']) ?>
                                                </span>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">
                                            Enroll in the course to access all lessons.
                                        </p>
                                        <p>
                                            This section contains <?= $section['lesson_count'] ?? 0 ?> lessons.
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Reviews -->
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Student Reviews</h3>
                    
                    <?php if (!empty($reviews)): ?>
                        <div class="row">
                            <?php foreach ($reviews as $review): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex mb-3">
                                            <img src="<?= htmlspecialchars($review['profile_pic'] ?? 'assets/default-user.jpg') ?>" 
                                                 class="rounded-circle me-3" 
                                                 width="50" 
                                                 height="50" 
                                                 alt="<?= htmlspecialchars($review['name']) ?>">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($review['name']) ?></h6>
                                                <div class="text-warning">
                                                    <?= str_repeat('★', (int)$review['rating']) ?>
                                                    <?= str_repeat('☆', 5 - (int)$review['rating']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
                                        <small class="text-muted">
                                            <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <a href="course-reviews.php?id=<?= $course_id ?>" class="btn btn-outline-primary mt-3">
                            View All Reviews
                        </a>
                    <?php else: ?>
                        <p class="text-muted">No reviews yet. Be the first to review!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Tutor Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="<?= htmlspecialchars($course['profile_pic'] ?? 'assets/default-user.jpg') ?>" 
                         class="rounded-circle mb-3" 
                         width="120" 
                         height="120" 
                         alt="<?= htmlspecialchars($course['tutor_name']) ?>">
                    
                    <h4><?= htmlspecialchars($course['tutor_name']) ?></h4>
                    <p class="text-muted">Course Instructor</p>
                    
                    <?php if (!empty($course['tutor_bio'])): ?>
                        <p class="card-text"><?= nl2br(htmlspecialchars(substr($course['tutor_bio'], 0, 200))) ?>...</p>
                    <?php endif; ?>
                    
                    <a href="tutor.php?id=<?= $course['tutor_id'] ?>" class="btn btn-outline-primary">
                        View Profile
                    </a>
                </div>
            </div>
            
            <!-- Course Features -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">This Course Includes</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-video text-primary me-2"></i>
                            <?= $course['video_count'] ?? 0 ?> on-demand videos
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            <?= $course['resource_count'] ?? 0 ?> downloadable resources
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-mobile-alt text-primary me-2"></i>
                            Access on mobile and TV
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="fas fa-certificate text-primary me-2"></i>
                            Certificate of completion
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>