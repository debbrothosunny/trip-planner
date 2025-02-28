<?php

namespace App\Models;

use App\Core\Database;
use mysqli;
use PDO;  
class TripParticipant {
    private $conn; 
    private $db;
    private $table_name = "trip_participants";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addParticipant($trip_id, $user_id) {
        $query = "INSERT INTO " . $this->table_name . " (trip_id, user_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $trip_id, $user_id);
        return $stmt->execute();
    }

    public function getTripsByUser($user_id)
{
    $stmt = $this->db->prepare("
        SELECT t.id, t.name, t.start_date, t.end_date, t.budget 
        FROM trips t 
        JOIN trip_participants tp ON t.id = tp.trip_id
        WHERE tp.user_id = ? AND tp.status = 'accepted'
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

