<?php
session_start();
require_once './config/config.php';
require_once 'inventory_manager.php';

class InventoryHandler {
    private $conn;
    private $inventoryManager;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->inventoryManager = new InventoryManager($conn);
    }

    // Product Management Methods
    public function handleProductAddition() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productData = [
                'product_code' => $_POST['product_code'],
                'product_name' => $_POST['product_name'],
                'description' => $_POST['description'],
                'unit_price' => $_POST['unit_price'],
                'stock_quantity' => $_POST['stock_quantity'],
                'min_stock_level' => $_POST['min_stock_level'],
                'is_vat_exempt' => isset($_POST['is_vat_exempt']) ? 1 : 0
            ];

            $result = $this->inventoryManager->addProduct($productData);
            return [
                'success' => $result,
                'message' => $result ? 'Product added successfully' : 'Failed to add product'
            ];
        }
    }

    // Stock Management Methods
    public function updateStock($product_id, $new_quantity) {
        $this->handleStockNotifications($product_id, $new_quantity);
        
        $stmt = $this->conn->prepare("UPDATE products SET stock_quantity = ? WHERE product_id = ?");
        return $stmt->execute([$new_quantity, $product_id]);
    }

    // Notification Methods
    private function handleStockNotifications($product_id, $new_quantity) {
        $product = $this->inventoryManager->getProductById($product_id);
        $notificationsManager = new NotificationsManager($this->conn);
        
        if ($new_quantity <= $product['minimum_stock'] && $new_quantity > 0) {
            $notificationsManager->createStockNotification(
                $product['product_name'], 
                $new_quantity, 
                'minimum'
            );
        }
        
        if ($new_quantity == 0) {
            $notificationsManager->createStockNotification(
                $product['product_name'], 
                0, 
                'exhausted'
            );
        }
        
        if ($new_quantity > $product['stock_quantity'] && $product['stock_quantity'] <= $product['minimum_stock']) {
            $notificationsManager->createStockNotification(
                $product['product_name'], 
                $new_quantity, 
                'restocked'
            );
        }
    }
}

// Initialize and handle requests
$inventoryHandler = new InventoryHandler($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = $inventoryHandler->handleProductAddition();
    echo json_encode($response);
}
