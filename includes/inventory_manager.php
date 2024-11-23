<?php
class InventoryManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAllProducts() {
        $query = "SELECT 
            product_id,
            product_code,
            product_name,
            description,
            unit_price,
            stock_quantity,
            min_stock_level,
            is_vat_exempt
        FROM products 
        ORDER BY product_name";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProductById($product_id) {
        $query = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateProduct($productData) {
        $query = "UPDATE products SET 
                  product_code = ?,
                  product_name = ?,
                  description = ?,
                  unit_price = ?,
                  min_stock_level = ?,
                  is_vat_exempt = ?
                  WHERE product_id = ?";
                  
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $productData['product_code'],
            $productData['product_name'],
            $productData['description'],
            $productData['unit_price'],
            $productData['min_stock_level'],
            $productData['is_vat_exempt'],
            $productData['product_id']
        ]);
    }
    
    public function addStock($stockData) {
        try {
            $this->conn->beginTransaction();
            
            // Update product stock
            $updateQuery = "UPDATE products 
                           SET stock_quantity = stock_quantity + ? 
                           WHERE product_id = ?";
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->execute([
                $stockData['quantity'],
                $stockData['product_id']
            ]);
            
            // Record stock transaction
            $transactionQuery = "INSERT INTO stock_transactions 
                               (product_id, quantity, transaction_type, notes) 
                               VALUES (?, ?, 'add', ?)";
            $stmt = $this->conn->prepare($transactionQuery);
            $stmt->execute([
                $stockData['product_id'],
                $stockData['quantity'],
                $stockData['notes']
            ]);
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
       
    public function getSettings() {
        $query = "SELECT * FROM system_settings LIMIT 1";
        $stmt = $this->conn->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getProductHistory($productId) {
        $query = "SELECT * FROM stock_transactions 
                  WHERE product_id = ? 
                  ORDER BY transaction_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($data) {
        $query = "INSERT INTO products (product_code, product_name, description, unit_price, stock_quantity, min_stock_level, is_vat_exempt) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
                  
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['product_code'],
            $data['product_name'],
            $data['description'],
            $data['unit_price'],
            $data['stock_quantity'],
            $data['min_stock_level'],
            isset($data['is_vat_exempt']) ? 1 : 0
        ]);
    }
 
    public function calculatePriceWithVAT($price, $vatRate) {
        return $price * (1 + ($vatRate / 100));
    }
}
