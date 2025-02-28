<?php
namespace App\Controllers;
use PDO;
use Core\Database; // ✅ Correct the namespace
class ParticipantController {
    private $db;

    public function __construct() {
        $database = Database::getInstance(); // Use the singleton instance
        $this->db = $database->getConnection(); // Get the connection
    }

    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'participant') {
            header("Location: /login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        // Fetch trip invitations (Pending, Accepted, Declined)
        $stmt = $this->db->prepare("
            SELECT ti.id, t.name AS trip_name, ti.status 
            FROM trip_participants ti
            JOIN trips t ON ti.trip_id = t.id
            WHERE ti.user_id = ?
        ");
        $stmt->execute([$userId]);
        $tripInvitations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch joined trips (Accepted trips)
        $stmt = $this->db->prepare("
            SELECT t.id AS trip_id, t.name AS trip_name, t.start_date, t.end_date, t.budget
            FROM trips t
            JOIN trip_participants ti ON ti.trip_id = t.id
            WHERE ti.user_id = ? AND ti.status = 'accepted'
        ");
        $stmt->execute([$userId]);
        $joinedTrips = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pass data to the view
        $data = [
            'tripInvitations' => $tripInvitations,
            'joinedTrips' => $joinedTrips,
        ];

        // Load participant dashboard view
        $viewPath = __DIR__ . '/../../resources/views/participant/dashboard.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Participant dashboard view not found!";
        }
    }
}
