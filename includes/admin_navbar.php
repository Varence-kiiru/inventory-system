<?php
$stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'company_logo'");
$stmt->execute();
$logo = $stmt->fetchColumn();
?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
            <img src="<?php echo !empty($logo) ? '../' . $logo : '../assets/images/logo.png'; ?>" alt="Logo" height="30" class="me-2">
            <i class="fas fa-shield-alt"></i> Admin Panel
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
