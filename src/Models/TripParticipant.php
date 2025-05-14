<?php

namespace App\Models;

use PDO;
use PDOException;


class TripParticipant {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch all trips for the user (i.e., participant can see all trips)
    public function getAllTripsForParticipant($userId) {
        $sql = "SELECT t.id AS trip_id, t.name AS trip_name, t.start_date, t.end_date, t.budget, COALESCE(tp.status, 'pending') AS status,  u.name AS creator_name, u.email AS creator_email
                FROM trips t
                LEFT JOIN trip_participants tp ON tp.trip_id = t.id AND tp.user_id = :user_id
                LEFT JOIN users u ON t.user_id = u.id
                GROUP BY t.id
                ORDER BY t.start_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getPaginatedTripsForParticipant($userId, $limit, $offset, $tripStyleFilter = null, $minBudget = null, $maxBudget = null) {
        $sql = "SELECT t.id AS trip_id, t.name AS trip_name, t.start_date, t.end_date, t.budget, t.trip_style, COALESCE(tp.status, 'pending') AS status, u.name AS creator_name, u.email AS creator_email
                FROM trips t
                LEFT JOIN trip_participants tp ON tp.trip_id = t.id AND tp.user_id = :user_id
                LEFT JOIN users u ON t.user_id = u.id
                WHERE 1=1";

        if ($tripStyleFilter !== null && $tripStyleFilter !== '') {
            $sql .= " AND t.trip_style LIKE :trip_style";
        }
        if ($minBudget !== null && is_numeric($minBudget)) {
            $sql .= " AND t.budget >= :min_budget";
        }
        if ($maxBudget !== null && is_numeric($maxBudget)) {
            $sql .= " AND t.budget <= :max_budget";
        }

        $sql .= " GROUP BY t.id
                  ORDER BY t.start_date ASC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($tripStyleFilter !== null && $tripStyleFilter !== '') {
            $stmt->bindValue(':trip_style', '%' . $tripStyleFilter . '%', PDO::PARAM_STR);
        }
        if ($minBudget !== null && is_numeric($minBudget)) {
            $stmt->bindParam(':min_budget', $minBudget, PDO::PARAM_INT);
        }
        if ($maxBudget !== null && is_numeric($maxBudget)) {
            $stmt->bindParam(':max_budget', $maxBudget, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTotalTripsForParticipant($userId, $tripStyleFilter = null, $minBudget = null, $maxBudget = null) {
        $sql = "SELECT COUNT(DISTINCT t.id)
                FROM trips t
                LEFT JOIN trip_participants tp ON tp.trip_id = t.id AND tp.user_id = :user_id
                WHERE 1=1";

        if ($tripStyleFilter !== null && $tripStyleFilter !== '') {
            $sql .= " AND t.trip_style LIKE :trip_style";
        }
        if ($minBudget !== null && is_numeric($minBudget)) {
            $sql .= " AND t.budget >= :min_budget";
        }
        if ($maxBudget !== null && is_numeric($maxBudget)) {
            $sql .= " AND t.budget <= :max_budget";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($tripStyleFilter !== null && $tripStyleFilter !== '') {
            $stmt->bindValue(':trip_style', '%' . $tripStyleFilter . '%', PDO::PARAM_STR);
        }
        if ($minBudget !== null && is_numeric($minBudget)) {
            $stmt->bindParam(':min_budget', $minBudget, PDO::PARAM_INT);
        }
        if ($maxBudget !== null && is_numeric($maxBudget)) {
            $stmt->bindParam(':max_budget', $maxBudget, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }


    public function getTotalAcceptedParticipants(int $tripId): int
    {
        $sql = "SELECT COUNT(*) FROM trip_participants WHERE trip_id = :tripId AND status = 'accepted'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tripId', $tripId, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error fetching accepted participant count: " . $e->getMessage());
            return 0;
        } finally {
            $stmt->closeCursor();
        }
    }


    /**
     * Accept or decline a trip invitation
     */
    public function updateStatus($userId, $tripId, $status) {
        $stmt = $this->db->prepare("UPDATE trip_participants SET status = :status, responded_at = NOW(), updated_at = NOW() WHERE user_id = :user_id AND trip_id = :trip_id");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function getTripDetails($tripId)
    {
        try {
            // Fetch itinerary
            $sql_itinerary = "SELECT * FROM trip_itineraries WHERE trip_id = :trip_id ORDER BY itinerary_date ASC";
            error_log("Executing Itinerary SQL: " . $sql_itinerary . " with tripId: " . $tripId);
            $stmt = $this->db->prepare($sql_itinerary);
            $stmt->execute([':trip_id' => $tripId]);
            $itinerary = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Fetched Itinerary: " . print_r($itinerary, true));
    
            // Fetch accommodations with hotel name, and room details
            $sql_accommodations = "SELECT 
                h.name AS hotel_name,
                c.name AS country_name,
                s.name AS state_name,
                rt.name AS room_type_name,
                hr.capacity AS room_capacity,
                hr.description AS room_description,
                hr.amenities AS room_amenities,
                a.check_in_date,
                a.check_out_date
            FROM accommodations a
            INNER JOIN hotels h ON a.hotel_id = h.id
            INNER JOIN hotel_rooms hr ON a.hotel_id = hr.hotel_id AND a.room_id = hr.id
            INNER JOIN room_types rt ON hr.room_type_id = rt.id
            INNER JOIN countries c ON h.country_id = c.id  -- Join to get country name
            INNER JOIN states s ON h.state_id = s.id      -- Join to get state name
            WHERE a.trip_id = :trip_id";
    
            error_log("Executing Accommodations SQL: " . $sql_accommodations . " with tripId: " . $tripId);
            $stmt = $this->db->prepare($sql_accommodations);
            $stmt->execute([':trip_id' => $tripId]);
            $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Fetched Accommodations: " . print_r($accommodations, true));
    
            // Fetch transportations
            $sql_transportations = "SELECT * FROM transportations WHERE trip_id = :trip_id";
            error_log("Executing Transportations SQL: " . $sql_transportations . " with tripId: " . $tripId);
            $stmt = $this->db->prepare($sql_transportations);
            $stmt->execute([':trip_id' => $tripId]);
            $transportations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Fetched Transportations: " . print_r($transportations, true));
    
            // Fetch expenses
            $sql_expenses = "SELECT * FROM trip_expenses WHERE trip_id = :trip_id ORDER BY expense_date ASC";
            error_log("Executing Expenses SQL: " . $sql_expenses . " with tripId: " . $tripId);
            $stmt = $this->db->prepare($sql_expenses);
            $stmt->execute([':trip_id' => $tripId]);
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Fetched Expenses: " . print_r($expenses, true));
    
            // Fetch accepted participants count
            $sql_participants = "SELECT COUNT(*) AS accepted_count FROM trip_participants WHERE trip_id = :trip_id AND status = 'accepted'";
            error_log("Executing Participants SQL: " . $sql_participants . " with tripId: " . $tripId);
            $stmt = $this->db->prepare($sql_participants);
            $stmt->execute([':trip_id' => $tripId]);
            $acceptedParticipants = $stmt->fetch(PDO::FETCH_ASSOC)['accepted_count'] ?? 0;
            error_log("Accepted Participants: " . print_r($acceptedParticipants, true));
    
            return [
                'itinerary' => $itinerary,
                'accommodations' => $accommodations,
                'transportation' => $transportations,
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



    public function getParticipantNameByUserId($userId) {
        $query = "SELECT name FROM users WHERE id = ?"; // Assuming 'users' table has 'id' and 'name'
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['name'];
        } else {
            return null; // Or handle the case where the user is not found
        }
    }


    public function getParticipation(int $userId, int $tripId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM trip_participants WHERE user_id = :user_id AND trip_id = :trip_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the status of a user's participation in a trip.
     *
     * @param int $userId The ID of the user.
     * @param int $tripId The ID of the trip.
     * @param string $status The new status ('pending', 'accepted', 'declined', etc.).
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateParticipationStatus(int $userId, int $tripId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE trip_participants SET status = :status, updated_at = NOW() WHERE user_id = :user_id AND trip_id = :trip_id");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function getUserArchivedTrips(int $userId, int $limit, int $offset)
    {
        $statuses = ['declined']; // Only look for 'declined'
        $namedPlaceholders = implode(',', array_map(function ($key) {
            return ":status_$key";
        }, array_keys($statuses)));
        $sql = "SELECT t.*, tp.status AS participant_status
                FROM trips t
                JOIN trip_participants tp ON t.id = tp.trip_id
                WHERE tp.user_id = :userId AND tp.status IN ($namedPlaceholders)
                ORDER BY t.start_date DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        foreach ($statuses as $key => $status) {
            $stmt->bindParam(":status_$key", $status, PDO::PARAM_STR);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalArchivedTripsForParticipant(int $userId): int
    {
        $statuses = ['declined']; // Only look for 'declined'
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $sql = "SELECT COUNT(t.id)
                FROM trips t
                JOIN trip_participants tp ON t.id = tp.trip_id
                WHERE tp.user_id = ? AND tp.status IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge([$userId], $statuses));
        return $stmt->fetchColumn();
    }


    public function getUserActiveTrips(int $userId, int $limit, int $offset)
    {
        $sql = "SELECT t.*, tp.status AS participant_status
                FROM trips t
                JOIN trip_participants tp ON t.id = tp.trip_id
                WHERE tp.user_id = ? AND tp.status IN ('accepted', 'pending')
                ORDER BY t.start_date ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalActiveTripsForParticipant(int $userId): int
    {
        $sql = "SELECT COUNT(t.id)
                FROM trips t
                JOIN trip_participants tp ON t.id = tp.trip_id
                WHERE tp.user_id = ? AND tp.status IN ('accepted', 'pending')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

     // Add this function to your TripParticipant model
     public function getParticipantStatus(int $tripId, int $userId): ?string
     {
         $sql = "SELECT status FROM trip_participants WHERE trip_id = :trip_id AND user_id = :user_id";
         $stmt = $this->db->prepare($sql);
         $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
         $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
         $stmt->execute();
         $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
         if ($result) {
             return $result['status'];
         } else {
             return null;
         }
     }


  
}