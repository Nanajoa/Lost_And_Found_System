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
}
?>
