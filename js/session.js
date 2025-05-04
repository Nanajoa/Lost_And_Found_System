/**
 * session.js - Handle session management for the Lost and Found System
 * 
 * This script provides functions to manage user sessions across the application.
 * It ensures session data is properly handled and helps prevent session-related errors.
 */

/**
 * Check if a session is active
 * @returns {boolean} True if a session is active, false otherwise
 */
function isSessionActive() {
    // This is a client-side check for session status
    return document.cookie.includes('PHPSESSID=');
}

/**
 * Initialize a session if one is not already active
 * This is a client-side representation of the PHP startSessionIfNotStarted function
 */
function initSession() {
    if (!isSessionActive()) {
        // Make an AJAX request to a PHP script that starts a session
        fetch('../db/start_session.php', {
            method: 'GET',
            credentials: 'same-origin' // Include cookies in the request
        }).then(response => {
            console.log('Session initialized');
        }).catch(error => {
            console.error('Error initializing session:', error);
        });
    }
}

// Initialize the session when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initSession();
});

/**
 * Handle session timeout
 * This function can be used to redirect users to the login page
 * when their session expires.
 */
function handleSessionTimeout() {
    // Check session status periodically
    setInterval(function() {
        fetch('../db/check_session.php', {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.active) {
                // Redirect to login page if session is not active
                window.location.href = '../views/login.php';
            }
        })
        .catch(error => {
            console.error('Error checking session:', error);
        });
    }, 300000); // Check every 5 minutes (300000 ms)
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        isSessionActive,
        initSession,
        handleSessionTimeout
    };
} 