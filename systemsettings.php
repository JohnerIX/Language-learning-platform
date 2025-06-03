<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/admin-auth.php'; // Ensures admin is logged in

$pageTitle = "System Settings - Admin";
require __DIR__ . '/includes/header.php';

// In a real application, you would fetch current settings from a database here.
$current_settings = [
    'site_name' => 'Learn Lugha Platform',
    'site_email' => 'admin@learnlugha.com',
    'maintenance_mode' => false,
    'items_per_page' => 10
];

// Placeholder for handling form submission - no actual saving in this step
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid CSRF token.";
    } else {
        // Process and sanitize inputs (example)
        $current_settings['site_name'] = sanitize_input($_POST['site_name'] ?? $current_settings['site_name']);
        $current_settings['site_email'] = sanitize_input($_POST['site_email'] ?? $current_settings['site_email']);
        $current_settings['maintenance_mode'] = isset($_POST['maintenance_mode']);
        $current_settings['items_per_page'] = (int)($_POST['items_per_page'] ?? $current_settings['items_per_page']);

        // In a real app, save these to database or a config file.
        // For now, just show a success message.
        $_SESSION['success_message'] = "Settings updated (Placeholder - not actually saved).";
        // log_admin_activity("Updated system settings (placeholder)");

        // Redirect to prevent form resubmission
        header("Location: systemsettings.php");
        exit();
    }
    // If CSRF check failed and error message was set, redirect to show it
    if (isset($_SESSION['error_message'])) {
        header("Location: systemsettings.php");
        exit();
    }
}
?>

<div class="container-fluid py-4">
    <!-- SweetAlert Display Script (if not globally handled by header for all admin pages) -->
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">System Settings</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="systemsettings.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                   value="<?= htmlspecialchars($current_settings['site_name']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site_email" class="form-label">Default Admin Email</label>
                            <input type="email" class="form-control" id="site_email" name="site_email"
                                   value="<?= htmlspecialchars($current_settings['site_email']) ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="items_per_page" class="form-label">Items Per Page (for pagination)</label>
                            <input type="number" class="form-control" id="items_per_page" name="items_per_page"
                                   value="<?= htmlspecialchars($current_settings['items_per_page']) ?>" min="1">
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Maintenance Mode</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="maintenance_mode"
                                       name="maintenance_mode" <?= $current_settings['maintenance_mode'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                            </div>
                            <small class="form-text text-muted">If enabled, users will see a maintenance page.</small>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mt-4 mb-3">Email Settings (Placeholder)</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="smtp_host" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="e.g., smtp.example.com">
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                            <label for="smtp_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" placeholder="e.g., 587">
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="smtp_user" class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" id="smtp_user" name="smtp_user">
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                            <label for="smtp_pass" class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_pass" name="smtp_pass">
                        </div>
                    </div>
                </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings (Placeholder)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/includes/footer.php';
?>
