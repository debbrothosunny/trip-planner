<?php

namespace App\Models;

use PDO;
use Core\Database; // ✅ Correct namespace

class User {
    private $conn;
    private $table = "users"; // Table name

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




    // Fetch user by ID
    public function getUser($user_id) {
        $query = "SELECT id, name, email FROM " . $this->table . " WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user profile (name, email, and password)
    public function updateProfile($user_id, $name, $email, $password = null) {
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email";
        
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query .= ", password = :password";
        }

        $query .= " WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        
        if ($password) {
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        }

        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }



   
}

