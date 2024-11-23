<?php
require_once '../config/config.php';
require_once '../includes/inventory_manager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventoryManager = new InventoryManager($conn);
    
    $stockData = [
        'product_id' => $_POST['product_id'],
        'quantity' => $_POST['quantity'],
        'notes' => $_POST['notes']
    ];
    
    $result = $inventoryManager->addStock($stockData);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}
