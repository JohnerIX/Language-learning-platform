<?php
require_once __DIR__ . '/includes/config.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    redirect('login.php');
}

$pageTitle = "Create New Course";
require __DIR__ . '/includes/header.php';

// Initialize variables
$error = '';
$success = false;
$courseData = [
    'title' => '',
    'language' => '',
    'level' => '',
    'description' => '',
    'price' => 0,
    'is_free' => 0
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    try {
        // Basic validation
        $requiredFields = ['course_title', 'course_language', 'course_level', 'course_description'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All required fields must be filled");
            }
        }

        // Process thumbnail upload
        $thumbnailPath = '';
        if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/course_thumbs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['course_thumbnail']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($fileExt, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['course_thumbnail']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $targetPath)) {
                throw new Exception("Failed to upload thumbnail");
            }
            
            $thumbnailPath = '/uploads/course_thumbs/' . $fileName;
        } else {
            throw new Exception("Course thumbnail is required");
        }

        // Process pricing
        $price = isset($_POST['course_price']) ? floatval($_POST['course_price']) : 0;
        $isFree = isset($_POST['is_free']) ? 1 : 0;
        if ($isFree) {
            $price = 0;
        }

        // Save to database
        $stmt = $conn->prepare("
            INSERT INTO courses 
            (tutor_id, title, language, level, description, thumbnail_url, price, is_free, created_at, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['course_title'],
            $_POST['course_language'],
            $_POST['course_level'],
            $_POST['course_description'],
            $thumbnailPath,
            $price,
            $isFree
        ]);

        $courseId = $conn->lastInsertId();

        // Process curriculum if exists
        if (!empty($_POST['sections'])) {
            foreach ($_POST['sections'] as $section) {
                $stmt = $conn->prepare("
                    INSERT INTO course_sections 
                    (course_id, title, `order`)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$courseId, $section['title'], $section['order']]);
                $sectionId = $conn->lastInsertId();

                // Process lessons
                if (!empty($section['lessons'])) {
                    foreach ($section['lessons'] as $lesson) {
                        $stmt = $conn->prepare("
                            INSERT INTO lessons 
                            (course_id, section_id, title, `order`, lesson_type)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $courseId,
                            $sectionId,
                            $lesson['title'],
                            $lesson['order'],
                            $lesson['type']
                        ]);
                    }
                }
            }
        }

        $_SESSION['success_message'] = 'Course created successfully! It will be visible after admin approval.';
        redirect('tutor-dashboard.php');

    } catch (Exception $e) {
        $error = $e->getMessage();
        // Preserve form data
        $courseData = [
            'title' => $_POST['course_title'] ?? '',
            'language' => $_POST['course_language'] ?? '',
            'level' => $_POST['course_level'] ?? '',
            'description' => $_POST['course_description'] ?? '',
            'price' => $_POST['course_price'] ?? 0,
            'is_free' => isset($_POST['is_free']) ? 1 : 0
        ];
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
        <!-- Course Information -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-info-circle me-2"></i> Course Information</h4>
            </div>
            <div class="card-body bg-light">
                <div class="mb-3">
                    <label for="course_title" class="form-label fw-bold">Course Title*</label>
                    <input type="text" class="form-control" id="course_title" name="course_title" 
                           value="<?= htmlspecialchars($courseData['title']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_language" class="form-label fw-bold">Language*</label>
                        <select class="form-select" id="course_language" name="course_language" required>
                            <option value="">Select language</option>
                            <option value="Luganda" <?= $courseData['language'] === 'Luganda' ? 'selected' : '' ?>>Luganda</option>
                            <option value="Runyankole" <?= $courseData['language'] === 'Runyankole' ? 'selected' : '' ?>>Runyankole</option>
                            <option value="Acholi" <?= $courseData['language'] === 'Acholi' ? 'selected' : '' ?>>Acholi</option>
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
                </div>

                <div class="mb-3">
                    <label for="course_description" class="form-label fw-bold">Description*</label>
                    <textarea class="form-control" id="course_description" name="course_description" 
                              rows="5" required><?= htmlspecialchars($courseData['description']) ?></textarea>
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
                        <p class="small text-muted">Recommended: 1280×720px JPG/PNG</p>
                    </div>
                    <img id="thumbnailPreview" class="img-fluid mt-3 d-none rounded border">
                </div>
            </div>
        </div>

        <!-- Curriculum Builder -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-list-ol me-2"></i> Course Curriculum</h4>
                <button type="button" class="btn btn-sm btn-success" id="addSectionBtn">
                    <i class="fas fa-plus me-1"></i> Add Section
                </button>
            </div>
            <div class="card-body bg-light">
                <div id="curriculumSections">
                    <!-- Sections will be added here -->
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
                            <p class="mb-1">Price: <span id="pricePreview"><?= $courseData['is_free'] ? 'FREE' : 'UGX ' . number_format($courseData['price']) ?></span></p>
                            <small class="text-muted">Platform fee: 15%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-flex justify-content-end mb-5">
            <button type="submit" class="btn btn-success btn-lg px-4 fw-bold" name="save_course">
                <i class="fas fa-save me-2"></i> Save & Publish
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail Upload
    const thumbnailUpload = document.getElementById('course_thumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const thumbnailUploadArea = document.getElementById('thumbnailUploadArea');
    
    thumbnailUploadArea.addEventListener('click', () => thumbnailUpload.click());
    thumbnailUpload.addEventListener('change', updateThumbnailPreview);
    
    function updateThumbnailPreview() {
        const file = thumbnailUpload.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                thumbnailPreview.src = e.target.result;
                thumbnailPreview.classList.remove('d-none');
                thumbnailUploadArea.querySelector('.upload-prompt').classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    }

    // Pricing Toggle
    const priceInput = document.getElementById('course_price');
    const isFreeCheckbox = document.getElementById('is_free');
    const pricePreview = document.getElementById('pricePreview');
    
    isFreeCheckbox.addEventListener('change', function() {
        priceInput.disabled = this.checked;
        updatePricePreview();
    });
    
    priceInput.addEventListener('input', updatePricePreview);
    
    function updatePricePreview() {
        pricePreview.textContent = isFreeCheckbox.checked ? 'FREE' : `UGX ${parseInt(priceInput.value).toLocaleString()}`;
    }

    // Curriculum Builder
    let sectionCount = 0;
    let lessonCount = 0;
    const curriculumSections = document.getElementById('curriculumSections');
    
    document.getElementById('addSectionBtn').addEventListener('click', addNewSection);
    
    function addNewSection() {
        sectionCount++;
        const sectionId = `section-${sectionCount}`;
        
        const sectionHTML = `
            <div class="section mb-4 border rounded" id="${sectionId}">
                <div class="section-header bg-light p-3 d-flex justify-content-between align-items-center">
                    <input type="text" class="form-control section-title" placeholder="Section Title" required>
                    <button type="button" class="btn btn-sm btn-danger remove-section">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="section-content p-3">
                    <div class="lessons-list mb-3"></div>
                    <button type="button" class="btn btn-sm btn-outline-success add-lesson">
                        <i class="fas fa-plus me-1"></i> Add Lesson
                    </button>
                </div>
            </div>
        `;
        
        const sectionElement = document.createElement('div');
        sectionElement.innerHTML = sectionHTML;
        curriculumSections.appendChild(sectionElement);
        
        // Add event listeners
        sectionElement.querySelector('.remove-section').addEventListener('click', () => {
            if (confirm('Delete this section and all its lessons?')) {
                sectionElement.remove();
            }
        });
        
        sectionElement.querySelector('.add-lesson').addEventListener('click', () => {
            addNewLesson(sectionElement.querySelector('.lessons-list'));
        });
    }
    
    function addNewLesson(container) {
        lessonCount++;
        const lessonId = `lesson-${lessonCount}`;
        
        const lessonHTML = `
            <div class="lesson-item mb-3 p-3 border rounded" id="${lessonId}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <input type="text" class="form-control lesson-title" placeholder="Lesson Title" required>
                    <select class="form-select lesson-type ms-2" style="width: 150px;">
                        <option value="video">Video</option>
                        <option value="audio">Audio</option>
                        <option value="pdf">PDF</option>
                        <option value="quiz">Quiz</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-lesson">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        
        const lessonElement = document.createElement('div');
        lessonElement.innerHTML = lessonHTML;
        container.appendChild(lessonElement);
        
        lessonElement.querySelector('.remove-lesson').addEventListener('click', () => {
            lessonElement.remove();
        });
    }
    
    // Form submission handler
    document.getElementById('courseForm').addEventListener('submit', function(e) {
        // Prepare curriculum data for submission
        const sections = [];
        document.querySelectorAll('.section').forEach((section, sectionIndex) => {
            const sectionTitle = section.querySelector('.section-title').value;
            if (!sectionTitle) return;
            
            const lessons = [];
            section.querySelectorAll('.lesson-item').forEach((lesson, lessonIndex) => {
                const lessonTitle = lesson.querySelector('.lesson-title').value;
                const lessonType = lesson.querySelector('.lesson-type').value;
                
                if (lessonTitle) {
                    lessons.push({
                        title: lessonTitle,
                        type: lessonType,
                        order: lessonIndex + 1
                    });
                }
            });
            
            sections.push({
                title: sectionTitle,
                order: sectionIndex + 1,
                lessons: lessons
            });
        });
        
        // Add hidden input for curriculum data
        const curriculumInput = document.createElement('input');
        curriculumInput.type = 'hidden';
        curriculumInput.name = 'sections';
        curriculumInput.value = JSON.stringify(sections);
        this.appendChild(curriculumInput);
    });
    
    // Add initial section
    addNewSection();
});
</script>

<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .upload-area {
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-area:hover {
        background-color: #f1f1f1 !important;
    }
    .section {
        background-color: white;
    }
    .section-header {
        border-bottom: 1px solid #dee2e6;
    }
    .lesson-item {
        background-color: #f8f9fa;
    }
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>