<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Trip
{
    private $db;
    protected $table = 'trips'; 

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Creates a new trip.
     *
     * @param string $name
     * @param int $user_id
     * @param string $start_date
     * @param string $end_date
     * @param float $budget
     * @return int The ID of the newly created trip.
     * @throws PDOException If there is a database error.
     */

     
     public function createTrip(string $name, int $user_id, string $start_date, string $end_date, float $budget, string $trip_style, string $destination): int
    {
        $query = "INSERT INTO " . $this->table . " (name, user_id, start_date, end_date, budget, trip_style, destination) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $user_id);
        $stmt->bindValue(3, $start_date);
        $stmt->bindValue(4, $end_date);
        $stmt->bindValue(5, $budget);
        $stmt->bindValue(6, $trip_style);
        $stmt->bindValue(7, $destination);

        if ($stmt->execute()) {
            return (int) $this->db->lastInsertId();
        } else {
            throw new PDOException('Failed to insert trip');
        }
    }


    /**
     * Retrieves trips by user ID.
     *
     * @param int $userId
     * @return array An array of trips, or an empty array if no trips are found.
     * @throws PDOException
     */
    public function getTripsByUserId(int $userId): array
    {
        $query = "SELECT * FROM trips WHERE user_id = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        try{
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Get Trips by User ID): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }
    }

    /**
     * Retrieves a trip by its ID.
     *
     * @param int $id
     * @return array|null An array representing the trip, or null if not found.
     * @throws PDOException
     */
    public function getTripById(int $id): ?array
    {
        $query = "SELECT * FROM trips WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            $trip = $stmt->fetch(PDO::FETCH_ASSOC);
            return $trip ?: null; // Return null if no trip is found.  The null coalescing operator is good, but this is more explicit
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Get Trip by ID): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }

    }

    /**
     * Updates an existing trip.
     *
     * @param int $id
     * @param string $name
     * @param string $start_date
     * @param string $end_date
     * @param float $budget
     * @return int The number of affected rows.
     * @throws PDOException
     */
    public function updateTrip(int $id, string $name, string $start_date, string $end_date, float $budget): int
    {
        $query = "UPDATE trips SET name = ?, start_date = ?, end_date = ?, budget = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $start_date);
        $stmt->bindValue(3, $end_date);
        $stmt->bindValue(4, $budget);
        $stmt->bindValue(5, $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->rowCount(); // Return the number of affected rows
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Update Trip): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }
    }

    /**
     * Deletes a trip by its ID.
     *
     * @param int $id
     * @return bool True on success, false on failure.  Should this be changed to return the number of rows affected?
     * @throws PDOException
     */
    public function deleteTrip(int $id): bool
    {
        $sql = "DELETE FROM trips WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->rowCount() > 0; // Check if any rows were affected
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Delete Trip): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }
    }

    /**
      * Fetches all trips.
      *
      * @return array An array of all trips.
      * @throws PDOException
      */
      public function getAllTrips(): array
      {
          $sql = "SELECT id, name AS trip_name, start_date, end_date, budget, trip_style, destination FROM " . $this->table;
          $stmt = $this->db->prepare($sql);
          try {
              $stmt->execute();
              return $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
              throw new PDOException("Database Error (Get All Trips): " . $e->getMessage());
          } finally {
              $stmt->closeCursor();
          }
      }

     /**
      * Retrieves the creator of a trip.
      *
      * @param int $tripId The ID of the trip.
      * @return array|null An array containing the creator's name and email, or null if not found.
      * @throws PDOException
      */
      public function getTripCreator(int $tripId): ?array
    {
        $sql = "SELECT u.name AS creator_name, 
                       u.email AS creator_email, 
                       u.country, 
                       u.city,
                       u.id AS creator_id, -- Make sure to select the user's ID
                       u.profile_photo -- Select the profile photo
                FROM trips t
                JOIN users u ON t.user_id = u.id
                WHERE t.id = :tripId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tripId', $tripId, PDO::PARAM_INT);
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Get Trip Creator): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }
    }


     public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM trips WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // For recommendations
    public function getLastAcceptedTripsForUser(int $userId, int $limit = 2): array
    {
        $sql = "SELECT t.trip_style, t.destination
                FROM trips t
                INNER JOIN trip_participants tp ON t.id = tp.trip_id
                WHERE tp.user_id = :userId
                  AND tp.status = 'accepted'
                ORDER BY tp.created_at DESC -- Order by acceptance date
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Database Error (Get Last Accepted Trips): " . $e->getMessage());
        } finally {
            $stmt->closeCursor();
        }
    }

    public function getUniqueTripStyles()
    {
        $sql = "SELECT DISTINCT trip_style FROM trips ORDER BY trip_style ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch only the 'trip_style' column
    }

    public function getUserExpiredPublicTrips(int $userId): array
    {
        $currentDate = date('Y-m-d');
        $sql = "SELECT id, name AS trip_name, start_date, end_date, budget
                FROM trips
                WHERE user_id = :user_id AND end_date < :current_date
                ORDER BY end_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':current_date', $currentDate, PDO::PARAM_STR);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error (Trip::getUserExpiredPublicTrips): " . $e->getMessage());
            return [];
        } finally {
            $stmt->closeCursor();
        }
    }

    public function getTripItineraries(int $tripId): array
    {
        $sql = "SELECT id, trip_id, day_title, location, description, itinerary_date
                FROM trip_itineraries
                WHERE trip_id = :trip_id
                ORDER BY itinerary_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error (Trip::getTripItineraries): " . $e->getMessage());
            return [];
        } finally {
            $stmt->closeCursor();
        }
    }

}

