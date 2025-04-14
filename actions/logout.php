<?php
require_once __DIR__ . '/../includes/auth.php';

// Start session
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to index page
header('Location: /Lost_And_Found_System/index.php');
exit();
?> 