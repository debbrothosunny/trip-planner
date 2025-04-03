<?php

namespace App\Models;

use PDO;
use Core\Database; // Import the Database class

class Accommodation {
    private $conn;
    private $db;

    // Constructor to initialize the database connection
     public function __construct($db) {
        $this->db = $db;
    }

    // Get all accommodations by hotel_id (removed trip_itinery_id)
    public function getAllByHotel($hotel_id) {
        $query = "SELECT * FROM accommodations WHERE hotel_id = :hotel_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':hotel_id', $hotel_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get trips associated with the logged-in user (no change needed here)
    public function getTripsByUser($userId) {
        $query = "SELECT * FROM trips WHERE user_id = :user_id"; 
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new accommodation entry 
    public function create($user_id, $hotel_id, $room_type, $check_in_date, $check_out_date, $status, $trip_id) {
        // ... (hotel and trip ID validation) ...
    
        $query = "INSERT INTO accommodations (user_id, hotel_id, room_type, check_in_date, check_out_date, status, trip_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
    
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $hotel_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $room_type, PDO::PARAM_STR);
        $stmt->bindValue(4, $check_in_date, PDO::PARAM_STR);
        $stmt->bindValue(5, $check_out_date, PDO::PARAM_STR);
        $stmt->bindValue(6, $status, PDO::PARAM_INT);
        $stmt->bindValue(7, $trip_id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return $this->db->lastInsertId(); // Return the ID of the new record
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("Failed to insert accommodation: " . implode(", ", $errorInfo));
            return ['error' => true, 'message' => "Database error: " . $errorInfo[2]];
        }
    }
    
    


    // Update accommodation data (removed trip_itinery_id and room_id)
    public function update($id, $user_id, $hotel_id, $check_in_date, $check_out_date, $price, $status) {
        $query = "UPDATE accommodations SET user_id = :user_id, hotel_id = :hotel_id,
                  check_in_date = :check_in_date, check_out_date = :check_out_date, price = :price, status = :status
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':hotel_id', $hotel_id);
        $stmt->bindParam(':check_in_date', $check_in_date);
        $stmt->bindParam(':check_out_date', $check_out_date);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        return $stmt->execute(); // true on success, false on failure
    }

    // Get accommodation by user_id and hotel_id (room_type now from accommodations table)
    public function getAccommodationsByUserId($userId) {
        $sql = "SELECT 
                    a.id,
                    h.name AS hotel_name,
                    a.room_type,
                    r.price,
                    r.total_rooms,
                    r.available_rooms,
                    r.description,
                    a.check_in_date,
                    a.check_out_date,
                    a.status
                FROM accommodations a
                JOIN hotels h ON a.hotel_id = h.id
                LEFT JOIN hotel_rooms r ON h.id = r.hotel_id AND a.room_type = r.room_type
                WHERE a.user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    


    
    
    

    // Find accommodation by ID (no change needed here)
    public function find($id) {
        $query = "SELECT * FROM accommodations WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return a single result as associative array
    }


    public function getAccommodationByIdAndUser($id, $userId) {
        $query = "SELECT * FROM accommodations WHERE id = :id AND user_id = :user_id LIMIT 1";
        
        // Prepare statement
        $stmt = $this->db->prepare($query);
        
        // Bind values
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();

        // Fetch and return the result
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


     // For Booking Function 
     public function getAllPendingBookingsWithHotelRoomDetails()
     {
         $sql = "
             SELECT 
                 a.*, 
                 u.name AS user_name,
                 h.name AS hotel_name,
                 h.location,
                 h.description AS hotel_description,
                 r.room_type,
                 r.price,
                 r.total_rooms AS hotel_total_rooms,  -- ✅ Hotel room total_rooms
                 r.available_rooms,                    -- ✅ Optionally include available_rooms
                 r.description AS room_description
             FROM accommodations a
             JOIN users u ON a.user_id = u.id
             JOIN hotels h ON a.hotel_id = h.id
             JOIN hotel_rooms r 
                 ON r.hotel_id = a.hotel_id AND r.room_type = a.room_type
             WHERE a.status = '0'
         ";
     
         $stmt = $this->db->prepare($sql);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }
     
     
     
     
     
     
     
     
     


    public function confirmAccommodation($id)
    {
        $sql = "UPDATE accommodations SET status = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }



}
