<?php

namespace App\Models;

use PDO;
use PDOException;
use Core\Database; // Database connection singleton

class Hotel {
    private $db;
    private $table = "hotels";

    public function __construct($db) {
        $this->db = $db;
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
    public function createHotel($country_id, $state_id, $name, $address, $description, $star_rating, $status) {
        $stmt = $this->db->prepare("
            INSERT INTO hotels (country_id, state_id, name, address, description, star_rating, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$country_id, $state_id, $name, $address, $description, $star_rating, $status]);
    }

    // Update an existing hotel
    public function updateHotel( $country_id, $state_id, $name, $address, $description, $star_rating, $status, $id) {
        $stmt = $this->db->prepare("
            UPDATE hotels
            SET country_id = ?,
                state_id = ?,
                name = ?,
                address = ?,
                description = ?,
                star_rating = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$country_id, $state_id, $name, $address, $description, $star_rating, $status, $id]);
    }

    // Delete a hotel by ID
    public function deleteHotel($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }




    public function getAllHotelsWithLimit(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("
            SELECT
                h.*,
                c.name AS country_name,
                s.name AS state_name
            FROM
                hotels h
            LEFT JOIN
                countries c ON h.country_id = c.id
            LEFT JOIN
                states s ON h.state_id = s.id
            ORDER BY h.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAllHotels(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
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






    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function findAllActive(): array
    {
        $sql = "
            SELECT
                h.id,
                h.name,
                h.address,
                h.description,
                h.star_rating,
                h.status,
                c.name AS country_name,
                s.name AS state_name
            FROM
                hotels h
            INNER JOIN
                countries c ON h.country_id = c.id
            INNER JOIN
                states s ON h.state_id = s.id
            WHERE
                h.status = 0
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search function
    public function searchHotelsByNameOrLocation($searchTerm) {
        $this->db->query("SELECT hr.*, h.name AS hotel_name, h.location AS hotel_location
                            FROM hotel_rooms hr
                            JOIN hotels h ON hr.hotel_id = h.id
                            WHERE h.name LIKE :term OR h.location LIKE :term OR hr.room_type LIKE :term");
        $this->db->bind(':term', '%' . $searchTerm . '%');
        return $this->db->fetchAll(); // Assuming you have a fetchAll() method
    }





    public function findAllCountries(): array
    {
        $sql = "SELECT id, name FROM countries WHERE status = 0 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findStatesByCountryId(int $countryId): array
    {
        $sql = "SELECT id, name FROM states WHERE country_id = :country_id AND status = 0 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findHotelsByCountryAndState(int $countryId, int $stateId): array
    {
        $sql = "SELECT id, name FROM hotels WHERE country_id = :country_id AND state_id = :state_id AND status = 0 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        $stmt->bindParam(':state_id', $stateId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    
}
