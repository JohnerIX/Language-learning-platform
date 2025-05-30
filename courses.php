<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Browse Courses";
require __DIR__ . '/includes/header.php';

// Initialize variables
$subscribedCourses = [];
$courses = [];
$error = null;
$searchTerm = isset($_GET['search_term']) ? trim($_GET['search_term']) : '';

try {
    // Check subscriptions if logged in
    if (isset($_SESSION['user_id'])) {
        // Check if subscriptions table exists before querying
        $stmtCheckSubTable = $conn->query("SHOW TABLES LIKE 'subscriptions'");
        if ($stmtCheckSubTable->rowCount() > 0) {
            $stmtSub = $conn->prepare("SELECT course_id FROM subscriptions WHERE user_id = ?");
            $stmtSub->execute([$_SESSION['user_id']]);
            $subscribedCourses = $stmtSub->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    // Base SQL query
    $sql = "
        SELECT c.*, u.name AS tutor_name 
        FROM courses c
        JOIN users u ON c.tutor_id = u.user_id
    ";
    $params = [];
    $conditions = ["c.status = 'published'"]; // Only show published courses

    // Add search condition if search term is provided
    if (!empty($searchTerm)) {
        $conditions[] = "(c.title LIKE ? OR c.description LIKE ?)";
        $params[] = "%" . $searchTerm . "%";
        $params[] = "%" . $searchTerm . "%";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY c.is_featured DESC, c.created_at DESC"; // Featured courses first

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    error_log($error); // Log the detailed error
}
?>

<div class="container py-5">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <h1 class="mb-4">Available Courses</h1>

    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <form action="courses.php" method="GET" class="d-flex">
                <input type="text" name="search_term" class="form-control me-2" 
                       placeholder="Search for courses (e.g., title, keyword)" 
                       value="<?= htmlspecialchars($_GET['search_term'] ?? '') ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
    
    <?php if (!empty($searchTerm) && !$error): ?>
        <h4 class="mb-3">Search results for: "<?= htmlspecialchars($searchTerm) ?>"</h4>
    <?php endif; ?>

    <?php if (empty($courses) && !$error): ?>
        <div class="alert alert-info">
            <?= !empty($searchTerm) ? 'No courses found matching your search term: "' . htmlspecialchars($searchTerm) . '". Try a different keyword.' : 'No courses available at the moment. Please check back later!' ?>
        </div>
    <?php elseif(!empty($courses)): ?>
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