<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
// $servername = 'localhost';  
// $username = 'papa.badu';           
// $password = 'password';              
// $db_name = 'webtech_fall2024_papa_badu'; 


$servername = 'localhost';  
$username = 'root';           
$password = '';              
$db_name = 'db'; 
// try {
//     // Create connection
//     $conn = new mysqli($servername, $username, $password, $db_name);

//     // Check connection
//     if ($conn->connect_errno) {
//         throw new Exception("Connection failed: " . $conn->connect_error);
//     }

//     // Additional connection test
//     $result = $conn->query("SELECT 1");
//     if (!$result) {
//         throw new Exception("Query test failed: " . $conn->error);
//     }

//     //echo "Connected successfully";
// } catch (Exception $e) {
//     // Log the error
//     error_log($e->getMessage());

//     // Detailed error output
//     die("Database Connection Error: " . $e->getMessage() . 
//         "<br>Error Number: " . $conn->connect_errno);
// }
function getDatabaseConnection() {
    global $servername, $username, $password, $db_name; // Use global variables

    // Create connection
    $conn = new mysqli($servername, $username, $password, $db_name);

    // Check connection
    if ($conn->connect_errno) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Additional connection test
    $result = $conn->query("SELECT 1");
    if (!$result) {
        throw new Exception("Query test failed: " . $conn->error);
    }

    return $conn; // Return the connection
}

// Optional: Test the connection when this file is included
try {
    $conn = getDatabaseConnection();
    // Uncomment the next line to confirm connection
    // echo "Connected successfully";
} catch (Exception $e) {
    // Log the error
    error_log($e->getMessage());

    // Detailed error output
    die("Database Connection Error: " . $e->getMessage() . 
        "<br>Error Number: " . $conn->connect_errno);
}
?>