<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../db/config.php';
require_once '../services/NotificationService.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['notification_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit();
}

$notificationService = new NotificationService($conn);
$success = $notificationService->markAsRead($data['notification_id']);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read']);
} 