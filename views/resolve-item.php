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
    // Start transaction
    $conn->begin_transaction();

    // Verify the item belongs to the current user and is claimed
    $stmt = $conn->prepare("
        SELECT id, name, description, date_lost, location_seen_at, image
        FROM LostItems 
        WHERE id = ? AND user_id = ? AND user_type = ? AND found_status = 'claimed'
    ");
    $stmt->bind_param("iis", $item_id, $user_id, $user_type);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    if (!$item) {
        throw new Exception("Item not found or not eligible for resolution");
    }

    // Insert into ReturnedItems table
    $stmt = $conn->prepare("
        INSERT INTO ReturnedItems (lost_item_id, returned_at)
        VALUES (?, NOW())
    ");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();

    // Update LostItems status to resolved
    $stmt = $conn->prepare("
        UPDATE LostItems 
        SET found_status = 'resolved' 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Redirect back to reports page with success message
    header('Location: reports.php?success=1');
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    header('Location: reports.php?error=' . urlencode($e->getMessage()));
    exit;
}
?> 