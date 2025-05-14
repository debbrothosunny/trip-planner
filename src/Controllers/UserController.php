<?php
namespace App\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Models\TripItinerary;
use App\Models\Follower; 
use App\Models\PollModel;
use \Core\Database;
use PDOException;
use PDO;
use \DateTime;
include_once __DIR__ . '/../../helpers/csrf_helper.php';
class UserController
{
    private $trip;
    private $view; // You might be using a view handler elsewhere
    private $user;
    private $itinerary;
    private $pollModel;
    protected $pdo;
    private $db;
    protected $followerModel; // Add the Follower model

    public function __construct()
    {
        $this->db = Database::getInstance();

        if (!$this->db) {
            die("Database instance is null.");
        }

        $this->pdo = $this->db->getConnection();

        if (!$this->pdo) {
            die("PDO connection failed in UserController.");
        }

        $this->trip = new Trip($this->pdo);
        $this->itinerary = new TripItinerary($this->pdo);
        $this->user = new User($this->pdo);
        $this->pollModel = new PollModel($this->db); 
        $this->followerModel = new Follower($this->pdo); // Instantiate the Follower model

       
    }

    public function dashboard()
        {
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
                header("Location: /");
                exit();
            }

            $user_id = $_SESSION['user']['id'];
            $userName = $_SESSION['user']['name'];

            // Fetch user's profile photo path AND creation date
            $stmt = $this->pdo->prepare("SELECT profile_photo, created_at FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $profilePhoto = $user['profile_photo'] ?? null;
            $activeSince = $user['created_at'] ?? null; // Get the created_at value

            // Fetch trips created by the logged-in user (no change needed)
            $trips = $this->trip->getTripsByUserId($user_id);

            // Calculate total accepted participants for user's trips (no change needed)
            $totalAcceptedParticipants = 0;
            foreach ($trips as $trip) {
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) FROM trip_participants
                    WHERE trip_id = :trip_id AND status = 'accepted'
                ");
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->execute();
                $totalAcceptedParticipants += $stmt->fetchColumn();
            }

            // Calculate ongoing trips with progress (no change needed)
            $ongoingTrips = [];
            $currentTime = time();
            foreach ($trips as $trip) {
                $startDate = strtotime($trip['start_date']);
                $endDate = strtotime($trip['end_date']);

                if ($startDate !== false && $endDate !== false && $startDate <= $currentTime && $endDate >= $currentTime) {
                    $totalDuration = $endDate - $startDate;
                    $elapsedTime = $currentTime - $startDate;
                    $progress = ($totalDuration > 0) ? ($elapsedTime / $totalDuration) * 100 : 0;

                    $ongoingTrips[] = [
                        'trip' => $trip,
                        'progress' => min(100, max(0, $progress)),
                    ];
                }
            }

            // Calculate upcoming trips (no change needed)
            $upcomingTrips = array_filter($trips, function ($trip) {
                return strtotime($trip['start_date']) > time();
            });

            // Fetch follower count for the logged-in user (no change needed)
            $followerCount = $this->followerModel->getFollowerCount($user_id);

            // Fetch polls for the user's created trips (no change needed)
            $createdTripsIds = array_column($trips, 'id');
            $pollsForMyTrips = $this->pollModel->getPollsByTripIds($createdTripsIds);

            // Pass data to the view, including 'activeSince'
            $data = [
                'userName' => $userName,
                'profilePhoto' => $profilePhoto,
                'trips' => $trips,
                'totalAcceptedParticipants' => $totalAcceptedParticipants,
                'ongoingTrips' => $ongoingTrips,
                'upcomingTrips' => $upcomingTrips,
                'followerCount' => $followerCount,
                'pollsForMyTrips' => $pollsForMyTrips,
                'activeSince' => $activeSince, // Pass the activeSince data to the view
            ];

            // Extract the data array so that variables are available in the view
            extract($data);

