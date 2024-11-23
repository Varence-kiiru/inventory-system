<?php
session_start();
require_once 'config/config.php';
require_once 'includes/settings_manager.php';

$settingsManager = new SettingsManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settingsManager->updateSettings($_POST);
}

$settings = $settingsManager->getSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h2>System Settings</h2>
                    <p class="text-muted">Configure your inventory system preferences</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <!-- Company Information -->
                                <h5 class="mb-4">Company Information</h5>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="company_name" 
                                                value="<?php echo $settings['company_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" name="contact_email"
                                                value="<?php echo $settings['contact_email']; ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Logo -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Logo</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo $settings['company_logo'] ?? 'assets/images/default-logo.png'; ?>" 
                                                    alt="Company Logo" 
                                                    class="img-thumbnail" 
                                                    style="height: 60px; width: auto;">
                                                <input type="file" 
                                                    class="form-control" 
                                                    name="company_logo" 
                                                    accept="image/*">
                                            </div>
                                            <small class="text-muted">Recommended size: 200x60px, Max: 2MB</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- VAT Settings -->
                                <h5 class="mb-4">VAT Configuration</h5>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">VAT Rate (%)</label>
                                            <input type="number" step="0.01" class="form-control" name="vat_rate"
                                                   value="<?php echo $settings['vat_rate']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">VAT Number</label>
                                            <input type="text" class="form-control" name="vat_number"
                                                   value="<?php echo $settings['vat_number']; ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Settings -->
                                <h5 class="mb-4">Inventory Settings</h5>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Low Stock Alert Threshold</label>
                                            <input type="number" class="form-control" name="low_stock_threshold"
                                                   value="<?php echo $settings['low_stock_threshold']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default Currency</label>
                                            <select class="form-select" name="currency">
                                                <option value="KES" <?php echo $settings['currency'] === 'KES' ? 'selected' : ''; ?>>KES</option>
                                                <option value="USD" <?php echo $settings['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notification Settings -->
                                <h5 class="mb-4">Notification Preferences</h5>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="email_notifications"
                                                   <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Email Notifications</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="stock_alerts"
                                                   <?php echo $settings['stock_alerts'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Low Stock Alerts</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4">Quick Actions</h5>
                            <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" type="button" id="startBackup">
                                <i class="fas fa-database me-2"></i>Backup Database
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="exportSettings">
                                <i class="fas fa-file-export me-2"></i>Export Settings
                            </button>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4">System Information</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Version:</strong> 1.0.0
                                </li>
                                <li class="mb-2">
                                    <strong>Last Backup:</strong> <?php echo date('d M Y', strtotime($settings['last_backup'])); ?>
                                </li>
                                <li class="mb-2">
                                    <strong>PHP Version:</strong> <?php echo phpversion(); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Backup Modal -->
    <div class="modal fade" id="backupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Backup Database</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This will create a backup of your current database. The process may take a few minutes.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="startBackup">Start Backup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/settings.js"></script>
</body>
</html>
