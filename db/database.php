<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // XAMPP default has empty password
define('DB_NAME', 'db');

/**
 * Get database connection
 * @return mysqli Database connection
 */
function getDatabaseConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

/**
 * Create lost_and_found database and tables if they don't exist
 */
function initializeDatabase() {
    // Connect to MySQL server without selecting a database
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating database: " . $conn->error;
        return;
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Create Students table
    $sql = "CREATE TABLE IF NOT EXISTS Students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        school_id VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating Students table: " . $conn->error;
        return;
    }
    
    // Create Staff table
    $sql = "CREATE TABLE IF NOT EXISTS Staff (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        faculty_id VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating Staff table: " . $conn->error;
        return;
    }
    
    // Create Admin table
    $sql = "CREATE TABLE IF NOT EXISTS Admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating Admin table: " . $conn->error;
        return;
    }
    
    // Create LostItems table
    $sql = "CREATE TABLE IF NOT EXISTS LostItems (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        date_lost DATE NOT NULL,
        location_seen_at VARCHAR(255),
        found_status ENUM('pending', 'resolved') DEFAULT 'pending',
        user_id INT NOT NULL,
        user_type ENUM('student', 'staff') NOT NULL,
        image LONGBLOB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id, user_type)
    )";
    
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating LostItems table: " . $conn->error;
        return;
    }
    
    // Create Claims table
    $sql = "CREATE TABLE IF NOT EXISTS Claims (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('student', 'staff') NOT NULL,
        lost_item_id INT NOT NULL,
        date_claimed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        FOREIGN KEY (lost_item_id) REFERENCES LostItems(id) ON DELETE CASCADE,
        INDEX (user_id, user_type)
    )";
    
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating Claims table: " . $conn->error;
        return;
    }
    
    // Create Notifications table
    $sql = "CREATE TABLE IF NOT EXISTS Notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('student', 'staff') NOT NULL,
        message TEXT NOT NULL,
        date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id, user_type)
    )";
    
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating Notifications table: " . $conn->error;
        return;
    }
    
    // Close connection
    $conn->close();
    
    return true;
}

// Initialize the database when this file is loaded
initializeDatabase();
?>