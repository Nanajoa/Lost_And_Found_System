<?php
// Database configuration
$host = 'localhost';       // XAMPP default host
$db   = 'lost_and_found';  // Replace with your actual database name
$user = 'root';            // XAMPP default user
$pass = '';                // XAMPP default has no password (unless you set one)
$charset = 'utf8mb4';      // Character set

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options for PDO (PHP Data Objects)
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

// Attempt connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Database connection successful!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>