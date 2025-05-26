<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start debugging session
error_log("\n\n=== NEW REQUEST ===");
error_log("REQUEST URI: " . $_SERVER['REQUEST_URI']);
error_log("SESSION user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log("GET course_id: " . ($_GET['course_id'] ?? 'NOT SET'));
error_log("GET lesson: " . ($_GET['lesson'] ?? 'NOT SET'));

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Debug after includes
error_log("After includes - SESSION user_id: " . ($_SESSION['user_id'] ?? 'STILL NOT SET'));

// Verify user is logged in and has access
if (!isset($_SESSION['user_id'])) {
    error_log("REDIRECT TRIGGERED: User not logged in");
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['error'] = "Please login to access courses";
    die("<div style='background:red;color:white;padding:20px;'>DEBUG: Would redirect to login.php here. User ID not set in session.</div>");
    // header("Location: login.php");
    // exit();
}

if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    error_log("REDIRECT TRIGGERED: Invalid course_id - " . ($_GET['course_id'] ?? 'NULL'));
    $_SESSION['error'] = "Invalid course specified";
    die("<div style='background:red;color:white;padding:20px;'>DEBUG: Would redirect to courses.php here. Invalid course_id: " . htmlspecialchars($_GET['course_id'] ?? 'NULL') . "</div>");
    // header("Location: courses.php");
    // exit();
}

$course_id = (int)$_GET['course_id'];
$lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : null;

error_log("Processing course_id: $course_id, lesson_id: " . ($lesson_id ?? 'NULL'));

