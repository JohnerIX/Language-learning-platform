<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$course = [];
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $course_id = (int)$_POST['course_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $difficulty = trim($_POST['difficulty']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    // Validate inputs
    if (empty($title)) {
        $errors['title'] = "Course title is required";
    }
    if (empty($description)) {
        $errors['description'] = "Description is required";
    }
    if (empty($category)) {
        $errors['category'] = "Category is required";
    }
    
    // If no errors, update course
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                UPDATE courses 
                SET title = ?, description = ?, category = ?, difficulty = ?, is_published = ?, updated_at = NOW()
                WHERE course_id = ?
            ");
            $stmt->execute([$title, $description, $category, $difficulty, $is_published, $course_id]);
            
            $success = true;
            $_SESSION['success'] = "Course updated successfully!";
            
            // Get updated course data
            $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch();
            
        } catch (PDOException $e) {
            $errors['database'] = "Database error: " . $e->getMessage();
        }
    }
} else {
    // GET request - load course data
    if (isset($_GET['id'])) {
        $course_id = (int)$_GET['id'];
        
        try {
            $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch();
            
            if (!$course) {
                $_SESSION['error'] = "Course not found";
                header("Location: manage-courses.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: manage-courses.php");
            exit();
        }
    } else {
        header("Location: manage-courses.php");
        exit();
    }
}

$pageTitle = "Edit Course: " . htmlspecialchars($course['title'] ?? '');
require __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Edit Course</h1>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                    <a href="course-details.php?id=<?= $course_id ?>" class="btn btn-sm btn-outline-success ms-3">
                        View Course
                    </a>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (!empty($errors['database'])): ?>
                <div class="alert alert-danger"><?= $errors['database'] ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                
                <div class="mb-3">
                    <label for="title" class="form-label">Course Title *</label>
                    <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                           id="title" name="title" value="<?= htmlspecialchars($course['title'] ?? '') ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= $errors['title'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                              id="description" name="description" rows="5"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" 
                                id="category" name="category">
                            <option value="">Select Category</option>
                            <option value="Beginner" <?= ($course['category'] ?? '') === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= ($course['category'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= ($course['category'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                            <option value="Specialized" <?= ($course['category'] ?? '') === 'Specialized' ? 'selected' : '' ?>>Specialized</option>
                        </select>
                        <?php if (isset($errors['category'])): ?>
                            <div class="invalid-feedback"><?= $errors['category'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="difficulty" class="form-label">Difficulty Level</label>
                        <select class="form-select" id="difficulty" name="difficulty">
                            <option value="Easy" <?= ($course['difficulty'] ?? '') === 'Easy' ? 'selected' : '' ?>>Easy</option>
                            <option value="Medium" <?= ($course['difficulty'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="Hard" <?= ($course['difficulty'] ?? '') === 'Hard' ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_published" 
                           name="is_published" <?= ($course['is_published'] ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_published">Publish this course</label>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="manage-courses.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
            
            <!-- Course Sections Management -->
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Course Sections</h3>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                        <i class="fas fa-plus"></i> Add Section
                    </button>
                </div>
                
                <?php
                // Fetch course sections
                $stmt = $conn->prepare("SELECT * FROM course_sections WHERE course_id = ? ORDER BY `order` ASC");
                $stmt->execute([$course['course_id']]);
                $sections = $stmt->fetchAll();
                ?>
                
                <?php if (empty($sections)): ?>
                    <div class="alert alert-info">No sections added yet.</div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($sections as $section): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($section['title']) ?></strong>
                                    <small class="text-muted ms-2">Order: <?= $section['order'] ?></small>
                                </div>
                                <div>
                                    <a href="edit-section.php?id=<?= $section['section_id'] ?>" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="delete-section.php" method="post" class="d-inline">
                                        <input type="hidden" name="section_id" value="<?= $section['section_id'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Delete this section and all its lessons?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="add-section.php" method="post">
                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sectionTitle" class="form-label">Section Title</label>
                        <input type="text" class="form-control" id="sectionTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="sectionOrder" class="form-label">Order</label>
                        <input type="number" class="form-control" id="sectionOrder" name="order" 
                               value="<?= count($sections) + 1 ?>" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>