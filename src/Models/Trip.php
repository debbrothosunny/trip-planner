<?php

namespace App\Models;

use PDO;
use PDOException;


class Trip
{
    private $db;
    private $table = 'trips';  // Ensure table name is defined if needed

    // Constructor to initialize PDO object
    public function __construct($db)
    {
        $this->db = $db; // Initialize the database connection
    }

    // Method to create a trip
    public function createTrip($name, $user_id, $start_date, $end_date, $budget)
    {
        $query = "INSERT INTO " . $this->table . " (name, user_id, start_date, end_date, budget) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        // Bind values to the placeholders
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $user_id);
        $stmt->bindValue(3, $start_date);
        $stmt->bindValue(4, $end_date);
        $stmt->bindValue(5, $budget);

        // Execute and return the result
        return $stmt->execute();
    }

    // Method to fetch trips by user ID
    public function getTripsByUserId($userId)
    {
        $query = "SELECT * FROM trips WHERE user_id = :userId";  // Updated column name
        $stmt = $this->db->prepare($query);  // Use $this->db for the prepared statement
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);  // Use the correct parameter type for userId
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return the trips as an array
    }

    // Method to get trip by ID
    public function getTripById($id)
    {
        // Query to fetch a trip by its ID
        $query = "SELECT * FROM trips WHERE id = ?";
        $stmt = $this->db->prepare($query);  // Correcting this to use $this->db

        // Bind value to the placeholder
        $stmt->bindValue(1, $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Return the fetched data
    }



    public function updateTrip($id, $name, $start_date, $end_date, $budget)
    {
        $query = "UPDATE trips SET name = ?, start_date = ?, end_date = ?, budget = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);

        // Use bindValue() for PDO
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $start_date);
        $stmt->bindValue(3, $end_date);
        $stmt->bindValue(4, $budget);
        $stmt->bindValue(5, $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteTrip($id)
    {
        $sql = "DELETE FROM trips WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error (Delete Trip): " . $e->getMessage());
            return false;
        }
    }



    // Fetch all trips from the trips table
    public function getAllTrips() {
        $query = "SELECT id, name FROM trips"; // Query to get all trips
        $stmt = $this->db->query($query); // Use $this->conn for executing the query
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return the result as an associative array
    }
 

    public function getTripCreator($tripId) {
        $sql = "SELECT users.name, users.email FROM trips 
                JOIN users ON trips.user_id = users.id 
                WHERE trips.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tripId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

