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
    header('Location: item-details.php?id=' . $_POST['item_id']);
    exit;
}

$item_id = (int)$_POST['item_id'];
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$conn = getDatabaseConnection();

try {
    // Start transaction
    $conn->begin_transaction();

    // First, check if the item exists and is claimed
    $stmt = $conn->prepare("
        SELECT id, found_status 
        FROM LostItems 
        WHERE id = ? AND found_status = 'claimed'
    ");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Item not found or not claimed");
    }

    // Then, check if the current user has a notification for this item
    $stmt = $conn->prepare("
        SELECT id 
        FROM Notifications 
        WHERE lost_item_id = ? AND user_id = ? AND user_type = ?
    ");
    $stmt->bind_param("iis", $item_id, $user_id, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("You are not the one who claimed this item");
    }

    // Delete the notification
    $stmt = $conn->prepare("
        DELETE FROM Notifications 
        WHERE lost_item_id = ? AND user_id = ? AND user_type = ?
    ");
    $stmt->bind_param("iis", $item_id, $user_id, $user_type);
    $stmt->execute();

    // Update the item status back to pending
    $stmt = $conn->prepare("
        UPDATE LostItems 
        SET found_status = 'pending' 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Redirect back to item details with success message
    header('Location: item-details.php?id=' . $item_id . '&success=unclaimed');
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    header('Location: item-details.php?id=' . $item_id . '&error=' . urlencode($e->getMessage()));
    exit;
}
?> 