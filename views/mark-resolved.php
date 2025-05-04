<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../db/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_id'])) {
    $conn = getDatabaseConnection();
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Get claim details
        $stmt = $conn->prepare("
            SELECT c.lost_item_id, c.user_id as claimer_id, li.user_id as owner_id, li.user_type
            FROM Claims c
            JOIN LostItems li ON c.lost_item_id = li.id
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $_POST['claim_id']);
        $stmt->execute();
        $claim = $stmt->get_result()->fetch_assoc();
        
        if (!$claim) {
            throw new Exception("Claim not found");
        }
        
        // Update claim status
        $stmt = $conn->prepare("UPDATE Claims SET status = 'resolved' WHERE id = ?");
        $stmt->bind_param("i", $_POST['claim_id']);
        $stmt->execute();
        
        // Update lost item status
        $stmt = $conn->prepare("UPDATE LostItems SET found_status = 'resolved' WHERE id = ?");
        $stmt->bind_param("i", $claim['lost_item_id']);
        $stmt->execute();
        
        // Create notification for claimer
        $message = "Your claim for item #{$claim['lost_item_id']} has been marked as resolved.";
        $stmt = $conn->prepare("
            INSERT INTO Notifications (user_id, user_type, message, date_sent)
            VALUES (?, 'student', ?, NOW())
        ");
        $stmt->bind_param("is", $claim['claimer_id'], $message);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Redirect back to notifications
        header('Location: notifications.php?success=1');
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header('Location: notifications.php?error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: notifications.php');
    exit;
} 