<?php

namespace App\Models;

use PDO;
use Core\Database; // Import the Database class

class Accommodation {
    private $conn;
    private $db;
    private $table = "accommodations";

    // Constructor to initialize the database connection

    public function __construct() {
        // Get PDO connection using Singleton pattern
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all accommodations by owner_id

     public function getAllByTrip($trip_id) {
        $query = "SELECT * FROM accommodations WHERE trip_id = :trip_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTripsByUser($userId) {
        // Query to fetch trips associated with the logged-in user
        $query = "SELECT * FROM trips WHERE user_id = :user_id";  // Adjust the table name and column
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        // Return the result as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function create($trip_id, $name, $location, $price, $amenities, $check_in_time, $check_out_time) {
        // First, check if the trip_id exists in the trips table
        $query = "SELECT COUNT(*) FROM trips WHERE id = :trip_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Check if the trip_id exists
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            // If no such trip_id exists, log error and return false
            error_log("Invalid trip_id: $trip_id does not exist.");
            return false;
        }
    
        // Prepare the SQL query to insert accommodation data
        $query = "INSERT INTO accommodations (trip_id, name, location, price, amenities, check_in_time, check_out_time)
                  VALUES (:trip_id, :name, :location, :price, :amenities, :check_in_time, :check_out_time)";
        
        // Prepare the statement using PDO
        $stmt = $this->db->prepare($query);
    
        // Bind the parameters to the prepared statement
        $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':amenities', $amenities, PDO::PARAM_STR);
        $stmt->bindParam(':check_in_time', $check_in_time, PDO::PARAM_STR);
        $stmt->bindParam(':check_out_time', $check_out_time, PDO::PARAM_STR);
    
    }
    
    
    
    public function update($id, $trip_id, $name, $location, $price, $amenities, $check_in_time, $check_out_time) {
        $query = "UPDATE accommodations SET trip_id = :trip_id, name = :name, location = :location, 
                  price = :price, amenities = :amenities, check_in_time = :check_in_time, 
                  check_out_time = :check_out_time WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':trip_id', $trip_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':amenities', $amenities);
        $stmt->bindParam(':check_in_time', $check_in_time);
        $stmt->bindParam(':check_out_time', $check_out_time);
        $stmt->bindParam(':id', $id);
    
        // Execute the query and return true or false based on success
        return $stmt->execute(); // true on success, false on failure
    }
    

    public function getAccommodationByIdAndUser($id, $userId) {
        // Query to fetch accommodation by ID and user_id
        $query = "SELECT * FROM accommodations WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    
        // Return the result as an associative array
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


     // Implementing the `find` method
     public function find($id) {
        $query = "SELECT * FROM accommodations WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single result as associative array
    }
    

    
}

