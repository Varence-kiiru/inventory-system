<?php
session_start();
require_once '../config/config.php';
require_once '../includes/sales_manager.php';

$salesManager = new SalesManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = $_POST['sale_id'];
    $status = $_POST['status'];
    
    $result = $salesManager->updateSaleStatus($sale_id, $status);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}
