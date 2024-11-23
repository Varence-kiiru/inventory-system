<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth($conn);
    $result = $auth->login($_POST['email'], $_POST['password']);
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
    } else {
        echo json_encode(['success' => false, 'message' => $result['message']]);
    }
}
