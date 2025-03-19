<?php
namespace App\Controllers;

use PDO;
use PDOException;
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
            $participantStmt = $this->db->prepare("
            SELECT users.id AS user_id, 
                   users.name AS user_name, 
                   trip_participants.status AS trip_status, 
                   payments.payment_status, 
                   payments.amount  -- ✅ Fetch payment amount
            FROM trip_participants
            JOIN users ON trip_participants.user_id = users.id
            LEFT JOIN payments ON payments.user_id = users.id AND payments.trip_id = :trip_id
            WHERE trip_participants.trip_id = :trip_id
        ");
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



    // ✅ Accept Participant Payment

    public function acceptPayment($tripId, $userId) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /login");
            exit();
        }
    
        try {
            // Start a transaction to ensure atomicity
            $this->db->beginTransaction();
    
            // Update the payment status in the payments table to 'Completed'
            $stmt = $this->db->prepare("
                UPDATE payments
                SET payment_status = 'completed'
                WHERE user_id = :user_id AND trip_id = :trip_id AND payment_status = 'pending'
            ");
            
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Optionally: Insert a record in the payments table for this transaction (if required)
            // For now, it's assumed the payment already exists, and we are only updating its status.
    
            // Commit the transaction
            $this->db->commit();
    
            // Redirect back to the dashboard with a success message
            header("Location: /admin/dashboard");
            exit();
        } catch (PDOException $e) {
            // Rollback the transaction in case of error
            $this->db->rollBack();
    
            // Optionally log the error
            header("Location: /admin/dashboard?payment_error=1");
            exit();
        }
    }
    




    
}