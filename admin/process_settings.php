<?php
session_start();
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create required directories
    $directories = [
        '../assets',
        '../assets/uploads'
    ];

    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    $settings = [
        'company_name' => $_POST['company_name'],
        'vat_number' => $_POST['vat_number'],
        'contact_email' => $_POST['contact_email'],
        'vat_rate' => $_POST['vat_rate'],
        'invoice_footer' => $_POST['invoice_footer']
    ];

    // Handle logo upload with directory creation
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === 0) {
        $uploadDir = '../assets/uploads/';
        $fileName = 'logo_' . time() . '.' . pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $filePath)) {
            $settings['company_logo'] = 'assets/uploads/' . $fileName;
        }
    }

    // Save settings to database
    foreach ($settings as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) 
                              VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }

    echo json_encode(['success' => true]);
}
