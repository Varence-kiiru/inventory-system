<?php
class CustomerManager {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAllCustomers() {
        $query = "SELECT * FROM customers ORDER BY name";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCustomer($customerId) {
        $query = "SELECT * FROM customers WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$customerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addCustomer($customerData) {
        $query = "INSERT INTO customers (name, company, email, phone, address, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, 'active', NOW())";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $customerData['name'],
            $customerData['company'],
            $customerData['email'],
            $customerData['phone'],
            $customerData['address']
        ]);
    }
    
    public function updateCustomer($customerData) {
        $query = "UPDATE customers 
                 SET name = ?, 
                     company = ?, 
                     email = ?, 
                     phone = ?, 
                     address = ?, 
                     status = ? 
                 WHERE customer_id = ?";
                 
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $customerData['name'],
            $customerData['company'],
            $customerData['email'],
            $customerData['phone'],
            $customerData['address'],
            $customerData['status'],
            $customerData['customer_id']
        ]);
    }
    
    public function getCustomerHistory($customerId) {
        $query = "SELECT s.*, COUNT(si.sale_item_id) as total_items 
                 FROM sales s 
                 LEFT JOIN sale_items si ON s.sale_id = si.sale_id 
                 WHERE s.customer_id = ? 
                 GROUP BY s.sale_id 
                 ORDER BY s.created_at DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
