<?php
/**
 * Authentication functions for the applciation
 */

/**
 * Start session if not already started
 */
function startSessionIfNotStarted() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if a user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    startSessionIfNotStarted();
    return isset($_SESSION['user_id']);
}

/**
 * Get the current user's ID
 * @return int|null The user's ID or null if not logged in
 */
function getCurrentUserId() {
    startSessionIfNotStarted();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get the current user's role
 * @return string|null The user's role or null if not logged in
 */
function getCurrentUserRole() {
    startSessionIfNotStarted();
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if the current user is an admin
 * @return bool True if user is an admin, false otherwise
 */
function isAdmin() {
    return getCurrentUserRole() === 'admin';
}

/**
 * Check if the current user is a staff member
 * @return bool True if user is a staff member, false otherwise
 */
function isStaff() {
    return getCurrentUserRole() === 'staff';
}

/**
 * Check if the current user is a student
 * @return bool True if user is a student, false otherwise
 */
function isStudent() {
    return getCurrentUserRole() === 'student';
}

/**
 * Require the user to be logged in
 * Redirects to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /Lost_And_Found_System/view/login.php');
        exit();
    }
}

/**
 * Require the user to be an admin
 * Redirects to dashboard if not an admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /Lost_And_Found_System/view/dashboard.php');
        exit();
    }
}

/**
 * Require the user to be a staff member
 * Redirects to dashboard if not a staff member
 */
function requireStaff() {
    requireLogin();
    if (!isStaff()) {
        header('Location: /Lost_And_Found_System/view/dashboard.php');
        exit();
    }
}

/**
 * Require the user to be a student
 * Redirects to dashboard if not a student
 */
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header('Location: /Lost_And_Found_System/view/dashboard.php');
        exit();
    }
}

session_start();
require_once 'database.php';

/**
 * Check if an email already exists in any user table
 * @param string $email Email to check
 * @return bool True if email exists, false otherwise
 */
function emailExists($email) {
    $conn = getDatabaseConnection();
    
    // Check in Students table
    $sql = "SELECT id FROM Students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return true;
    }
    
    // Check in Staff table
    $sql = "SELECT id FROM Staff WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return true;
    }
    
    // Check in Admin table
    $sql = "SELECT id FROM Admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return true;
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

/**
 * Register a new student
 * @param string $first_name First name
 * @param string $last_name Last name
 * @param string $email Email
 * @param string $school_id School ID
 * @param string $password Password
 * @return array Result with success status and message
 */
function registerStudent($first_name, $last_name, $email, $school_id, $password) {
    $conn = getDatabaseConnection();
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if school ID already exists
    $sql = "SELECT id FROM Students WHERE school_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $school_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return [
            'success' => false,
            'message' => 'This school ID is already registered.'
        ];
    }
    
    // Insert new student
    $sql = "INSERT INTO Students (first_name, last_name, email, school_id, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $school_id, $hashed_password);
    
    if ($stmt->execute()) {
        // Get the user ID
        $user_id = $conn->insert_id;
        
        // Start session and set session variables
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_type"] = "student";
        $_SESSION["email"] = $email;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;
        
        $stmt->close();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Registration successful.'
        ];
    } else {
        $stmt->close();
        $conn->close();
        
        return [
            'success' => false,
            'message' => 'Something went wrong. Please try again later.'
        ];
    }
}

/**
 * Register a new staff member
 * @param string $first_name First name
 * @param string $last_name Last name
 * @param string $email Email
 * @param string $faculty_id Faculty ID
 * @param string $password Password
 * @return array Result with success status and message
 */
function registerStaff($first_name, $last_name, $email, $faculty_id, $password) {
    $conn = getDatabaseConnection();
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if faculty ID already exists
    $sql = "SELECT id FROM Staff WHERE faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return [
            'success' => false,
            'message' => 'This faculty ID is already registered.'
        ];
    }
    
    // Insert new staff
    $sql = "INSERT INTO Staff (first_name, last_name, email, faculty_id, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $faculty_id, $hashed_password);
    
    if ($stmt->execute()) {
        // Get the user ID
        $user_id = $conn->insert_id;
        
        // Start session and set session variables
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_type"] = "staff";
        $_SESSION["email"] = $email;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;
        
        $stmt->close();
        $conn->close();
        
        return [
            'success' => true,
            'message' => 'Registration successful.'
        ];
    } else {
        $stmt->close();
        $conn->close();
        
        return [
            'success' => false,
            'message' => 'Something went wrong. Please try again later.'
        ];
    }
}

/**
 * Login a user
 * @param string $email Email
 * @param string $password Password
 * @return array Result with success status, message, and user data
 */
function loginUser($email, $password) {
    $conn = getDatabaseConnection();
    
    // Check in Students table
    $sql = "SELECT id, first_name, last_name, email, password FROM Students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row["password"])) {
            // Password is correct
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_type"] = "student";
            $_SESSION["email"] = $row["email"];
            $_SESSION["first_name"] = $row["first_name"];
            $_SESSION["last_name"] = $row["last_name"];
            
            $stmt->close();
            $conn->close();
            
            return [
                'success' => true,
                'message' => 'Login successful.',
                'user' => [
                    'id' => $row["id"],
                    'email' => $row["email"],
                    'first_name' => $row["first_name"],
                    'last_name' => $row["last_name"],
                    'type' => 'student'
                ]
            ];
        } else {
            $stmt->close();
            $conn->close();
            
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
    }
    
    // Check in Staff table
    $sql = "SELECT id, first_name, last_name, email, password FROM Staff WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row["password"])) {
            // Password is correct
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_type"] = "staff";
            $_SESSION["email"] = $row["email"];
            $_SESSION["first_name"] = $row["first_name"];
            $_SESSION["last_name"] = $row["last_name"];
            
            $stmt->close();
            $conn->close();
            
            return [
                'success' => true,
                'message' => 'Login successful.',
                'user' => [
                    'id' => $row["id"],
                    'email' => $row["email"],
                    'first_name' => $row["first_name"],
                    'last_name' => $row["last_name"],
                    'type' => 'staff'
                ]
            ];
        } else {
            $stmt->close();
            $conn->close();
            
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
    }
    
    // Check in Admin table
    $sql = "SELECT id, first_name, last_name, email, password FROM Admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row["password"])) {
            // Password is correct
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_type"] = "admin";
            $_SESSION["email"] = $row["email"];
            $_SESSION["first_name"] = $row["first_name"];
            $_SESSION["last_name"] = $row["last_name"];
            
            $stmt->close();
            $conn->close();
            
            return [
                'success' => true,
                'message' => 'Login successful.',
                'user' => [
                    'id' => $row["id"],
                    'email' => $row["email"],
                    'first_name' => $row["first_name"],
                    'last_name' => $row["last_name"],
                    'type' => 'admin'
                ]
            ];
        } else {
            $stmt->close();
            $conn->close();
            
            return [
                'success' => false,
                'message' => 'Invalid email or password.'
            ];
        }
    }
    
    $stmt->close();
    $conn->close();
    
    return [
        'success' => false,
        'message' => 'Invalid email or password.'
    ];
}

/**
 * Log out a user
 */
function logoutUser() {
    // Initialize the session
    session_start();
    
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

/**
 * Handle authentication actions (login, logout)
 */
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'logout') {
        logoutUser();
        header("location: ../index.php");
        exit;
    }
}
?>