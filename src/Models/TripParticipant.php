<?php

namespace App\Models;

use PDO;
use PDOException;
use Core\Database; // Import the Database class

class TripParticipant {
    private $db;
 
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); // Singleton DB connection
    }

    // Fetch all trips for the user (i.e., participant can see all trips)
    public function getAllTripsForParticipant($userId) {
        $stmt = $this->db->prepare("
            SELECT t.id AS trip_id, t.name AS trip_name, t.start_date, t.end_date, t.budget, 
                   COALESCE(tp.status, 'pending') AS status, tp.responded_at
            FROM trips t
            LEFT JOIN trip_participants tp ON tp.trip_id = t.id AND tp.user_id = :user_id
            ORDER BY t.start_date ASC
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
    





    /**
     * Accept or decline a trip invitation
     */
    public function updateStatus($userId, $tripId, $status) {
        $stmt = $this->db->prepare("UPDATE trip_participants SET status = :status WHERE user_id = :user_id AND trip_id = :trip_id");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTripDetails($tripId) {
        try {
            // Fetch itinerary separately
            $stmt = $this->db->prepare("SELECT * FROM trip_itineraries WHERE trip_id = :trip_id ORDER BY itinerary_date ASC");
            $stmt->execute([':trip_id' => $tripId]);
            $itinerary = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Fetch accommodations separately
            $stmt = $this->db->prepare("SELECT * FROM accommodations WHERE trip_id = :trip_id");
            $stmt->execute([':trip_id' => $tripId]);
            $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Fetch transportation separately
            $stmt = $this->db->prepare("SELECT * FROM transportation WHERE trip_id = :trip_id");
            $stmt->execute([':trip_id' => $tripId]);
            $transportation = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Fetch expenses separately
            $stmt = $this->db->prepare("SELECT * FROM trip_expenses WHERE trip_id = :trip_id ORDER BY expense_date ASC");
            $stmt->execute([':trip_id' => $tripId]);
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return [
                'itinerary' => $itinerary,
                'accommodations' => $accommodations,
                'transportation' => $transportation,
                'expenses' => $expenses
            ];
        } catch (PDOException $e) {
            return [];
        }
    }
    
    

}

