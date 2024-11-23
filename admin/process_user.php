<?php
session_start();
require_once '../config/config.php';
require_once '../includes/user_manager.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userManager = new UserManager($conn);
    $response = ['success' => false, 'message' => ''];

    switch($_POST['action']) {
        case 'create':
            $result = $userManager->createUser([
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ]);
            $response = ['success' => $result, 'message' => 'User created successfully'];
            break;

        case 'update':
            $result = $userManager->updateUser($_POST['user_id'], [
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ]);
            $response = ['success' => $result, 'message' => 'User updated successfully'];
            break;

        case 'update_status':
            $result = $userManager->updateUserStatus($_POST['user_id'], $_POST['status']);
            $response = ['success' => $result, 'message' => 'User status updated successfully'];
            break;

        case 'delete':
            $result = $userManager->deleteUser($_POST['user_id']);
            $response = ['success' => $result, 'message' => 'User deleted successfully'];
            break;
    }

    echo json_encode($response);
    exit;
}