try {
    // Verify subscription
    error_log("Checking subscription for user_id: {$_SESSION['user_id']}, course_id: $course_id");
    $stmt = $conn->prepare("
        SELECT 1 FROM subscriptions 
        WHERE user_id = ? AND course_id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $subscriptionExists = $stmt->fetch();
    
    if (!$subscriptionExists) {
        error_log("REDIRECT TRIGGERED: User not enrolled in course");
        $_SESSION['error'] = "You're not enrolled in this course";
        die("<div style='background:red;color:white;padding:20px;'>DEBUG: Would redirect to course-details.php here. User {$_SESSION['user_id']} not enrolled in course $course_id</div>");
        // header("Location: course-details.php?id=$course_id");
        // exit();
    }

    // Get course details
    error_log("Fetching course details for course_id: $course_id");
    $stmt = $conn->prepare("
        SELECT c.*, u.name AS tutor_name
        FROM courses c
        JOIN users u ON c.tutor_id = u.user_id
        WHERE c.course_id = ?
    ");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        error_log("ERROR: Course not found for course_id: $course_id");
        die("<div style='background:red;color:white;padding:20px;'>Course not found!</div>");
    }

    // Get all course sections
    $stmt = $conn->prepare("
        SELECT * FROM course_sections
        WHERE course_id = ?
        ORDER BY `order` ASC
    ");
    $stmt->execute([$course_id]);
    $sections = $stmt->fetchAll();

    // Get current lesson (if specified)
    $current_lesson = null;
    if ($lesson_id) {
        error_log("Fetching lesson details for lesson_id: $lesson_id");
        $stmt = $conn->prepare("
            SELECT l.*, s.title AS section_title
            FROM lessons l
            JOIN course_sections s ON l.section_id = s.section_id
            WHERE l.lesson_id = ? AND s.course_id = ?
        ");
        $stmt->execute([$lesson_id, $course_id]);
        $current_lesson = $stmt->fetch();

        if (!$current_lesson) {
            error_log("REDIRECT TRIGGERED: Lesson not found");
            $_SESSION['error'] = "Lesson not found";
            die("<div style='background:red;color:white;padding:20px;'>DEBUG: Would redirect to learn.php?course_id=$course_id here. Lesson $lesson_id not found.</div>");
            // header("Location: learn.php?course_id=$course_id");
            // exit();
        }

        // Record lesson progress
        $stmt = $conn->prepare("
            INSERT INTO lesson_progress (user_id, lesson_id, started_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE last_accessed = NOW()
        ");
        $stmt->execute([$_SESSION['user_id'], $lesson_id]);
    }

    // Get all lessons with progress
    $stmt = $conn->prepare("
        SELECT l.lesson_id, l.title, l.duration, l.section_id, 
               lp.completed_at, lp.last_accessed,
               COUNT(q.quiz_id) AS has_quiz
        FROM lessons l
        JOIN course_sections s ON l.section_id = s.section_id
        LEFT JOIN lesson_progress lp ON l.lesson_id = lp.lesson_id AND lp.user_id = ?
        LEFT JOIN quizzes q ON l.lesson_id = q.lesson_id
        WHERE s.course_id = ?
        GROUP BY l.lesson_id
        ORDER BY s.`order`, l.`order`
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $lessons = $stmt->fetchAll();

    // Calculate course progress
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total_lessons,
               SUM(CASE WHEN lp.completed_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_lessons
        FROM lessons l
        JOIN course_sections s ON l.section_id = s.section_id
        LEFT JOIN lesson_progress lp ON l.lesson_id = lp.lesson_id AND lp.user_id = ?
        WHERE s.course_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $progress = $stmt->fetch();
    $completion_percentage = $progress['total_lessons'] > 0 ? 
        round(($progress['completed_lessons'] / $progress['total_lessons']) * 100) : 0;

} catch (PDOException $e) {
    error_log("DATABASE ERROR: " . $e->getMessage());
    error_log("SQLSTATE: " . $e->getCode());
    $_SESSION['error'] = "Error loading course content: " . $e->getMessage();
    die("<div style='background:red;color:white;padding:20px;'>DATABASE ERROR: " . htmlspecialchars($e->getMessage()) . "</div>");
    // header("Location: courses.php");
    // exit();
}

// Debug before rendering page
error_log("Rendering page for course_id: $course_id");
$pageTitle = "Learning: " . $course['title'];
require __DIR__ . '/includes/header.php';
?>


<div class="container-fluid bg-dark text-light min-vh-100">
    <div class="row">
        <!-- Sidebar - Course Navigation -->
        <div class="col-md-3 col-lg-2 d-md-block bg-black sidebar collapse py-3">
            <div class="position-sticky pt-3">
                <h5 class="text-success mb-3"><?= htmlspecialchars($course['title']) ?></h5>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Course Progress</small>
                        <small><?= $completion_percentage ?>%</small>
                    </div>
                    <div class="progress bg-secondary" style="height: 6px;">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: <?= $completion_percentage ?>%">
                        </div>
                    </div>
                </div>
                
                <!-- Course Sections -->
                <div class="accordion accordion-flush" id="courseAccordion">
                    <?php foreach ($sections as $section): ?>
                    <div class="accordion-item bg-dark border-success">
                        <h2 class="accordion-header" id="heading<?= $section['section_id'] ?>">
                            <button class="accordion-button collapsed bg-dark text-light" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?= $section['section_id'] ?>"
                                    aria-expanded="false">
                                <?= htmlspecialchars($section['title']) ?>
                                <span class="badge bg-success ms-2">
                                    <?= count(array_filter($lessons, fn($l) => $l['section_id'] == $section['section_id'])) ?>
                                </span>
                            </button>
                        </h2>
                        <div id="collapse<?= $section['section_id'] ?>" 
                             class="accordion-collapse collapse" 
                             aria-labelledby="heading<?= $section['section_id'] ?>">
                            <div class="accordion-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($lessons as $lesson): ?>
                                        <?php if ($lesson['section_id'] == $section['section_id']): ?>
                                        <li class="list-group-item bg-dark border-success">
                                            <a href="learn.php?course_id=<?= $course_id ?>&lesson=<?= $lesson['lesson_id'] ?>" 
                                               class="d-flex justify-content-between align-items-center text-decoration-none 
                                                      <?= $current_lesson && $lesson['lesson_id'] == $current_lesson['lesson_id'] ? 'text-success fw-bold' : 'text-light' ?>">
                                                <span>
                                                    <?php if ($lesson['completed_at']): ?>
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                    <?php elseif ($lesson['last_accessed']): ?>
                                                        <i class="fas fa-play-circle text-warning me-2"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-circle me-2"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($lesson['title']) ?>
                                                </span>
                                                <span class="badge bg-<?= $lesson['has_quiz'] ? 'info' : 'secondary' ?>">
                                                    <?= $lesson['duration'] ? gmdate("i:s", $lesson['duration']) : '--:--' ?>
                                                </span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-success">
                    <?= $current_lesson ? htmlspecialchars($current_lesson['title']) : 'Select a Lesson' ?>
                </h2>
                
                <?php if ($current_lesson): ?>
                    <form method="post" action="mark-complete.php" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="lesson_id" value="<?= $current_lesson['lesson_id'] ?>">
                        <input type="hidden" name="course_id" value="<?= $course_id ?>">
                        
                        <?php if ($current_lesson['completed_at'] ?? false): ?>
                            <button type="submit" name="action" value="incomplete" 
                                    class="btn btn-outline-warning">
                                <i class="fas fa-undo"></i> Mark Incomplete
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action" value="complete" 
                                    class="btn btn-success">
                                <i class="fas fa-check"></i> Mark Complete
                            </button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>

            <?php if ($current_lesson): ?>
                <!-- Lesson Content Area -->
                <div class="card bg-dark border-success mb-4">
                    <div class="card-body">
                        <!-- Video/Audio Player -->
                        <?php if ($current_lesson['video_url']): ?>
                            <div class="ratio ratio-16x9 mb-4">
                                <video controls class="w-100" 
                                       poster="<?= htmlspecialchars($current_lesson['thumbnail_url'] ?? '') ?>">
                                    <source src="<?= htmlspecialchars($current_lesson['video_url']) ?>" 
                                            type="video/mp4">
                                    Your browser doesn't support HTML5 video.
                                </video>
                            </div>
                        <?php elseif ($current_lesson['audio_url']): ?>
                            <div class="mb-4">
                                <audio controls class="w-100">
                                    <source src="<?= htmlspecialchars($current_lesson['audio_url']) ?>" 
                                            type="audio/mpeg">
                                    Your browser doesn't support HTML5 audio.
                                </audio>
                            </div>
                        <?php endif; ?>

                        <!-- Lesson Content -->
                        <div class="lesson-content mb-4">
                            <?= $current_lesson['content'] ?>
                        </div>

                        <!-- Attachments -->
                        <?php 
                        $stmt = $conn->prepare("
                            SELECT * FROM lesson_attachments
                            WHERE lesson_id = ?
                            ORDER BY created_at DESC
                        ");
                        $stmt->execute([$current_lesson['lesson_id']]);
                        $attachments = $stmt->fetchAll();
                        
                        if (!empty($attachments)): ?>
                            <div class="mb-4">
                                <h5 class="text-success mb-3">Attachments</h5>
                                <div class="list-group">
                                    <?php foreach ($attachments as $file): ?>
                                        <a href="<?= htmlspecialchars($file['file_url']) ?>" 
                                           class="list-group-item list-group-item-action bg-dark border-success text-light"
                                           target="_blank">
                                            <i class="fas 
                                                <?= strpos($file['mime_type'], 'pdf') !== false ? 'fa-file-pdf text-danger' : 
                                                   (strpos($file['mime_type'], 'zip') !== false ? 'fa-file-archive text-warning' : 
                                                   'fa-file-download text-success') ?> 
                                                me-2"></i>
                                            <?= htmlspecialchars($file['filename']) ?>
                                            <small class="text-muted ms-2">(<?= formatFileSize($file['file_size']) ?>)</small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Quiz Section -->
                        <?php 
                        $stmt = $conn->prepare("
                            SELECT 1 FROM quizzes
                            WHERE lesson_id = ?
                            LIMIT 1
                        ");
                        $stmt->execute([$current_lesson['lesson_id']]);
                        
                        if ($stmt->fetch()): ?>
                            <div class="mt-4 pt-3 border-top border-success">
                                <a href="quiz.php?lesson_id=<?= $current_lesson['lesson_id'] ?>" 
                                   class="btn btn-outline-info">
                                    <i class="fas fa-question-circle"></i> Take Quiz
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between">
                    <?php
                    // Find previous lesson
                    $prev_lesson = null;
                    $found_current = false;
                    foreach ($lessons as $lesson) {
                        if ($found_current && $lesson['section_id'] == $current_lesson['section_id']) {
                            $prev_lesson = $lesson;
                            break;
                        }
                        if ($lesson['lesson_id'] == $current_lesson['lesson_id']) {
                            $found_current = true;
                        }
                    }
                    ?>
                    
                    <?php if ($prev_lesson): ?>
                        <a href="learn.php?course_id=<?= $course_id ?>&lesson=<?= $prev_lesson['lesson_id'] ?>" 
                           class="btn btn-outline-success">
                            <i class="fas fa-arrow-left"></i> Previous
                        </a>
                    <?php else: ?>
                        <span class="btn btn-outline-secondary disabled">
                            <i class="fas fa-arrow-left"></i> Previous
                        </span>
                    <?php endif; ?>

                    <?php
                    // Find next lesson
                    $next_lesson = null;
                    $found_current = false;
                    foreach (array_reverse($lessons) as $lesson) {
                        if ($found_current && $lesson['section_id'] == $current_lesson['section_id']) {
                            $next_lesson = $lesson;
                            break;
                        }
                        if ($lesson['lesson_id'] == $current_lesson['lesson_id']) {
                            $found_current = true;
                        }
                    }
                    ?>
                    
                    <?php if ($next_lesson): ?>
                        <a href="learn.php?course_id=<?= $course_id ?>&lesson=<?= $next_lesson['lesson_id'] ?>" 
                           class="btn btn-success">
                            Next <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <form method="post" action="mark-complete.php" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="course_id" value="<?= $course_id ?>">
                            <input type="hidden" name="lesson_id" value="<?= $current_lesson['lesson_id'] ?>">
                            <button type="submit" name="action" value="complete" 
                                    class="btn btn-success">
                                Complete Section <i class="fas fa-check"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- No lesson selected -->
                <div class="card bg-dark border-success">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book-open fa-4x text-success mb-4"></i>
                        <h3 class="text-success">Welcome to <?= htmlspecialchars($course['title']) ?></h3>
                        <p class="text-light">Select a lesson from the sidebar to begin learning</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
require __DIR__ . '/includes/footer.php';

// Helper function to format file sizes
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>