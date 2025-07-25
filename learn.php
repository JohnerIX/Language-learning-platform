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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Learning Platform</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: all 0.3s;
            overflow-y: auto;
        }
        .lesson-content img {
            max-width: 100%;
            height: auto;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(25, 135, 84, 0.1);
        }
        .list-group-item:hover {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100">
        <div class="row">
            <!-- Sidebar - Course Navigation -->
            <div class="col-md-3 col-lg-2 d-md-block bg-black sidebar collapse show py-3" id="sidebarMenu">
                <div class="position-sticky pt-3">
                    <button class="btn btn-sm btn-success d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                        <i class="fas fa-bars"></i> Toggle Menu
                    </button>
                    
                    <h5 class="text-success mb-3 border-bottom border-success pb-2">
                        <i class="fas fa-book me-2"></i><?= htmlspecialchars($course['title']) ?>
                    </h5>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4 p-3 bg-dark rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Course Progress</small>
                            <small class="text-success fw-bold"><?= $completion_percentage ?>%</small>
                        </div>
                        <div class="progress bg-secondary" style="height: 8px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: <?= $completion_percentage ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Course Sections -->
                    <div class="accordion accordion-flush" id="courseAccordion">
                        <?php foreach ($sections as $section): ?>
                        <div class="accordion-item bg-dark border-success mb-2 rounded">
                            <h2 class="accordion-header" id="heading<?= $section['section_id'] ?>">
                                <button class="accordion-button collapsed bg-dark text-light d-flex align-items-center py-3" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?= $section['section_id'] ?>"
                                        aria-expanded="false" 
                                        aria-controls="collapse<?= $section['section_id'] ?>">
                                    <i class="fas fa-folder-open me-2 text-success"></i>
                                    <span class="flex-grow-1 text-start"><?= htmlspecialchars($section['title']) ?></span>
                                    <span class="badge bg-success rounded-pill ms-2">
                                        <?= count(array_filter($lessons, fn($l) => $l['section_id'] == $section['section_id'])) ?>
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse<?= $section['section_id'] ?>" 
                                 class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?= $section['section_id'] ?>"
                                 data-bs-parent="#courseAccordion">
                                <div class="accordion-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($lessons as $lesson): ?>
                                            <?php if ($lesson['section_id'] == $section['section_id']): ?>
                                            <li class="list-group-item bg-dark border-success border-top-0 border-start-0 border-end-0">
                                                <a href="learn.php?course_id=<?= $course_id ?>&lesson=<?= $lesson['lesson_id'] ?>" 
                                                   class="d-flex justify-content-between align-items-center text-decoration-none p-2 rounded
                                                          <?= $current_lesson && $lesson['lesson_id'] == $current_lesson['lesson_id'] ? 'bg-success bg-opacity-10 text-success fw-bold' : 'text-light' ?>">
                                                    <span class="d-flex align-items-center">
                                                        <?php if ($lesson['completed_at']): ?>
                                                            <i class="fas fa-check-circle text-success me-3"></i>
                                                        <?php elseif ($lesson['last_accessed']): ?>
                                                            <i class="fas fa-play-circle text-warning me-3"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-circle me-3 text-muted"></i>
                                                        <?php endif; ?>
                                                        <?= htmlspecialchars($lesson['title']) ?>
                                                    </span>
                                                    <span class="badge bg-<?= $lesson['has_quiz'] ? 'info' : 'secondary' ?> rounded-pill">
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
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <div><?= $_SESSION['success'] ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-success pb-3">
                    <div>
                        <h2 class="text-success mb-1">
                            <?= $current_lesson ? htmlspecialchars($current_lesson['title']) : 'Select a Lesson' ?>
                        </h2>
                        <?php if ($current_lesson): ?>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                <?= $current_lesson['duration'] ? gmdate("i \m\i\\n s \s\\e\\c", $current_lesson['duration']) : 'No duration specified' ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($current_lesson): ?>
                        <form method="post" action="mark-complete.php" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="lesson_id" value="<?= $current_lesson['lesson_id'] ?>">
                            <input type="hidden" name="course_id" value="<?= $course_id ?>">
                            
                            <?php if ($current_lesson['completed_at'] ?? false): ?>
                                <button type="submit" name="action" value="incomplete" 
                                        class="btn btn-outline-warning d-flex align-items-center">
                                    <i class="fas fa-undo me-1"></i> Mark Incomplete
                                </button>
                            <?php else: ?>
                                <button type="submit" name="action" value="complete" 
                                        class="btn btn-success d-flex align-items-center">
                                    <i class="fas fa-check me-1"></i> Mark Complete
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if ($current_lesson): ?>
                    <!-- Lesson Content Area -->
                    <div class="card bg-dark border-success mb-4">
                        <div class="card-body p-4">
                            <?php
                            $has_video = !empty($current_lesson['video_url']);
                            $has_audio = !empty($current_lesson['audio_url']);
                            $has_text_content = !empty(trim($current_lesson['content']));
                            ?>

                            <?php if ($has_video): ?>
                                <div class="ratio ratio-16x9 mb-4 rounded overflow-hidden border border-success">
                                    <video controls class="w-100 bg-black" 
                                           poster="<?= htmlspecialchars($current_lesson['thumbnail_url'] ?? '') ?>">
                                        <source src="<?= htmlspecialchars($current_lesson['video_url']) ?>" type="video/mp4">
                                        <p class="text-light bg-dark p-2">Your browser doesn't support HTML5 video. If video does not load, the source might be unavailable or in an unsupported format.</p>
                                    </video>
                                </div>
                            <?php elseif ($has_audio): ?>
                                <div class="mb-4 p-3 bg-black rounded border border-success">
                                    <audio controls class="w-100">
                                        <source src="<?= htmlspecialchars($current_lesson['audio_url']) ?>" type="audio/mpeg">
                                        <p class="text-light bg-dark p-2">Your browser doesn't support HTML5 audio. If audio does not load, the source might be unavailable or in an unsupported format.</p>
                                    </audio>
                                </div>
                            <?php endif; ?>

                            // Lesson Content
                            <div class="lesson-content mb-4 p-3 bg-black rounded border border-success text-light">
                                <?php if ($has_text_content): ?>
                                    <?= $current_lesson['content'] // Assuming this is HTML or safe to output directly ?>
                                <?php elseif (!$has_video && !$has_audio): ?>
                                    <p class="text-muted">No content (video, audio, or text) available for this lesson.</p>
                                <?php elseif ($has_video || $has_audio): ?>
                                    <p class="text-muted">No additional textual content for this lesson. Please refer to the media above.</p>
                                <?php endif; ?>
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
                                    <h5 class="text-success mb-3 d-flex align-items-center">
                                        <i class="fas fa-paperclip me-2"></i> Attachments
                                    </h5>
                                    <div class="list-group">
                                        <?php foreach ($attachments as $file): ?>
                                            <a href="<?= htmlspecialchars($file['file_url']) ?>" 
                                               class="list-group-item list-group-item-action bg-dark border-success text-light d-flex justify-content-between align-items-center"
                                               target="_blank">
                                                <span class="d-flex align-items-center">
                                                    <i class="fas 
                                                        <?= strpos($file['mime_type'], 'pdf') !== false ? 'fa-file-pdf text-danger' : 
                                                           (strpos($file['mime_type'], 'zip') !== false ? 'fa-file-archive text-warning' : 
                                                           'fa-file-download text-success') ?> 
                                                        me-3"></i>
                                                    <?= htmlspecialchars($file['filename']) ?>
                                                </span>
                                                <small class="text-muted">(<?= formatFileSize($file['file_size']) ?>)</small>
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
                                <div class="mt-4 pt-3 border-top border-success text-center">
                                    <a href="quiz.php?lesson_id=<?= $current_lesson['lesson_id'] ?>" 
                                       class="btn btn-outline-info px-4">
                                        <i class="fas fa-question-circle me-2"></i> Take Quiz
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4">
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
                               class="btn btn-outline-success px-4">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </a>
                        <?php else: ?>
                            <span class="btn btn-outline-secondary disabled px-4">
                                <i class="fas fa-arrow-left me-2"></i> Previous
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
                               class="btn btn-success px-4">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php else: ?>
                            <form method="post" action="mark-complete.php" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                                <input type="hidden" name="lesson_id" value="<?= $current_lesson['lesson_id'] ?>">
                                <button type="submit" name="action" value="complete" 
                                        class="btn btn-success px-4">
                                    Complete Section <i class="fas fa-check ms-2"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- No lesson selected -->
                    <div class="card bg-dark border-success">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-book-open fa-4x text-success mb-4"></i>
                            <h3 class="text-success mb-3">Welcome to <?= htmlspecialchars($course['title']) ?></h3>
                            <p class="text-light mb-4">Select a lesson from the sidebar to begin your learning journey</p>
                            <div class="d-inline-block p-3 bg-black rounded border border-success">
                                <i class="fas fa-arrow-left text-success me-2"></i>
                                <span class="text-light">Course content is available in the sidebar</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure proper accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all accordions
            var accordions = document.querySelectorAll('.accordion-button');
            accordions.forEach(function(button) {
                button.addEventListener('click', function() {
                    var target = this.getAttribute('data-bs-target');
                    var collapse = document.querySelector(target);
                    new bootstrap.Collapse(collapse, {
                        toggle: true
                    });
                });
            });
            
            // Mobile sidebar toggle
            var sidebar = document.getElementById('sidebarMenu');
            var toggler = document.querySelector('[data-bs-target="#sidebarMenu"]');
            if (toggler) {
                toggler.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>

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