<?php
session_start();
require_once '../config/config.php';

// Check admin authorization
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get current settings
$stmt = $conn->prepare("SELECT setting_key, setting_value FROM system_settings");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">System Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="settingsForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">VAT Number</label>
                                <input type="text" name="vat_number" class="form-control"
                                       value="<?php echo htmlspecialchars($settings['vat_number'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control"
                                       value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Company Logo</label>
                                <?php if (!empty($settings['company_logo'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo htmlspecialchars($settings['company_logo']); ?>" 
                                             alt="Current Logo" style="max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="company_logo" class="form-control" accept="image/*">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Default VAT Rate (%)</label>
                                <input type="number" name="vat_rate" class="form-control" step="0.01"
                                       value="<?php echo htmlspecialchars($settings['vat_rate'] ?? '16'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Invoice Footer Text</label>
                                <textarea name="invoice_footer" class="form-control" rows="3"><?php 
                                    echo htmlspecialchars($settings['invoice_footer'] ?? ''); 
                                ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'process_settings.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Settings saved successfully');
                        location.reload();
                    }
                }
            });
        });
    </script>
</body>
</html>
