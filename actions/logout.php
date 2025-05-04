<?php
require_once __DIR__ . '/../includes/auth.php';

// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start a new session to prevent session fixation
session_start();
session_regenerate_id(true);

// Redirect to index page
header('Location: /Lost_And_Found_System/index.php');
exit();
?> 