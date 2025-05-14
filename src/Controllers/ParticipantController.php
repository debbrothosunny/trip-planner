<?php
namespace App\Controllers;
use PDO;
use PDOException;
use App\Models\TripParticipant; 
use App\Models\Payment; 
use App\Models\User;
use App\Models\Follower;
use App\Models\Trip; 
use App\Models\TripInvitation; 
use App\Models\TripReview; 
use App\Models\PollModel; 
use Core\Database;
class ParticipantController {
    private $db;
    private $paymentModel;
    private $tripParticipantModel;
    private $tripModel;
    private $userModel;
    private $invitationModel;
    private $followerModel;
    private $pollModel;
    

    public function __construct() {
        $database = Database::getInstance(); // Get the singleton instance
        $this->db = $database->getConnection(); // Get the database connection
    
        // Initialize models and pass the database connection
        $this->paymentModel = new Payment($this->db);
        $this->userModel = new User($this->db);
        $this->tripParticipantModel = new TripParticipant($this->db);
        $this->tripModel = new Trip($this->db); // Initialize Trip model
        $this->invitationModel = new TripInvitation($this->db); 
        $this->followerModel = new Follower($this->db);
        $this->pollModel = new PollModel($this->db);
        
        session_start(); // Start session
    }


    public function dashboard()
    {


        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'participant') {
            header("Location: /");
            exit();
        }

        $userId = $_SESSION['user']['id'];

        // Get all trips for the participant
        $participantTrips = $this->tripParticipantModel->getAllTripsForParticipant($userId);
        $acceptedTripIds = array_column(array_filter($participantTrips, function ($trip) {
            return isset($trip['status']) && $trip['status'] === 'accepted';
        }), 'trip_id');

        // Get participant details, including profile photo and created_at, from database
        $stmt = $this->db->prepare("SELECT name, profile_photo, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        $activeSince = $participant['created_at'] ?? null; // Get the created_at value

        // Calculate ongoing trips and accepted trips
        $ongoingTrips = [];
        $acceptedTrips = [];
        $now = new \DateTime('now', new \DateTimeZone('Asia/Dhaka'));

        foreach ($participantTrips as $trip) {
            try {
                $startDate = new \DateTime($trip['start_date'], new \DateTimeZone('Asia/Dhaka'));
                $endDate = new \DateTime($trip['end_date'], new \DateTimeZone('Asia/Dhaka'));

                if ($now >= $startDate && $now <= $endDate) {
                    $ongoingTrips[] = $trip;
                }

                if (isset($trip['status']) && $trip['status'] === 'accepted') {
                    $acceptedTrips[] = $trip;
                }
            } catch (PDOException $e) {
                error_log("Error parsing date: " . $trip['start_date'] . " or " . $trip['end_date'] . " - " . $e->getMessage());
            }
        }

        // Fetch recommendations
        $lastAcceptedTrips = $this->tripModel->getLastAcceptedTripsForUser($userId, 2);
        $availableTrips = $this->tripModel->getAllTrips();

        $recommendations = [];
        foreach ($availableTrips as $availableTrip) {
            if (in_array($availableTrip['id'], $acceptedTripIds)) {
                continue;
            }

            foreach ($lastAcceptedTrips as $acceptedTrip) {
                if ($availableTrip['trip_style'] == $acceptedTrip['trip_style'] || $availableTrip['destination'] == $acceptedTrip['destination']) {
                    $recommendations[] = $availableTrip;
                    break;
                }
            }
        }

        // 🔥🔥 **Mute check added here**  
       // MUTE logic - handle mute request BEFORE everything else
        if (isset($_GET['mute_recommendation'])) {
            $mutedId = (int) $_GET['mute_recommendation'];
            if (!isset($_SESSION['muted_recommendation_ids'])) {
                $_SESSION['muted_recommendation_ids'] = [];
            }
            if (!in_array($mutedId, $_SESSION['muted_recommendation_ids'])) {
                $_SESSION['muted_recommendation_ids'][] = $mutedId;
            }
            header("Location: /participant/dashboard");
            exit();
        }

        $data = [
            'ongoingTrips' => $ongoingTrips,
            'acceptedTrips' => $acceptedTrips,
            'participant' => $participant,
            'recommendations' => $recommendations,
            'activeSince' => $activeSince,
        ];

        $dashboardViewPath = __DIR__ . '/../../resources/views/participant/dashboard.php';
        if (file_exists($dashboardViewPath)) {
            extract($data);
            include $dashboardViewPath;
        } else {
            echo "Participant dashboard view not found!";
        }
    }


    
    public function Trips()
    {
        // Check if the user is logged in and is a participant
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'participant') {
            header("Location: /"); // Or redirect to a relevant page
            exit();
        }
    
