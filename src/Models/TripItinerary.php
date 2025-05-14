<?php

namespace App\Models;

use Core\Database;

use Exception;
use PDO;
use PDOException;
class TripItinerary {
    private $conn;
    private $itinerary;
    private $db;

    // Constructor accepts PDO instance and assigns it to the db property
    public function __construct($db)
    {
        $this->db = $db; // Initialize the database connection
    }



   // Get all itineraries for a specific trip
    public function getAll($trip_id) {
        $query = "SELECT * FROM trip_itineraries WHERE trip_id = ?";

        if (!$this->db) {
            die("Database connection is null in TripItinerary.");
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $trip_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single itinerary by ID
    public function getById($id) {
        $query = "SELECT * FROM trip_itineraries WHERE id = ?";
        
        // Prepare the query using PDO
        $stmt = $this->db->prepare($query);  // Using PDO's prepare()

        // Bind the parameter using PDO
        $stmt->bindValue(1, $id, PDO::PARAM_INT); // Use bindValue() and bind the ID as an integer

        // Execute the query
        $stmt->execute();

        // Fetch the result using PDO
        $result = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch a single result as an associative array

        return $result;
    }

    // Create a new itinerary
    public function create($trip_id, $day_title, $description, $location, $itinerary_date, $image = null)
    {
        $query = "INSERT INTO trip_itineraries (trip_id, day_title, description, location, itinerary_date, image) VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare the query using PDO
        $stmt = $this->db->prepare($query);

        // Bind the parameters using PDO
        $stmt->bindValue(1, $trip_id, PDO::PARAM_INT);        // Bind trip_id as an integer
        $stmt->bindValue(2, $day_title, PDO::PARAM_STR);       // Bind day_title as a string
        $stmt->bindValue(3, $description, PDO::PARAM_STR);    // Bind description as a string
        $stmt->bindValue(4, $location, PDO::PARAM_STR);        // Bind location as a string
        $stmt->bindValue(5, $itinerary_date, PDO::PARAM_STR); // Bind itinerary_date as a string
        $stmt->bindValue(6, $image, PDO::PARAM_STR);          // Bind image as a string (nullable)

        // Execute the statement
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }





    // Update an existing itinerary
    public function update($id, $day_title, $description, $location, $itinerary_date, $image = null)
    {
        if (!$this->db) {
            die("Error: Database connection not initialized.");
        }

        $query = "UPDATE trip_itineraries SET day_title = ?, description = ?, location = ?, itinerary_date = ?, image = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            die("Error: " . $this->db->errorInfo()[2]);
        }

        $stmt->bindValue(1, $day_title, PDO::PARAM_STR);
        $stmt->bindValue(2, $description, PDO::PARAM_STR);
        $stmt->bindValue(3, $location, PDO::PARAM_STR);
        $stmt->bindValue(4, $itinerary_date, PDO::PARAM_STR);
        $stmt->bindValue(5, $image, PDO::PARAM_STR);
        $stmt->bindValue(6, $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

     // Example method to get itinerary data
     public function getItineraryByTripId($trip_id)
     {
         $query = "SELECT * FROM itineraries WHERE trip_id = ?";
 
         $stmt = $this->db->prepare($query);
         $stmt->bindValue(1, $trip_id, PDO::PARAM_INT);
         $stmt->execute();
 
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }


    // Delete an itinerary.php
    public function delete($id) {
        // ... (rest of your model's delete function) ...
    
        $query = "DELETE FROM trip_itineraries WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        error_log("Executing DELETE query: " . $query . " with ID: " . $id); // Log the query
    
        try {
            $result = $stmt->execute();
            error_log("Delete query result: " . ($result ? 'success' : 'failure')); // Log the result
            if ($result) {
                return true;
            } else {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true)); // Log detailed error info
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception during delete: " . $e->getMessage());
            return false;
        } finally {
            $stmt->closeCursor();
        }
    }
    


}