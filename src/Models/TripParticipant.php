<?php

namespace App\Models;

use PDO;
use PDOException;
use Core\Database; // Import the Database class

class TripParticipant {
    private $db;

    private $conn; // This will hold the PDO connection
 
    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch all trips for the user (i.e., participant can see all trips)
    public function getAllTripsForParticipant($userId) {
        $stmt = $this->db->prepare("
            SELECT t.id AS trip_id, t.name AS trip_name, t.start_date, t.end_date, t.budget, 
                   COALESCE(tp.status, 'pending') AS status, tp.responded_at,
                   u.name AS creator_name, u.email AS creator_email
            FROM trips t
            LEFT JOIN trip_participants tp ON tp.trip_id = t.id AND tp.user_id = :user_id
            LEFT JOIN users u ON t.user_id = u.id  -- Join users table to get creator details
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
            // Fetch itinerary
            $stmt = $this->db->prepare("SELECT * FROM trip_itineraries WHERE trip_id = :trip_id ORDER BY itinerary_date ASC");
            $stmt->execute([':trip_id' => $tripId]);
            $itinerary = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
         // Fetch accommodations of users in this trip
            $stmt = $this->db->prepare("
            SELECT 
                u.name AS user_name,
                a.room_type,
                a.check_in_date,
                a.check_out_date,
                hr.description AS room_description
            FROM accommodations a
            INNER JOIN users u ON u.id = a.user_id
            INNER JOIN hotel_rooms hr ON hr.id = a.hotel_id  -- Assuming hotel_rooms has an 'id' column
            WHERE a.trip_id = :trip_id
            ");
            $stmt->execute([':trip_id' => $tripId]);
            $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
            // Fetch transportation
            $stmt = $this->db->prepare("SELECT * FROM transportation WHERE trip_id = :trip_id");
            $stmt->execute([':trip_id' => $tripId]);
            $transportation = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Fetch expenses
            $stmt = $this->db->prepare("SELECT * FROM trip_expenses WHERE trip_id = :trip_id ORDER BY expense_date ASC");
            $stmt->execute([':trip_id' => $tripId]);
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Fetch count of accepted participants
            $stmt = $this->db->prepare("SELECT COUNT(*) AS accepted_count FROM trip_participants WHERE trip_id = :trip_id AND status = 'accepted'");
            $stmt->execute([':trip_id' => $tripId]);
            $acceptedParticipants = $stmt->fetch(PDO::FETCH_ASSOC)['accepted_count'] ?? 0;
    
            return [
                'itinerary' => $itinerary,
                'accommodations' => $accommodations,
                'transportation' => $transportation,
                'expenses' => $expenses,
                'accepted_participants' => $acceptedParticipants
            ];
        } catch (PDOException $e) {
            error_log("Trip detail error: " . $e->getMessage());
            return [];
        }
    }
    
  
    


   // Fetch participant status by tripId and userId
   public function getParticipantByTripId($tripId, $userId) {
    $query = "SELECT * FROM trip_participants WHERE trip_id = ? AND user_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$tripId, $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Returns an associative array or false
    }



    
    

}