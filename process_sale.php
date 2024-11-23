<?php
header('Content-Type: application/json');
session_start();
require_once 'config/config.php';
require_once 'includes/sales_manager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $salesManager = new SalesManager($conn);
        
        $sale_id = $salesManager->createSale(
            $_POST['customer_id'],
            $_POST['products'],
            $_POST['quantities'],
            $_POST['payment_method'],
            $_POST['payment_status']
        );

        echo json_encode([
            'success' => true,
            'sale_id' => $sale_id,
            'message' => 'Sale completed successfully'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
