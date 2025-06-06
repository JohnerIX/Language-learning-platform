<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Verify tutor access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tutor') {
    $_SESSION['error_message'] = "You must be logged in as a tutor to create a course.";
    header("Location: login.php");
    exit();
}

$pageTitle = "Create New Course";

// Initialize variables for form repopulation
$courseData = [
    'title' => $_POST['course_title'] ?? '',
    'language' => $_POST['course_language'] ?? '',
    'level' => $_POST['course_level'] ?? '',
    'category' => $_POST['course_category'] ?? '',
    'description' => $_POST['course_description'] ?? '',
    'price' => $_POST['course_price'] ?? 0,
    'is_free' => isset($_POST['is_free']) ? 1 : 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF verification
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            throw new Exception("Invalid CSRF token. Please try again.");
        }

        // Basic validation
        $requiredFields = ['course_title', 'course_language', 'course_level', 'course_description', 'status'];
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
        $status = in_array($_POST['status'], ['draft', 'pending']) ? $_POST['status'] : 'draft';

        // Thumbnail Upload
        $thumbnailPath = null;
        $thumbUploadDir = __DIR__ . '/uploads/course_thumbs/';
        if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir($thumbUploadDir)) {
                if (!mkdir($thumbUploadDir, 0755, true)) {
                    throw new Exception("Failed to create thumbnail upload directory: " . $thumbUploadDir);
                }
            }
            $fileExt = strtolower(pathinfo($_FILES['course_thumbnail']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            if (!in_array($fileExt, $allowedTypes)) throw new Exception("Invalid thumbnail type. JPG, JPEG, PNG, GIF allowed.");
            if ($_FILES['course_thumbnail']['size'] > $maxSize) throw new Exception("Thumbnail too large (max 2MB).");
            
            $fileName = uniqid('thumb_', true) . '.' . $fileExt;
            if (!move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $thumbUploadDir . $fileName)) {
                throw new Exception("Failed to upload thumbnail.");
            }
            $thumbnailPath = 'uploads/course_thumbs/' . $fileName;
        } elseif ($_FILES['course_thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
             throw new Exception("Thumbnail upload error: code " . $_FILES['course_thumbnail']['error']);
        }


        // Pricing
        $price = isset($_POST['course_price']) ? floatval($_POST['course_price']) : 0;
        $isFree = isset($_POST['is_free']) ? 1 : 0;
        if ($isFree) $price = 0;

        $duration_minutes = 0;

        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO courses (tutor_id, title, description, language, level, category, thumbnail_url, price, is_free, status, duration_minutes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$_SESSION['user_id'], $course_title, $course_description, $course_language, $course_level, $course_category, $thumbnailPath, $price, $isFree, $status, $duration_minutes]);
        $course_id = $conn->lastInsertId();

        $curriculum_data_json = $_POST['curriculum_data_json'] ?? '[]';
        $curriculum = json_decode($curriculum_data_json, true);
        $lesson_files_base_dir = __DIR__ . '/uploads/course_materials/';
        $lesson_files_db_prefix = 'uploads/course_materials/';

        if (!is_dir($lesson_files_base_dir)) {
            if (!mkdir($lesson_files_base_dir, 0755, true)) {
                throw new Exception("Failed to create course materials upload directory: " . $lesson_files_base_dir);
            }
        }

        $total_course_duration = 0;

        if (is_array($curriculum)) {
            foreach ($curriculum as $section_data) {
                $section_title = sanitize_input($section_data['title']);
                $section_order = (int)$section_data['order'];
                $stmt_section = $conn->prepare("INSERT INTO course_sections (course_id, title, `order`) VALUES (?, ?, ?)");
                $stmt_section->execute([$course_id, $section_title, $section_order]);
                $section_id = $conn->lastInsertId();

                if (is_array($section_data['lessons'])) {
                    foreach ($section_data['lessons'] as $lesson_data) {
                        $lesson_title = sanitize_input($lesson_data['title']);
                        $lesson_order = (int)$lesson_data['order'];
                        $lesson_type = sanitize_input($lesson_data['type']);
                        $lesson_temp_id = $lesson_data['temp_id'] ?? null;

                        $content = null; $video_url = null; $file_path = null; $lesson_duration = 0;

                        if ($lesson_type === 'text') {
                            $content = $lesson_data['content'] ?? '';
                            $word_count = str_word_count(strip_tags($content));
                            $lesson_duration = ceil($word_count / 200);
                        } elseif ($lesson_type === 'video_url') {
                            $video_url = filter_var($lesson_data['video_url'] ?? '', FILTER_VALIDATE_URL) ? $lesson_data['video_url'] : null;
                            $lesson_duration = (int)($lesson_data['duration_minutes'] ?? 5);
                        } elseif ($lesson_type === 'pdf_file' || $lesson_type === 'audio_file') {
                            $file_input_array_name = 'lesson_files';

                            if (isset($_FILES[$file_input_array_name]['name'][$section_data['temp_id']][$lesson_temp_id][$lesson_type]) &&
                                $_FILES[$file_input_array_name]['error'][$section_data['temp_id']][$lesson_temp_id][$lesson_type] === UPLOAD_ERR_OK) {

                                $original_name = $_FILES[$file_input_array_name]['name'][$section_data['temp_id']][$lesson_temp_id][$lesson_type];
                                $tmp_name = $_FILES[$file_input_array_name]['tmp_name'][$section_data['temp_id']][$lesson_temp_id][$lesson_type];
                                $file_size = $_FILES[$file_input_array_name]['size'][$section_data['temp_id']][$lesson_temp_id][$lesson_type];

                                $fileExt = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                                $allowed_pdf = ['pdf']; $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
                                $max_file_size = 10 * 1024 * 1024; // 10MB

                                if ($lesson_type === 'pdf_file' && !in_array($fileExt, $allowed_pdf)) throw new Exception("Invalid PDF type for lesson '{$lesson_title}'.");
                                if ($lesson_type === 'audio_file' && !in_array($fileExt, $allowed_audio)) throw new Exception("Invalid Audio type for lesson '{$lesson_title}'.");
                                if ($file_size > $max_file_size) throw new Exception("File too large for lesson '{$lesson_title}' (max 10MB).");

                                $new_lesson_filename = uniqid('lesson_', true) . '.' . $fileExt;
                                if (!move_uploaded_file($tmp_name, $lesson_files_base_dir . $new_lesson_filename)) {
                                    throw new Exception("Failed to upload file for lesson '{$lesson_title}'.");
                                }
                                $file_path = $lesson_files_db_prefix . $new_lesson_filename;
                                $lesson_duration = (int)($lesson_data['duration_minutes'] ?? ($lesson_type === 'audio_file' ? 3 : 10));
                            } elseif (empty($lesson_data['file_path'])) {
                                throw new Exception("File not provided for lesson '{$lesson_title}' of type '{$lesson_type}'.");
                            }
                        }
                        $total_course_duration += $lesson_duration;

                        $stmt_lesson = $conn->prepare("INSERT INTO lessons (course_id, section_id, title, `order`, lesson_type, content, video_url, file_path, duration_minutes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                        $stmt_lesson->execute([$course_id, $section_id, $lesson_title, $lesson_order, $lesson_type, $content, $video_url, $file_path, $lesson_duration]);
                    }
                }
            }
        }

        if ($total_course_duration > 0) {
            $stmt_update_duration = $conn->prepare("UPDATE courses SET duration_minutes = ? WHERE course_id = ?");
            $stmt_update_duration->execute([$total_course_duration, $course_id]);
        }

        $material_titles = $_POST['material_titles'] ?? [];
        if (isset($_FILES['materials']['name']) && is_array($_FILES['materials']['name'])) {
            $material_upload_dir = __DIR__ . '/uploads/course_materials/';
             if (!is_dir($material_upload_dir)) {
                if (!mkdir($material_upload_dir, 0755, true)) {
                    throw new Exception("Failed to create general materials upload directory.");
                }
            }
            foreach ($_FILES['materials']['name'] as $key => $name) {
                if ($_FILES['materials']['error'][$key] === UPLOAD_ERR_OK) {
                    $material_tmp_name = $_FILES['materials']['tmp_name'][$key];
                    $material_title = sanitize_input($material_titles[$key] ?? 'Untitled Material');
                    $material_file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $material_filename = uniqid('material_',true) . '.' . $material_file_ext;

                    if (move_uploaded_file($material_tmp_name, $material_upload_dir . $material_filename)) {
                        $material_db_path = 'uploads/course_materials/' . $material_filename;
                        $stmt_material = $conn->prepare("INSERT INTO course_materials (course_id, title, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
                        $stmt_material->execute([$course_id, $material_title, $material_db_path]);
                    } else {
                        error_log("Failed to upload a general material: " . $name);
                    }
                }
            }
        }

        $conn->commit();
        $_SESSION['success_message'] = "Course '{$course_title}' created successfully with status: {$status}!";
        header("Location: tutor-dashboard.php");
        exit();

    } catch (PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        error_log("DB Error in create-course.php: " . $e->getMessage());
        $_SESSION['error_message'] = "Database operation failed: " . $e->getMessage();
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['error_message'] = $e->getMessage();
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <h1 class="mb-4">Create New Course</h1>

    <form method="POST" enctype="multipart/form-data" id="courseForm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="curriculum_data_json" id="curriculum_data_json">

        <!-- Course Information -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white"><h4><i class="fas fa-info-circle me-2"></i>Course Information</h4></div>
            <div class="card-body bg-light">
                <div class="mb-3">
                    <label for="course_title" class="form-label fw-bold">Course Title*</label>
                    <input type="text" class="form-control" id="course_title" name="course_title" value="<?= htmlspecialchars($courseData['title']) ?>" maxlength="100" required>
                    <div class="form-text text-end"><span id="titleCounter"><?= strlen($courseData['title']) ?></span>/100 characters</div>
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
                            <option value="Swahili" <?= $courseData['language'] === 'Swahili' ? 'selected' : '' ?>>Swahili</option>
                            <option value="English" <?= $courseData['language'] === 'English' ? 'selected' : '' ?>>English</option>
                            <option value="Other" <?= $courseData['language'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="course_level" class="form-label fw-bold">Level*</label>
                        <select class="form-select" id="course_level" name="course_level" required>
                            <option value="">Select level</option>
                            <option value="Beginner" <?= $courseData['level'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $courseData['level'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= $courseData['level'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="course_category" class="form-label fw-bold">Category</label>
                    <input type="text" class="form-control" id="course_category" name="course_category" value="<?= htmlspecialchars($courseData['category']) ?>" placeholder="e.g., Business, Health, Technology">
                </div>
                <div class="mb-3">
                    <label for="course_description" class="form-label fw-bold">Short Description*</label>
                    <textarea class="form-control" id="course_description" name="course_description" rows="3" required placeholder="A brief overview of the course."><?= htmlspecialchars($courseData['description']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Thumbnail Upload -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white"><h4><i class="fas fa-image me-2"></i>Course Thumbnail</h4></div>
            <div class="card-body bg-light">
                 <input type="file" class="form-control" id="course_thumbnail" name="course_thumbnail" accept="image/*">
                 <img id="thumbnailPreview" class="img-fluid mt-2" style="max-height: 200px; display: none;" alt="Thumbnail Preview">
                 <small class="form-text text-muted">Recommended: 1280x720px JPG/PNG/GIF (Max 2MB). Optional for draft.</small>
            </div>
        </div>

        <!-- Pricing -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white"><h4><i class="fas fa-tag me-2"></i>Pricing</h4></div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_price" class="form-label fw-bold">Course Price (UGX)</label>
                        <input type="number" class="form-control" id="course_price" name="course_price" value="<?= htmlspecialchars($courseData['price']) ?>" min="0" step="1000" <?= $courseData['is_free'] ? 'disabled' : '' ?>>
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="is_free" name="is_free" value="1" <?= $courseData['is_free'] ? 'checked' : '' ?> onchange="document.getElementById('course_price').disabled = this.checked; if(this.checked) document.getElementById('course_price').value = '0';">
                            <label class="form-check-label" for="is_free">This is a free course</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Materials (Optional) -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white">
                <h4><i class="fas fa-file-alt me-2"></i> Course Materials (Optional)</h4>
            </div>
            <div class="card-body bg-light">
                <div id="materialUploadsContainer">
                    <!-- Initial material upload row (template for JS) -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addMaterialBtn"><i class="fas fa-plus me-1"></i> Add Material</button>
            </div>
        </div>

        <!-- Curriculum Builder -->
        <div class="card mb-4 border-dark">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-list-ol me-2"></i> Course Curriculum*</h4>
                <button type="button" class="btn btn-sm btn-success" id="addSectionBtn"><i class="fas fa-plus me-1"></i> Add Section</button>
            </div>
            <div class="card-body bg-light">
                <div id="curriculumSections" class="sortable-sections">
                    <!-- Sections and lessons will be added here by JavaScript -->
                </div>
                <p class="form-text text-muted mt-2">Drag sections or lessons to reorder them. At least one section and one lesson are required for 'Submit for Review'.</p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-between mb-5">
            <a href="tutor-dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Cancel</a>
            <div>
                <button type="submit" name="status" value="draft" class="btn btn-info"><i class="fas fa-save me-2"></i> Save Draft</button>
                <button type="submit" name="status" value="pending" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i> Submit for Review</button>
            </div>
        </div>
    </form>
</div>

<!-- Hidden HTML Templates -->
<div id="sectionTemplate" class="d-none">
    <div class="section mb-3 border rounded bg-white shadow-sm" data-order="" data-temp-id="">
        <div class="section-header bg-secondary-subtle p-2 d-flex justify-content-between align-items-center">
            <input type="text" class="form-control section-title flex-grow-1 me-2" placeholder="Section Title (e.g., Introduction)" required>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary handle-section me-1" title="Reorder Section"><i class="fas fa-arrows-alt"></i></button>
                <button type="button" class="btn btn-sm btn-danger remove-section" title="Delete Section"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="section-content p-3">
            <div class="lessons-list sortable-lessons"></div>
            <button type="button" class="btn btn-sm btn-outline-success add-lesson mt-2"><i class="fas fa-plus me-1"></i> Add Lesson</button>
        </div>
    </div>
</div>

<div id="lessonTemplate" class="d-none">
    <div class="lesson-item mb-2 p-2 border rounded bg-light" data-order="" data-temp-id="">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <input type="text" class="form-control lesson-title flex-grow-1 me-2" placeholder="Lesson Title" required>
            <select class="form-select lesson-type" style="width: 150px;">
                <option value="text" selected>Text</option>
                <option value="video_url">Video URL</option>
                <option value="pdf_file">PDF</option>
                <option value="audio_file">Audio</option>
            </select>
        </div>
        <div class="lesson-content-fields mt-2">
            <div class="lesson-field lesson-field-text">
                <textarea class="form-control lesson-text-content" rows="3" placeholder="Enter text, HTML, or Markdown..."></textarea>
            </div>
            <div class="lesson-field lesson-field-video_url" style="display:none;">
                <input type="url" class="form-control lesson-video-url" placeholder="e.g., https://youtube.com/watch?v=...">
                <input type="number" class="form-control lesson-duration-minutes mt-1" placeholder="Est. duration (mins)" min="0">
            </div>
            <div class="lesson-field lesson-field-pdf_file" style="display:none;">
                <input type="file" class="form-control lesson-pdf-file" accept=".pdf">
                <input type="number" class="form-control lesson-duration-minutes mt-1" placeholder="Est. duration (mins)" min="0">
            </div>
            <div class="lesson-field lesson-field-audio_file" style="display:none;">
                <input type="file" class="form-control lesson-audio-file" accept="audio/*">
                 <input type="number" class="form-control lesson-duration-minutes mt-1" placeholder="Est. duration (mins)" min="0">
            </div>
        </div>
        <div class="d-flex justify-content-end align-items-center mt-2">
            <button type="button" class="btn btn-sm btn-outline-secondary handle-lesson me-1" title="Reorder Lesson"><i class="fas fa-arrows-alt"></i></button>
            <button type="button" class="btn btn-sm btn-outline-danger remove-lesson" title="Delete Lesson"><i class="fas fa-trash"></i></button>
        </div>
    </div>
</div>

<div id="materialUploadTemplate" class="d-none">
    <div class="material-upload-item mb-3 border p-3 bg-white rounded">
        <div class="mb-2">
            <label class="form-label">File*</label>
            <input type="file" name="materials[]" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Material Title/Description (Optional)</label>
            <input type="text" name="material_titles[]" class="form-control" placeholder="e.g., Introduction Slides">
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-material-item"><i class="fas fa-trash"></i> Remove</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert for PHP messages
    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= addslashes($_SESSION['error_message']) ?>'});
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({ icon: 'success', title: 'Success!', text: '<?= addslashes($_SESSION['success_message']) ?>'});
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    // Thumbnail Preview
    const thumbnailInput = document.getElementById('course_thumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    if(thumbnailInput && thumbnailPreview) {
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                thumbnailPreview.src = URL.createObjectURL(file);
                thumbnailPreview.style.display = 'block';
            } else {
                thumbnailPreview.style.display = 'none';
            }
        });
    }
    
    // Title character counter
    const courseTitleInput = document.getElementById('course_title');
    const titleCounter = document.getElementById('titleCounter');
    if(courseTitleInput && titleCounter){
        courseTitleInput.addEventListener('input', function() {
            titleCounter.textContent = this.value.length;
        });
    }

    // --- General Materials ---
    const materialsContainer = document.getElementById('materialUploadsContainer');
    const addMaterialBtn = document.getElementById('addMaterialBtn');
    const materialTemplateHTML = document.getElementById('materialUploadTemplate')?.innerHTML; // Use optional chaining

    if (addMaterialBtn && materialsContainer && materialTemplateHTML) { // Check if template exists
        addMaterialBtn.addEventListener('click', function() {
            const newMaterialRow = document.createElement('div');
            newMaterialRow.innerHTML = materialTemplateHTML;
            materialsContainer.appendChild(newMaterialRow.firstElementChild);
        });
    }
    if (materialsContainer) { // Check if container exists before adding listener
        materialsContainer.addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('remove-material-item') || e.target.closest('.remove-material-item'))) {
                e.target.closest('.material-upload-item').remove();
            }
        });
    }


    // --- Curriculum Builder ---
    const sectionsContainer = document.getElementById('curriculumSections');
    const addSectionBtn = document.getElementById('addSectionBtn');
    const sectionTemplateHtml = document.getElementById('sectionTemplate')?.innerHTML;
    const lessonTemplateHtml = document.getElementById('lessonTemplate')?.innerHTML;

    if (!sectionsContainer || !addSectionBtn || !sectionTemplateHtml || !lessonTemplateHtml) {
        console.warn("Curriculum builder elements not fully found. Dynamic functionality may be limited.");
    } else {
        // Initialize Sortable for sections
        Sortable.create(sectionsContainer, {
            animation: 150,
            handle: '.handle-section',
            ghostClass: 'sortable-ghost-section',
            onEnd: updateOrders
        });

        addSectionBtn.addEventListener('click', function() {
            const newSectionEl = document.createElement('div');
            newSectionEl.innerHTML = sectionTemplateHtml;
            const sectionDiv = newSectionEl.firstElementChild;
            const tempSectionId = 's_temp_' + createUniqueId();
            sectionDiv.setAttribute('data-temp-id', tempSectionId);

            const sectionTitleInput = sectionDiv.querySelector('.section-title');
            sectionTitleInput.name = `sections[${tempSectionId}][title]`;

            sectionsContainer.appendChild(sectionDiv);
            initializeLessonSortable(sectionDiv.querySelector('.lessons-list'));
            updateOrders();
        });

        sectionsContainer.addEventListener('click', function(e) {
            const target = e.target;
            const closestRemoveSection = target.closest('.remove-section');
            const closestAddLesson = target.closest('.add-lesson');
            const closestRemoveLesson = target.closest('.remove-lesson');
            const closestLessonType = target.closest('.lesson-type');

            if (closestRemoveSection) {
                closestRemoveSection.closest('.section').remove();
                updateOrders();
            } else if (closestAddLesson) {
                const lessonListDiv = closestAddLesson.closest('.section-content').querySelector('.lessons-list');
                const parentSectionDiv = closestAddLesson.closest('.section');
                const sectionTempId = parentSectionDiv.getAttribute('data-temp-id');

                const newLessonEl = document.createElement('div');
                newLessonEl.innerHTML = lessonTemplateHtml;
                const lessonDiv = newLessonEl.firstElementChild;
                const tempLessonId = 'l_temp_' + createUniqueId();
                lessonDiv.setAttribute('data-temp-id', tempLessonId);

                lessonDiv.querySelector('.lesson-title').name = `lessons[${sectionTempId}][${tempLessonId}][title]`;
                const lessonTypeSelect = lessonDiv.querySelector('.lesson-type');
                lessonTypeSelect.name = `lessons[${sectionTempId}][${tempLessonId}][type]`;
                lessonDiv.querySelector('.lesson-text-content').name = `lessons[${sectionTempId}][${tempLessonId}][text_content]`;
                lessonDiv.querySelector('.lesson-video-url').name = `lessons[${sectionTempId}][${tempLessonId}][video_url]`;
                lessonDiv.querySelector('.lesson-pdf-file').name = `lesson_files[${sectionTempId}][${tempLessonId}][pdf_file]`;
                lessonDiv.querySelector('.lesson-audio-file').name = `lesson_files[${sectionTempId}][${tempLessonId}][audio_file]`;
                lessonDiv.querySelectorAll('.lesson-duration-minutes').forEach(input => {
                     input.name = `lessons[${sectionTempId}][${tempLessonId}][duration_minutes]`;
                });

                lessonListDiv.appendChild(lessonDiv);
                handleLessonTypeChange.call(lessonTypeSelect);
                updateOrders();
            } else if (closestRemoveLesson) {
                closestRemoveLesson.closest('.lesson-item').remove();
                updateOrders();
            } else if (closestLessonType && target.classList.contains('lesson-type')) {
                 handleLessonTypeChange.call(target);
            }
        });
    }


    function createUniqueId() {
        return Date.now().toString(36) + Math.random().toString(36).substring(2);
    }

    function handleLessonTypeChange() {
        const lessonItem = this.closest('.lesson-item');
        const selectedType = this.value;
        lessonItem.querySelectorAll('.lesson-field').forEach(field => field.style.display = 'none');
        const fieldToShow = lessonItem.querySelector('.lesson-field-' + selectedType);
        if (fieldToShow) fieldToShow.style.display = 'block';
    }

    function initializeLessonSortable(lessonsListDiv) {
        if (lessonsListDiv) { // Ensure element exists
            Sortable.create(lessonsListDiv, {
                animation: 150,
                handle: '.handle-lesson',
                ghostClass: 'sortable-ghost-lesson',
                onEnd: updateOrders
            });
        }
    }

    function updateOrders() {
        document.querySelectorAll('#curriculumSections .section').forEach((section, sectionIndex) => {
            section.setAttribute('data-order', sectionIndex + 1);
            section.querySelectorAll('.lesson-item').forEach((lesson, lessonIndex) => {
                lesson.setAttribute('data-order', lessonIndex + 1);
            });
        });
    }

    const courseForm = document.getElementById('courseForm');
    const curriculumJsonInput = document.getElementById('curriculum_data_json');

    if (courseForm && curriculumJsonInput) { // Ensure elements exist
        courseForm.addEventListener('submit', function(e) {
            updateOrders();
            const curriculum = [];
            document.querySelectorAll('#curriculumSections .section').forEach(sectionEl => {
                const sectionTempId = sectionEl.getAttribute('data-temp-id');
                const sectionData = {
                    temp_id: sectionTempId,
                    title: sectionEl.querySelector('.section-title').value,
                    order: parseInt(sectionEl.getAttribute('data-order')),
                    lessons: []
                };
                sectionEl.querySelectorAll('.lessons-list .lesson-item').forEach(lessonEl => {
                    const lessonTempId = lessonEl.getAttribute('data-temp-id');
                    const lessonType = lessonEl.querySelector('.lesson-type').value;
                    const lesson = {
                        temp_id: lessonTempId,
                        title: lessonEl.querySelector('.lesson-title').value,
                        order: parseInt(lessonEl.getAttribute('data-order')),
                        type: lessonType,
                        content: lessonType === 'text' ? lessonEl.querySelector('.lesson-text-content').value : null,
                        video_url: lessonType === 'video_url' ? lessonEl.querySelector('.lesson-video-url').value : null,
                        duration_minutes: lessonEl.querySelector('.lesson-field-' + lessonType + ' .lesson-duration-minutes')?.value || 0
                    };
                    sectionData.lessons.push(lesson);
                });
                curriculum.push(sectionData);
            });
            curriculumJsonInput.value = JSON.stringify(curriculum);

            const statusButtonValue = e.submitter ? e.submitter.value : 'draft';
            if (statusButtonValue === 'pending') {
                if (curriculum.length === 0 || curriculum.some(s => s.lessons.length === 0)) {
                    e.preventDefault();
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'To submit for review, the course must have at least one section, and each section must have at least one lesson.'});
                    return false;
                }
                 // Check for required thumbnail if submitting for review
                if (!thumbnailInput.files[0] && !thumbnailPreview.src.includes('uploads/course_thumbs/')) { // A bit simplistic check for existing thumb
                    // Check if there's already a thumbnail (e.g. if editing an existing draft, though this is create-course)
                    // For create-course, thumbnail is generally required for pending status.
                     e.preventDefault();
                     Swal.fire({ icon: 'error', title: 'Validation Error', text: 'A course thumbnail is required to submit for review.'});
                     return false;
                }
            }
        });
    }

    document.querySelectorAll('.lesson-type').forEach(sel => handleLessonTypeChange.call(sel));
    document.querySelectorAll('.sortable-lessons').forEach(initializeLessonSortable);
    if (sectionsContainer) updateOrders(); // Initial call if sectionsContainer exists
});
</script>

<style>
    body { background-color: #f8f9fa; }
    .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .handle-section, .handle-lesson { cursor: move; }
    .sortable-ghost-section { opacity: 0.5; background: #c8ebfb; }
    .sortable-ghost-lesson { opacity: 0.4; background: #e6f7ff; }
    .section-header:hover { background-color: #e9ecef !important; }
    .lesson-item:hover { background-color: #f1f1f1 !important; }
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>