<?php
class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT user_id, full_name, email, role FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT user_id, full_name, email, role, status FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($data) {
        $stmt = $this->conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ]);
    }

    public function updateUser($id, $data) {
        $stmt = $this->conn->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE user_id = ?");
        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['role'],
            $id
        ]);
    }

    public function updateUserStatus($userId, $status) {
        $stmt = $this->conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
        $result = $stmt->execute([$status, $userId]);
        
        // Add error logging
        if (!$result) {
            error_log("Failed to update user status: " . print_r($stmt->errorInfo(), true));
        }
        return $result;
    }

    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt->execute([$id]);
    }
}
