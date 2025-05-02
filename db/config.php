<?php
// Include the DatabaseSingleton class
require_once 'patterns/DatabaseSingleton.php'; // Ensure the path is correct

// Get the database connection instance
$db = DatabaseSingleton::getInstance();
$conn = $db->getConnection(); // This is your DB connection

// Test the connection (Optional, for debugging)
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// You can now use `$conn` for all database operations
?>
