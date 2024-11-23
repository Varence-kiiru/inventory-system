<?php
session_start();

// Redirect to login if no user session exists
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/config.php';
require_once 'includes/notifications_manager.php';

// Get user ID from session
$userId = $_SESSION['user']['user_id'];

$notificationsManager = new NotificationsManager($conn);
$notifications = $notificationsManager->getAllNotifications($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">All Notifications</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?> p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="notification-icon bg-<?php echo $notification['type']; ?> me-3">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="notification-content">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                            <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted"><?php echo date('d M Y H:i', strtotime($notification['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.notification-item').click(function() {
                const notificationId = $(this).data('id');
                $.post('includes/mark_notification_read.php', { id: notificationId }, function() {
                    location.reload();
                });
            });
        });
    </script>
</body>
</html>
