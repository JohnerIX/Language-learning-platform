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
$error = '';
$success = false;
$courseData = [
    'title' => '',
    'language' => '',
    'level' => '',
    'description' => '',
    'price' => 0,
    'is_free' => 0,
    'duration_hours' => 0,
    'duration_minutes' => 30
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF verification
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid CSRF token");
        }

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
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($fileExt, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed");
            }
            
            if ($_FILES['course_thumbnail']['size'] > $maxSize) {
                throw new Exception("Thumbnail must be less than 2MB");
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

        // Process duration
        $duration_hours = (int)$_POST['duration_hours'];
        $duration_minutes = (int)$_POST['duration_minutes'];
        $total_minutes = ($duration_hours * 60) + $duration_minutes;

        // Determine status
        $status = isset($_POST['publish']) ? 'pending' : 'draft';

        // Begin transaction
        $conn->beginTransaction();

        try {
            // Save to database
            $stmt = $conn->prepare("
                INSERT INTO courses 
                (tutor_id, title, language, level, description, thumbnail_url, price, is_free, 
                 duration_minutes, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['course_title'],
                $_POST['course_language'],
                $_POST['course_level'],
                $_POST['course_description'],
                $thumbnailPath,
                $price,
                $isFree,
                $total_minutes,
                $status
            ]);

            $courseId = $conn->lastInsertId();

            // Process curriculum if exists
            if (!empty($_POST['sections'])) {
                $sections = json_decode($_POST['sections'], true);
                
                foreach ($sections as $section) {
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
                                (course_id, section_id, title, `order`, lesson_type, content)
                                VALUES (?, ?, ?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                $courseId,
                                $sectionId,
                                $lesson['title'],
                                $lesson['order'],
                                $lesson['type'],
                                $lesson['content'] ?? null
                            ]);
                        }
                    }
                }
            }

            // Process materials uploads
            if (!empty($_FILES['materials']['name'][0])) {
                $uploadDir = __DIR__ . '/uploads/course_materials/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                foreach ($_FILES['materials']['tmp_name'] as $index => $tmpName) {
                    if ($_FILES['materials']['error'][$index] === UPLOAD_ERR_OK) {
                        $fileName = uniqid() . '_' . basename($_FILES['materials']['name'][$index]);
                        $targetPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $stmt = $conn->prepare("
                                INSERT INTO course_materials 
                                (course_id, file_name, file_path, file_type)
                                VALUES (?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                $courseId,
                                $_FILES['materials']['name'][$index],
                                '/uploads/course_materials/' . $fileName,
                                $_POST['material_types'][$index]
                            ]);
                        }
                    }
                }
            }

            // Commit transaction
            $conn->commit();

            $_SESSION['success_message'] = $status === 'pending' 
                ? 'Course submitted for approval!' 
                : 'Draft saved successfully!';
            header("Location: tutor-dashboard.php");
            exit();

        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
        // Preserve form data
        $courseData = [
            'title' => $_POST['course_title'] ?? '',
            'language' => $_POST['course_language'] ?? '',
            'level' => $_POST['course_level'] ?? '',
            'description' => $_POST['course_description'] ?? '',
            'price' => $_POST['course_price'] ?? 0,
            'is_free' => isset($_POST['is_free']) ? 1 : 0,
            'duration_hours' => $_POST['duration_hours'] ?? 0,
            'duration_minutes' => $_POST['duration_minutes'] ?? 30
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
                </div>

                <div class="mb-3">
                    <label for="course_description" class="form-label fw-bold">Description*</label>
                    <ul class="nav nav-tabs" id="descriptionTabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#editTab">Edit</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#previewTab">Preview</button>
                        </li>
                    </ul>
                    <div class="tab-content border p-3 bg-white">
                        <div class="tab-pane fade show active" id="editTab">
                            <textarea class="form-control" id="course_description" name="course_description" 
                                      rows="5" required><?= htmlspecialchars($courseData['description']) ?></textarea>
                        </div>
                        <div class="tab-pane fade" id="previewTab">
                            <div id="descriptionPreview" class="rendered-markdown"></div>
                        </div>
                    </div>
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

        <!-- Course Duration -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-clock me-2"></i> Estimated Duration</h4>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hours</label>
                        <input type="number" name="duration_hours" class="form-control" 
                               value="<?= $courseData['duration_hours'] ?>" min="0" max="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Minutes</label>
                        <input type="number" name="duration_minutes" class="form-control" 
                               value="<?= $courseData['duration_minutes'] ?>" min="0" max="59">
                    </div>
                </div>
                <p class="text-muted">Estimated total: <span id="durationDisplay">
                    <?= $courseData['duration_hours'] ?>h <?= $courseData['duration_minutes'] ?>m
                </span></p>
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

        <!-- Course Materials -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-file-alt me-2"></i> Course Materials</h4>
            </div>
            <div class="card-body bg-light">
                <div id="materialUploads">
                    <div class="material-upload mb-3 border p-3 bg-white rounded">
                        <div class="mb-2">
                            <label class="form-label">File</label>
                            <input type="file" name="materials[]" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Type</label>
                            <select name="material_types[]" class="form-select">
                                <option value="pdf">PDF</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-material">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addMaterial">
                    <i class="fas fa-plus me-1"></i> Add Material
                </button>
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
                <div id="curriculumSections" class="sortable-sections">
                    <!-- Sections will be added here -->
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-between mb-5">
            <a href="tutor-dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Cancel
            </a>
            <div class="btn-group">
                <button type="submit" name="save_draft" class="btn btn-secondary">
                    <i class="fas fa-save me-2"></i> Save Draft
                </button>
                <button type="submit" name="publish" class="btn btn-success">
                    <i class="fas fa-paper-plane me-2"></i> Publish
                </button>
            </div>
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

<!-- Include required libraries -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@4.0.0/marked.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character Counter
    document.getElementById('course_title').addEventListener('input', function() {
        document.getElementById('titleCounter').textContent = this.value.length;
    });

    // Description Preview
    document.getElementById('course_description').addEventListener('input', function() {
        document.getElementById('descriptionPreview').innerHTML = marked.parse(this.value);
    });

    // Duration Calculation
    const durationHours = document.querySelector('input[name="duration_hours"]');
    const durationMinutes = document.querySelector('input[name="duration_minutes"]');
    const durationDisplay = document.getElementById('durationDisplay');
    
    function updateDurationDisplay() {
        const hours = parseInt(durationHours.value) || 0;
        const mins = parseInt(durationMinutes.value) || 0;
        durationDisplay.textContent = `${hours}h ${mins}m`;
    }
    
    durationHours.addEventListener('input', updateDurationDisplay);
    durationMinutes.addEventListener('input', updateDurationDisplay);

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
        pricePreview.textContent = isFreeCheckbox.checked ? 'FREE' : 
            `UGX ${parseInt(priceInput.value).toLocaleString()}`;
    }

    // Thumbnail Upload
    const thumbnailUpload = document.getElementById('course_thumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const thumbnailUploadArea = document.getElementById('thumbnailUploadArea');
    const uploadProgress = document.getElementById('uploadProgress');
    
    thumbnailUploadArea.addEventListener('click', () => thumbnailUpload.click());
    thumbnailUpload.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                thumbnailPreview.src = e.target.result;
                thumbnailPreview.classList.remove('d-none');
                thumbnailUploadArea.querySelector('.upload-prompt').classList.add('d-none');
            };
            reader.readAsDataURL(file);
            
            // Show progress
            uploadProgress.classList.remove('d-none');
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                uploadProgress.querySelector('.progress-bar').style.width = `${progress}%`;
                if (progress >= 100) clearInterval(interval);
            }, 200);
        }
    });

    // Materials Upload
    document.getElementById('addMaterial').addEventListener('click', function() {
        const newUpload = document.createElement('div');
        newUpload.className = 'material-upload mb-3 border p-3 bg-white rounded';
        newUpload.innerHTML = `
            <div class="mb-2">
                <label class="form-label">File</label>
                <input type="file" name="materials[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Type</label>
                <select name="material_types[]" class="form-select">
                    <option value="pdf">PDF</option>
                    <option value="video">Video</option>
                    <option value="audio">Audio</option>
                    <option value="image">Image</option>
                </select>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-material">
                <i class="fas fa-trash"></i> Remove
            </button>
        `;
        document.getElementById('materialUploads').appendChild(newUpload);
        
        // Add remove handler
        newUpload.querySelector('.remove-material').addEventListener('click', function() {
            newUpload.remove();
        });
    });

    // Curriculum Builder
    let sectionCount = 0;
    let lessonCount = 0;
    const curriculumSections = document.getElementById('curriculumSections');
    
    // Initialize sortable sections
    const sortableSections = new Sortable(curriculumSections, {
        animation: 150,
        handle: '.handle',
        ghostClass: 'sortable-ghost',
        onEnd: function() {
            updateSectionOrders();
        }
    });
    
    // Add new section
    document.getElementById('addSectionBtn').addEventListener('click', addNewSection);
    
    function addNewSection() {
        sectionCount++;
        const sectionId = `section-${sectionCount}`;
        const sectionTemplate = document.getElementById('sectionTemplate').cloneNode(true);
        const sectionElement = sectionTemplate.content || sectionTemplate;
        
        const sectionNode = sectionElement.querySelector('.section');
        sectionNode.id = sectionId;
        sectionNode.dataset.order = sectionCount;
        
        const clone = sectionNode.cloneNode(true);
        curriculumSections.appendChild(clone);
        
        // Initialize sortable for lessons in this section
        const lessonsList = clone.querySelector('.lessons-list');
        new Sortable(lessonsList, {
            animation: 150,
            handle: '.handle',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateLessonOrders(lessonsList);
            }
        });
        
        // Add event listeners
        clone.querySelector('.remove-section').addEventListener('click', function() {
            if (confirm('Delete this section and all its lessons?')) {
                clone.remove();
                updateSectionOrders();
            }
        });
        
        clone.querySelector('.add-lesson').addEventListener('click', function() {
            addNewLesson(lessonsList);
        });
    }
    
    // Add new lesson
    function addNewLesson(container) {
        lessonCount++;
        const lessonId = `lesson-${lessonCount}`;
        const lessonTemplate = document.getElementById('lessonTemplate').cloneNode(true);
        const lessonElement = lessonTemplate.content || lessonTemplate;
        
        const lessonNode = lessonElement.querySelector('.lesson-item');
        lessonNode.id = lessonId;
        lessonNode.dataset.order = container.children.length + 1;
        
        const clone = lessonNode.cloneNode(true);
        container.appendChild(clone);
        
        // Handle lesson type change
        const typeSelect = clone.querySelector('.lesson-type');
        typeSelect.addEventListener('change', function() {
            // Hide all content types
            clone.querySelectorAll('.lesson-content-upload > div').forEach(el => {
                el.style.display = 'none';
            });
            
            // Show selected type
            const type = this.value;
            if (type === 'video' || type === 'audio') {
                clone.querySelector(`.${type}-upload`).style.display = 'block';
            } else if (type === 'text') {
                clone.querySelector('.text-content').style.display = 'block';
            }
        });
        
        // Trigger initial change
        typeSelect.dispatchEvent(new Event('change'));
        
        // Handle file upload progress
        const fileInput = clone.querySelector('.video-file');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const progressBar = clone.querySelector('.video-progress');
                progressBar.classList.remove('d-none');
                
                let progress = 0;
                const interval = setInterval(() => {
                    progress += 10;
                    progressBar.querySelector('.progress-bar').style.width = `${progress}%`;
                    if (progress >= 100) clearInterval(interval);
                }, 200);
            });
        }
        
        // Remove lesson
        clone.querySelector('.remove-lesson').addEventListener('click', function() {
            clone.remove();
            updateLessonOrders(container);
        });
    }
    
    // Update section orders
    function updateSectionOrders() {
        document.querySelectorAll('.section').forEach((section, index) => {
            section.dataset.order = index + 1;
        });
    }
    
    // Update lesson orders
    function updateLessonOrders(container) {
        container.querySelectorAll('.lesson-item').forEach((lesson, index) => {
            lesson.dataset.order = index + 1;
        });
    }
    
    // Prepare form data for submission
    document.getElementById('courseForm').addEventListener('submit', function(e) {
        // Prepare curriculum data
        const sections = [];
        document.querySelectorAll('.section').forEach((section, sectionIndex) => {
            const sectionTitle = section.querySelector('.section-title').value;
            if (!sectionTitle) return;
            
            const lessons = [];
            section.querySelectorAll('.lesson-item').forEach((lesson, lessonIndex) => {
                const lessonTitle = lesson.querySelector('.lesson-title').value;
                const lessonType = lesson.querySelector('.lesson-type').value;
                let lessonContent = '';
                
                if (lessonType === 'text') {
                    lessonContent = lesson.querySelector('.text-content').value;
                } else if (lessonType === 'video' || lessonType === 'audio') {
                    // In a real app, you'd handle file uploads here
                    lessonContent = 'File would be uploaded here';
                }
                
                if (lessonTitle) {
                    lessons.push({
                        title: lessonTitle,
                        type: lessonType,
                        content: lessonContent,
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