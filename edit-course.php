<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course = null;
$sections = []; // Initialize to prevent errors if course not found

if ($course_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            // Authorization Logic
            if (!($_SESSION['user_role'] === 'admin' || ($_SESSION['user_role'] === 'tutor' && $course['tutor_id'] == $_SESSION['user_id']))) {
                $_SESSION['error_message'] = "You are not authorized to edit this course.";
                header("Location: tutor-dashboard.php");
                exit();
            }

            // Fetch sections for Tab 2 (Content & Structure) - will be used later when inserting tab content
            // $stmt_sections = $conn->prepare("SELECT * FROM course_sections WHERE course_id = ? ORDER BY `order` ASC");
            // $stmt_sections->execute([$course['course_id']]);
            // $sections = $stmt_sections->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $_SESSION['error_message'] = "Course not found.";
            header("Location: tutor-dashboard.php"); // Or an appropriate error page
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error fetching course data in edit-course.php: " . $e->getMessage());
        $_SESSION['error_message'] = "Database error fetching course data.";
        header("Location: tutor-dashboard.php"); // Or an appropriate error page
        exit();
    }
} else {
    $_SESSION['error_message'] = "No course ID specified.";
    header("Location: tutor-dashboard.php"); // Or an appropriate error page
    exit();
}

// Handle POST Actions (Course Details, Sections)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
        // Redirect immediately if CSRF fails for any action
        header("Location: edit-course.php?id=" . $course_id . (isset($_POST['form_action']) && $_POST['form_action'] !== 'update_details' ? "&active_tab=content" : ""));
        exit();
    }

    $action = $_POST['form_action'];

    try {
        if ($action === 'update_details') {
            // Sanitize and validate inputs
        $new_title = sanitize_input($_POST['course_title']);
        $new_description = sanitize_input($_POST['course_description']);
        $new_language = sanitize_input($_POST['course_language']);
        $new_level = sanitize_input($_POST['course_level']);
        $new_category = sanitize_input($_POST['course_category'] ?? null);
        $new_price = isset($_POST['course_price']) ? floatval($_POST['course_price']) : 0;
        $new_is_free = isset($_POST['is_free']) ? 1 : 0;
        if ($new_is_free) {
            $new_price = 0;
        }

        $update_fields = [
            'title' => $new_title,
            'description' => $new_description,
            'language' => $new_language,
            'level' => $new_level,
            'category' => $new_category,
            'price' => $new_price,
            'is_free' => $new_is_free,
        ];
        $sql_set_parts = [];
        $params = [];

        foreach ($update_fields as $key => $value) {
            $sql_set_parts[] = "$key = ?";
            $params[] = $value;
        }

        // Handle new thumbnail upload
        $new_thumbnail_path_for_db = $course['thumbnail_url']; // Keep old one by default
        if (isset($_FILES['new_course_thumbnail']) && $_FILES['new_course_thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/course_thumbs/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['error_message'] = "Failed to create thumbnail upload directory.";
                    // Potentially redirect or stop further processing
                }
            }
            
            $fileExt = strtolower(pathinfo($_FILES['new_course_thumbnail']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($fileExt, $allowedTypes)) {
                $_SESSION['error_message'] = "Invalid thumbnail file type. Only JPG, JPEG, PNG, GIF allowed.";
            } elseif ($_FILES['new_course_thumbnail']['size'] > $maxSize) {
                $_SESSION['error_message'] = "Thumbnail file size exceeds 2MB limit.";
            } else {
                // Delete old thumbnail if it exists and is a local file
                if (!empty($course['thumbnail_url']) && !filter_var($course['thumbnail_url'], FILTER_VALIDATE_URL) && file_exists(__DIR__ . '/' . $course['thumbnail_url'])) {
                    unlink(__DIR__ . '/' . $course['thumbnail_url']);
                }

                $fileName = uniqid('thumb_', true) . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['new_course_thumbnail']['tmp_name'], $targetPath)) {
                    $new_thumbnail_path_for_db = 'uploads/course_thumbs/' . $fileName;
                    $sql_set_parts[] = "thumbnail_url = ?";
                    $params[] = $new_thumbnail_path_for_db;
                } else {
                    $_SESSION['error_message'] = "Failed to upload new thumbnail.";
                }
            }
        }

        if (!isset($_SESSION['error_message'])) { // Proceed if no upload error
            if (!empty($sql_set_parts)) {
                $sql_set_parts[] = "updated_at = NOW()";
                $params[] = $course_id; // For the WHERE clause

                $sql = "UPDATE courses SET " . implode(", ", $sql_set_parts) . " WHERE course_id = ?";
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    $_SESSION['success_message'] = "Course details updated successfully!";
                    // log_admin_activity("Updated course details for course ID: $course_id");

                    // Refresh course data after update
                    $stmt_refresh = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
                    $stmt_refresh->execute([$course_id]);
                    $course = $stmt_refresh->fetch(PDO::FETCH_ASSOC);

                } catch (PDOException $e) {
                    error_log("Error updating course details: " . $e->getMessage());
                    $_SESSION['error_message'] = "Database error updating course details.";
                }
            } else {
                 // This case should ideally not be reached if form is submitted,
                 // but as a fallback if only thumbnail was attempted and failed.
                 if(!isset($_SESSION['error_message'])) { // if no specific error was set by thumbnail logic
                    $_SESSION['info_message'] = "No changes were made to the course details.";
                 }
            }
        }
    }
            // ... (existing update_details logic from previous step, assumed to be here)
            // Ensure this part also correctly refreshes $course variable if details change.
             if (!isset($_SESSION['error_message'])) {
                $_SESSION['success_message'] = "Course details updated successfully!";
                // Refresh course data after update
                $stmt_refresh = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
                $stmt_refresh->execute([$course_id]);
                $course = $stmt_refresh->fetch(PDO::FETCH_ASSOC);
            }
            header("Location: edit-course.php?id=" . $course_id); // Stays on details tab by default
            exit();

        } elseif ($action === 'add_section') {
            $section_title = sanitize_input($_POST['section_title']);
            $section_order = !empty($_POST['section_order']) ? (int)$_POST['section_order'] : null;

            if (empty($section_title)) {
                $_SESSION['error_message'] = "Section title is required.";
            } else {
                if ($section_order === null) {
                    $stmt_max_order = $conn->prepare("SELECT MAX(`order`) AS max_order FROM course_sections WHERE course_id = ?");
                    $stmt_max_order->execute([$course_id]);
                    $max_order_result = $stmt_max_order->fetch(PDO::FETCH_ASSOC);
                    $section_order = ($max_order_result && $max_order_result['max_order'] !== null) ? $max_order_result['max_order'] + 1 : 1;
                }
                $stmt = $conn->prepare("INSERT INTO course_sections (course_id, title, `order`) VALUES (?, ?, ?)");
                $stmt->execute([$course_id, $section_title, $section_order]);
                $_SESSION['success_message'] = "Section added successfully.";
                // log_admin_activity("Added section '{$section_title}' to course ID: $course_id");
            }
            header("Location: edit-course.php?id=" . $course_id . "&active_tab=content");
            exit();

        } elseif ($action === 'edit_section') {
            $section_id_edit = (int)$_POST['section_id'];
            $section_title_edit = sanitize_input($_POST['section_title']);
            $section_order_edit = (int)$_POST['section_order'];

            if (empty($section_title_edit) || $section_order_edit < 1) {
                $_SESSION['error_message'] = "Section title and a valid order are required.";
            } else {
                // Check if this section belongs to the current course and user
                $stmt_check = $conn->prepare("SELECT cs.section_id FROM course_sections cs JOIN courses c ON cs.course_id = c.course_id WHERE cs.section_id = ? AND c.course_id = ? AND (c.tutor_id = ? OR ? = 'admin')");
                $stmt_check->execute([$section_id_edit, $course_id, $_SESSION['user_id'], $_SESSION['user_role']]);
                if (!$stmt_check->fetch()) {
                     $_SESSION['error_message'] = "Unauthorized or invalid section.";
                } else {
                    $stmt = $conn->prepare("UPDATE course_sections SET title = ?, `order` = ? WHERE section_id = ? AND course_id = ?");
                    $stmt->execute([$section_title_edit, $section_order_edit, $section_id_edit, $course_id]);
                    $_SESSION['success_message'] = "Section updated successfully.";
                    // log_admin_activity("Updated section ID: $section_id_edit for course ID: $course_id");
                }
            }
            header("Location: edit-course.php?id=" . $course_id . "&active_tab=content");
            exit();

        } elseif ($action === 'delete_section') {
            $section_id_delete = (int)$_POST['section_id'];
             // Check if this section belongs to the current course and user
            $stmt_check = $conn->prepare("SELECT cs.section_id FROM course_sections cs JOIN courses c ON cs.course_id = c.course_id WHERE cs.section_id = ? AND c.course_id = ? AND (c.tutor_id = ? OR ? = 'admin')");
            $stmt_check->execute([$section_id_delete, $course_id, $_SESSION['user_id'], $_SESSION['user_role']]);
            if (!$stmt_check->fetch()) {
                 $_SESSION['error_message'] = "Unauthorized or invalid section for deletion.";
            } else {
                $conn->beginTransaction();
                try {
                    // Delete lessons associated with the section first
                    $stmt_delete_lessons = $conn->prepare("DELETE FROM lessons WHERE section_id = ?");
                    $stmt_delete_lessons->execute([$section_id_delete]);
                    // Then delete the section
                    $stmt_delete_section = $conn->prepare("DELETE FROM course_sections WHERE section_id = ? AND course_id = ?");
                    $stmt_delete_section->execute([$section_id_delete, $course_id]);
                    $conn->commit();
                    $_SESSION['success_message'] = "Section and its lessons deleted successfully.";
                    // log_admin_activity("Deleted section ID: $section_id_delete and its lessons from course ID: $course_id");
                } catch (PDOException $e) {
                    $conn->rollBack();
                    error_log("Error deleting section: " . $e->getMessage());
                    $_SESSION['error_message'] = "Database error deleting section.";
                }
            }
            header("Location: edit-course.php?id=" . $course_id . "&active_tab=content");
            exit();

        } elseif ($action === 'add_lesson' || $action === 'edit_lesson') {
            $lesson_title = sanitize_input($_POST['lesson_title']);
            $lesson_type = sanitize_input($_POST['lesson_type']);
            $lesson_order = (int)$_POST['lesson_order'];
            $section_id_for_lesson = (int)$_POST['section_id_modal_input']; // From modal's hidden input
            $lesson_id_for_edit = ($action === 'edit_lesson') ? (int)$_POST['lesson_id_modal_input'] : null;

            $lesson_content = null;
            $lesson_video_url = null;
            $lesson_file_path = null;

            if (empty($lesson_title) || empty($lesson_type) || $lesson_order < 1 || empty($section_id_for_lesson)) {
                throw new Exception("Lesson title, type, order, and section ID are required.");
            }
            // Ensure section belongs to this course
            $stmt_check_section = $conn->prepare("SELECT section_id FROM course_sections WHERE section_id = ? AND course_id = ?");
            $stmt_check_section->execute([$section_id_for_lesson, $course_id]);
            if(!$stmt_check_section->fetch()){ throw new Exception("Invalid section specified for the lesson."); }


            if ($lesson_type === 'text') {
                $lesson_content = $_POST['lesson_text_content'] ?? '';
            } elseif ($lesson_type === 'video_url') {
                $lesson_video_url = sanitize_input($_POST['lesson_video_url'] ?? '');
                if (!empty($lesson_video_url) && !filter_var($lesson_video_url, FILTER_VALIDATE_URL)) {
                    throw new Exception("Invalid Video URL provided.");
                }
            } elseif ($lesson_type === 'pdf_file' || $lesson_type === 'audio_file') {
                $file_input_name = ($lesson_type === 'pdf_file') ? 'lesson_pdf_file' : 'lesson_audio_file';
                $material_upload_dir = __DIR__ . '/uploads/course_materials/'; // Define it here

                if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
                    if (!is_dir($material_upload_dir)) {
                        if (!mkdir($material_upload_dir, 0755, true)) { throw new Exception("Failed to create materials upload directory."); }
                    }
                    $fileExt = strtolower(pathinfo($_FILES[$file_input_name]['name'], PATHINFO_EXTENSION));
                    $allowed_pdf = ['pdf'];
                    $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
                    $max_size = 10 * 1024 * 1024; // 10MB

                    if ($lesson_type === 'pdf_file' && !in_array($fileExt, $allowed_pdf)) { throw new Exception("Invalid PDF file type. Only .pdf allowed."); }
                    if ($lesson_type === 'audio_file' && !in_array($fileExt, $allowed_audio)) { throw new Exception("Invalid audio file type (allowed: mp3, wav, ogg, m4a)."); }
                    if ($_FILES[$file_input_name]['size'] > $max_size) { throw new Exception("File size exceeds limit (10MB)."); }

                    // Delete old file if editing and new one is uploaded
                    if ($action === 'edit_lesson' && $lesson_id_for_edit) {
                        $stmt_old_file = $conn->prepare("SELECT file_path FROM lessons WHERE lesson_id = ?");
                        $stmt_old_file->execute([$lesson_id_for_edit]);
                        $old_file_path = $stmt_old_file->fetchColumn();
                        if ($old_file_path && file_exists(__DIR__ . '/' . $old_file_path)) {
                            unlink(__DIR__ . '/' . $old_file_path);
                        }
                    }
                    $new_filename = uniqid('lessonfile_', true) . '.' . $fileExt;
                    if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $material_upload_dir . $new_filename)) {
                        $lesson_file_path = 'uploads/course_materials/' . $new_filename;
                    } else { throw new Exception("Failed to upload lesson file."); }
                } elseif ($action === 'add_lesson') {
                     throw new Exception("A file is required when creating a 'PDF File' or 'Audio File' lesson type.");
                }
                // If editing and no new file is uploaded, keep the old path (handled below)
            }

            if ($action === 'add_lesson') {
                $stmt = $conn->prepare("INSERT INTO lessons (course_id, section_id, title, `order`, lesson_type, content, video_url, file_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([$course_id, $section_id_for_lesson, $lesson_title, $lesson_order, $lesson_type, $lesson_content, $lesson_video_url, $lesson_file_path]);
                $_SESSION['success_message'] = "Lesson added successfully.";
            } else { // edit_lesson
                $current_lesson_data_for_edit = null;
                if ($lesson_id_for_edit) {
                    $stmt_old_lesson = $conn->prepare("SELECT lesson_type, content, video_url, file_path FROM lessons WHERE lesson_id = ? AND section_id = ? AND course_id = ?");
                    $stmt_old_lesson->execute([$lesson_id_for_edit, $section_id_for_lesson, $course_id]);
                    $current_lesson_data_for_edit = $stmt_old_lesson->fetch(PDO::FETCH_ASSOC);
                    if(!$current_lesson_data_for_edit){ throw new Exception("Lesson not found or not part of this section/course for editing.");}
                } else { throw new Exception("Lesson ID missing for edit action."); }

                // If type changed from a file type to non-file type, delete old file
                if (($current_lesson_data_for_edit['lesson_type'] === 'pdf_file' || $current_lesson_data_for_edit['lesson_type'] === 'audio_file') &&
                    ($lesson_type !== 'pdf_file' && $lesson_type !== 'audio_file') &&
                    !empty($current_lesson_data_for_edit['file_path']) && file_exists(__DIR__ . '/' . $current_lesson_data_for_edit['file_path'])) {
                    unlink(__DIR__ . '/' . $current_lesson_data_for_edit['file_path']);
                    $current_lesson_data_for_edit['file_path'] = null; // Ensure it's cleared if type changes
                }

                // If no new file uploaded for a file type during edit, retain existing file_path
                if (($lesson_type === 'pdf_file' || $lesson_type === 'audio_file') && $lesson_file_path === null) {
                    $lesson_file_path = $current_lesson_data_for_edit['file_path'] ?? null;
                }

                // Clear fields not relevant to the new type
                if ($lesson_type !== 'text') $lesson_content = null;
                if ($lesson_type !== 'video_url') $lesson_video_url = null;
                if ($lesson_type !== 'pdf_file' && $lesson_type !== 'audio_file') $lesson_file_path = null;


                $stmt = $conn->prepare("UPDATE lessons SET title = ?, `order` = ?, lesson_type = ?, content = ?, video_url = ?, file_path = ?, updated_at = NOW() WHERE lesson_id = ? AND section_id = ? AND course_id = ?");
                $stmt->execute([$lesson_title, $lesson_order, $lesson_type, $lesson_content, $lesson_video_url, $lesson_file_path, $lesson_id_for_edit, $section_id_for_lesson, $course_id]);
                $_SESSION['success_message'] = "Lesson updated successfully.";
            }
            header("Location: edit-course.php?id=" . $course_id . "&active_tab=content"); exit();

        } elseif ($action === 'delete_lesson') {
            $lesson_id_delete = (int)$_POST['lesson_id'];
            // Fetch lesson to delete its file if necessary
            $stmt_old_lesson = $conn->prepare("SELECT lesson_type, file_path FROM lessons WHERE lesson_id = ? AND course_id = ?");
            $stmt_old_lesson->execute([$lesson_id_delete, $course_id]);
            $lesson_to_delete = $stmt_old_lesson->fetch(PDO::FETCH_ASSOC);

            if ($lesson_to_delete && ($lesson_to_delete['lesson_type'] === 'pdf_file' || $lesson_to_delete['lesson_type'] === 'audio_file') && !empty($lesson_to_delete['file_path'])) {
                if (file_exists(__DIR__ . '/' . $lesson_to_delete['file_path'])) {
                    unlink(__DIR__ . '/' . $lesson_to_delete['file_path']);
                }
            }

            $stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ? AND course_id = ?");
            $stmt->execute([$lesson_id_delete, $course_id]);
            $_SESSION['success_message'] = "Lesson deleted successfully.";
            header("Location: edit-course.php?id=" . $course_id . "&active_tab=content"); exit();
        }

    } catch (PDOException $e) {
        error_log("General POST error in edit-course.php (PDO): " . $e->getMessage());
        $_SESSION['error_message'] = "A database error occurred: " . $e->getMessage();
    } catch (Exception $ex) { // Catch other general exceptions
        error_log("General POST error in edit-course.php (General): " . $ex->getMessage());
        $_SESSION['error_message'] = "An error occurred: " . $ex->getMessage();
    }
    // Redirect if any error message was set and not handled by specific action redirects
    if(isset($_SESSION['error_message']) || isset($_SESSION['info_message'])){
        $redirect_tab_on_error = (isset($_POST['form_action']) && $_POST['form_action'] !== 'update_details') ? '&active_tab=content' : '';
        header("Location: edit-course.php?id=" . $course_id . $redirect_tab_on_error);
        exit();
    }
}


