<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../patterns/DatabaseSingleton.php';

echo "<h1>Database Connection Test</h1>";

// Test 1: Basic Connection
echo "<h2>1. Testing Basic Connection</h2>";
try {
    $db = DatabaseSingleton::getInstance();
    $conn = $db->getConnection();
    echo "<p style='color:green'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check Database Existence
echo "<h2>2. Checking Database</h2>";
try {
    $result = $conn->query("SELECT DATABASE()");
    $row = $result->fetch_row();
    $current_db = $row[0];
    echo "<p>Current database: " . $current_db . "</p>";
    
    if ($current_db === 'lost_and_found') {
        echo "<p style='color:green'>✓ Connected to correct database</p>";
    } else {
        echo "<p style='color:red'>✗ Wrong database selected</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error checking database: " . $e->getMessage() . "</p>";
}

// Test 3: Check Tables
echo "<h2>3. Checking Tables</h2>";
$tables = ['Students', 'Staff', 'Admin', 'LostItems', 'Claims', 'Notifications'];
foreach ($tables as $table) {
    try {
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
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

// Test 4: Test Transaction Support
echo "<h2>4. Testing Transaction Support</h2>";
try {
    $db->beginTransaction();
    echo "<p style='color:green'>✓ Transaction started successfully</p>";
    
    // Try a simple insert
    $test_email = "test_" . time() . "@ashesi.edu.gh";
    $stmt = $conn->prepare("INSERT INTO Students (first_name, last_name, email, school_id, password) VALUES (?, ?, ?, ?, ?)");
    $first_name = "Test";
    $last_name = "User";
    $school_id = "TEST" . time();
    $password = password_hash("test123", PASSWORD_DEFAULT);
    $stmt->bind_param("sssss", $first_name, $last_name, $test_email, $school_id, $password);
    
    if ($stmt->execute()) {
        echo "<p style='color:green'>✓ Test insert successful</p>";
    } else {
        echo "<p style='color:red'>✗ Test insert failed</p>";
    }
    
    // Rollback the transaction
    $db->rollback();
    echo "<p style='color:green'>✓ Transaction rolled back successfully</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Transaction test failed: " . $e->getMessage() . "</p>";
}

// Close connection
$conn->close();
?> 