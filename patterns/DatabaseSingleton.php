<?php
class DatabaseSingleton {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Establish database connection (adjust with your credentials)
        $this->conn = new mysqli("localhost", "root", "", "lost_and_found_system");
        
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseSingleton();
        }
        return self::$instance;
    }

    // Get the database connection
    public function getConnection() {
        return $this->conn;
    }
}
?>