$pageTitle = "Manage Course: " . htmlspecialchars($course['title'] ?? 'Unknown Course');
require __DIR__ . '/includes/header.php';
// SweetAlert display script is now part of the main HTML body.
?>

<div class="container py-4">
    <h1 class="mb-4">Manage Course: <?= htmlspecialchars($course['title']) ?></h1>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['error_message'])): ?>
            Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= addslashes($_SESSION['error_message']) ?>'});
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            Swal.fire({ icon: 'success', title: 'Success!', text: '<?= addslashes($_SESSION['success_message']) ?>'});
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    });
    </script>

    <ul class="nav nav-tabs mb-3" id="courseEditTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#detailsTabPane" type="button" role="tab" aria-controls="detailsTabPane" aria-selected="true">Course Details</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#contentTabPane" type="button" role="tab" aria-controls="contentTabPane" aria-selected="false">Content & Structure</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settingsTabPane" type="button" role="tab" aria-controls="settingsTabPane" aria-selected="false">Settings & Publishing</button>
        </li>
    </ul>

    <div class="tab-content" id="courseEditTabsContent">
        <!-- Tab 1: Course Details Pane (Active by default) -->
        <div class="tab-pane fade show active" id="detailsTabPane" role="tabpanel" aria-labelledby="details-tab">
            <form method="POST" enctype="multipart/form-data" action="edit-course.php?id=<?= $course['course_id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="form_action" value="update_details">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5>Edit Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="course_title" class="form-label">Course Title*</label>
                            <input type="text" class="form-control" id="course_title" name="course_title" value="<?= htmlspecialchars($course['title'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="course_description" class="form-label">Short Description*</label>
                            <textarea class="form-control" id="course_description" name="course_description" rows="3" required><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_language" class="form-label">Language*</label>
                                <select class="form-select" id="course_language" name="course_language" required>
                                    <option value="">Select language</option>
                                    <option value="Luganda" <?= ($course['language'] ?? '') === 'Luganda' ? 'selected' : '' ?>>Luganda</option>
                                    <option value="Runyoro" <?= ($course['language'] ?? '') === 'Runyoro' ? 'selected' : '' ?>>Runyoro</option>
                                    <option value="Lusoga" <?= ($course['language'] ?? '') === 'Lusoga' ? 'selected' : '' ?>>Lusoga</option>
                                    <option value="Rukiga" <?= ($course['language'] ?? '') === 'Rukiga' ? 'selected' : '' ?>>Rukiga</option>
                                    <option value="Swahili" <?= ($course['language'] ?? '') === 'Swahili' ? 'selected' : '' ?>>Swahili</option>
                                    <option value="English" <?= ($course['language'] ?? '') === 'English' ? 'selected' : '' ?>>English</option>
                                    <option value="Other" <?= ($course['language'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="course_level" class="form-label">Level*</label>
                                <select class="form-select" id="course_level" name="course_level" required>
                                    <option value="Beginner" <?= ($course['level'] ?? '') === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                                    <option value="Intermediate" <?= ($course['level'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                    <option value="Advanced" <?= ($course['level'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="course_category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="course_category" name="course_category" value="<?= htmlspecialchars($course['category'] ?? '') ?>" placeholder="e.g., Business, Technology, Arts">
                        </div>

                        <hr>
                        <h5 class="mt-3">Pricing</h5>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="course_price" class="form-label">Price (UGX)</label>
                                <input type="number" class="form-control" id="course_price" name="course_price" value="<?= htmlspecialchars($course['price'] ?? '0') ?>" min="0" step="1000" <?= ($course['is_free'] ?? 0) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_free" name="is_free" value="1" <?= ($course['is_free'] ?? 0) ? 'checked' : '' ?> onchange="document.getElementById('course_price').disabled = this.checked; if(this.checked) document.getElementById('course_price').value = '0';">
                                    <label class="form-check-label" for="is_free">
                                        This is a free course
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mt-3">Course Thumbnail</h5>
                        <div class="mb-3">
                            <label for="new_course_thumbnail" class="form-label">Upload New Thumbnail (Optional)</label>
                            <input type="file" class="form-control" id="new_course_thumbnail" name="new_course_thumbnail" accept="image/*">
                            <small class="form-text text-muted">Current thumbnail:</small><br>
                            <?php
                            $raw_thumbnail_url_detail = $course['thumbnail_url'] ?? null;
                            $final_thumbnail_url_detail = 'images/default-course.jpg';
                            if (!empty($raw_thumbnail_url_detail)) {
                                if (preg_match('~^https?://~i', $raw_thumbnail_url_detail)) { $final_thumbnail_url_detail = $raw_thumbnail_url_detail; }
                                elseif (strpos($raw_thumbnail_url_detail, 'uploads/course_thumbs/') === 0) { $final_thumbnail_url_detail = $raw_thumbnail_url_detail; }
                                elseif (strpos($raw_thumbnail_url_detail, '/') === false) { $final_thumbnail_url_detail = 'uploads/course_thumbs/' . $raw_thumbnail_url_detail; }
                                else { if (strpos($raw_thumbnail_url_detail, '/') === 0) { $final_thumbnail_url_detail = $raw_thumbnail_url_detail; } else { $final_thumbnail_url_detail = 'uploads/course_thumbs/' . $raw_thumbnail_url_detail;}}
                            }
                            $final_thumbnail_url_detail = str_replace('//', '/', $final_thumbnail_url_detail);
                            if (empty($final_thumbnail_url_detail) || $final_thumbnail_url_detail === 'uploads/course_thumbs/' || $final_thumbnail_url_detail === '/') { $final_thumbnail_url_detail = 'images/default-course.jpg'; }
                            ?>
                            <img src="<?= htmlspecialchars($final_thumbnail_url_detail) ?>" alt="Current Thumbnail" style="max-width: 200px; max-height: 100px; margin-top: 10px;" onerror="this.onerror=null; this.src='images/default-course.jpg';">
                        </div>

                        <div class="mt-4">
                            <button type="submit" name="form_action" value="update_details" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Course Details
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab 2: Content & Structure Pane -->
        <div class="tab-pane fade" id="contentTabPane" role="tabpanel" aria-labelledby="content-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Course Content & Structure</h4>
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                    <i class="fas fa-plus"></i> Add New Section
                </button>
            </div>
            <?php
            // Ensure $sections is fetched if not already (it should be by the top script block)
            if (!isset($sections_tab)) { // Use $sections_tab if defined, otherwise fallback to $sections
                $stmt_sections_display = $conn->prepare("SELECT * FROM course_sections WHERE course_id = ? ORDER BY `order` ASC");
                $stmt_sections_display->execute([$course['course_id']]);
                $sections_to_display = $stmt_sections_display->fetchAll(PDO::FETCH_ASSOC);
            } else {
                 $sections_to_display = $sections_tab;
            }
            ?>
            <?php if (empty($sections_to_display)): ?>
                <div class="alert alert-info">No sections added yet. Click "Add New Section" to get started.</div>
            <?php else: ?>
                <div class="accordion" id="sectionsAccordion">
                    <?php foreach ($sections_to_display as $section_idx => $section): ?>
                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="sectionHeading<?= $section['section_id'] ?>">
                                <button class="accordion-button <?= $section_idx > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#sectionCollapse<?= $section['section_id'] ?>" aria-expanded="<?= $section_idx === 0 ? 'true' : 'false' ?>" aria-controls="sectionCollapse<?= $section['section_id'] ?>">
                                    <strong>Section <?= $section_idx + 1 ?>:</strong> <?= htmlspecialchars($section['title']) ?>
                                    <span class="text-muted small ms-2">(Order: <?= htmlspecialchars($section['order']) ?>)</span>
                                </button>
                            </h2>
                            <div id="sectionCollapse<?= $section['section_id'] ?>" class="accordion-collapse collapse <?= $section_idx === 0 ? 'show' : '' ?>" aria-labelledby="sectionHeading<?= $section['section_id'] ?>">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-end mb-2">
                                        <button class="btn btn-sm btn-outline-primary me-2 edit-section-btn"
                                                data-bs-toggle="modal" data-bs-target="#editSectionModal"
                                                data-section-id="<?= $section['section_id'] ?>"
                                                data-section-title="<?= htmlspecialchars($section['title']) ?>"
                                                data-section-order="<?= htmlspecialchars($section['order']) ?>">
                                            <i class="fas fa-edit"></i> Edit Section Details
                                        </button>
                                        <form method="POST" action="edit-course.php?id=<?= $course['course_id'] ?>&active_tab=content" class="d-inline delete-section-form">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <input type="hidden" name="form_action" value="delete_section">
                                            <input type="hidden" name="section_id" value="<?= $section['section_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Delete Entire Section
                                            </button>
                                        </form>
                                    </div>
                                    <?php
                                    $stmt_lessons = $conn->prepare("SELECT * FROM lessons WHERE section_id = ? ORDER BY `order` ASC");
                                    $stmt_lessons->execute([$section['section_id']]);
                                    $lessons_data = $stmt_lessons->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <h6 class="mt-3">Lessons:</h6>
                                    <?php if (empty($lessons_data)): ?>
                                        <p class="text-muted">No lessons in this section yet.</p>
                                    <?php else: ?>
                                        <ul class="list-group mb-3">
                                            <?php foreach ($lessons_data as $lesson): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas <?= ($lesson['lesson_type'] === 'video_url') ? 'fa-video' : (($lesson['lesson_type'] === 'pdf_file') ? 'fa-file-pdf text-danger' : (($lesson['lesson_type'] === 'audio_file') ? 'fa-file-audio text-info' : 'fa-file-alt')) ?> me-2"></i>
                                                        <?= htmlspecialchars($lesson['title']) ?>
                                                        <span class="badge bg-secondary ms-2"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $lesson['lesson_type']))) ?></span>
                                                        <small class="text-muted ms-2">(Order: <?= htmlspecialchars($lesson['order']) ?>)</small>
                                                    </span>
                                                    <div>
                                                        <button class="btn btn-xs btn-outline-primary edit-lesson-btn"
                                                                data-bs-toggle="modal" data-bs-target="#lessonEditorModal"
                                                                data-lesson-id="<?= $lesson['lesson_id'] ?>"
                                                                data-section-id="<?= $section['section_id'] ?>"
                                                                data-title="<?= htmlspecialchars($lesson['title']) ?>"
                                                                data-type="<?= htmlspecialchars($lesson['lesson_type']) ?>"
                                                                data-content="<?= htmlspecialchars($lesson['content'] ?? '') // For text type ?>"
                                                                data-video-url="<?= htmlspecialchars($lesson['video_url'] ?? '') // For video_url type ?>"
                                                                data-file-path="<?= htmlspecialchars($lesson['file_path'] ?? '') // For pdf_file or audio_file type ?>"
                                                                data-order="<?= htmlspecialchars($lesson['order']) ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" action="edit-course.php?id=<?= $course['course_id'] ?>&active_tab=content" class="d-inline delete-lesson-form">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                            <input type="hidden" name="form_action" value="delete_lesson">
                                                            <input type="hidden" name="lesson_id" value="<?= $lesson['lesson_id'] ?>">
                                                            <button type="submit" class="btn btn-xs btn-outline-danger ms-1">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-success add-lesson-btn" type="button"
                                            data-bs-toggle="modal" data-bs-target="#lessonEditorModal"
                                            data-section-id="<?= $section['section_id'] ?>">
                                        <i class="fas fa-plus"></i> Add Lesson to this Section
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php // "Add New Section" button moved to the top of the tab pane header ?>
        </div>

        <!-- Tab 3: Settings & Publishing Pane -->
        <div class="tab-pane fade" id="settingsTabPane" role="tabpanel" aria-labelledby="settings-tab">
            <h4>Settings & Publishing</h4>
            <p>Current Status: <span class="badge bg-info"><?= htmlspecialchars(ucfirst($course['status'] ?? 'N/A')) ?></span></p>
            <p><em>More settings (e.g., changing status, managing course visibility, etc.) will be available here in a future update.</em></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Edit Section Modal Population
    const editSectionModal = document.getElementById('editSectionModal');
    if (editSectionModal) {
        editSectionModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const sectionId = button.getAttribute('data-section-id');
            const sectionTitle = button.getAttribute('data-section-title');
            const sectionOrder = button.getAttribute('data-section-order');

            const modalTitle = editSectionModal.querySelector('.modal-title');
            const sectionIdInput = editSectionModal.querySelector('#edit_section_id');
            const sectionTitleInput = editSectionModal.querySelector('#edit_section_title');
            const sectionOrderInput = editSectionModal.querySelector('#edit_section_order');

            modalTitle.textContent = 'Edit Section: ' + sectionTitle;
            sectionIdInput.value = sectionId;
            sectionTitleInput.value = sectionTitle;
            sectionOrderInput.value = sectionOrder;
        });
    }

    // Handle Delete Section Confirmation
    const deleteSectionForms = document.querySelectorAll('.delete-section-form');
    deleteSectionForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this section and all its lessons? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Activate Tab from URL Parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('active_tab');
    if (activeTab) {
        const tabElement = document.querySelector('#' + activeTab + '-tab');
        if (tabElement) {
            new bootstrap.Tab(tabElement).show();
        }
    }

    // --- Lesson Modal JavaScript ---
    const lessonEditorModalEl = document.getElementById('lessonEditorModal');
    const lessonForm = document.getElementById('lessonForm');
    const lessonTypeModalSelect = document.getElementById('lesson_type_modal');
    const lessonContentFieldsDiv = document.getElementById('lessonContentFields');
    const lessonEditorModalLabel = document.getElementById('lessonEditorModalLabel');
    const lessonIdModalInput = document.getElementById('lesson_id_modal_input');
    const sectionIdModalInputForLesson = document.getElementById('section_id_modal_input');
    const lessonFormActionInput = document.getElementById('lesson_form_action');
    const lessonTitleModalInput = document.getElementById('lesson_title_modal');
    const lessonOrderModalInput = document.getElementById('lesson_order_modal');
    const lessonTextContentModalTextarea = document.getElementById('lesson_text_content_modal');
    const lessonVideoUrlModalInput = document.getElementById('lesson_video_url_modal');
    const lessonPdfFileInput = document.getElementById('lesson_pdf_file_modal'); // Input for new PDF
    const lessonAudioFileInput = document.getElementById('lesson_audio_file_modal'); // Input for new Audio
    const currentPdfFileDisplay = document.getElementById('current_pdf_file_modal_display');
    const currentAudioFileDisplay = document.getElementById('current_audio_file_modal_display');

    if (lessonTypeModalSelect && lessonContentFieldsDiv) {
        lessonTypeModalSelect.addEventListener('change', function() {
            lessonContentFieldsDiv.querySelectorAll('.lesson-type-field').forEach(div => {
                div.style.display = 'none';
            });
            const selectedType = this.value;
            const targetDivId = 'lesson_' + selectedType + '_div'; // e.g., lesson_pdf_file_div
            const targetDiv = document.getElementById(targetDivId);
            if (targetDiv) {
                targetDiv.style.display = 'block';
            }
        });
    }

    document.querySelectorAll('.add-lesson-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sectionId = this.getAttribute('data-section-id');
            // Ensure a Bootstrap modal instance is correctly initialized if not already
            const lessonModalInstance = bootstrap.Modal.getInstance(lessonEditorModalEl) || new bootstrap.Modal(lessonEditorModalEl);
            
            if(lessonEditorModalLabel) lessonEditorModalLabel.textContent = 'Add New Lesson';
            if(lessonForm) lessonForm.reset();
            if(lessonIdModalInput) lessonIdModalInput.value = '';
            if(sectionIdModalInputForLesson) sectionIdModalInputForLesson.value = sectionId;
            if(lessonFormActionInput) lessonFormActionInput.value = 'add_lesson';

            const lessonsInCurrentSection = document.querySelectorAll(`#sectionCollapse${sectionId} .list-group-item`).length;
            if(lessonOrderModalInput) lessonOrderModalInput.value = lessonsInCurrentSection + 1;
            
            if(currentPdfFileDisplay) currentPdfFileDisplay.textContent = 'No file uploaded yet.'; // Clear current file display
            if(currentAudioFileDisplay) currentAudioFileDisplay.textContent = 'No file uploaded yet.';// Clear current file display
            if(lessonPdfFileInput) lessonPdfFileInput.value = ''; // Clear file input
            if(lessonAudioFileInput) lessonAudioFileInput.value = ''; // Clear file input

            if(lessonTypeModalSelect) {
                lessonTypeModalSelect.value = 'text';
                lessonTypeModalSelect.dispatchEvent(new Event('change'));
            }

            if(lessonModalInstance) lessonModalInstance.show();
        });
    });

    document.querySelectorAll('.edit-lesson-btn').forEach(button => {
        button.addEventListener('click', function() {
            const lessonModalInstance = bootstrap.Modal.getInstance(lessonEditorModalEl) || new bootstrap.Modal(lessonEditorModalEl);
            if(lessonEditorModalLabel) lessonEditorModalLabel.textContent = 'Edit Lesson';
            if(lessonForm) lessonForm.reset();

            if(lessonIdModalInput) lessonIdModalInput.value = this.getAttribute('data-lesson-id');
            if(sectionIdModalInputForLesson) sectionIdModalInputForLesson.value = this.getAttribute('data-section-id');
            if(lessonFormActionInput) lessonFormActionInput.value = 'edit_lesson';

            if(lessonTitleModalInput) lessonTitleModalInput.value = this.getAttribute('data-title');
            const lessonType = this.getAttribute('data-type');
            if(lessonTypeModalSelect) lessonTypeModalSelect.value = lessonType;
            if(lessonOrderModalInput) lessonOrderModalInput.value = this.getAttribute('data-order');

            // Clear all specific content fields before populating
            if(lessonTextContentModalTextarea) lessonTextContentModalTextarea.value = '';
            if(lessonVideoUrlModalInput) lessonVideoUrlModalInput.value = '';
            if(currentPdfFileDisplay) currentPdfFileDisplay.textContent = 'Upload new file to replace current, or leave empty to keep current.';
            if(currentAudioFileDisplay) currentAudioFileDisplay.textContent = 'Upload new file to replace current, or leave empty to keep current.';
            if(lessonPdfFileInput) lessonPdfFileInput.value = '';
            if(lessonAudioFileInput) lessonAudioFileInput.value = '';


            if (lessonType === 'text' && lessonTextContentModalTextarea) {
                lessonTextContentModalTextarea.value = this.getAttribute('data-content');
            } else if (lessonType === 'video_url' && lessonVideoUrlModalInput) {
                lessonVideoUrlModalInput.value = this.getAttribute('data-video-url');
            } else if (lessonType === 'pdf_file' && currentPdfFileDisplay) {
                const filePath = this.getAttribute('data-file-path');
                if (filePath && filePath !== 'null' && filePath.trim() !== '') currentPdfFileDisplay.textContent = 'Current: ' + filePath.split('/').pop() + '. Upload new to replace.';
                else currentPdfFileDisplay.textContent = 'No PDF file uploaded yet.';
            } else if (lessonType === 'audio_file' && currentAudioFileDisplay) {
                 const filePath = this.getAttribute('data-file-path');
                if (filePath && filePath !== 'null' && filePath.trim() !== '') currentAudioFileDisplay.textContent = 'Current: ' + filePath.split('/').pop() + '. Upload new to replace.';
                else currentAudioFileDisplay.textContent = 'No audio file uploaded yet.';
            }

            if(lessonTypeModalSelect) lessonTypeModalSelect.dispatchEvent(new Event('change'));
            if(lessonModalInstance) lessonModalInstance.show();
        });
    });

    document.querySelectorAll('.delete-lesson-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this lesson? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="edit-course.php?id=<?= $course['course_id'] ?>&active_tab=content">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="form_action" value="add_section">
            <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_section_title" class="form-label">Section Title*</label>
                        <input type="text" class="form-control" id="add_section_title" name="section_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_section_order" class="form-label">Order (Optional)</label>
                        <input type="number" class="form-control" id="add_section_order" name="section_order" min="1" placeholder="Leave blank for next available">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Section</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="edit-course.php?id=<?= $course['course_id'] ?>&active_tab=content">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="form_action" value="edit_section">
            <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
            <input type="hidden" name="section_id" id="edit_section_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_section_title" class="form-label">Section Title*</label>
                        <input type="text" class="form-control" id="edit_section_title" name="section_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_section_order" class="form-label">Order*</label>
                        <input type="number" class="form-control" id="edit_section_order" name="section_order" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Section</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lesson Editor Modal -->
<div class="modal fade" id="lessonEditorModal" tabindex="-1" aria-labelledby="lessonEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="lessonForm" method="POST" action="edit-course.php?id=<?= $course['course_id'] ?>&active_tab=content" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                <input type="hidden" name="lesson_id" id="lesson_id_modal_input" value="">
                <input type="hidden" name="section_id_modal_input" id="section_id_modal_input" value="">
                <input type="hidden" name="form_action" id="lesson_form_action" value=""> <!-- 'add_lesson' or 'edit_lesson' -->

                <div class="modal-header">
                    <h5 class="modal-title" id="lessonEditorModalLabel">Add New Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lesson_title_modal" class="form-label">Lesson Title*</label>
                        <input type="text" class="form-control" id="lesson_title_modal" name="lesson_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="lesson_type_modal" class="form-label">Lesson Type*</label>
                        <select class="form-select" id="lesson_type_modal" name="lesson_type">
                            <option value="text">Text Content</option>
                            <option value="video_url">Video URL</option>
                            <option value="pdf_file">PDF File</option>
                            <option value="audio_file">Audio File</option>
                        </select>
                    </div>

                    <!-- Conditional Content Fields -->
                    <div id="lessonContentFields">
                        <div class="mb-3 lesson-type-field" id="lesson_text_content_div">
                            <label for="lesson_text_content_modal" class="form-label">Text Content</label>
                            <textarea class="form-control" id="lesson_text_content_modal" name="lesson_text_content" rows="10"></textarea>
                            <small class="form-text text-muted">You can use HTML or Markdown (if a parser is implemented on display).</small>
                        </div>
                        <div class="mb-3 lesson-type-field" id="lesson_video_url_div" style="display: none;">
                            <label for="lesson_video_url_modal" class="form-label">Video URL</label>
                            <input type="url" class="form-control" id="lesson_video_url_modal" name="lesson_video_url" placeholder="e.g., https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="mb-3 lesson-type-field" id="lesson_pdf_file_div" style="display: none;">
                            <label for="lesson_pdf_file_modal" class="form-label">PDF File</label>
                            <input type="file" class="form-control" id="lesson_pdf_file_modal" name="lesson_pdf_file" accept=".pdf">
                            <small id="current_pdf_file_modal_display" class="form-text text-muted mt-1"></small>
                        </div>
                        <div class="mb-3 lesson-type-field" id="lesson_audio_file_div" style="display: none;">
                            <label for="lesson_audio_file_modal" class="form-label">Audio File</label>
                            <input type="file" class="form-control" id="lesson_audio_file_modal" name="lesson_audio_file" accept="audio/*">
                            <small id="current_audio_file_modal_display" class="form-text text-muted mt-1"></small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lesson_order_modal" class="form-label">Order (within section)</label>
                        <input type="number" class="form-control" id="lesson_order_modal" name="lesson_order" min="1" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveLessonBtn">Save Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
require __DIR__ . '/includes/footer.php';
?>