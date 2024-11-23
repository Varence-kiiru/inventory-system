<?php
require_once '../config/config.php';
require_once '../includes/inventory_manager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventoryManager = new InventoryManager($conn);
    
    $productData = [
        'product_id' => $_POST['product_id'],
        'product_code' => $_POST['product_code'],
        'product_name' => $_POST['product_name'],
        'description' => $_POST['description'],
        'unit_price' => $_POST['unit_price'],
        'min_stock_level' => $_POST['min_stock_level'],
        'is_vat_exempt' => isset($_POST['is_vat_exempt']) ? 1 : 0
    ];
    
    $result = $inventoryManager->updateProduct($productData);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Product updated successfully' : 'Failed to update product'
    ]);
}
