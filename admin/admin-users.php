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
  <title>Admin Users</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <h1>Admin Users Page</h1>
  <p>This is a placeholder for the admin users page.</p>
</body>
</html> 