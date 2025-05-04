<?php
/**
 * start_session.php - Safely start a PHP session
 * 
 * This script is intended to be called via AJAX from the client-side
 * to start a PHP session if one isn't already active.
 */

// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Return success response
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?> 