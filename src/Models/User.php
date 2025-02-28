<?php

namespace App\Models;

use PDO;
use Core\Database; // ✅ Correct namespace

class User {
    private $conn;

    // Constructor: Get DB connection
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();  // Static method to get DB connection
    }

    // Find a user by email
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Login method (fetches user data by email)
    public function login($email)
    {
        // Query to select user by email
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return the user data or false if not found
    }

    // Register method (adds new user)
    public function register(string $name, string $email, string $password) {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        return $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);
    }

    // Get all users (returns id and name)
    public function getAllUsers() {
        $query = "SELECT id, name FROM users";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



   
}

