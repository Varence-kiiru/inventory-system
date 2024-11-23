<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// At the top of navbar.php, get the settings
require_once 'includes/settings_manager.php';
require_once 'includes/notifications_manager.php';
$notificationsManager = new NotificationsManager($conn);
$unreadCount = $notificationsManager->getUnreadCount($_SESSION['user']['user_id']);
$unreadNotifications = $notificationsManager->getUnreadNotifications($_SESSION['user']['user_id']);
$settingsManager = new SettingsManager($conn);
$settings = $settingsManager->getSettings();
?>

<nav class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo $settings['company_logo'] ?? 'assets/images/default-logo.png'; ?>" 
             alt="Company Logo" 
             class="logo">
        <h5><?php echo $settings['company_name']; ?></h5>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-solar-panel"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="inventory.php" class="nav-link">
                <i class="fas fa-boxes"></i>
                <span>Inventory</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="sales.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                <span>Sales</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="customers.php" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="settings.php" class="nav-link">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</nav>

<header class="main-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-link sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="d-flex align-items-center">
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="admin/users.php">
                    <i class="fas fa-user-shield"></i> Admin Panel
                </a>
            </li>
        <?php endif; ?>
        <div class="dropdown me-3">
            <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="badge bg-danger"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                <h6 class="dropdown-header">Notifications</h6>
                <?php if ($unreadNotifications): ?>
                    <?php foreach ($unreadNotifications as $notification): ?>
                        <a href="#" class="dropdown-item notification-item" data-id="<?php echo $notification['notification_id']; ?>">
                            <div class="d-flex align-items-center">
                                <div class="notification-icon bg-<?php echo $notification['type']; ?> me-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="mb-1 font-weight-bold"><?php echo htmlspecialchars($notification['title']); ?></p>
                                    <small class="text-muted"><?php echo htmlspecialchars($notification['message']); ?></small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dropdown-item">No new notifications</div>
                <?php endif; ?>
                <div class="dropdown-divider"></div>
                <a href="notifications.php" class="dropdown-item text-center">View all notifications</a>
            </div>
        </div>
            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="dropdown-toggle d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                    <img src="<?php echo !empty($_SESSION['user']['profile_photo']) ? 'uploads/profile/' . $_SESSION['user']['profile_photo'] : 'assets/images/default-avatar.png'; ?>" 
                         class="rounded-circle me-2" 
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <span class="text-dark"><?php echo $_SESSION['user']['full_name']; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
