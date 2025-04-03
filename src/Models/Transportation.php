<?php

namespace App\Models;

use Core\Database;
use PDO;

class Transportation
{  
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


     // CRUD operations
     public function create($data)
     {
         // Prepare SQL query to insert new transportation record
         $query = "INSERT INTO transportation (trip_id, type, company_name, departure_location, arrival_location, departure_date, arrival_date, booking_reference, user_id, amount)
                   VALUES (:trip_id, :type, :company_name, :departure_location, :arrival_location, :departure_date, :arrival_date, :booking_reference, :user_id, :amount)";
     
         $stmt = $this->db->prepare($query);
     
         // Bind the parameters
         $stmt->bindParam(':trip_id', $data['trip_id']);
         $stmt->bindParam(':type', $data['type']);
         $stmt->bindParam(':company_name', $data['company_name']);
         $stmt->bindParam(':departure_location', $data['departure_location']);
         $stmt->bindParam(':arrival_location', $data['arrival_location']);
     
         // Format the dates to YYYY-MM-DD if they are not already in that format
         $departureDate = $data['departure_date'];
         if ($departureDate && strtotime($departureDate) !== false) {
             $departureDate = date('Y-m-d', strtotime($departureDate));
         }
         $stmt->bindParam(':departure_date', $departureDate);
     
         $arrivalDate = $data['arrival_date'];
         if ($arrivalDate && strtotime($arrivalDate) !== false) {
             $arrivalDate = date('Y-m-d', strtotime($arrivalDate));
         }
         $stmt->bindParam(':arrival_date', $arrivalDate);
     
         $stmt->bindParam(':booking_reference', $data['booking_reference']);
         $stmt->bindParam(':user_id', $data['user_id']);
         $stmt->bindParam(':amount', $data['amount']);
     
         // Execute the query and return the last inserted ID
         if ($stmt->execute()) {
             return $this->db->lastInsertId(); // Return the ID of the newly inserted transportation
         }
     
         error_log("Error executing query in create: " . print_r($stmt->errorInfo(), true)); // Log database errors
         return false; // If insertion failed, return false
     }

    public function getTripsByUserId($userId)
    {
        $query = "SELECT 
                    transportation.id, 
                    trips.name AS trip_name, 
                    transportation.company_name, 
                    transportation.departure_location, 
                    transportation.arrival_location, 
                    transportation.departure_date, 
                    transportation.arrival_date, 
                    transportation.booking_reference,
                    transportation.amount  -- Include amount in the result
                  FROM transportation
                  JOIN trips ON transportation.trip_id = trips.id
                  WHERE trips.user_id = ?";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    public function getAllTrip()
    {
        $query = "SELECT id, name FROM trips"; // Fetch only trips, no joins needed
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM transportation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE transportation SET trip_id = ?, type = ?, company_name = ?, departure_location = ?, arrival_location = ?, departure_date = ?, arrival_date = ?, booking_reference = ?, user_id = ?, amount = ? WHERE id = ?");
        return $stmt->execute([
            $data['trip_id'], $data['type'], $data['company_name'],
            $data['departure_location'], $data['arrival_location'], $data['departure_date'],
            $data['arrival_date'], $data['booking_reference'], $data['user_id'], $data['amount'], $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM transportation WHERE id = ?");
        return $stmt->execute([$id]);
    }


}
