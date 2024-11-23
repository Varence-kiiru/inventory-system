<?php
class DashboardManager {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDashboardMetrics() {
        $metrics = [
            'daily_sales' => $this->getDailySales(),
            'monthly_vat' => $this->getMonthlyVAT(),
            'total_products' => $this->getTotalProducts(),
            'pending_orders' => $this->getPendingOrders(),
            'low_stock_items' => $this->getLowStockCount(),
            'top_products' => $this->getTopProducts(),
            'recent_customers' => $this->getRecentCustomers(),
            'vat_summary' => $this->getVATSummary(),
            'sales_chart' => $this->getSalesChartData(),
            'products_chart' => $this->getProductsChartData(),
            'system_alerts' => $this->getSystemAlerts()
        ];

        // Recent transactions
        $transactionQuery = "SELECT 
            s.sale_id as id,
            c.name as customer_name,
            p.product_name,
            si.unit_price * si.quantity as amount,
            s.status
            FROM sales s
            JOIN customers c ON s.customer_id = c.customer_id
            JOIN sale_items si ON s.sale_id = si.sale_id
            JOIN products p ON si.product_id = p.product_id
            ORDER BY s.created_at DESC
            LIMIT 5";

        $stmt = $this->conn->prepare($transactionQuery);
        $stmt->execute();
        $metrics['recent_transactions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $metrics;
    }

    private function getDailySales() {
        $query = "SELECT COALESCE(SUM(total_amount), 0) as daily_sales 
                 FROM sales 
                 WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['daily_sales'];
    }

    private function getMonthlyVAT() {
        $query = "SELECT COALESCE(SUM(si.vat_amount), 0) as monthly_vat 
                 FROM sales s 
                 JOIN sale_items si ON s.sale_id = si.sale_id 
                 WHERE MONTH(s.created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(s.created_at) = YEAR(CURRENT_DATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['monthly_vat'];
    }

    private function getTotalProducts() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM products");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getPendingOrders() {
        $query = "SELECT COUNT(*) as pending_orders 
                 FROM sales 
                 WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_orders'];
    }

    private function getLowStockCount() {
        $query = "SELECT COUNT(*) as low_stock 
                 FROM products 
                 WHERE stock_quantity <= min_stock_level";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['low_stock'];
    }

    private function getTopProducts() {
        $query = "SELECT p.product_name, p.product_code,
                        COUNT(si.sale_item_id) as units_sold,
                        SUM(si.quantity * si.unit_price) as revenue
                 FROM products p
                 LEFT JOIN sale_items si ON p.product_id = si.product_id
                 GROUP BY p.product_id
                 ORDER BY revenue DESC
                 LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getRecentCustomers() {
        $query = "SELECT name, company, created_at 
                 FROM customers 
                 ORDER BY created_at DESC 
                 LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getVATSummary() {
        $query = "SELECT 
                    SUM(CASE WHEN p.is_vat_exempt = 0 THEN si.vat_amount ELSE 0 END) as collected,
                    SUM(CASE WHEN p.is_vat_exempt = 1 THEN (si.quantity * si.unit_price) ELSE 0 END) as exempt
                 FROM sale_items si
                 JOIN products p ON si.product_id = p.product_id
                 JOIN sales s ON si.sale_id = s.sale_id
                 WHERE DATE(s.created_at) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getSalesChartData() {
        $query = "SELECT DATE(created_at) as date, SUM(total_amount) as total
                 FROM sales
                 WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 GROUP BY DATE(created_at)
                 ORDER BY date";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_map(fn($row) => date('d M', strtotime($row['date'])), $data),
            'datasets' => [[
                'label' => 'Daily Sales',
                'data' => array_column($data, 'total'),
                'borderColor' => '#38b6ff',
                'backgroundColor' => 'rgba(56, 182, 255, 0.1)',
                'fill' => true
            ]]
        ];
    }

    private function getProductsChartData() {
        $query = "SELECT p.product_name, COUNT(si.product_id) as sales_count
                 FROM products p
                 LEFT JOIN sale_items si ON p.product_id = si.product_id
                 GROUP BY p.product_id
                 ORDER BY sales_count DESC
                 LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($data, 'product_name'),
            'datasets' => [[
                'data' => array_column($data, 'sales_count'),
                'backgroundColor' => ['#38b6ff', '#ff6b6b', '#4ecdc4', '#45b7af', '#96ceb4']
            ]]
        ];
    }

    private function getRecentTransactions() {
        $query = "SELECT s.sale_id, s.total_amount, s.created_at, c.name as customer_name
                 FROM sales s
                 LEFT JOIN customers c ON s.customer_id = c.customer_id
                 ORDER BY s.created_at DESC
                 LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

                public function getSystemAlerts() {
                $alerts = [];
    
                // Pending Payments Alert
                $pendingQuery = "SELECT s.sale_id, s.total_amount, c.name, s.created_at 
                                FROM sales s 
                                JOIN customers c ON s.customer_id = c.customer_id 
                                WHERE s.status = 'pending' 
                                ORDER BY s.created_at DESC";
                $stmt = $this->conn->prepare($pendingQuery);
                $stmt->execute();
                $pendingPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                foreach($pendingPayments as $payment) {
                    $alerts[] = [
                        'type' => 'warning',
                        'title' => 'Pending Payment',
                        'timestamp' => date('d M H:i', strtotime($payment['created_at'])),
                        'message' => "Payment pending from {$payment['name']} for KSH " . number_format($payment['total_amount'], 2)
                    ];
                }
    
                // Low Stock Alert
                $lowStockQuery = "SELECT product_name, stock_quantity 
                                  FROM products 
                                  WHERE stock_quantity <= 10"; // Using fixed threshold of 10 units
                $stmt = $this->conn->prepare($lowStockQuery);
                $stmt->execute();
                $lowStockItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                foreach($lowStockItems as $item) {
                    $alerts[] = [
                        'type' => 'danger',
                        'title' => 'Low Stock Alert',
                        'timestamp' => date('d M H:i'),
                        'message' => "{$item['product_name']} is running low ({$item['stock_quantity']} units remaining)"
                    ];
                }
    
                return $alerts;
}
}
