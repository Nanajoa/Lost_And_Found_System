<?php
// New usage with DatabaseSingleton

require_once '../patterns/DatabaseSingleton.php'; // Include the singleton class

// Get the singleton instance
$db = DatabaseSingleton::getInstance();

// Get the database connection
$conn = $db->getConnection();

// Use $conn in your queries as before
$query = "SELECT * FROM LostItems";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['name'] . "<br>";
    }
} else {
    echo "No items found.";
}
?>