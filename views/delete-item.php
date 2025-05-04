<?php
require_once __DIR__ . '/../db/database.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if item_id is provided
if (!isset($_POST['item_id'])) {
    header('Location: reports.php');
    exit;
}

$item_id = (int)$_POST['item_id'];
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$conn = getDatabaseConnection();

try {
    // Verify the item belongs to the current user
    $stmt = $conn->prepare("
        SELECT id 
        FROM LostItems 
        WHERE id = ? AND user_id = ? AND user_type = ?
    ");
    $stmt->bind_param("iis", $item_id, $user_id, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Item not found or you don't have permission to delete it");
    }

    // Delete the item
    $stmt = $conn->prepare("DELETE FROM LostItems WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();

    // Redirect back to reports page with success message
    header('Location: reports.php?success=2');
    exit;

} catch (Exception $e) {
    header('Location: reports.php?error=' . urlencode($e->getMessage()));
    exit;
}
?> 