<?php
session_start();
require_once '../config/config.php';
require_once '../includes/notifications_manager.php';

$notificationsManager = new NotificationsManager($conn);
$count = $notificationsManager->getUnreadCount($_SESSION['user']['user_id']);

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
