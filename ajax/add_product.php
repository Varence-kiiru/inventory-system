<?php
require_once '../config/config.php';
require_once '../includes/inventory_manager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventoryManager = new InventoryManager($conn);
    $result = $inventoryManager->addProduct($_POST);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
}