        $userId = $_SESSION['user_id'];
    
        // Create an instance of the Database class and get the connection
        $database = Database::getInstance();
        $db = $database->getConnection(); // Get the database connection
    
        // Create instances of the required models
        $tripParticipantModel = new TripParticipant($db);
        $paymentModel = new Payment($db);
        $tripModel = new Trip($db);
    
        // Retrieve the trip_style filter from the GET parameters
        $tripStyleFilter = $_GET['trip_style'] ?? null;
        $minBudgetFilter = $_GET['min_budget'] ?? null;
        $maxBudgetFilter = $_GET['max_budget'] ?? null;
    
        // Pagination settings
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 20;
        $offset = ($page - 1) * $itemsPerPage;
    
        // Get total count for pagination, applying the trip_style filter
        $totalTrips = $tripParticipantModel->getTotalTripsForParticipant($userId, $tripStyleFilter, $minBudgetFilter, $maxBudgetFilter);
        $totalPages = ceil($totalTrips / $itemsPerPage);
    
        // Get paginated trips for the participant, applying the trip_style filter
        $trips = $tripParticipantModel->getPaginatedTripsForParticipant($userId, $itemsPerPage, $offset, $tripStyleFilter, $minBudgetFilter, $maxBudgetFilter);
    
        // Fetch unique trip styles from the database
        $uniqueTripStyles = $tripModel->getUniqueTripStyles();
    
        // Array to store payment statuses for each trip
        $paymentStatuses = [];
    
        // Check for upcoming trips within the next 7 days and fetch payment status and participant count
        $upcomingTrips = [];
        $today = new \DateTime();
        $interval = new \DateInterval('P7D');
    
        foreach ($trips as &$trip) {
            $startDate = new \DateTime($trip['start_date']);
            if ($today->diff($startDate)->days <= 7 && $today <= $startDate) {
                $upcomingTrips[] = &$trip;
            }
    
            // Fetch the payment status for the current trip and user
            $paymentStatus = $paymentModel->getPaymentStatus($userId, $trip['trip_id']);
            $paymentStatuses[$trip['trip_id']] = $paymentStatus;
    
            // Get creator details including profile photo
            $tripDetails = $tripModel->getTripCreator($trip['trip_id']); // Use the getTripCreator method
            $trip['creator_name'] = $tripDetails['creator_name'];
            $trip['creator_email'] = $tripDetails['creator_email'];
            $trip['country'] = $tripDetails['country'];
            $trip['city'] = $tripDetails['city'];
            $trip['creator_id'] = $tripDetails['creator_id'];
            $trip['creator_profile_photo'] = $tripDetails['profile_photo'] ?? 'default_profile.png'; // Get profile photo
    
            // Fetch and add the total accepted participants count to the trip array
            $acceptedCount = $tripParticipantModel->getTotalAcceptedParticipants($trip['trip_id']);
            $trip['accepted_participants'] = $acceptedCount;
        }
        unset($trip);
    
        // Prepare the data to pass to the view
        $data = [
            'trips' => $trips,
            'upcomingTrips' => $upcomingTrips,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'payment_status' => $paymentStatuses,
            'tripStyleFilter' => $tripStyleFilter,
            'uniqueTripStyles' => $uniqueTripStyles,
        ];
    
        // Load the participant dashboard view
        $viewPath = __DIR__ . '/../../resources/views/participant/trips.php';
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
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'], $_POST['status'])) {
            $userId = $_SESSION['user_id'];
            $tripId = $_POST['trip_id'];
            $status = $_POST['status'];
            $timestamp = date('Y-m-d H:i:s');

