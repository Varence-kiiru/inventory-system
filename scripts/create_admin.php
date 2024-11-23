<?php
require_once '../config/config.php';

$adminData = [
    'full_name' => 'System Administrator',
    'email' => 'admin@oliviangroup.com',
    'password' => password_hash('password', PASSWORD_DEFAULT),
    'role' => 'admin'
];

$stmt = $conn->prepare("
    INSERT INTO users (full_name, email, password, role, created_at) 
    VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
");

$stmt->execute([
    $adminData['full_name'],
    $adminData['email'],
    $adminData['password'],
    $adminData['role']
]);

echo "Initial admin user created successfully!\n";
echo "Email: admin@oliviangroup.com\n";
echo "Password: password\n";
