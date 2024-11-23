<?php
session_start();
require_once 'config/config.php';
require_once 'includes/settings_manager.php';

$settingsManager = new SettingsManager($conn);
$settings = $settingsManager->getSettings();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);
        
        // Send reset email
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;
        $to = $email;
        $subject = "Password Reset Request";
        $emailMessage = "Hello,\n\nYou have requested to reset your password. Click the link below to reset it:\n\n$resetLink\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
        
        mail($to, $subject, $emailMessage);
        
        $message = "Password reset instructions have been sent to your email.";
        $messageType = "success";
    } else {
        $message = "If this email exists in our system, you will receive reset instructions.";
        $messageType = "info";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo htmlspecialchars($settings['company_name']); ?></title>
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
                         alt="<?php echo htmlspecialchars($settings['company_name']); ?>"
                         class="login-logo">
                    <h4 class="text-dark mb-2">Forgot Password</h4>
                    <p class="text-muted">Enter your email to receive reset instructions</p>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Send Reset Link
                    </button>

                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Back to Login
                        </a>
                    </div>
                </form>

                <div class="footer">
                    © <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['company_name']); ?>. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
