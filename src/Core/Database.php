<?php

namespace Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $conn;
    private $host = "localhost";
    private $db_name = "trip_planner";
    private $username = "root";
    private $password = "";
    private $db;
    // Private constructor prevents direct instantiation
   private function __construct() {
    try {
        $this->conn = new PDO(
            "mysql:host={$this->host};dbname={$this->db_name}",
            $this->username,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
    } catch (PDOException $e) {
        die("Database Connection Failed: " . $e->getMessage());
    }
}


    // Get the single instance of Database connection
   // Singleton method to get the database instance
   public static function getInstance() {
    if (self::$instance === null) {
        self::$instance = new self();
    }
    return self::$instance;
}

    // Return the database connection   
    public function getConnection() {
        if ($this->conn === null) {
            die("Connection is null, check your Database configuration.");
        }
        return $this->conn;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {}
}
