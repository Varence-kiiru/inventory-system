<?php
require_once '../config/config.php';
require_once '../includes/customer_manager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerManager = new CustomerManager($conn);
    $result = $customerManager->updateCustomer($_POST);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}
