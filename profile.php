<?php
require_once __DIR__ . '/includes/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$pageTitle = "My Profile";
require __DIR__ . '/includes/header.php';

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

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
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
                $stmt->execute(['/uploads/profile_pics/' . $fileName, $_SESSION['user_id']]);
                $_SESSION['profile_pic'] = '/uploads/profile_pics/' . $fileName;
            }
        }

        $_SESSION['success_message'] = 'Profile updated successfully!';
        redirect('profile.php');
    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="<?= htmlspecialchars($user['profile_pic'] ?? 'images/profilepic.jpg') ?>" 
                         class="rounded-circle mb-3" 
                         width="150" 
                         height="150" 
                         alt="Profile Picture">
                    
                    <h4><?= htmlspecialchars($user['name']) ?></h4>
                    <p class="text-muted mb-1"><?= ucfirst($user['role']) ?></p>
                    
                    <form method="post" enctype="multipart/form-data" class="mt-3">
                        <div class="mb-3">
                            <input type="file" name="profile_pic" id="profile_pic" class="form-control d-none"
                                   accept="image/*">
                            <label for="profile_pic" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-camera me-2"></i>Change Photo
                            </label>
                        </div>
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
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
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
                                       value="<?= htmlspecialchars($user['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="language_preference" class="form-label">Preferred Language</label>
                                <select class="form-select" id="language_preference" name="language_preference">
                                    <option value="Luganda" <?= ($user['language_preference'] ?? '') === 'Luganda' ? 'selected' : '' ?>>Luganda</option>
                                    <option value="Runyankole" <?= ($user['language_preference'] ?? '') === 'Runyankole' ? 'selected' : '' ?>>Runyankole</option>
                                    <option value="Luo" <?= ($user['language_preference'] ?? '') === 'Luo' ? 'selected' : '' ?>>Luo</option>
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
                               COUNT(up.lesson_id) as completed_lessons,
                               COUNT(l.lesson_id) as total_lessons
                        FROM enrollments e
                        JOIN courses c ON e.course_id = c.course_id
                        LEFT JOIN lessons l ON c.course_id = l.course_id
                        LEFT JOIN user_progress up ON up.lesson_id = l.lesson_id AND up.user_id = ?
                        WHERE e.user_id = ?
                        GROUP BY c.course_id
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
                            <?php foreach ($courses as $course): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <img src="<?= htmlspecialchars($course['thumbnail_url']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($course['title']) ?>">
                                    <div class="card-body">
                                        <h6><?= htmlspecialchars($course['title']) ?></h6>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" 
                                                 style="width: <?= ($course['total_lessons'] > 0) ? round(($course['completed_lessons']/$course['total_lessons'])*100) : 0 ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= $course['completed_lessons'] ?> of <?= $course['total_lessons'] ?> lessons completed
                                        </small>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="learn.php?id=<?= $course['course_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary w-100">
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
// Preview profile picture before upload
document.getElementById('profile_pic').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.rounded-circle').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
        this.form.submit();
    }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>