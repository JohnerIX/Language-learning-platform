<?php
require_once __DIR__ . '/includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$pageTitle = "My Profile";

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
    $language_preference = filter_input(INPUT_POST, 'language_preference', FILTER_SANITIZE_STRING);

    try {
        // Update basic info
        $stmt = $conn->prepare("
            UPDATE users 
            SET name = ?, email = ?, phone = ?, bio = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$name, $email, $phone, $bio, $_SESSION['user_id']]);

        // Update preferences in user_meta
        $stmt = $conn->prepare("
            INSERT INTO user_meta (user_id, language_preference) 
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE language_preference = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $language_preference, $language_preference]);

        // Handle profile picture upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/profile_pics/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExt = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $fileName = 'user_' . $_SESSION['user_id'] . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
                $dbPath = '/uploads/profile_pics/' . $fileName;
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
                $stmt->execute([$dbPath, $_SESSION['user_id']]);
                $_SESSION['profile_pic'] = $dbPath; // Update session variable for header
            } else {
                $_SESSION['error_message'] = 'Failed to move uploaded profile picture.';
            }
        }

        if (!isset($_SESSION['error_message'])) { // Only set success if no error occurred during pic upload
            $_SESSION['success_message'] = 'Profile updated successfully!';
        }
        redirect('profile.php');
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
        error_log("Profile update PDOException: " . $e->getMessage());
        redirect('profile.php');
    }
}

require __DIR__ . '/includes/header.php';

// Fetch user data (fetch after potential update)
$stmt = $conn->prepare("SELECT u.*, um.language_preference FROM users u LEFT JOIN user_meta um ON u.user_id = um.user_id WHERE u.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= addslashes($_SESSION['error_message']) ?>',
            background: '#f8d7da',
            color: '#721c24',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= addslashes($_SESSION['success_message']) ?>',
            background: '#d4edda',
            color: '#155724',
            confirmButtonColor: '#28a745'
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
});
</script>
<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="<?= htmlspecialchars($user['profile_pic'] ?? 'images/profilepic.jpg') ?>" 
                         class="rounded-circle mb-3" 
                         width="150" 
                         height="150" 
                         alt="Profile Picture"
                         style="object-fit: cover;">
                    
                    <h4><?= htmlspecialchars($user['name']) ?></h4>
                    <p class="text-muted mb-1"><?= ucfirst(htmlspecialchars($user['role'])) ?></p>
                    
                    <form method="post" enctype="multipart/form-data" class="mt-3" id="profilePicForm">
                        <div class="mb-3">
                            <input type="file" name="profile_pic" id="profile_pic_input" class="form-control d-none"
                                   accept="image/*">
                            <label for="profile_pic_input" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-camera me-2"></i>Change Photo
                            </label>
                        </div>
                         <!-- Hidden submit button for profile picture, triggered by JS -->
                        <button type="submit" name="update_profile" id="submitProfilePic" style="display:none;">Update Picture</button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Account Settings</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="change-password.php" class="text-decoration-none">
                                <i class="fas fa-lock me-2"></i> Change Password
                            </a>
                        </li>
                        <?php if ($user['role'] === 'tutor'): ?>
                        <li class="list-group-item">
                            <a href="tutor-dashboard.php" class="text-decoration-none">
                                <i class="fas fa-chalkboard-teacher me-2"></i> Tutor Dashboard
                            </a>
                        </li>
                        <?php endif; ?>
                         <?php if ($user['role'] === 'admin'): ?>
                        <li class="list-group-item">
                            <a href="admin-dashboard.php" class="text-decoration-none">
                                <i class="fas fa-user-shield me-2"></i> Admin Dashboard
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item">
                            <a href="logout.php" class="text-danger text-decoration-none">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="language_preference" class="form-label">Preferred Language</label>
                                <select class="form-select" id="language_preference" name="language_preference">
                                    <option value="Luganda" <?= ($user['language_preference'] ?? '') === 'Luganda' ? 'selected' : '' ?>>Luganda</option>
                                    <option value="Runyankole" <?= ($user['language_preference'] ?? '') === 'Runyankole' ? 'selected' : '' ?>>Runyankole</option>
                                    <option value="Luo" <?= ($user['language_preference'] ?? '') === 'Luo' ? 'selected' : '' ?>>Luo</option>
                                    <option value="English" <?= ($user['language_preference'] ?? '') === 'English' ? 'selected' : '' ?>>English</option>
                                    <option value="Swahili" <?= ($user['language_preference'] ?? '') === 'Swahili' ? 'selected' : '' ?>>Swahili</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
            
            <?php if ($user['role'] === 'learner'): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">My Courses</h5>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $conn->prepare("
                        SELECT c.course_id, c.title, c.thumbnail_url, 
                               COALESCE(SUM(CASE WHEN lp.completed_at IS NOT NULL THEN 1 ELSE 0 END), 0) as completed_lessons,
                               (SELECT COUNT(*) FROM lessons WHERE course_id = c.course_id) as total_lessons
                        FROM subscriptions s
                        JOIN courses c ON s.course_id = c.course_id
                        LEFT JOIN lessons l ON c.course_id = l.course_id -- This join is for total lessons calculation if not done by subquery
                        LEFT JOIN lesson_progress lp ON l.lesson_id = lp.lesson_id AND lp.user_id = ?
                        WHERE s.user_id = ?
                        GROUP BY c.course_id, c.title, c.thumbnail_url
                    ");
                    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
                    $courses = $stmt->fetchAll();
                    ?>
                    
                    <?php if (empty($courses)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p>You haven't enrolled in any courses yet</p>
                            <a href="courses.php" class="btn btn-primary">Browse Courses</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($courses as $course):
                                $percentage = ($course['total_lessons'] > 0) ? round(($course['completed_lessons'] / $course['total_lessons']) * 100) : 0;
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="<?= htmlspecialchars($course['thumbnail_url'] ?? 'images/default_course.jpg') ?>"
                                         class="card-img-top" 
                                         style="height: 150px; object-fit: cover;"
                                         alt="<?= htmlspecialchars($course['title']) ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title"><?= htmlspecialchars($course['title']) ?></h6>
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: <?= $percentage ?>%"
                                                 aria-valuenow="<?= $percentage ?>"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted mb-auto">
                                            <?= $course['completed_lessons'] ?> of <?= $course['total_lessons'] ?> lessons completed (<?= $percentage ?>%)
                                        </small>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0">
                                        <a href="learn.php?course_id=<?= $course['course_id'] ?>"
                                           class="btn btn-sm btn-outline-primary w-100 mt-2">
                                            Continue Learning
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Preview profile picture before upload and auto-submit main form part
document.getElementById('profile_pic_input').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e_reader) {
            document.querySelector('.rounded-circle').src = e_reader.target.result;
        }
        reader.readAsDataURL(this.files[0]);
        // This will submit the entire form, including other changes
        // document.getElementById('profilePicForm').submit(); // This was submitting only the pic form
        // Instead, we can make it part of the main form or use AJAX
        // For now, let's assume the user will click "Save Changes" after selecting a new pic.
        // To auto-submit only the picture, a separate form or AJAX is needed.
        // The current setup has one "Save Changes" button for all profile info.
        // If we want the picture to save on selection, the form structure needs change or AJAX.
    }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>