<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
  header("location: ../admin-login.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Reports</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <h1>Admin Reports Page</h1>
  <p>This is a placeholder for the admin reports page.</p>
</body>
</html> 