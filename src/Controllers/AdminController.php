<?php
namespace App\Controllers;

use PDO;
use Core\Database; // ✅ Correct the namespace

class AdminController {
    private $db;



    public function __construct() {
        $database = Database::getInstance(); // Use the singleton instance
        $this->db = $database->getConnection(); // Get the connection
    }

    // ✅ Dashboard: View System Analytics & Users
    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /login");
            exit();
        }
    
        // Fetch system analytics
        $analytics = $this->systemAnalytics();
    
        // Fetch all registered users
        $stmt = $this->db->query("SELECT id, name, email, role FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch trip names from the trips table (using the 'name' column)
        $tripStmt = $this->db->query("SELECT id, name FROM trips");
        $trips = $tripStmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch participants for each trip
        $participants = [];
        foreach ($trips as $trip) {
            $tripId = $trip['id'];
            $participantStmt = $this->db->prepare("SELECT users.id AS user_id, users.name AS user_name, trip_participants.status
                                                  FROM trip_participants
                                                  JOIN users ON trip_participants.user_id = users.id
                                                  WHERE trip_participants.trip_id = :trip_id");
            $participantStmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $participantStmt->execute();
            $participants[$tripId] = $participantStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        // Pass data to the view
        $data = [
            'total_users' => $analytics['total_users'],
            'total_trips' => $analytics['total_trips'],
            'users' => $users,
            'trips' => $trips,
            'participants' => $participants // Pass the participants data here
        ];
    
        // Load the dashboard view
        $dashboardViewPath = __DIR__ . '/../../resources/views/admin/dashboard.php';
        if (file_exists($dashboardViewPath)) {
            include $dashboardViewPath;
        } else {
            echo "Dashboard view not found!";
        }
    }
    



    public function viewUserTrips($id) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /login");
            exit();
        }
    
        // Fetch the user's details by ID
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$user) {
            echo "User not found!";
            return;
        }
    
        // Fetch trips and related accommodations associated with the user
        $tripStmt = $this->db->prepare("
            SELECT 
                trips.id AS trip_id, 
                trips.name AS trip_name, 
                trips.start_date, 
                trips.end_date, 
                trips.budget, 
                accommodations.name AS accommodation_name, 
                accommodations.location, 
                accommodations.price, 
                accommodations.check_in_time, 
                accommodations.check_out_time
            FROM trips 
            LEFT JOIN accommodations ON trips.id = accommodations.trip_id 
            WHERE trips.user_id = ?");
        $tripStmt->execute([$id]);
        $userTrips = $tripStmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Pass data to the view
        $data = [
            'user' => $user,
            'userTrips' => $userTrips,
        ];
    
        // Load the view to display the user's trips and accommodations
        $viewPath = __DIR__ . '/../../resources/views/admin/user_trips.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View for user trips not found!";
        }
    }
    
    
    
    
    
    

    // ✅ Fetch Users List

    // ✅ Delete User
    public function deleteUser($id) {
        // Prepare and execute the delete query
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        // ✅ Redirect back to the admin dashboard
        header("Location: /admin/dashboard");
        exit();
    }

    // ✅ Monitor System Analytics
    public function systemAnalytics() {
        $userCountStmt = $this->db->query("SELECT COUNT(*) AS count FROM users");
        $userCount = $userCountStmt->fetch(PDO::FETCH_ASSOC)['count'];

        $tripCountStmt = $this->db->query("SELECT COUNT(*) AS count FROM trips");
        $tripCount = $tripCountStmt->fetch(PDO::FETCH_ASSOC)['count'];

        return ['total_users' => $userCount, 'total_trips' => $tripCount];
    }

    
}
