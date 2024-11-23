<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'includes/settings_manager.php';

$auth = new Auth($conn);
$settingsManager = new SettingsManager($conn);
$settings = $settingsManager->getSettings();

// Redirect if already logged in
if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['status'] === 'inactive') {
            $error = 'Your account is inactive. Please contact the system administrator for reactivation.';
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'full_name' => htmlspecialchars($user['full_name']),
                'email' => htmlspecialchars($user['email']),
                'profile_photo' => htmlspecialchars($user['profile_photo']),
                'role' => $user['role']
            ];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password';
            sleep(1); // Delay to slow brute force attempts
        }
    } else {
        $error = 'Invalid email or password';
        sleep(1);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .login-logo {
            max-height: 100px;
            width: auto;
            margin-bottom: 1.5rem;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            margin: auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            text-align: center;
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="login-container">
        <div class="container">
            <div class="login-card bg-white">
                <div class="text-center mb-4">
                    <img src="<?php echo htmlspecialchars($settings['company_logo'] ?? 'assets/images/default-logo.png'); ?>" 
                         alt="The Olivian Group" 
                         class="login-logo">
                    <h4 class="text-dark mb-2">Welcome Back!</h4>
                    <p class="text-muted">Sign in to continue to Dashboard</p>
                </div>
                
                <form method="POST">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>

                    <div class="text-center">
                        <a href="forgot-password.php" class="text-decoration-none">
                            <i class="fas fa-key me-1"></i>Forgot Password?
                        </a>
                    </div>
                </form>

                <div class="footer">
                    &copy; <?php echo date('Y'); ?> The Olivian Group Limited. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>

