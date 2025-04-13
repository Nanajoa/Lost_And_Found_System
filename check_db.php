<?php
require_once __DIR__ . '/db/database.php';

$conn = getDatabaseConnection();

// Check Students table
echo "Checking Students table:\n";
$result = $conn->query("SELECT * FROM Students");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Email: " . $row["email"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "\n";
    }
} else {
    echo "No records found in Students table\n";
}

// Check Staff table
echo "\nChecking Staff table:\n";
$result = $conn->query("SELECT * FROM Staff");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Email: " . $row["email"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "\n";
    }
} else {
    echo "No records found in Staff table\n";
}

// Check Admin table
echo "\nChecking Admin table:\n";
$result = $conn->query("SELECT * FROM Admin");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Email: " . $row["email"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "\n";
    }
} else {
    echo "No records found in Admin table\n";
}

$conn->close();
?> 