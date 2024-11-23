<?php

class SalesManager {
    private $conn;
    private $vat_rate = 0.16;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Sale Retrieval Methods
    public function getAllSales() {
        $query = "SELECT s.sale_id, s.created_at, s.total_amount, s.status,
                         c.name as customer_name, COUNT(si.sale_item_id) as item_count,
                         COALESCE(SUM(si.vat_amount), 0) as total_vat
                  FROM sales s
                  LEFT JOIN customers c ON s.customer_id = c.customer_id
                  LEFT JOIN sale_items si ON s.sale_id = si.sale_id
                  GROUP BY s.sale_id, s.created_at, s.total_amount, s.status, c.name
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSaleById($sale_id) {
        $query = "SELECT s.*, c.name as customer_name, c.company, c.email, c.phone, c.address
                  FROM sales s
                  JOIN customers c ON s.customer_id = c.customer_id
                  WHERE s.sale_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$sale_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSaleDetails($saleId) {
        $query = "SELECT s.*, c.name as customer_name, c.phone, s.status, s.payment_method
                  FROM sales s
                  JOIN customers c ON s.customer_id = c.customer_id
                  WHERE s.sale_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($sale) {
            $sale['items'] = $this->getSaleItems($saleId);
            return $sale;
        }
        return null;
    }

    public function getSaleItems($saleId) {
        $query = "SELECT si.*, p.product_name
                  FROM sale_items si
                  JOIN products p ON si.product_id = p.product_id
                  WHERE si.sale_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$saleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Related Data Methods
    public function getCustomers() {
        $query = "SELECT customer_id, name, company, email, phone
                 FROM customers
                 WHERE status = 'active'
                 ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProducts() {
        $query = "SELECT * FROM products
                 WHERE stock_quantity > 0
                 ORDER BY product_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Sale Management Methods
    public function createSale($customer_id, $products, $quantities, $payment_method, $status) {
        try {
            $this->conn->beginTransaction();
            
            // Check stock availability before processing
            foreach ($products as $key => $product_id) {
                $quantity = $quantities[$key];
                
                $stockQuery = "SELECT stock_quantity FROM products WHERE product_id = ?";
                $stmt = $this->conn->prepare($stockQuery);
                $stmt->execute([$product_id]);
                $currentStock = $stmt->fetchColumn();
                
                if ($currentStock < $quantity || $currentStock == 0) {
                    throw new Exception("Insufficient stock for selected product(s)");
                }
            }
            
            // Initial sales record creation with status
            $query = "INSERT INTO sales (
                customer_id,
                total_amount,
                payment_method,
                status,
                created_at
            ) VALUES (
                :customer_id,
                0,
                :payment_method,
                :status,
                NOW()
            )";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'customer_id' => $customer_id,
                'payment_method' => $payment_method,
                'status' => $status
            ]);

            $sale_id = $this->conn->lastInsertId();
            $total_amount = 0;

            foreach ($products as $key => $product_id) {
                $quantity = $quantities[$key];
                
                $productQuery = "SELECT * FROM products WHERE product_id = ?";
                $stmt = $this->conn->prepare($productQuery);
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                $unit_price_incl_vat = $product['unit_price'];
                $unit_price = $product['is_vat_exempt'] ?
                           $unit_price_incl_vat :
                           $unit_price_incl_vat / (1 + $this->vat_rate);
                
                $line_total = $unit_price * $quantity;
                $vat_amount = $product['is_vat_exempt'] ? 0 : ($line_total * $this->vat_rate);

                $itemQuery = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, vat_amount)
                             VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($itemQuery);
                $stmt->execute([$sale_id, $product_id, $quantity, $unit_price, $vat_amount]);

                $updateStockQuery = "UPDATE products
                                   SET stock_quantity = stock_quantity - ?
                                   WHERE product_id = ?";
                $stmt = $this->conn->prepare($updateStockQuery);
                $stmt->execute([$quantity, $product_id]);

                $total_amount += $line_total + $vat_amount;
            }

            $updateSaleQuery = "UPDATE sales SET total_amount = ? WHERE sale_id = ?";
            $stmt = $this->conn->prepare($updateSaleQuery);
            $stmt->execute([$total_amount, $sale_id]);

            $this->conn->commit();
            return $sale_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function completeSale($sale_data) {
        try {
            $this->conn->beginTransaction();
            
            $sale_id = $this->createSale(
                $sale_data['customer_id'],
                $sale_data['products'],
                $sale_data['quantities'],
                $sale_data['payment_method'],
                $sale_data['status']
            );

            $this->conn->commit();

            return [
                'success' => true,
                'redirect' => $sale_data['status'] === 'pending' ? 'sales.php' : 'sale_invoice.php?id=' . $sale_id
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateSaleStatus($sale_id, $new_status) {
        if ($new_status === 'paid') {
            $query = "UPDATE sales SET status = 'paid' WHERE sale_id = ? AND status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$sale_id]);
            return $stmt->rowCount() > 0;
        }
        return false;
    }
}
