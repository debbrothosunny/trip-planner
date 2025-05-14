<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
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
    public function createRoom($hotel_id, $room_type_id, $capacity, $price, $description, $total_rooms, $available_rooms, $status, $amenities) {
        $stmt = $this->db->prepare("
            INSERT INTO hotel_rooms (
                hotel_id,
                room_type_id,
                capacity,
                price,
                description,
                total_rooms,
                available_rooms,
                status,
                amenities,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$hotel_id, $room_type_id, $capacity, $price, $description, $total_rooms, $available_rooms, $status, $amenities]);
    }

    // Update an existing room
    public function updateRoom($id, $hotel_id, $room_type_id, $capacity, $price, $description, $total_rooms, $available_rooms, $status, $amenities) {
        $stmt = $this->db->prepare("
            UPDATE hotel_rooms
            SET hotel_id = ?,
                room_type_id = ?,
                capacity = ?,
                price = ?,
                description = ?,
                total_rooms = ?,
                available_rooms = ?,
                status = ?,
                amenities = ?
            WHERE id = ?
        ");
        return $stmt->execute([$hotel_id, $room_type_id, $capacity, $price, $description, $total_rooms, $available_rooms, $status, $amenities, $id]);
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



    public function decreaseAvailableAndTotalRooms(int $hotelId, int $roomId, int $bookedRooms): bool
    {
        $sql = "
            UPDATE hotel_rooms
            SET
                available_rooms = available_rooms - :booked,
                total_rooms = total_rooms - :booked
            WHERE
                hotel_id = :hotel_id AND id = :room_id AND available_rooms >= :booked AND total_rooms >= :booked
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindParam(':booked', $bookedRooms, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getHotelRoomDetails(int $roomId): array|false
    {
        $sql = "SELECT * FROM hotel_rooms WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $roomId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll(): array
    {
        $sql = "SELECT hr.id, rt.name AS room_type, hr.price, hr.amenities
                FROM hotel_rooms hr
                JOIN room_types rt ON hr.room_type_id = rt.id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all hotel rooms: " . $e->getMessage());
            return [];
        }
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding hotel room by ID: " . $e->getMessage());
            return false;
        }
    }
    // Search function
    public function searchRoomsByName($searchTerm) {
        $this->db->query("SELECT hr.*, h.name AS hotel_name 
                           FROM hotel_rooms hr
                           JOIN hotels h ON hr.hotel_id = h.id
                           WHERE h.name LIKE :term OR hr.room_type LIKE :term");
        $this->db->bind(':term', '%' . $searchTerm . '%');
        return $this->db->fetchAll(); // Assuming you have a fetchAll() method
    }





    public function findRoomTypesByHotelId(int $hotelId): array
    {
        $sql = "SELECT DISTINCT
                        hr.room_type_id AS id,
                        rt.name,
                        hr.price AS default_price,
                        hr.description,
                        hr.amenities,
                        hr.total_rooms,
                        hr.available_rooms
                FROM hotel_rooms hr
                JOIN room_types rt ON hr.room_type_id = rt.id
                WHERE hr.hotel_id = :hotel_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPriceByHotelAndType(int $hotelId, int $roomTypeId): ?array
    {
        $sql = "SELECT price
                FROM hotel_rooms
                WHERE hotel_id = :hotel_id
                  AND room_type_id = :room_type_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);
        $stmt->bindParam(':room_type_id', $roomTypeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}