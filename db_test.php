<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once './db/database.php';
require_once './db/auth.php';

echo "<h1>Database Diagnostic Test</h1>";

// Test database connection
echo "<h2>Database Connection</h2>";
try {
    $conn = getDatabaseConnection();
    echo "<p style='color:green'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check if tables exist
echo "<h2>Table Structure</h2>";
$tables = ['Students', 'Staff', 'Admin', 'LostItems', 'Claims', 'Notifications'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "<p style='color:green'>✓ Table '$table' exists</p>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE $table");
        echo "<table border='1' style='margin-left: 20px; margin-bottom: 10px;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count rows
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $result->fetch_assoc()['count'];
        echo "<p style='margin-left: 20px;'>Row count: $count</p>";
    } else {
        echo "<p style='color:red'>✗ Table '$table' does not exist</p>";
    }
}

// Test user registration
echo "<h2>User Registration Test</h2>";
$testEmail = "test_" . time() . "@ashesi.edu.gh";
$testSchoolId = "TEST" . time();
$testPassword = "password123";

try {
    $result = registerStudent("Test", "User", $testEmail, $testSchoolId, $testPassword);
    if ($result['success']) {
        echo "<p style='color:green'>✓ Test user registration successful</p>";
        
        // Now try to login with this user
        echo "<h2>User Login Test</h2>";
        $loginResult = loginUser($testEmail, $testPassword);
        if ($loginResult['success']) {
            echo "<p style='color:green'>✓ Test user login successful</p>";
            echo "<pre>" . print_r($loginResult, true) . "</pre>";
            
            // Check session variables
            echo "<h2>Session Variables</h2>";
            echo "<pre>" . print_r($_SESSION, true) . "</pre>";
        } else {
            echo "<p style='color:red'>✗ Test user login failed: " . $loginResult['message'] . "</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Test user registration failed: " . $result['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Test user registration threw exception: " . $e->getMessage() . "</p>";
}

// Test email exists function
echo "<h2>Email Exists Function Test</h2>";
if (function_exists('emailExists')) {
    $exists = emailExists($testEmail);
    echo "<p>Test email exists: " . ($exists ? "Yes" : "No") . "</p>";
    
    $randomEmail = "nonexistent_" . time() . "@ashesi.edu.gh";
    $exists = emailExists($randomEmail);
    echo "<p>Random email exists: " . ($exists ? "Yes" : "No") . "</p>";
} else {
    echo "<p style='color:red'>✗ emailExists function not found</p>";
}
?> 