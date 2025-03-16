<?php
namespace App\Controllers;
use PDO;
use PDOException;
use App\Models\TripParticipant; 
use App\Models\TripReview; 
use Core\Database;// ✅ Correct the namespace
class ParticipantController {
    private $db;
    

    public function __construct() {
        $database = Database::getInstance(); // Use the singleton instance
        $this->db = $database->getConnection(); // Get the connection
        session_start();
    }


    public function dashboard() {
       
    
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
    
                // Fetch trip details to get start and end dates (adjusted column name)
                $tripStmt = $db->prepare("SELECT start_date, end_date FROM trips WHERE id = :trip_id");  // 'id' instead of 'trip_id'
                $tripStmt->execute([':trip_id' => $tripId]);
                $trip = $tripStmt->fetch(PDO::FETCH_ASSOC);
    
                if (!$trip) {
                    $_SESSION['message'] = "Trip not found.";
                    header("Location: /participant/dashboard");
                    exit();
                }
    
                $newTripStartDate = $trip['start_date'];
                $newTripEndDate = $trip['end_date'];
    
                // Check if the user has any accepted trips with overlapping dates
                $overlappingTripStmt = $db->prepare(
                    "SELECT * FROM trip_participants 
                     JOIN trips ON trip_participants.trip_id = trips.id  -- Adjusted column name
                     WHERE trip_participants.user_id = :user_id 
                     AND trip_participants.status = 'accepted' 
                     AND (
                         (trips.start_date BETWEEN :start_date AND :end_date) 
                         OR (trips.end_date BETWEEN :start_date AND :end_date) 
                         OR (trips.start_date <= :start_date AND trips.end_date >= :end_date)
                     )"
                );
                $overlappingTripStmt->execute([
                    ':user_id' => $userId,
                    ':start_date' => $newTripStartDate,
                    ':end_date' => $newTripEndDate
                ]);
    
                if ($overlappingTripStmt->rowCount() > 0) {
                    $_SESSION['message'] = "You already have an accepted trip during this time period.";
                    header("Location: /participant/dashboard");
                    exit();
                }
    
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
            
            // Fetch the participant details (status) for this trip
            $participantDetails = $tripDetailsModel->getParticipantByTripId($tripId, $_SESSION['user_id']);
            $participantStatus = $participantDetails['status'] ?? 'pending'; // Default to 'pending' if no status is found
    
            // Fetch the reviews for the trip from the TripReview model
            $tripReviewModel = new TripReview($this->db);
            $reviews = $tripReviewModel->getReviewsByTrip($tripId, $_SESSION['user_id']); // Pass user_id to avoid their own review
    
            // Include the trip_id for the status update form
            $tripDetails['trip_id'] = $tripId;
    
            // Pass the data to the view
            $viewData = compact('itinerary', 'accommodations', 'transportation', 'expenses', 'tripDetails', 'participantStatus', 'reviews');
            
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
    
    




    public function submitReview($tripId) {
        // Check if the participant has accepted the trip (status = 'accepted')
        $tripParticipantModel = new TripParticipant();
        $participant = $tripParticipantModel->getParticipantByTripId($tripId, $_SESSION['user_id']);
    
        // Ensure the participant data is valid and status is 'accepted'
        if (!$participant || !isset($participant['status']) || $participant['status'] !== 'accepted') {
            echo "You cannot review this trip until you accept it!";
            return;
        }
    
        // Collect rating and review from the POST request
        $rating = $_POST['rating'] ?? null;
        $reviewText = $_POST['review_text'] ?? null;
    
        // Ensure rating and review text are provided
        if ($rating && $reviewText) {
            // Save the review
            $tripReviewModel = new TripReview($this->db); // You need to pass $this->db to TripReview
            $userId = $_SESSION['user_id'];  // Assuming user_id is stored in the session
            $tripReviewModel->saveReview($tripId, $userId, $rating, $reviewText);
            echo "Thank you for your feedback!";
        } else {
            echo "Please provide both a rating and a review.";
        }
    }



 // ✅ Show trip participant profile
 public function showProfile() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        $query = "
            SELECT u.id, u.name, u.email, tp.trip_id, tp.status
            FROM users u 
            JOIN trip_participants tp ON u.id = tp.user_id 
            WHERE u.id = :user_id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$participant) {
            $_SESSION['error'] = "Trip Participant not found!";
            header("Location: /dashboard");
            exit();
        }

        require __DIR__ . '/../../resources/views/participant/profile.php';

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// ✅ Update trip participant profile (Only Name, Email, Password)
    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

        try {
            // Ensure the user is a trip participant
            $stmt = $this->db->prepare("SELECT * FROM trip_participants WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['error'] = "Unauthorized access!";
                header("Location: /dashboard");
                exit();
            }

            // ✅ Update user details (only name, email, password if provided)
            if ($password) {
                $query = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :user_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            } else {
                $query = "UPDATE users SET name = :name, email = :email WHERE id = :user_id";
                $stmt = $this->db->prepare($query);
            }

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: /participant/profile");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating profile!";
            header("Location: /participant/profile");
            exit();
        }
    }
    
    
    
    
      
    
}