            $dashboardViewPath = __DIR__ . '/../../resources/views/user/dashboard.php';
            if (file_exists($dashboardViewPath)) {
                include $dashboardViewPath;
            } else {
                echo "User dashboard view not found!";
            }
    }

  


    // user Profile Upade Logic
    public function showProfile() {
 
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
    
        // Fetch user details from the database
        $user = $this->user->getUser($user_id);
    
        if (!$user) {
            $_SESSION['error'] = "User not found!";
            header("Location: /dashboard.php");
            exit();
        }
    
        // ✅ Define the profile view path
        $profileViewPath = __DIR__ . '/../../resources/views/user/profile.php';
    
        // ✅ Check if view file exists before including
        if (file_exists($profileViewPath)) {
            include $profileViewPath;
        } else {
            echo "User profile view not found!";
        }
    }
    
    
    public function updateProfile() {
        

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

        // Handle profile photo upload
        $profilePhotoPath = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_photo']['type'], $allowedMimeTypes)) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/image/profile_photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $uniqueFilename = uniqid() . '_' . basename($_FILES['profile_photo']['name']);
                $profilePhotoPath = 'image/profile_photos/' . $uniqueFilename;
                if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $uniqueFilename)) {
                    $_SESSION['error'] = "Error uploading profile photo.";
                    header("Location: /user/profile");
                    exit();
                }
                if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
                    $_SESSION['error'] = "Profile photo size exceeds the limit (2MB).";
                    header("Location: /user/profile");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Invalid profile photo format. Only JPEG, PNG, and GIF are allowed.";
                header("Location: /user/profile");
                exit();
            }
        }

        // Update user profile
        if ($this->user->updateProfile(
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
            $_SESSION['success'] = "Profile updated successfully!";
            // Optionally, update session user data if needed
            $updatedUser = $this->user->getUser($user_id);
            if ($updatedUser) {
                $_SESSION['user'] = $updatedUser; // Assuming you store user data in $_SESSION['user']
            }
        } else {
            $_SESSION['error'] = "Failed to update profile!";
        }

        // Redirect to the profile page route
        header("Location: /user/profile");
        exit();
    }
    


    public function myTripParticipants()
    {
        
    
        // Ensure user is logged in
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            header("Location: /login");
            exit();
        }
    
        $user_id = $_SESSION['user']['id'];
    
        // Ensure database connection exists
        if (!$this->pdo) {
            die("Database connection is not initialized.");
        }
    
        // Fetch trips where the logged-in user is the creator
        $stmt = $this->pdo->prepare("
            SELECT id AS trip_id, name AS trip_name
            FROM trips
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch ACCEPTED participants for each trip
        $participants = [];
        foreach ($trips as $trip) {
            $trip_id = $trip['trip_id'];
            $stmt = $this->pdo->prepare("
                SELECT users.id AS user_id, users.name AS user_name, users.email AS user_email,
                       trip_participants.status,
                       COALESCE(payments.payment_status, 'pending') AS payment_status
                FROM trip_participants
                JOIN users ON trip_participants.user_id = users.id
                LEFT JOIN payments ON trip_participants.user_id = payments.user_id AND payments.trip_id = trip_participants.trip_id
                WHERE trip_participants.trip_id = :trip_id AND trip_participants.status = 'accepted'
            ");
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->execute();
            $participants[$trip_id] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        // Fetch pending itinerary edit requests for each trip (with participant name)
        $editRequests = [];
        $itineraryItems = [];
        $stmtEditRequests = $this->pdo->prepare("
            SELECT er.*, u.name AS user_name, u.id AS user_id
            FROM itinerary_edit_requests er
            JOIN users u ON er.user_id = u.id
            WHERE er.trip_id = :trip_id AND er.status = 'pending'
        ");
        $stmtItineraryItems = $this->pdo->prepare("
            SELECT id AS itinerary_id, day_title
            FROM trip_itineraries
            WHERE id = :itinerary_id
        ");
    
        foreach ($trips as $trip) {
            $stmtEditRequests->bindParam(':trip_id', $trip['trip_id'], PDO::PARAM_INT);
            $stmtEditRequests->execute();
            $editRequests[$trip['trip_id']] = $stmtEditRequests->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($editRequests[$trip['trip_id']] as $request) {
                $itineraryId = $request['itinerary_id'];
                if (!isset($itineraryItems[$itineraryId])) {
                    $stmtItineraryItems->bindParam(':itinerary_id', $itineraryId, PDO::PARAM_INT);
                    $stmtItineraryItems->execute();
                    $item = $stmtItineraryItems->fetch(PDO::FETCH_ASSOC);
                    if ($item) {
                        $itineraryItems[$itineraryId] = $item;
                    } else {
                        $itineraryItems[$itineraryId] = ['day_title' => 'N/A'];
                    }
                }
            }
        }
    
        $data = [
            'trips' => $trips,
            'participants' => $participants,
            'editRequests' => $editRequests,
            'itineraryItems' => $itineraryItems,
        ];
    
        $participantsViewPath = __DIR__ . '/../../resources/views/user/my_trip_participants.php';
        if (file_exists($participantsViewPath)) {
            include $participantsViewPath;
        } else {
            echo "Trip participants view not found!";
        }
    }
    
    

    // Show the create trip form


    public function viewTrips()
    {
        
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $user_id = $_SESSION['user']['id'];
            try {
                $trips = $this->trip->getTripsByUserId($user_id);
                $tripsData = json_encode($trips);
                // Load view
                //loadView('user/view_trip', ['tripsData' => $tripsData]); // Example
                include __DIR__ . '/../../resources/views/user/view_trip.php'; // Include the view directly
            } catch (PDOException $e) {
                error_log("Database error in viewTrips: " . $e->getMessage());
                $_SESSION['error_message'] = "Failed to retrieve trips. Please try again.";
                header("Location: /user/view-trip");
                exit();
            }
        } else {
            echo "User session not found. Please log in.";
            //header("Location: /login");
            //exit();
        }
    }



    private function checkForNewTripMatches($newTrip)
    {
        $matchingParticipants = [];
        $participants = $this->user->getAllParticipants();

        foreach ($participants as $participant) {
            $hasMatch = false;
            $lastAcceptedTrips = $this->trip->getLastAcceptedTripsForUser($participant['id'], 2);

            foreach ($lastAcceptedTrips as $acceptedTrip) {
                if ($acceptedTrip['trip_style'] == $newTrip['trip_style'] && $acceptedTrip['destination'] == $newTrip['destination']) {
                    $hasMatch = true;
                    break;
                }
            }

            if ($hasMatch) {
                $matchingParticipants[] = $participant['id'];
                // Trigger notification logic (for demonstration, echoing modal HTML)
                echo '<script>$(document).ready(function(){ $("#newTripModal_' . $participant['id'] . '").modal("show"); });</script>';
                echo '<div class="modal fade" id="newTripModal_' . $participant['id'] . '" tabindex="-1" aria-labelledby="newTripModalLabel" aria-hidden="true">';
                echo '  <div class="modal-dialog">';
                echo '    <div class="modal-content">';
                echo '      <div class="modal-header">';
                echo '        <h5 class="modal-title" id="newTripModalLabel">New Trip Notification!</h5>';
                echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                echo '      </div>';
                echo '      <div class="modal-body">';
                echo '        A new trip matching your recent preferences...';
                echo '        <p>Life Style: ' . htmlspecialchars($newTrip['trip_style'] ?? '') . '</p>';
                echo '        <p>Destination: ' . htmlspecialchars($newTrip['destination'] ?? '') . '</p>';
                echo '        <p>Trip Name: ' . htmlspecialchars($newTrip['name'] ?? '') . '</p>';
                echo '        <a href="/trips/' . ($newTrip['id'] ?? '') . '" class="btn btn-primary">View Trip</a>';
                echo '      </div>';
                echo '      <div class="modal-footer">';
                echo '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
        }

        // You might want to return $matchingParticipants or handle notifications differently
    }

    public function showCreateTripForm()
    {
      
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit();
        }
        $csrf_token = getCsrfToken(); // Generate token
        include __DIR__ . '/../../resources/views/user/create_trip.php'; // Include view
    }

    public function storeTrip()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => "User not logged in.", 'success' => false]);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Verify CSRF Token
            if (!$this->verifyCsrfToken()) {
                http_response_code(403); // Forbidden
                echo json_encode(['error' => "Invalid CSRF token.", 'success' => false]);
                exit();
            }

            // 2. Validate and sanitize input
            $name = $this->sanitizeString($_POST['name']);
            $start_date = $this->sanitizeString($_POST['start_date']);
            $end_date = $this->sanitizeString($_POST['end_date']);
            $budget = filter_var(trim($_POST['budget']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $trip_style = $this->sanitizeString($_POST['style'] ?? '');
            $destination = $this->sanitizeString($_POST['destination'] ?? '');

            $user_id = $_SESSION['user']['id'];

            // 3. Validation
            if (empty($name) || empty($start_date) || empty($end_date) || empty($budget) || empty($trip_style) || empty($destination)) {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => "All fields are required.", 'success' => false]);
                exit();
            }

            if (!$this->validateDate($start_date) || !$this->validateDate($end_date)) {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => "Invalid date format. Use YYYY-MM-DD.", 'success' => false]);
                exit();
            }

            if ($budget <= 0) {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => "Budget must be greater than zero.", 'success' => false]);
                exit();
            }

            try {
                // Assuming the trip creation method is correctly defined
                $tripId = $this->trip->createTrip($name, $user_id, $start_date, $end_date, $budget, $trip_style, $destination);

                if ($tripId) {
                    // Success message
                    $_SESSION['success_message'] = "Trip created successfully!";

                    // Regenerate CSRF token after successful submission
                    $_SESSION['csrf_token'] = $this->generateCsrfToken();

                    // Send JSON success response
                    echo json_encode(['message' => "Trip created successfully!", 'success' => true, 'redirect' => '/user/view-trip']);
                    exit();
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode(['error' => "Failed to create the trip. Please try again.", 'success' => false]);
                    exit();
                }
            } catch (PDOException $e) {
                http_response_code(500); // Internal Server Error
                error_log("Database error creating trip: " . $e->getMessage());
                echo json_encode(['error' => "Failed to create the trip. Please try again.", 'success' => false]);
                exit();
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => "Invalid request method.", 'success' => false]);
            exit();
        }
    }



    private function verifyCsrfToken() {
        return isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token'];
    }
    
    protected function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    


    private function sanitizeString($input) {
        $input = trim($input);
        $input = stripslashes($input);  //Remove
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // Convert special chars
        return $input;
    }
    
    private function validateDate($date) {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function updateTrip($id)
    {
      

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Please log in to continue.']);
            exit();
        }

        try {
            $trip = $this->trip->getTripById($id);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            exit();
        }


        if (!$trip) {
            echo json_encode(['success' => false, 'message' => 'Trip not found!']);
            exit();
        }

        if ($trip['user_id'] !== $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = json_decode(file_get_contents('php://input'), true);

            if (isset($inputData['name'], $inputData['start_date'], $inputData['end_date'], $inputData['budget'])) {
                $name = $inputData['name'];
                $start_date = $inputData['start_date'];
                $end_date = $inputData['end_date'];
                $budget = $inputData['budget'];

                try {
                    $rowsAffected = $this->trip->updateTrip($id, $name, $start_date, $end_date, $budget);
                    if ($rowsAffected > 0) {
                        echo json_encode(['success' => true, 'message' => 'Trip updated successfully!']);
                    } else {
                        echo json_encode(['success' => true, 'message' => 'No changes were made.']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'Failed to update the trip: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid trip data provided.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function deleteTrip($id)
    {
        

        try {
            $trip = $this->trip->getTripById($id);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            exit();
        }

        if (!$trip) {
            echo json_encode(['success' => false, 'message' => 'Trip not found!']);
            return;
        }

        if ($trip['user_id'] !== $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
            return;
        }

        try {
            $success = $this->trip->deleteTrip($id);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Trip deleted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete the trip.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to delete the trip: ' . $e->getMessage()]);
        }
    }
    




    // Show all itineraries for a trip
    public function showItineraries($trip_id) {
        if (!$trip_id) {
            echo "Error: Trip ID is required.";
            exit;
        }
    
        // Fetch itineraries
        $itineraries = $this->itinerary->getAll($trip_id);
    
        // Define the correct path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/trip_itinerary_list.php'; // Adjust this path based on your directory structure
    
        // Check if the file exists before including it
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }
    

    

    // Show the create itinerary form
    public function create($trip_id) {
        // Step 1: Fetch trip details (optional)
        // Example: Fetching trip details using the trip_id
        $trip = $this->trip->getTripById($trip_id); // Assuming you have a method to fetch trip by ID
    
      
        if (!$trip) {
            echo "Trip not found!";
            exit;
        }
    
        // Step 3: Include the form view and pass trip_id to it
        // You can pass the $trip_id (and any other necessary data) to the view
        include __DIR__ . "/../../resources/views/user/trip_itinerary_create.php";
    }
   
    // Store a new itinerary 
    public function store($trip_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Fetch and sanitize values from POST data
            $day_title = filter_var(trim($_POST['day_title']), FILTER_SANITIZE_STRING);
            $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
            $location = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
            $itinerary_date = filter_var(trim($_POST['itinerary_date']), FILTER_SANITIZE_STRING);

            // Basic validation
            if (!$trip_id || empty($day_title) || empty($description) || empty($location) || empty($itinerary_date)) {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'message' => 'Error: Missing required fields!']);
                return;
            }

            // Handle image upload (adjust for multiple images)
            $images = [];
            if (isset($_FILES['images']) && is_array($_FILES['images']['error'])) {
                $uploadDir = 'image/itinerary_img/';
                $allowedTypes = ['image/jpeg', 'image/png'];

                foreach ($_FILES['images']['error'] as $key => $error) {
                    if ($error === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['images']['tmp_name'][$key];
                        $name = basename($_FILES['images']['name'][$key]);
                        $type = $_FILES['images']['type'][$key];
                        $path = $uploadDir . $name;

                        if (in_array($type, $allowedTypes)) {
                            if (move_uploaded_file($tmpName, $path)) {
                                $images[] = $name;
                            } else {
                                http_response_code(500); // Internal Server Error
                                echo json_encode(['success' => false, 'message' => 'Error: Failed to upload one or more images.']);
                                return;
                            }
                        } else {
                            http_response_code(400); // Bad Request
                            echo json_encode(['success' => false, 'message' => 'Error: Invalid image file type for one or more images.']);
                            return;
                        }
                    } elseif ($error !== UPLOAD_ERR_NO_FILE) {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(['success' => false, 'message' => 'Error: Image upload error.']);
                        return;
                    }
                }
            }

            // Call the create method of the TripItinerary model, now including the images array
            if ($this->itinerary->create($trip_id, $day_title, $description, $location, $itinerary_date, json_encode($images))) {
                echo json_encode(['success' => true, 'message' => 'Itinerary created successfully!']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['success' => false, 'message' => 'Error: Could not save itinerary.']);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Error: Invalid request method.']);
        }
        return; // Ensure no further execution after sending JSON response
    }
      

    // Show the edit form for a specific itinerary
    public function edit($trip_id, $id) {
        if (!$trip_id || !$id) {
            die("Error: Missing itinerary ID or trip ID.");
        }
    
        $data = $this->itinerary->getById($id);
    
        if (!$data) {
            die("Error: Itinerary not found.");
        }
    
        // Pass trip_id to the view
        $trip_id = htmlspecialchars($trip_id);
    
        // Use absolute path to avoid missing file issues
        $file_path = __DIR__ . "/../../resources/views/user/trip_itinerary_edit.php";
    
        if (!file_exists($file_path)) {
            die("Error: View file not found at $file_path");
        }
    
        include $file_path;
    }
    
    

    // Update an itinerary
    public function update($trip_id, $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $day_title = filter_var(trim($_POST['day_title']), FILTER_SANITIZE_STRING);
            $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
            $location = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);
            $itinerary_date = filter_var(trim($_POST['itinerary_date']), FILTER_SANITIZE_STRING);

            if (!$day_title || !$description || !$location || !$itinerary_date) {
                die("Error: All text fields are required.");
            }

            $image = null;
            $uploadError = false; // Flag to track upload errors

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'image/itinerary_img/';
                $imageName = basename($_FILES['image']['name']);
                $imagePath = $uploadDir . $imageName;
                $allowedTypes = ['image/jpeg', 'image/png'];

                if (in_array($_FILES['image']['type'], $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                        $image = $imageName;
                    } else {
                        echo "Error: Failed to upload new image.";
                        $uploadError = true;
                    }
                } else {
                    echo "Error: Invalid image file type.";
                    $uploadError = true;
                }
            } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
                $image = null; // Set to null to remove the image
                // Consider fetching the old image filename here if you want to delete the file.
            }

            // Only update the image in the database if a new one was successfully uploaded
            // or if the user explicitly requested removal. If there was an upload error
            // or no new image and no removal request, we keep the existing image (by passing null).
            if (!$uploadError) {
                if ($this->itinerary->update($id, $day_title, $description, $location, $itinerary_date, $image)) {
                    header("Location: /trip/$trip_id/itinerary");
                    exit();
                } else {
                    die("Error: Could not update itinerary.");
                }
            } else {
                // If there was an upload error, you might want to redirect back to the edit form
                // with an error message, or handle it in another way. For now, we'll just die.
                die("Error during image update. Other itinerary details might not have been saved.");
            }
        }
    }
    

    // Delete an itinerary
    public function deleteItineraryById($id) {
        if (!$id) {
            die("Error: Missing itinerary ID.");
        }
    
        $itineraryModel = new TripItinerary($this->db); // Ensure you are using the correct database connection variable
        $data = $itineraryModel->getById($id);
    
        if (!$data) {
            die("Error: Itinerary not found for ID ($id).");
        }
    
        if ($itineraryModel->delete($id)) {
            header("Location: /trip/itinerary"); // Adjust the redirect as needed
            exit();
        } else {
            die("Error: Failed to delete itinerary.");
        }
    }


    // Followes Function 

    public function followUser(int $followingId): void
    {
        $followerId = $this->getCurrentUserId(); // Replace with your actual method to get current user ID

        if (!$followerId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'You must be logged in to follow users.']);
            return;
        }

        if ($followerId === $followingId) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'You cannot follow yourself.']);
            return;
        }

        if ($this->followerModel->isFollowing($followerId, $followingId)) {
            http_response_code(409); // Conflict - Already following
            echo json_encode(['success' => false, 'message' => 'You are already following this user.']);
            return;
        }

        if ($this->followerModel->create($followerId, $followingId)) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Successfully followed user.']);
            return;
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to follow user. Please try again.']);
            return;
        }
    }

    /**
     * Handles the request to unfollow a user.
     *
     * @param int $followingId The ID of the user to unfollow (from the route parameter).
     * @return void Sends a JSON response indicating success or failure.
     */
    public function unfollowUser(int $followingId): void
    {
        $followerId = $this->getCurrentUserId(); // Replace with your actual method to get current user ID

        if (!$followerId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'You must be logged in to unfollow users.']);
            return;
        }

        if ($followerId === $followingId) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'You cannot unfollow yourself.']);
            return;
        }

        if (!$this->followerModel->isFollowing($followerId, $followingId)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'You are not currently following this user.']);
            return;
        }

        if ($this->followerModel->delete($followerId, $followingId)) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Successfully unfollowed user.']);
            return;
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to unfollow user. Please try again.']);
            return;
        }
    }

    /**
     * Replace this with your actual method to get the current logged-in user's ID.
     * This is just a placeholder.
     *
     * @return int|null The current user's ID or null if not logged in.
     */
    private function getCurrentUserId(): ?int
    {
        // Example using a session:
       
        return $_SESSION['user_id'] ?? null;

        // Replace the above with your actual authentication logic.
    }



    
    
    


}

