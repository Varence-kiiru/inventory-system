<?php
class NotificationsManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUnreadCount($userId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function getUnreadNotifications($userId, $limit = 5) {
        $stmt = $this->conn->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT " . (int)$limit
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllNotifications($userId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($notificationId) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
        return $stmt->execute([$notificationId]);
    }

    public function createNotification($userId, $title, $message, $type = 'info') {
        $stmt = $this->conn->prepare("
            INSERT INTO notifications (user_id, title, message, type) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $title, $message, $type]);
    }

    public function createStockNotification($product_name, $current_stock, $type) {
        $users = $this->getAdminUsers();
        
        $messages = [
            'minimum' => "Product '{$product_name}' has reached minimum stock level ({$current_stock} units remaining)",
            'exhausted' => "Product '{$product_name}' is out of stock",
            'restocked' => "Product '{$product_name}' has been restocked ({$current_stock} units available)"
        ];
        
        $titles = [
            'minimum' => 'Low Stock Alert',
            'exhausted' => 'Out of Stock Alert',
            'restocked' => 'Restock Notification'
        ];
        
        foreach ($users as $user) {
            $this->createNotification(
                $user['user_id'],
                $titles[$type],
                $messages[$type],
                $type === 'restocked' ? 'success' : 'warning'
            );
        }
    }

    private function getAdminUsers() {
        $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE role IN ('admin', 'manager')");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }}
