<?php
require_once __DIR__ . '/database.php';

try {
    $conn = getDatabaseConnection();
    
    // Update LostItems table
    $sql = "ALTER TABLE LostItems MODIFY COLUMN found_status ENUM('pending', 'claimed', 'resolved') DEFAULT 'pending'";
    $conn->query($sql);
    
    echo "Database updated successfully!";
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 