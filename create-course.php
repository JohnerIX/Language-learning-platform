<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    header("Location: login.php");
    exit();
}

$pageTitle = "Create New Course";
require __DIR__ . '/includes/header.php';

// Initialize variables
$error = ''; // Will be set to $_SESSION['error_message'] before redirect
$success = false; // Will be set to $_SESSION['success_message'] before redirect
$courseData = [
    'title' => '',
    'language' => '',
    'level' => '',
    'category' => '', // Added category
    'description' => '',
    'price' => 0,
    'is_free' => 0,
    // duration_hours and duration_minutes removed
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF verification
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            throw new Exception("Invalid CSRF token.");
        }

        // Basic validation for simplified form
        $requiredFields = ['course_title', 'course_language', 'course_level', 'course_description'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . " is required.");
            }
        }

        $course_title = sanitize_input($_POST['course_title']);
        $course_language = sanitize_input($_POST['course_language']);
        $course_level = sanitize_input($_POST['course_level']);
        $course_category = sanitize_input($_POST['course_category'] ?? null);
        $course_description = sanitize_input($_POST['course_description']);

        // Process thumbnail upload (remains largely the same)
        $thumbnailPath = null; // Set to null if not uploaded or error
        if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/course_thumbs/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception("Failed to create thumbnail upload directory.");
                }
            }
            
            $fileExt = strtolower(pathinfo($_FILES['course_thumbnail']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($fileExt, $allowedTypes)) {
                throw new Exception("Only JPG, JPEG, PNG, and GIF images are allowed for thumbnail.");
            }
            
            if ($_FILES['course_thumbnail']['size'] > $maxSize) {
                throw new Exception("Thumbnail image must be less than 2MB.");
            }
            
            $fileName = uniqid('thumb_', true) . '.' . $fileExt; // More unique filename
            $targetPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $targetPath)) {
                throw new Exception("Failed to upload thumbnail image.");
            }
            $thumbnailPath = 'uploads/course_thumbs/' . $fileName; // Relative path for DB
        } else if ($_FILES['course_thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle other upload errors specifically if needed
            throw new Exception("Error uploading thumbnail: " . $_FILES['course_thumbnail']['error']);
        }
        // Thumbnail is optional for draft, but if provided, must be valid.
        // If no thumbnail, $thumbnailPath remains null.

        // Process pricing
        $price = isset($_POST['course_price']) ? floatval($_POST['course_price']) : 0;
        $isFree = isset($_POST['is_free']) ? 1 : 0;
        if ($isFree) {
            $price = 0;
        }

        // Duration is removed, will be calculated later or set in edit-course.php
        // Status is always 'draft' now
        $status = 'draft';
        $duration_minutes = 0; // Default to 0 or NULL

        // Begin transaction
        $conn->beginTransaction();

        try {
            // Save to database (simplified INSERT)
            $stmt = $conn->prepare("
                INSERT INTO courses 
                (tutor_id, title, description, language, level, category, thumbnail_url, price, is_free,
                 status, created_at, updated_at, duration_minutes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $course_title,
                $course_description,
                $course_language,
                $course_level,
                $course_category,
                $thumbnailPath, // This can be NULL if no thumbnail was uploaded
                $price,
                $isFree,
                $status, // 'draft'
                $duration_minutes // 0 or NULL
            ]);

            $new_course_id = $conn->lastInsertId();

            // No curriculum or materials processing here anymore

            // Commit transaction
            $conn->commit();

            $_SESSION['success_message'] = "Course basics saved as draft! Now, let's add content and structure it.";
            header("Location: edit-course.php?id=" . $new_course_id); // Redirect to edit page
            exit();

        } catch (Exception $e) {
            $conn->rollBack();
            // Log detailed error for admin
            error_log("DB Error in create-course.php: " . $e->getMessage());
            // Set user-friendly error message
            $_SESSION['error_message'] = "Database operation failed: " . $e->getMessage();
            // No redirect here, let the catch block below handle form repopulation and error display
            throw $e; // Re-throw to be caught by the outer catch
        }

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        // Preserve form data for repopulation
        $courseData = [
            'title' => $_POST['course_title'] ?? '',
            'language' => $_POST['course_language'] ?? '',
            'level' => $_POST['course_level'] ?? '',
            'category' => $_POST['course_category'] ?? '',
            'description' => $_POST['course_description'] ?? '',
            'price' => $_POST['course_price'] ?? 0,
            'is_free' => isset($_POST['is_free']) ? 1 : 0,
        ];
        // No redirect here, error will be displayed on the same page by SweetAlert
    }
}
?>

