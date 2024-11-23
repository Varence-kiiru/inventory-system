<?php
require_once 'config/config.php';
require_once 'includes/backup_manager.php';

$backupManager = new BackupManager($conn);

if ($_POST['action'] === 'backup_db') {
    $filename = $backupManager->createDatabaseBackup();
    echo json_encode(['success' => true, 'filename' => $filename]);
} elseif ($_POST['action'] === 'export_settings') {
    $filename = $backupManager->exportSettings();
    echo json_encode(['success' => true, 'filename' => $filename]);
}
