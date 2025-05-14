<?php

namespace App\Models;

use PDO;
use PDOException;
use Core\Database; // ✅ Correct namespace

class User {
    private $conn;
    private $db;
    private $table = "users"; // Table name

    // Constructor: Get DB connection
    public function __construct($db)
    {
        $this->db = $db;
    }


    // Find a user by email
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Login method (fetches user data by email)
    public function login($email)
    {
        // Query to select user by email
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return the user data or false if not found
    }



    // Register method (adds new user)
    public function register(string $name, string $email, string $password, ?string $phone = null, ?string $profilePhoto = null, ?string $country = null, ?string $city = null, ?string $language = null, ?string $currency = null, ?string $gender = null) {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, phone, profile_photo, country, city, language, currency, gender) VALUES (:name, :email, :password, :phone, :profile_photo, :country, :city, :language, :currency, :gender)");
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'profile_photo' => $profilePhoto,
            'country' => $country,
            'city' => $city,
            'language' => $language,
            'currency' => $currency,
            'gender' => $gender
        ]);
    }

    // Get total number of users
    public function getTotalUsers(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Get users with limit and offset for pagination
    public function getUsersWithLimit(int $limit, int $offset): array
    {
        $sql = "SELECT id, name, email, role, phone, status, country, city, profile_photo, currency, language, gender
                FROM users
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all users (returns id and name) - adjusted to use getUsersWithLimit for consistency
    public function getAllUsers(bool $includeInactive = true): array
    {
        $sql = "SELECT id, name, email, role, phone, status, country, city, profile_photo,currency, language, gender FROM users";
        if (!$includeInactive) {
            $sql .= " WHERE status = 0"; // Only select active users if $includeInactive is false
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch user by ID with additional profile information
    public function getUser($user_id) {
        $query = "SELECT id, name, email, phone, country, city, role, language, currency, gender, profile_photo
                  FROM " . $this->table . "
                  WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function getCreatedTrips(int $userId): array
    {
        $tripModel = new \App\Models\Trip($this->db); // Instantiate your Trip model, adjust namespace if needed
        return $tripModel->getTripsByUserId($userId);
    }

    // Update user profile (including additional fields)
    public function updateProfile(
        $user_id,
        $name,  
        $email,
        $password = null,
        $phone = null,
        $profilePhoto = null,
        $country = null,
        $language = null,
        $currency = null,
        $gender = null
    ) {
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email, phone = :phone, country = :country, language = :language, currency = :currency, gender = :gender";

        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query .= ", password = :password";
        }

        if ($profilePhoto !== null) {
            $query .= ", profile_photo = :profile_photo";
        }

        $query .= " WHERE id = :user_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
        $stmt->bindParam(":country", $country, PDO::PARAM_STR);
        $stmt->bindParam(":language", $language, PDO::PARAM_STR);
        $stmt->bindParam(":currency", $currency, PDO::PARAM_STR);
        $stmt->bindParam(":gender", $gender, PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        if ($password) {
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        }

        if ($profilePhoto !== null) {
            $stmt->bindParam(":profile_photo", $profilePhoto, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }


    public function getUserById(int $id): ?array
    {
        $sql = "SELECT id, name, email, role, phone, status FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    

    public function updateUserStatus(int $id, int $status): bool
    {
        $sql = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


  // For recommendations
    public function getAllParticipants(): array
    {
        $query = "SELECT id FROM " . $this->table . " WHERE role = 'participant'";
        $stmt = $this->db->prepare($query);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Get All Participants): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }
    }


    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            // Log the error or handle it appropriately
            error_log("Database Error (User::find): " . $e->getMessage());
            return null;
        } finally {
            $stmt->closeCursor();
        }
    }
}