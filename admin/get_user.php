<?php
require_once '../config/config.php';
require_once '../includes/user_manager.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $userManager = new UserManager($conn);
    $user = $userManager->getUserById($_GET['id']);
    echo json_encode($user);
}