            try {
                $db = Database::getInstance()->getConnection();

                // Fetch trip details to get start and end dates
                $tripStmt = $db->prepare("SELECT start_date, end_date FROM trips WHERE id = :trip_id");
                $tripStmt->execute([':trip_id' => $tripId]);
                $trip = $tripStmt->fetch(PDO::FETCH_ASSOC);

                if (!$trip) {
                    $_SESSION['message'] = "Trip not found.";
                    header("Location: /participant/trips");
                    exit();
                }

                $newTripStartDate = $trip['start_date'];
                $newTripEndDate = $trip['end_date'];

                if ($status === 'accepted') {
                    // Check if the user has any accepted trips with overlapping dates
                    $overlappingTripStmt = $db->prepare(
                        "SELECT * FROM trip_participants
                        JOIN trips ON trip_participants.trip_id = trips.id
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
                        header("Location: /participant/trips");
                        exit();
                    }
                }

                // Ensure participant exists before updating or inserting
                $checkStmt = $db->prepare("SELECT * FROM trip_participants WHERE user_id = :user_id AND trip_id = :trip_id");
                $checkStmt->execute([
                    ':user_id' => $userId,
                    ':trip_id' => $tripId
                ]);

                if ($checkStmt->rowCount() == 0) {
                    // Insert participant if not already in the table
                    $insertStmt = $db->prepare("INSERT INTO trip_participants (trip_id, user_id, status, updated_at)
                                                VALUES (:trip_id, :user_id, :status, :updated_at)");
                    $insertStmt->execute([
                        ':trip_id' => $tripId,
                        ':user_id' => $userId,
                        ':status' => $status,
                        ':updated_at' => $timestamp
                    ]);
                } else {
                    // Update the existing participant status
                    $updateStmt = $db->prepare("UPDATE trip_participants
                                                SET status = :status, updated_at = :updated_at
                                                WHERE user_id = :user_id AND trip_id = :trip_id");
                    $updateStmt->execute([
                        ':status' => $status,
                        ':updated_at' => $timestamp,
                        ':user_id' => $userId,
                        ':trip_id' => $tripId
                    ]);
                }

                $_SESSION['message'] = "Status updated successfully!";
                header("Location: /participant/trips");
                exit();

            } catch (PDOException $e) {
                $_SESSION['message'] = "Database Error: " . $e->getMessage();
                header("Location: /participant/trips");
                exit();
            }
        }

        $_SESSION['message'] = "Invalid request.";
        header("Location: /participant/trips");
        exit();
    }
    

   // Cancel Trip
   public function cancelTrip()
   {
       // Ensure the user is logged in and has the participant role
       if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'participant') {
           header('Location: /login'); // Adjust the path
           exit();
       }

       if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'])) {
           $tripId = filter_input(INPUT_POST, 'trip_id', FILTER_SANITIZE_NUMBER_INT);
           $userId = $_SESSION['user_id'];

           if (!$tripId) {
               $this->sendJsonResponse(['success' => false, 'message' => 'Invalid Trip ID.']);
               return;
           }

           // Check if the user is an accepted participant in this trip
           $participation = $this->tripParticipantModel->getParticipation($userId, $tripId);

           if (!$participation || $participation['status'] !== 'accepted') {
               $this->sendJsonResponse(['success' => false, 'message' => 'You are not an accepted participant in this trip.']);
               return;
           }

           // Check if the trip exists and its start date has not passed
           $trip = $this->tripModel->find($tripId);
           if (!$trip) {
               $this->sendJsonResponse(['success' => false, 'message' => 'Trip not found.']);
               return;
           }

           if (new \DateTime($trip['start_date']) <= new \DateTime()) {
               $this->sendJsonResponse(['success' => false, 'message' => 'Cannot cancel as the trip has already started.']);
               return;
           }

           // Get the latest payment status for the user and trip
           $paymentStatus = $this->paymentModel->getPaymentStatus($userId, $tripId);

