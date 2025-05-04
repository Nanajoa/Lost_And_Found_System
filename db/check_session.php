<?php
/**
 * check_session.php - Check if a PHP session is active
 * 
 * This script is intended to be called via AJAX from the client-side
 * to check if a PHP session is still active.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isActive = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Return session status
header('Content-Type: application/json');
echo json_encode(['active' => $isActive]);
?> 