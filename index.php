<?php
session_start();
require_once 'config/config.php';

// Redirect to dashboard if logged in
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/auth.php';
    $auth = new Auth($conn);
    
    $result = $auth->login($_POST['email'], $_POST['password']);
    if ($result['success']) {
        header('Location: dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The Olivian Group Limited</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 200px;">
                            <h4>Welcome Back</h4>
                            <p class="text-muted">Sign in to continue to Inventory System</p>
                        </div>
                        
                        <?php if (isset($result) && !$result['success']): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($result['message']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
