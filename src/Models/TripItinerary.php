<?php

namespace App\Models;

use Core\Database;
use mysqli;
use Exception;
use PDO;
class TripItinerary {
    private $conn;
    private $itinerary;
    private $db;

    // Constructor accepts PDO instance and assigns it to the db property
    public function __construct(PDO $pdo) {
        $this->db = $pdo;
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
    public function create($trip_id, $day_title, $description, $location, $itinerary_date) {
        $query = "INSERT INTO trip_itineraries (trip_id, day_title, description, location, itinerary_date) VALUES (?, ?, ?, ?, ?)";
        
        // Prepare the query using PDO
        $stmt = $this->db->prepare($query);
        
        // Bind the parameters using PDO
        $stmt->bindValue(1, $trip_id, PDO::PARAM_INT);       // Bind trip_id as an integer
        $stmt->bindValue(2, $day_title, PDO::PARAM_STR);      // Bind day_title as a string
        $stmt->bindValue(3, $description, PDO::PARAM_STR);    // Bind description as a string
        $stmt->bindValue(4, $location, PDO::PARAM_STR);       // Bind location as a string
        $stmt->bindValue(5, $itinerary_date, PDO::PARAM_STR); // Bind itinerary_date as a string
        
        // Execute the statement
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    // Store a new itinerary
    public function store() {
        // Ensure Itinerary Model is instantiated
        $this->itinerary = new TripItinerary($this->db);
        
        $trip_id = $_POST['trip_id'];
        $day_title = $_POST['day_title'];
        $description = $_POST['description'];
        $location = $_POST['location'];
        $itinerary_date = $_POST['itinerary_date']; // Ensure it matches the input field
        
        // Debug: Check if data is correctly received
        if (!$trip_id || !$day_title || !$description || !$location || !$itinerary_date) {
            die("Error: Missing required fields!");
        }
    
        // Call create method
        if ($this->itinerary->create($trip_id, $day_title, $description, $location, $itinerary_date)) {
            header("Location: /trip/$trip_id/itinerary");
            exit();
        } else {
            echo "Error: Could not save itinerary.";
        }
    }


    // Update an existing itinerary
    public function update($id, $day_title, $description, $location, $itinerary_date) {
        if (!$this->db) {
            die("Error: Database connection not initialized.");
        }

        $query = "UPDATE trip_itineraries SET day_title = ?, description = ?, location = ?, itinerary_date = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            die("Error: " . $this->db->errorInfo()[2]);
        }

        // Bind the parameters using PDO
        $stmt->bindValue(1, $day_title, PDO::PARAM_STR);      // Bind day_title as a string
        $stmt->bindValue(2, $description, PDO::PARAM_STR);    // Bind description as a string
        $stmt->bindValue(3, $location, PDO::PARAM_STR);       // Bind location as a string
        $stmt->bindValue(4, $itinerary_date, PDO::PARAM_STR); // Bind itinerary_date as a string
        $stmt->bindValue(5, $id, PDO::PARAM_INT);             // Bind id as an integer

        // Execute the statement
        if ($stmt->execute()) {
            return true;
        } else {
            die("Error executing query: " . $stmt->errorInfo()[2]);
        }
    }

     // Example method to get itinerary data
     public function getItineraryByTripId($trip_id)
     {
         $query = "SELECT * FROM itineraries WHERE trip_id = ?";
 
         $stmt = $this->conn->prepare($query);
         $stmt->bindValue(1, $trip_id, PDO::PARAM_INT);
         $stmt->execute();
 
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }


    // Delete an itinerary.php
    public function delete($id) {
        if (!is_numeric($id) || $id <= 0) {
            die("Error: Invalid itinerary ID.");
        }
    
        // Check if the ID exists before deleting
        $query_check = "SELECT id FROM trip_itineraries WHERE id = ?";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        if ($result->num_rows === 0) {
            die("Error: Itinerary not found.");
        }
        $stmt_check->close();
    
        // Proceed to delete the record
        $query = "DELETE FROM trip_itineraries WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
    
        try {
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                die("Error: Failed to delete itinerary.");
            }
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    


}