<?php
require_once __DIR__ . '/../../db/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Check if user is logged in and is an admin
requireAdmin();

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'] ?? null;
    $status = $_POST['status'] ?? null;
    
    if ($report_id && $status && in_array($status, ['pending', 'resolved'])) {
        try {
            $conn = getDatabaseConnection();
            $stmt = $conn->prepare("UPDATE LostItems SET found_status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $report_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 