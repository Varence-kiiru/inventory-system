<?php
class BackupManager {
    private $conn;
    private $backupDir = 'backups/';

    public function __construct($db) {
        $this->conn = $db;
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    public function createDatabaseBackup() {
        $tables = [];
        $result = $this->conn->query('SHOW TABLES');
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- Database Backup " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $result = $this->conn->query("SELECT * FROM $table");
            $numColumns = $result->columnCount();

            $sql .= "DROP TABLE IF EXISTS $table;\n";
            
            $createTableSql = $this->conn->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_NUM);
            $sql .= $createTableSql[1] . ";\n\n";

            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $sql .= "INSERT INTO $table VALUES (";
                for ($i = 0; $i < $numColumns; $i++) {
                    $row[$i] = addslashes($row[$i]);
                    $row[$i] = str_replace("\n", "\\n", $row[$i]);
                    $sql .= ($i === 0) ? "'" . $row[$i] . "'" : ",'" . $row[$i] . "'";
                }
                $sql .= ");\n";
            }
            $sql .= "\n\n";
        }

        $backupFile = $this->backupDir . 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
        file_put_contents($backupFile, $sql);

        return basename($backupFile);
    }

    public function exportSettings() {
        $stmt = $this->conn->query("SELECT * FROM system_settings");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $exportFile = $this->backupDir . 'settings_export_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($exportFile, json_encode($settings, JSON_PRETTY_PRINT));
        
        return basename($exportFile);
    }
}
