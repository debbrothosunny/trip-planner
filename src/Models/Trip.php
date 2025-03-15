<?php

namespace App\Models;

use PDO;
use Exception;

class Trip
{

    
    private $conn; // This will hold the PDO connection
    private $table = "trips";  // This defines the table name

    // Constructor to initialize the connection
    public function __construct(PDO $pdo)
    {
        $this->conn = $pdo;  // Set the PDO connection
    }

  
    public function createTrip($name, $user_id, $start_date, $end_date, $budget)
    {
        $query = "INSERT INTO trips (name, user_id, start_date, end_date, budget) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        // Use bindValue() for PDO
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $user_id);
        $stmt->bindValue(3, $start_date);
        $stmt->bindValue(4, $end_date);
        $stmt->bindValue(5, $budget);

        return $stmt->execute();
    }

    // Method to fetch trips by user ID

    public function getTripsByUserId($userId)
    {
        // Query to fetch trips for the given user, using user_id instead of owner_id
        $query = "SELECT * FROM trips WHERE user_id = :userId";  // Updated column name
        $stmt = $this->conn->prepare($query);  // Use the $conn property for prepared statements
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return the trips as an array
    }








    public function getTripById($id)
    {
        $query = "SELECT * FROM trips WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Use bindValue() for PDO
        $stmt->bindValue(1, $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTrip($id, $name, $start_date, $end_date, $budget)
    {
        $query = "UPDATE trips SET name = ?, start_date = ?, end_date = ?, budget = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

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
        $sql = "DELETE FROM trips WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        // Use bindValue() for PDO
        $stmt->bindValue(1, $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Fetch all trips from the trips table
    public function getAllTrips() {
        $query = "SELECT id, name FROM trips"; // Query to get all trips
        $stmt = $this->conn->query($query); // Use $this->conn for executing the query
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return the result as an associative array
    }
 

    public function getTripCreator($tripId) {
        $sql = "SELECT users.name, users.email FROM trips 
                JOIN users ON trips.user_id = users.id 
                WHERE trips.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tripId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

