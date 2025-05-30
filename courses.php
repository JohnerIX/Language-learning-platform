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
            <form action="courses.php" method="GET" class="d-flex" style="height: 40px;">
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
                <div class="card h-100 shadow-sm course-card">
                    <?php if ($course['is_featured']): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2" style="z-index:10;">Featured</span>
                    <?php endif; ?>
                    <?php
                    $thumbnail_url = $course['thumbnail_url'] ?? 'images/default-course.jpg';
                    if (!empty($course['thumbnail_url']) && !filter_var($course['thumbnail_url'], FILTER_VALIDATE_URL) && strpos($course['thumbnail_url'], '/') === false) {
                        // If it's not a URL and doesn't contain a slash, assume it's a filename in uploads/course_thumbs/
                        $thumbnail_url = 'uploads/course_thumbs/' . $course['thumbnail_url'];
                    } elseif (empty($course['thumbnail_url'])) {
                        $thumbnail_url = 'images/default-course.jpg'; // Explicit fallback
                    }
                    ?>
                    <img src="<?= htmlspecialchars($thumbnail_url) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($course['title']) ?>"
                         style="height: 200px; object-fit: cover; background-color: #f0f0f0;"
                         onerror="this.onerror=null; this.src='images/default-course.jpg';"> <!-- Fallback for broken images -->
                    
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
                                <a href="course-details.php?id=<?= $course['course_id'] ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-info-circle"></i> View Details & Subscribe
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent border-top-0 text-center">
                         <small class="text-muted">
                            Price: <?= htmlspecialchars($course['price'] > 0 ? 'UGX ' . number_format($course['price'], 2) : 'Free') ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
    transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
}
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>