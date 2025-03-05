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
            // Fetch all data in one query using JOINs or multiple queries in a single function
            $query = "
                SELECT
                    trip_itineraries.*,
                    accommodations.*,
                    transportation.*,
                    trip_expenses.*
                FROM trip_itineraries
                LEFT JOIN accommodations ON accommodations.trip_id = trip_itineraries.trip_id
                LEFT JOIN transportation ON transportation.trip_id = trip_itineraries.trip_id
                LEFT JOIN trip_expenses ON trip_expenses.trip_id = trip_itineraries.trip_id
                WHERE trip_itineraries.trip_id = :trip_id
                ORDER BY trip_itineraries.itinerary_date ASC, trip_expenses.expense_date ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':trip_id' => $tripId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Organize the fetched data into the required format
            $itinerary = [];
            $accommodations = [];
            $transportation = [];
            $expenses = [];
    
            // Split the result into respective arrays (this will depend on how the data is returned)
            foreach ($result as $row) {
                // Itinerary data
                $itinerary[] = [
                    'day_title' => $row['day_title'],
                    'description' => $row['description'],
                    'itinerary_date' => $row['itinerary_date']
                   
                ];
    
                // Accommodations data
                $accommodations[] = [
                    'name' => $row['name'],
                    'location' => $row['location'],
                    'check_in_time' => $row['check_in_time'],
                    'check_out_time' => $row['check_out_time'],
                    'price' => $row['price'],
                ];
    
                // Transportation data
                $transportation[] = [
                    'type' => $row['type'],
                    'company_name' => $row['company_name'],
                    'departure_location' => $row['departure_location'],
                    'arrival_location' => $row['arrival_location'],
                    'departure_date' => isset($row['departure_date']) ? $row['departure_date'] : null,  // Ensure this key exists
                    'arrival_date' => isset($row['arrival_date']) ? $row['arrival_date'] : null,  // Ensure this key exists
                    'booking_reference' => $row['booking_reference'],
                    'amount' => $row['amount'],
                ];
    
                // Expenses data
                $expenses[] = [
                    'description' => $row['description'],
                    'category' => $row['category'],
                    'amount' => $row['amount'],
                    'expense_date' => $row['expense_date']
                ];
            }
    
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

