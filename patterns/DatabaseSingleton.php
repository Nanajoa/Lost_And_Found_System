<?php
class DatabaseSingleton {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'lost_and_found';

        // Creating a new database connection
        try {
            $this->connection = new mysqli($servername, $username, $password, $dbname);
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Enable error reporting
            $this->connection->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
            
            // Set autocommit to false for transaction support
            $this->connection->autocommit(false);
            
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseSingleton();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
    
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }
    
    public function commit() {
        $this->connection->commit();
    }
    
    public function rollback() {
        $this->connection->rollback();
    }
}
?>
