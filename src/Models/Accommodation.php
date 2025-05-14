<?php

namespace App\Models;

use PDO;
use Core\Database; // Assuming you have a Database connection class

class Accommodation
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function create(
        int $userId,
        ?int $tripId,
        int $hotelId,
        int $roomTypeId, // This is the room TYPE ID
        string $checkInDate,
        string $checkOutDate,
        float $price,
        string $status = '0'
    ): string|false {
        // Find an available ROOM ID of the specified type in the given hotel
        $availableRoomId = $this->findAvailableRoomId($hotelId, $roomTypeId, $checkInDate, $checkOutDate);

        if (!$availableRoomId) {
            error_log("No available room of type {$roomTypeId} in hotel {$hotelId} for the given dates.");
            return false;
        }

        $sql = "INSERT INTO accommodations (
                        user_id, trip_id, hotel_id, room_id, check_in_date, check_out_date,
                        price, booking_date, status
                    ) VALUES (
                        :user_id, :trip_id, :hotel_id, :room_id, :check_in_date, :check_out_date,
                        :price, NOW(), :status
                    )";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $availableRoomId, PDO::PARAM_INT); // Use the found available room ID
        $stmt->bindParam(':check_in_date', $checkInDate);
        $stmt->bindParam(':check_out_date', $checkOutDate);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        } else {
            error_log("Accommodation creation failed: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    }

    private function findAvailableRoomId(int $hotelId, int $roomTypeId, string $checkInDate, string $checkOutDate): int|false
    {
        $sql = "SELECT hr.id
                    FROM hotel_rooms hr
                    WHERE hr.hotel_id = :hotel_id
                      AND hr.room_type_id = :room_type_id
                      AND hr.status = 0 -- Assuming 1 means active/available (adjust if needed)
                      AND NOT EXISTS (
                        SELECT 1
                        FROM accommodations a
                        WHERE a.room_id = hr.id
                          AND a.status IN ('0', '1') -- Assuming '0' and '1' are active booking statuses
                          AND :check_in_date < a.check_out_date
                          AND :check_out_date > a.check_in_date
                      )
                    LIMIT 1"; // Get only one available room

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);
        $stmt->bindParam(':room_type_id', $roomTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':check_in_date', $checkInDate);
        $stmt->bindParam(':check_out_date', $checkOutDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['id'] : false;
    }


    public function find(int $id): array|false
    {
        $sql = "SELECT a.*, h.name AS hotel_name, rt.name AS room_type_name
                    FROM accommodations a
                    JOIN hotels h ON a.hotel_id = h.id
                    JOIN hotel_rooms hr ON a.room_id = hr.id
                    JOIN room_types rt ON hr.room_type_id = rt.id
                    WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function findByUserId(int $userId): array
    {
        $sql = "SELECT
                    a.*,
                    h.name AS hotel_name,
                    rt.name AS room_type_name,
                    hr.price AS room_price,
                    hr.description AS room_description,
                    hr.amenities AS room_amenities,
                    (SELECT p.payment_status
                    FROM payments p
                    WHERE p.trip_id = a.trip_id -- Assuming trip_id links payments to accommodations
                    ORDER BY p.payment_date DESC
                    LIMIT 1) AS payment_status
                FROM accommodations a
                JOIN hotels h ON a.hotel_id = h.id
                JOIN hotel_rooms hr ON a.room_id = hr.id
                JOIN room_types rt ON hr.room_type_id = rt.id
                WHERE a.user_id = :user_id
                ORDER BY a.booking_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE accommodations SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }


    public function isRoomAvailableByTypeAndHotel(int $hotelId, int $roomTypeId, string $checkInDate, string $checkOutDate): bool
    {
        $sql = "SELECT COUNT(hr.id)
                    FROM hotel_rooms hr
                    WHERE hr.hotel_id = :hotel_id
                      AND hr.room_type_id = :room_type_id
                      AND hr.status = 0 -- Assuming 0 means active/available
                      AND NOT EXISTS (
                        SELECT 1
                        FROM accommodations a
                        WHERE a.room_id = hr.id
                          AND a.status IN ('0', '1') -- Assuming '0' and '1' are active booking statuses
                          AND :check_in_date < a.check_out_date
                          AND :check_out_date > a.check_in_date
                      )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':hotel_id', $hotelId, PDO::PARAM_INT);
        $stmt->bindParam(':room_type_id', $roomTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':check_in_date', $checkInDate);
        $stmt->bindParam(':check_out_date', $checkOutDate);
        $stmt->execute();

        // If the count is greater than 0, it means there is at least one available room of that type
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Deletes an accommodation booking by its ID.
     *
     * @param int $id
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM accommodations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Retrieves all accommodation bookings.
     *
     * @return array An array of all accommodation data as associative arrays.
     */
    public function findAll(): array
    {
        $sql = "SELECT a.*, h.name AS hotel_name, rt.name AS room_type_name
                    FROM accommodations a
                    JOIN hotels h ON a.hotel_id = h.id
                    JOIN hotel_rooms hr ON a.room_id = hr.id
                    JOIN room_types rt ON hr.room_type_id = rt.id
                    ORDER BY booking_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves accommodation bookings with associated user, hotel, and room data.
     *
     * @param int|null $userId Optional user ID to filter by.
     * @return array An array of accommodation data with related details.
     */
    public function findWithDetails(?int $userId = null): array
    {
        $sql = "SELECT
                        a.id AS accommodation_id,
                        a.check_in_date,
                        a.check_out_date,
                        a.price AS total_price, // Assuming 'price' in accommodations is the total
                        a.booking_date,
                        a.status,
                        u.id AS user_id,
                        u.name AS user_name,
                        u.email AS user_email,
                        h.id AS hotel_id,
                        h.name AS hotel_name,
                        h.address AS hotel_address, // Added address
                        rt.id AS room_type_id,
                        rt.name AS room_type_name,
                        hr.id AS room_id,
                        hr.price AS room_price
                    FROM accommodations a
                    JOIN users u ON a.user_id = u.id
                    JOIN hotels h ON a.hotel_id = h.id
                    JOIN hotel_rooms hr ON a.room_id = hr.id
                    JOIN room_types rt ON hr.room_type_id = rt.id
                    " . ($userId !== null ? "WHERE a.user_id = :user_id " : "") . "
                    ORDER BY a.booking_date DESC";

        $stmt = $this->db->prepare($sql);
        if ($userId !== null) {
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds a single accommodation record by its ID, including related hotel and room details.
     *
     * @param int $id The ID of the accommodation to retrieve.
     * @return array|false An associative array containing the accommodation details,
     * hotel name, and room type name, or false if not found.
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT
                        a.*,
                        h.name AS hotel_name,
                        rt.name AS room_type_name,
                        hr.price AS room_price
                    FROM accommodations a
                    JOIN hotels h ON a.hotel_id = h.id
                    JOIN hotel_rooms hr ON a.room_id = hr.id
                    JOIN room_types rt ON hr.room_type_id = rt.id
                    WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
   
    public function getAllPendingBookingsWithHotelRoomDetails(): array
    {
        $sql = "
            SELECT
                a.id AS accommodation_id,
                a.user_id,
                a.hotel_id,
                a.room_id,
                a.trip_id,
                a.check_in_date,
                a.check_out_date,
                a.price,
                a.booking_date,
                a.status AS accommodation_status,
                u.name AS user_name,
                h.name AS hotel_name,
                c.name AS country_name, -- Country Name
                s.name AS state_name,   -- State Name
                rt.name AS room_type
            FROM
                accommodations a
            INNER JOIN
                users u ON a.user_id = u.id
            INNER JOIN
                hotels h ON a.hotel_id = h.id
            INNER JOIN
                countries c ON h.country_id = c.id -- Join with countries table
            INNER JOIN
                states s ON h.state_id = s.id     -- Join with states table
            INNER JOIN
                hotel_rooms hr ON a.room_id = hr.id
            INNER JOIN
                room_types rt ON hr.room_type_id = rt.id
            WHERE
                a.status = 1 -- Assuming 0 means 'pending'
            ORDER BY
                a.booking_date DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function confirmAccommodation(int $accommodationId): bool
    {
        $sql = "UPDATE accommodations SET status = 1 WHERE id = :id"; // Assuming 1 means 'confirmed'
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $accommodationId, PDO::PARAM_INT);
        return $stmt->execute();
    }



    public function getPaymentDetailsByAccommodationId(int $accommodationId): ?array
    {
        $sql = "
            SELECT
                p.*
            FROM
                payments p
            WHERE
                p.trip_id = (SELECT a.trip_id FROM accommodations a WHERE a.id = :accommodation_id)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':accommodation_id', $accommodationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTripIdByAccommodationId(int $accommodationId): ?int
    {
        $sql = "SELECT trip_id FROM accommodations WHERE id = :accommodation_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':accommodation_id', $accommodationId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['trip_id'] : null;
    }


    public function markAsPaidByTripId(int $tripId): bool
    {
        $sql = "UPDATE payments SET payment_status = 'paid' WHERE trip_id = :trip_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}