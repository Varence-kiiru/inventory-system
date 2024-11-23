<?php

class Auth {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            if ($user['status'] === 'inactive') {
                return [
                    'success' => false,
                    'message' => 'Your account is inactive. Please contact the system administrator for reactivation.'
                ];
            }
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                return [
                    'success' => true,
                    'message' => 'Login successful'
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
}
