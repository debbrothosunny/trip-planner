<?php

namespace App\Models;

use PDO;
use Core\Database; // Assuming Database class handles the DB connection

class HotelRoom {
    private $db;
    private $table = "hotel_rooms";

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch all rooms
    public function getAllRooms() {
        $sql = "SELECT hr.*, h.name AS hotel_name 
                FROM hotel_rooms hr 
                JOIN hotels h ON hr.hotel_id = h.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single room by ID
    public function getRoomById($id) {
        $stmt = $this->db->prepare("SELECT * FROM hotel_rooms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new room
    public function createRoom($hotel_id, $room_type, $price, $total_rooms, $available_rooms, $description) {
        $stmt = $this->db->prepare("INSERT INTO hotel_rooms (hotel_id, room_type, price, total_rooms, available_rooms, description) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$hotel_id, $room_type, $price, $total_rooms, $available_rooms, $description]);
    }

    // Update an existing room
    public function updateRoom($id, $hotel_id, $room_type, $price, $total_rooms, $available_rooms, $description) {
        $stmt = $this->db->prepare("UPDATE hotel_rooms SET hotel_id = ?, room_type = ?, price = ?, total_rooms = ?, available_rooms = ?, description = ? WHERE id = ?");
        return $stmt->execute([$hotel_id, $room_type, $price, $total_rooms, $available_rooms, $description, $id]);
    }

    // Delete a room
    public function deleteRoom($id) {
        $stmt = $this->db->prepare("DELETE FROM hotel_rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }


    // Get all hotels from the database
    public function getAllHotels() {
        // Assuming you have a $db object to interact with the database
        $stmt = $this->db->query("SELECT * FROM hotels"); // Or your query to fetch hotel data
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Method to fetch rooms by hotel ID
    public function getRoomsByHotel($hotelId) {
        $sql = "SELECT room_type, price, total_rooms, available_rooms, description 
                FROM hotel_rooms 
                WHERE hotel_id = :hotel_id";

        // Prepare the SQL statement
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch and return the room data as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function decreaseAvailableAndTotalRooms($hotelId, $roomType, $totalRooms)
    {
        // First, get the current total_rooms and available_rooms to make sure they are correct
        $sql = "
            SELECT total_rooms, available_rooms 
            FROM hotel_rooms
            WHERE hotel_id = :hotelId AND room_type = :roomType
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
        $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
        $stmt->execute();
        $roomData = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Check if we have room data
        if ($roomData) {
            $newAvailableRooms = $roomData['available_rooms'] - $totalRooms;
            $newTotalRooms = $roomData['total_rooms'] - $totalRooms;
    
            // Ensure that the available and total rooms cannot go below zero
            if ($newAvailableRooms < 0) {
                $newAvailableRooms = 0;
            }
            if ($newTotalRooms < 0) {
                $newTotalRooms = 0;
            }
    
            // Now update the hotel_rooms table with the new values
            $updateSql = "
                UPDATE hotel_rooms
                SET available_rooms = :newAvailableRooms,
                    total_rooms = :newTotalRooms
                WHERE hotel_id = :hotelId AND room_type = :roomType
            ";
    
            $stmt = $this->db->prepare($updateSql);
            $stmt->bindParam(':newAvailableRooms', $newAvailableRooms, PDO::PARAM_INT);
            $stmt->bindParam(':newTotalRooms', $newTotalRooms, PDO::PARAM_INT);
            $stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
            $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            // Handle case where no room data was found
            throw new Exception("Room data not found for hotel_id: $hotelId and room_type: $roomType");
        }
    }
    

}
