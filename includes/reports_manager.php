<?php
class ReportsManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getSalesReport($startDate, $endDate) {
        $query = "SELECT 
                    COUNT(DISTINCT s.sale_id) as total_orders,
                    SUM(s.total_amount) as total_sales,
                    SUM(si.vat_amount) as total_vat,
                    AVG(s.total_amount) as average_order
                 FROM sales s
                 LEFT JOIN sale_items si ON s.sale_id = si.sale_id
                 WHERE s.created_at BETWEEN ? AND ?";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getProductReport($startDate, $endDate) {
        $query = "SELECT 
                    p.product_name,
                    p.stock_quantity as current_stock,
                    SUM(si.quantity) as units_sold,
                    SUM(si.quantity * si.unit_price + si.vat_amount) as revenue
                 FROM products p
                 LEFT JOIN sale_items si ON p.product_id = si.product_id
                 LEFT JOIN sales s ON si.sale_id = s.sale_id
                 WHERE s.created_at BETWEEN ? AND ?
                 GROUP BY p.product_id
                 ORDER BY revenue DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCustomerReport($startDate, $endDate) {
        $query = "SELECT 
                    c.name as customer_name,
                    COUNT(DISTINCT s.sale_id) as total_orders,
                    SUM(s.total_amount) as total_spent,
                    AVG(s.total_amount) as average_order
                 FROM customers c
                 LEFT JOIN sales s ON c.customer_id = s.customer_id
                 WHERE s.created_at BETWEEN ? AND ?
                 GROUP BY c.customer_id
                 ORDER BY total_spent DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDailySalesReport($startDate, $endDate) {
        $query = "SELECT 
                    DATE(created_at) as sale_date,
                    COUNT(DISTINCT sale_id) as orders,
                    SUM(total_amount) as revenue
                 FROM sales
                 WHERE created_at BETWEEN ? AND ?
                 GROUP BY DATE(created_at)
                 ORDER BY sale_date";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getInventoryReport() {
        $query = "SELECT 
                    p.product_name,
                    p.stock_quantity,
                    p.min_stock_level,
                    COUNT(si.sale_item_id) as times_sold,
                    SUM(si.quantity) as total_units_sold
                 FROM products p
                 LEFT JOIN sale_items si ON p.product_id = si.product_id
                 GROUP BY p.product_id
                 ORDER BY total_units_sold DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
