<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';      // Your database username
    private $password = '';          // Your database password
    private $database = 'st_rita_wedding';  // Updated database name
    private $conn;

    public function __construct() { 
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
} 
