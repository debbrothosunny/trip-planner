<?php

namespace App\Models;

use PDO;
use Core\Database; // Database connection singleton

class Hotel {
    private $db;
    private $table = "hotels";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Fetch all hotels
    public function getAllHotels() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a hotel by ID
    public function getHotelById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new hotel
    public function createHotel($name, $location, $description) {
        $query = "INSERT INTO {$this->table} (name, location, description, created_at) 
                  VALUES (:name, :location, :description, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Update an existing hotel
    public function updateHotel($id, $name, $location, $description) {
        $query = "UPDATE {$this->table} SET name = :name, location = :location, description = :description, updated_at = NOW() 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Delete a hotel by ID
    public function deleteHotel($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }




    // Fetch all locations
    public function getAllLocations() {
        $sql = "SELECT DISTINCT location FROM hotels";  // Assuming `location` is a column in `hotels` table
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getHotelsByLocation($location) {
        $location = trim($location);
        $sql = "SELECT id, name FROM hotels WHERE TRIM(location) = :location";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoomsByHotel($hotelId) {
        // SQL query to fetch room details based on hotel_id
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


    
    
    
    



    
}
