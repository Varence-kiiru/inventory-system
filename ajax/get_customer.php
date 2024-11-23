<?php
require_once '../config/config.php';
require_once '../includes/customer_manager.php';

if (isset($_GET['customer_id'])) {
    $customerManager = new CustomerManager($conn);
    $customer = $customerManager->getCustomer($_GET['customer_id']);
    
    header('Content-Type: application/json');
    echo json_encode($customer);
}
