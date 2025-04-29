<?php
// Check PHP MySQL Configuration
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL Extension Loaded: " . (extension_loaded('mysqli') ? 'Yes' : 'No') . "<br>";

// Detailed MySQL Connection Diagnostics
$servername = 'localhost';  
$username = 'root';           
$password = '';
$dbname = 'lost_and_found';   // ← this must match your phpMyAdmin DB name              

try {
    // Test basic connection without specifying database
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_errno) {
        throw new Exception("Basic Connection failed: " . $conn->connect_error);
    }

    // List available databases
    $result = $conn->query("SHOW DATABASES");
    echo "Available Databases:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Database'] . "<br>";
    }

    $conn->close();
} catch (Exception $e) {
    die("Diagnostic Error: " . $e->getMessage());
}