           // Update trip_participants status to 'pending' upon cancellation
           $updatedParticipant = $this->tripParticipantModel->updateParticipationStatus($userId, $tripId, 'pending');

           if ($updatedParticipant) {
               if ($paymentStatus === 'completed') {
                   // Calculate refund amount (10% cut from the total budget)
                   $refundPercentage = 0.10;
                   $totalBudget = $trip['budget'];
                   $refundAmount = $totalBudget - ($totalBudget * $refundPercentage);

                   // Update payment status to 'cancelled'
                   $updatedPayment = $this->paymentModel->updatePaymentStatus($userId, $tripId, 'cancelled');

                   if ($updatedPayment) {
                       $this->sendJsonResponse(['success' => true, 'message' => "Trip cancellation requested successfully. You will be refunded $" . number_format($refundAmount, 2) . " (10% deduction from the budget)."]);
                   } else {
                       $this->sendJsonResponse(['success' => false, 'message' => 'Trip cancellation successful, but failed to update payment status. Please contact support.']);
                   }
               } else {
                   $this->sendJsonResponse(['success' => true, 'message' => 'Trip cancellation requested successfully. No refund applicable as payment was not completed.']);
               }
           } else {
               $this->sendJsonResponse(['success' => false, 'message' => 'Failed to request trip cancellation. Please try again.']);
           }
       } else {
           // Invalid request
           header('HTTP/1.1 400 Bad Request');
           $this->sendJsonResponse(['success' => false, 'message' => 'Invalid request.']);
       }
   }

    // Helper function to send JSON response
    private function sendJsonResponse(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }


    



    public function archivedTrips() {
        // Check if the user is logged in and is a participant
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'participant') {
            header("Location: /"); // Or redirect to a relevant page
            exit();
        }

        $userId = $_SESSION['user_id'];

        // Pagination settings for archived trips
        $page = isset($_GET['archive_page']) ? (int)$_GET['archive_page'] : 1; // Default to page 1
        $itemsPerPage = 10; // Adjust as needed
        $offset = ($page - 1) * $itemsPerPage;

        // Get total count for archived trips
        $totalArchivedTrips = $this->tripParticipantModel->getTotalArchivedTripsForParticipant($userId);
        $totalPagesArchived = ceil($totalArchivedTrips / $itemsPerPage);

        // ✅ Fetch the paginated archived trips
        $archivedTrips = $this->tripParticipantModel->getUserArchivedTrips($userId, $itemsPerPage, $offset);

        // Prepare the data to pass to the view
        $data = [
            'archivedTrips' => $archivedTrips, // ✅ Pass the archived trips data
            'totalPagesArchived' => $totalPagesArchived,
            'currentArchivePage' => $page,
        ];

        // Load the archived trips view
        $viewPath = __DIR__ . '/../../resources/views/participant/archived_trips.php';
        if (file_exists($viewPath)) {
            // Extract variables for use in the view
            extract($data);
            include $viewPath;
        } else {
            echo "Participant archived trips view not found!";
        }
    }
   
    
    
    
    public function viewTripDetails($tripId) {
        // Fetch trip details from the database using the TripParticipant model
        $tripDetailsModel = new TripParticipant($this->db);
        $tripReviewModel = new TripReview($this->db);
        $pollModel = new PollModel($this->db); // Instantiate the PollModel

        // Fetch all trip-related details using a single method
        $tripDetails = $tripDetailsModel->getTripDetails($tripId);
        $polls = $this->pollModel->getPollsByTripId($tripId);

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
            $reviews = $tripReviewModel->getReviewsByTrip($tripId, $_SESSION['user_id']); // Pass user_id to avoid their own review

            // Fetch polls for this trip
            $polls = $pollModel->getPollsByTripId($tripId); // You'll need to create this method in PollModel

            // Include the trip_id for the status update form and poll creation form
            $tripDetails['trip_id'] = $tripId;

            // Pass all data to the view
            $viewData = compact('itinerary', 'accommodations', 'transportation', 'expenses', 'tripDetails', 'participantStatus', 'reviews', 'polls'); // Removed 'editRequests', added 'polls'

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
        $tripParticipantModel = new TripParticipant($this->db); // Pass the database connection
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
            $tripReviewModel = new TripReview($this->db); // Ensure $this->db is available
            $userId = $_SESSION['user_id'];   // Assuming user_id is stored in the session
            $tripReviewModel->saveReview($tripId, $userId, $rating, $reviewText);
            echo "Thank you for your feedback!";
        } else {
            echo "Please provide both a rating and a review.";
        }
    }




    // ✅ Show trip participant profile
    public function showProfile() {
        
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
    
        // Fetch user details from the database
        $user = $this->userModel->getUser($user_id);
    
        if (!$user) {
            $_SESSION['error'] = "User not found!";
            header("Location: /dashboard.php");
            exit();
        }
    
        // ✅ Define the profile view path
        $profileViewPath = __DIR__ . '/../../resources/views/participant/profile.php';
    
        // ✅ Check if view file exists before including
        if (file_exists($profileViewPath)) {
            include $profileViewPath;
        } else {
            echo "User profile view not found!";
        }
    }


    public function updateProfile()
    {
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: /");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        $phone = $_POST['phone'] ?? null;
        $country = $_POST['country'] ?? null;
        $language = $_POST['language'] ?? null;
        $currency = $_POST['currency'] ?? null;
        $gender = $_POST['gender'] ?? null;

        // Check if any changes were actually made
        $existingUser = $this->userModel->getUser($user_id);
        if ($existingUser) {
            $hasChanges = false;
            if ($name !== $existingUser['name']) $hasChanges = true;
            if ($email !== $existingUser['email']) $hasChanges = true;
            if ($phone !== $existingUser['phone']) $hasChanges = true;
            if ($country !== $existingUser['country']) $hasChanges = true;
            if ($language !== $existingUser['language']) $hasChanges = true;
            if ($currency !== $existingUser['currency']) $hasChanges = true;
            if ($gender !== $existingUser['gender']) $hasChanges = true;
            if (!empty($_FILES['profile_photo']['name'])) $hasChanges = true;
            if ($password !== null) $hasChanges = true;

            if (!$hasChanges) {
                echo json_encode(['success' => false, 'message' => 'No changes were made.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit();
        }

        // Handle profile photo upload
        $profilePhotoPath = $existingUser['profile_photo'] ?? null; // Keep existing if no new upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_photo']['type'], $allowedMimeTypes)) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/image/profile_photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $uniqueFilename = uniqid() . '_' . basename($_FILES['profile_photo']['name']);
                $newProfilePhotoPath = 'image/profile_photos/' . $uniqueFilename;
                if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $uniqueFilename)) {
                    echo json_encode(['success' => false, 'message' => 'Error uploading profile photo.']);
                    exit();
                }
                if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
                    echo json_encode(['success' => false, 'message' => 'Profile photo size exceeds the limit (2MB).']);
                    exit();
                }
                // Delete the old profile photo if a new one is uploaded
                if ($profilePhotoPath && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $profilePhotoPath)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $profilePhotoPath);
                }
                $profilePhotoPath = $newProfilePhotoPath;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid profile photo format. Only JPEG, PNG, and GIF are allowed.']);
                exit();
            }
        }

        // Update user profile
        if ($this->userModel->updateProfile(
            $user_id,
            $name,
            $email,
            $password,
            $phone,
            $profilePhotoPath,
            $country,
            $language,
            $currency,
            $gender
        )) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
            // Optionally, update session user data if needed
            $updatedUser = $this->userModel->getUser($user_id);
            if ($updatedUser) {
                $_SESSION['user'] = $updatedUser; // Assuming you store user data in $_SESSION['user']
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile!']);
        }
        exit();
    }

    




  





    public function generateInviteLink() {
        // 1. Get the trip ID from the form submission ($_POST)
        $tripId = $_POST['trip_id'] ?? null;
    
        // 2. Get the ID of the logged-in user (the inviter) from the session

        $inviterUserId = $_SESSION['user_id'] ?? null;
    
        if (!$inviterUserId) {
            // Handle cases where the user is not logged in
            header("Location: /login.php?error=not_logged_in"); // Redirect to login page with an error
            exit();
        }
    
        if (!$tripId) {
            // Handle cases where trip ID is missing
            header("Location: /trips.php?error=missing_trip_id"); // Redirect back to trips page with an error
            exit();
        }
    
        // 3. Generate a unique invitation code
        $uniqueCode = uniqid('INV_', true);
    
        // 4. Store the invitation record in the database using the model
        $created_at = date('Y-m-d H:i:s');
        $expires_at = null; // You can set an expiration date here if needed
        $status = 'pending';
        $invited_user_id = null;
    
        $invitationData = [
            'trip_id' => $tripId,
            'inviter_user_id' => $inviterUserId,
            'invitation_code' => $uniqueCode,
            'created_at' => $created_at,
            'expires_at' => $expires_at,
            'status' => $status,
            'invited_user_id' => $invited_user_id,
        ];
    
        $invitationId = $this->invitationModel->create($invitationData);
    
        if ($invitationId) {
            // 5. Generate the shareable invitation link
            $invitationLink = '/invite/' . $uniqueCode; // Assuming you'll create a route/page for this
    
            // 6. Include the view to display the link
            $inviteLinkViewPath = __DIR__ . '/../../resources/views/participant/generate_invite_link.php';
            if (file_exists($inviteLinkViewPath)) {
                include $inviteLinkViewPath;
                exit();
            } else {
                echo "Error: Invite link view not found!";
                exit();
            }
        } else {
            // Handle cases where saving the invitation failed
            header("Location: /trip_details.php?id=" . $tripId . "&error=invite_failed"); // Redirect back to trip details with an error
            exit();
        }
    }

    public function handleInviteLink($code)
    {
        // $code contains the unique invitation code from the URL
        $invitation = $this->invitationModel->findByCode($code); // Implement findByCode in your TripInvitation model

        if ($invitation && $invitation['status'] === 'pending') {
            // Invitation is valid and pending
            $trip = $this->tripModel->find($invitation['trip_id']); // Implement find in your Trip model

            if ($trip) {
                // Trip found, display information and a way to join
                include 'resources/views/participant/trip_details.php'; // Create this view
                exit();
            } else {
                echo "<h2>Error: Trip associated with this invitation not found.</h2>";
            }
        } else {
            echo "<h2>Invalid or expired invitation link.</h2>";
        }
    }



    public function userProfileDetails(int $userId)
    {
        // Fetch the profile user's details
        $profileUser = $this->userModel->find($userId);

        if (!$profileUser) {
            http_response_code(404);
            echo "User not found.";
            return;
        }

        $currentUserId = $this->getCurrentUserId();
        $isFollowing = false;
        if ($currentUserId && $currentUserId !== $userId) {
            $isFollowing = $this->followerModel->isFollowing($currentUserId, $userId);
        }

        // Fetch the profile user's expired public trips
        $expiredTrips = $this->tripModel->getUserExpiredPublicTrips($userId);
        
        $lastTripItineraries = [];
        $lastTrip = null; // Initialize $lastTrip
        if (!empty($expiredTrips)) {
            // Get the last expired trip (assuming they are ordered by end_date DESC)
            $lastTrip = reset($expiredTrips);
            $lastTripItineraries = $this->tripModel->getTripItineraries($lastTrip['id']);
        }

        $data = [
            'profileUser' => $profileUser,
            'previousTrips' => $expiredTrips,
            'isFollowing' => $isFollowing,
            'lastTripItineraries' => $lastTripItineraries,
            'lastTrip' => $lastTrip, // Pass $lastTrip to the view
        ];

        $viewPath = __DIR__ . '/../../resources/views/participant/user_profile_details.php';
        if (file_exists($viewPath)) {
            extract($data);
            include $viewPath;
        } else {
            echo "User profile details view not found!";
        }
    }

    private function getCurrentUserId(): ?int
    {
        // Example using a session:
       
        return $_SESSION['user_id'] ?? null;

        // Replace the above with your actual authentication logic.
    }
      
}
    
