<?php
namespace App\Controllers;
use PDO;
use PDOException;
use App\Models\TripParticipant; 
use Core\Database;// ✅ Correct the namespace
class ParticipantController {
    private $db;

    public function __construct() {
        $database = Database::getInstance(); // Use the singleton instance
        $this->db = $database->getConnection(); // Get the connection
    }
    public function dashboard() {
        session_start();
    
        // Check if the user is logged in and is a participant
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'participant') {
            header("Location: /"); // Or redirect to a relevant page
            exit();
        }
    
        $userId = $_SESSION['user_id'];
    
        // Create an instance of the TripParticipant model
        $tripParticipantModel = new TripParticipant();
    
        // Get all trips for the participant
        $trips = $tripParticipantModel->getAllTripsForParticipant($userId);
    
        // Check for upcoming trips within the next 7 days
        $upcomingTrips = [];
        $today = new \DateTime();
        $interval = new \DateInterval('P7D'); // 7 days
    
        foreach ($trips as $trip) {
            $startDate = new \DateTime($trip['start_date']);
            if ($today->diff($startDate)->days <= 7 && $today <= $startDate) {
                $upcomingTrips[] = $trip; // Add to upcoming trips array
            }
        }
    
        // Pass trips and upcoming trips data to the view
        $data = ['trips' => $trips, 'upcomingTrips' => $upcomingTrips];
    
        // Load the participant dashboard view
        $viewPath = __DIR__ . '/../../resources/views/participant/dashboard.php';
        if (file_exists($viewPath)) {
            // Extract variables for use in the view
            extract($data);
            include $viewPath;
        } else {
            echo "Participant dashboard view not found!";
        }
    }
    
    
    
    

    // 📌 Update Status - Accept or Decline a Trip
    public function updateStatus()
    {
        session_start();
    
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = "User not logged in.";
            header("Location: /login");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'], $_POST['status'])) {
            $userId = $_SESSION['user_id'];
            $tripId = $_POST['trip_id'];
            $status = $_POST['status'];
            $timestamp = date('Y-m-d H:i:s');
    
            try {
                $db = Database::getInstance()->getConnection();
    
                // Ensure participant exists before updating
                $checkStmt = $db->prepare("SELECT * FROM trip_participants WHERE user_id = :user_id AND trip_id = :trip_id");
                $checkStmt->execute([
                    ':user_id' => $userId,
                    ':trip_id' => $tripId
                ]);
    
                if ($checkStmt->rowCount() == 0) {
                    // If the participant is not in the trip_participants table, insert them first
                    $insertStmt = $db->prepare("INSERT INTO trip_participants (trip_id, user_id, status, responded_at, updated_at) 
                                                VALUES (:trip_id, :user_id, :status, :responded_at, :updated_at)");
                    $insertStmt->execute([
                        ':trip_id' => $tripId,
                        ':user_id' => $userId,
                        ':status' => $status,
                        ':responded_at' => $timestamp,
                        ':updated_at' => $timestamp
                    ]);
                } else {
                    // Update existing participant record
                    $stmt = $db->prepare("UPDATE trip_participants 
                                          SET status = :status, responded_at = :responded_at, updated_at = :updated_at
                                          WHERE user_id = :user_id AND trip_id = :trip_id");
                    $stmt->execute([
                        ':status' => $status,
                        ':responded_at' => $timestamp,
                        ':updated_at' => $timestamp,
                        ':user_id' => $userId,
                        ':trip_id' => $tripId
                    ]);
                }
    
                $_SESSION['message'] = "Status updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['message'] = "Database Error: " . $e->getMessage();
            }
        }
    
        header("Location: /participant/dashboard");
        exit();
    }
    
    
    

    public function viewTripDetails($tripId) {
        // Fetch trip details from the database using the TripParticipant model
        $tripDetailsModel = new TripParticipant($this->db);
    
        // Fetch all trip-related details using a single method
        $tripDetails = $tripDetailsModel->getTripDetails($tripId);
    
        // Check if trip details were fetched successfully
        if (!empty($tripDetails)) {
            // Extract details from the returned array
            $itinerary = $tripDetails['itinerary'] ?? [];
            $accommodations = $tripDetails['accommodations'] ?? [];
            $transportation = $tripDetails['transportation'] ?? [];
            $expenses = $tripDetails['expenses'] ?? [];
    
            // Include the trip_id for the status update form
            $tripDetails['trip_id'] = $tripId; 
    
            // Pass the data to the view
            $viewData = compact('itinerary', 'accommodations', 'transportation', 'expenses', 'tripDetails');
    
            // Render the view
            $viewPath = __DIR__ . '/../../resources/views/participant/trip_details.php';
            if (file_exists($viewPath)) {
                extract($viewData); // Extract data for use in the view
                include $viewPath;
            } else {
                echo "Trip details view not found!";
            }
        } else {
            echo "No details found for this trip!";
        }
    }
    
    
    
    
    
    
    
    
}
