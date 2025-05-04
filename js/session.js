/**
 * session.js - Handle session management for the Lost and Found System
 * 
 * This script provides functions to manage user sessions across the application.
 * It ensures session data is properly handled and helps prevent session-related errors.
 */

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

// Initialize session timeout handling when the page loads
document.addEventListener('DOMContentLoaded', function() {
    handleSessionTimeout();
});

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        handleSessionTimeout
    };
} 