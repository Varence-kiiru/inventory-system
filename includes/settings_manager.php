<?php
class SettingsManager {
    private $conn;
    private $default_settings = [
        'company_name' => 'The Olivian Group Limited',
        'contact_email' => 'info@oliviangroup.com',
        'vat_rate' => '16.00',
        'vat_number' => 'VAT123456789',
        'low_stock_threshold' => '10',
        'currency' => 'KES',
        'email_notifications' => true,
        'stock_alerts' => true,
        'last_backup' => null,
        'company_logo' => 'assets/images/default-logo.png',
    ];

    public function __construct($db) {
        $this->conn = $db;
        $this->initializeSettings();
    }

    private function initializeSettings() {
        $stmt = $this->conn->prepare("
            CREATE TABLE IF NOT EXISTS system_settings (
                setting_key VARCHAR(50) PRIMARY KEY,
                setting_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        $stmt->execute();

        // Insert default settings if they don't exist
        foreach ($this->default_settings as $key => $value) {
            $stmt = $this->conn->prepare("
                INSERT IGNORE INTO system_settings (setting_key, setting_value)
                VALUES (?, ?)
            ");
            $stmt->execute([$key, $value]);
        }
    }

    public function getSettings() {
        $settings = [];
        $stmt = $this->conn->query("SELECT setting_key, setting_value FROM system_settings");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return array_merge($this->default_settings, $settings);
    }

    public function updateSettings($data) {
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === 0) {
            // Create absolute path to uploads directory
            $uploadDir = dirname(dirname(__FILE__)) . '/assets/images/uploads/';
            
            // Ensure directory exists and is writable
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
            $fileName = 'company_logo_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            // Store relative path in database
            $dbPath = 'assets/images/uploads/' . $fileName;
            
            if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $targetPath)) {
                $data['company_logo'] = $dbPath;
            }
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->default_settings)) {
                $stmt = $this->conn->prepare("
                    UPDATE system_settings 
                    SET setting_value = ? 
                    WHERE setting_key = ?
                ");
                $stmt->execute([$value, $key]);
            }
        }
        return true;
    }
}
