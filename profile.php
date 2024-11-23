<?php
session_start();
require_once 'config/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['user_id'];

// Get current user data with validation
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if user not found
if (!$user) {
    header('Location: logout.php');
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Create upload directory if it doesn't exist
    if (!file_exists('uploads/profile')) {
        mkdir('uploads/profile', 0777, true);
    }

    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $fileName = time() . '_' . str_replace(' ', '_', $_FILES['profile_photo']['name']);
        $uploadPath = 'uploads/profile/' . $fileName;
        
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadPath)) {
            $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
            $stmt->execute([$fileName, $userId]);
            $_SESSION['user']['profile_photo'] = $fileName;
        }
    }
    
    // Update user information
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->execute([$fullName, $email, $phone, $userId]);
    
    // Update session data
    $_SESSION['user']['full_name'] = $fullName;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['phone'] = $phone;
    
    header('Location: profile.php?success=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - The Olivian Group Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="<?php echo !empty($user['profile_photo']) ? 'uploads/profile/' . htmlspecialchars($user['profile_photo']) : 'assets/images/default-avatar.png'; ?>" 
                                 class="rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="mb-0"><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Edit Profile</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control" name="profile_photo" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" 
                                            value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" 
                                            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" 
                                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