<div class="container py-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="courseForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <!-- Course Information -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-info-circle me-2"></i> Course Information</h4>
            </div>
            <div class="card-body bg-light">
                <div class="mb-3">
                    <label for="course_title" class="form-label fw-bold">Course Title*</label>
                    <input type="text" class="form-control" id="course_title" name="course_title" 
                           value="<?= htmlspecialchars($courseData['title']) ?>" maxlength="100" required>
                    <div class="form-text text-end">
                        <span id="titleCounter"><?= strlen($courseData['title']) ?></span>/100 characters
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_language" class="form-label fw-bold">Language*</label>
                        <select class="form-select" id="course_language" name="course_language" required>
                            <option value="">Select language</option>
                            <option value="Luganda" <?= $courseData['language'] === 'Luganda' ? 'selected' : '' ?>>Luganda</option>
                            <option value="Runyoro" <?= $courseData['language'] === 'Runyoro' ? 'selected' : '' ?>>Runyoro</option>
                            <option value="Lusoga" <?= $courseData['language'] === 'Lusoga' ? 'selected' : '' ?>>Lusoga</option>
                            <option value="Rukiga" <?= $courseData['language'] === 'Rukiga' ? 'selected' : '' ?>>Rukiga</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="course_level" class="form-label fw-bold">Level*</label>
                        <select class="form-select" id="course_level" name="course_level" required>
                            <option value="Beginner" <?= $courseData['level'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $courseData['level'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= $courseData['level'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="course_category" class="form-label fw-bold">Category</label>
                        <input type="text" class="form-control" id="course_category" name="course_category"
                               value="<?= htmlspecialchars($courseData['category'] ?? '') ?>" placeholder="e.g., Business, Technology, Arts">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="course_description" class="form-label fw-bold">Short Description*</label>
                    <textarea class="form-control" id="course_description" name="course_description"
                              rows="3" required placeholder="A brief overview of the course (max 200 characters recommended)"><?= htmlspecialchars($courseData['description']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Thumbnail Upload -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-image me-2"></i> Course Thumbnail*</h4>
            </div>
            <div class="card-body bg-light">
                <div class="upload-area bg-white border rounded p-4 text-center" id="thumbnailUploadArea">
                    <input type="file" id="course_thumbnail" name="course_thumbnail" accept="image/*" class="d-none" required>
                    <div class="upload-prompt">
                        <i class="fas fa-cloud-upload-alt fa-3x text-dark mb-3"></i>
                        <p class="fw-bold">Drag & drop your thumbnail here</p>
                        <p class="text-muted">or click to browse files</p>
                        <p class="small text-muted">Recommended: 1280×720px JPG/PNG (Max 2MB)</p>
                    </div>
                    <img id="thumbnailPreview" class="img-fluid mt-3 d-none rounded border">
                    <div class="progress mt-2 d-none" id="uploadProgress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-tag me-2"></i> Pricing</h4>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="course_price" class="form-label fw-bold">Course Price (UGX)</label>
                            <div class="input-group">
                                <span class="input-group-text">UGX</span>
                                <input type="number" class="form-control" id="course_price" name="course_price" 
                                       value="<?= $courseData['price'] ?>" min="0" step="1000" <?= $courseData['is_free'] ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_free" name="is_free" 
                                  <?= $courseData['is_free'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_free">This is a free course</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded border">
                            <h5>Preview</h5>
                            <p class="mb-1">Price: <span id="pricePreview">
                                <?= $courseData['is_free'] ? 'FREE' : 'UGX ' . number_format($courseData['price']) ?>
                            </span></p>
                            <small class="text-muted">Platform fee: 15%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-between mb-5">
            <a href="tutor-dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Cancel
            </a>
            <button type="submit" name="create_course" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Create Course and Add Content
            </button>
        </div>
    </form>
</div>

<!-- Section Template (Hidden) -->
<div id="sectionTemplate" class="d-none">
    <div class="section mb-4 border rounded" data-order="">
        <div class="section-header bg-light p-3 d-flex justify-content-between align-items-center">
            <input type="text" class="form-control section-title" placeholder="Section Title" required>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary handle me-1">
                    <i class="fas fa-arrows-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger remove-section">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="section-content p-3">
            <div class="lessons-list mb-3 sortable-lessons"></div>
            <button type="button" class="btn btn-sm btn-outline-success add-lesson">
                <i class="fas fa-plus me-1"></i> Add Lesson
            </button>
        </div>
    </div>
</div>

<!-- Lesson Template (Hidden) -->
<div id="lessonTemplate" class="d-none">
    <div class="lesson-item mb-3 p-3 border rounded bg-white" data-order="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <input type="text" class="form-control lesson-title me-2" placeholder="Lesson Title" required>
            <select class="form-select lesson-type" style="width: 150px;">
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="text">Text</option>
                <option value="quiz">Quiz</option>
            </select>
        </div>
        <div class="lesson-content-upload">
            <div class="video-upload mb-3" style="display: none;">
                <label class="form-label">Video File</label>
                <input type="file" class="form-control video-file" accept="video/*">
                <div class="progress mt-2 d-none video-progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
            <div class="text-content mb-3" style="display: none;">
                <label class="form-label">Content</label>
                <textarea class="form-control text-content" rows="3"></textarea>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-sm btn-outline-primary handle">
                <i class="fas fa-arrows-alt"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger remove-lesson">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
    </div>
</div>

<script>
// Placeholder for any simple JS needed for the simplified form.
// For example, thumbnail preview if kept.
document.addEventListener('DOMContentLoaded', function() {
    // Basic character counter for title (if needed)
    const courseTitleInput = document.getElementById('course_title');
    if (courseTitleInput) {
        const titleCounter = document.getElementById('titleCounter');
        if (titleCounter) {
            courseTitleInput.addEventListener('input', function() {
                titleCounter.textContent = this.value.length;
            });
        }
    }

    // Basic thumbnail preview (if #course_thumbnail and #thumbnailPreview exist)
    const thumbnailInput = document.getElementById('course_thumbnail');
    const thumbnailPreviewImg = document.getElementById('thumbnailPreview');
    const thumbnailUploadArea = document.getElementById('thumbnailUploadArea');

    if (thumbnailUploadArea && thumbnailInput) {
        thumbnailUploadArea.addEventListener('click', () => thumbnailInput.click());
        thumbnailInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file && thumbnailPreviewImg) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    thumbnailPreviewImg.src = e.target.result;
                    thumbnailPreviewImg.classList.remove('d-none');
                    if (thumbnailUploadArea.querySelector('.upload-prompt')) {
                        thumbnailUploadArea.querySelector('.upload-prompt').classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Pricing toggle (if #is_free and #course_price exist)
    const isFreeCheckbox = document.getElementById('is_free');
    const priceInput = document.getElementById('course_price');
    if (isFreeCheckbox && priceInput) {
        isFreeCheckbox.addEventListener('change', function() {
            priceInput.disabled = this.checked;
            if (this.checked) {
                priceInput.value = '0';
            }
        });
        // Initial state
        priceInput.disabled = isFreeCheckbox.checked;
    }
});
</script>

<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .upload-area {
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-area:hover {
        background-color: #f1f1f1 !important;
    }
    .sortable-ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }
    .handle {
        cursor: move;
    }
    .section-header, .lesson-item {
        transition: all 0.3s;
    }
    .section-header:hover {
        background-color: #e9ecef !important;
    }
    .lesson-item:hover {
        background-color: #f1f1f1 !important;
    }
    .rendered-markdown img {
        max-width: 100%;
    }
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>