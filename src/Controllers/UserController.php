<?php

namespace App\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Models\TripItinerary;
use \Core\Database;
use PDO;
class UserController
{
    private $trip;
    private $itinerary; 
    protected $pdo;
    private $db;
    private $user; // Add this property
    public function __construct() {
        // ✅ Get the database connection instance
        $this->db = Database::getInstance();
        
        if (!$this->db) {
            die("Database instance is null.");
        }

        // ✅ Assign the PDO connection to $this->pdo
        $this->pdo = $this->db->getConnection();

        if (!$this->pdo) {
            die("PDO connection failed in UserController.");
        }

        // ✅ Inject the PDO connection into models
        $this->trip = new Trip($this->pdo);
        $this->itinerary = new TripItinerary($this->pdo);
         // ✅ Initialize the User model
         $this->user = new User();
    }
    public function dashboard()
    {
        session_start();

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            header("Location: /login");
            exit();
        }

        $user_id = $_SESSION['user']['id'];

        // Fetch user's name
        $stmt = $this->pdo->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_name = $user ? $user['name'] : 'Guest';

        // ✅ Fetch all trips for the logged-in user
        $trips = $this->trip->getTripsByUserId($user_id);

        // ✅ Calculate trip statistics
        $totalTrips = count($trips);
        $ongoingTrips = count(array_filter($trips, fn($t) => strtotime($t['start_date']) <= time() && strtotime($t['end_date']) >= time()));
        $completedTrips = count(array_filter($trips, fn($t) => strtotime($t['end_date']) < time()));

        // ✅ Load the dashboard view
        $dashboardViewPath = __DIR__ . '/../../resources/views/user/dashboard.php';
        if (file_exists($dashboardViewPath)) {
            include $dashboardViewPath;
        } else {
            echo "User dashboard view not found!";
        }
    }

    



    // user Profile Upade Logic
    public function showProfile() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login.php");
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
        session_start();
    
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login.php");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
    
        // Update user profile
        if ($this->user->updateProfile($user_id, $name, $email, $password)) {
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update profile!";
        }
    
        // Redirect to the profile page route, not directly to the view file
        header("Location: /user/profile");
        exit();
    }
    


    public function myTripParticipants()
    {
        session_start();

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

        // Fetch participants for each trip
        $participants = [];
        foreach ($trips as $trip) {
            $trip_id = $trip['trip_id'];
            $stmt = $this->pdo->prepare("
                SELECT users.id AS user_id, users.name AS user_name, users.email AS user_email,
                       trip_participants.status,
                       COALESCE(payments.payment_status, 'pending') AS payment_status
                FROM trip_participants
                JOIN users ON trip_participants.user_id = users.id
                LEFT JOIN payments ON trip_participants.user_id = payments.user_id
                    AND payments.trip_id = trip_participants.trip_id
                WHERE trip_participants.trip_id = :trip_id
            ");
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->execute();
            $participants[$trip_id] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch pending itinerary edit requests for each trip
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
        session_start();
    
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $user_id = $_SESSION['user']['id'];
    
            // Get trips created by the user
            $trips = $this->trip->getTripsByUserId($user_id);
    
            // Check if user is new (no trips)
            $isNewUser = empty($trips);
    

            // Convert trips to JSON format for Vue.js
            $tripsData = json_encode($trips);
    
            // Load the trip view page
            $tripViewPath = __DIR__ . '/../../resources/views/user/view_trip.php';
            if (file_exists($tripViewPath)) {
                include $tripViewPath;
            } else {
                echo "Trip view page not found!";
            }
        } else {
            // Handle the case where the user is not logged in
            echo "User session not found. Please log in.";
            // Optionally redirect to the login page:
            // header("Location: /login");
            // exit();
        }
    }
    
    

    public function showCreateTripForm()
    {
        session_start();

        // Ensure the user is logged in
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        // Define the correct path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/create_trip.php';

        // Check if the file exists before including it
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }



    // Handle the trip creation
    public function createTrip()
    {
        session_start();
    
        // Ensure the user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = "User not logged in.";
            header("Location: /user/create-trip");
            exit();
        }
    
        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate input
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
            $start_date = filter_var(trim($_POST['start_date']), FILTER_SANITIZE_STRING);
            $end_date = filter_var(trim($_POST['end_date']), FILTER_SANITIZE_STRING);
            $budget = filter_var(trim($_POST['budget']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $owner_id = $_SESSION['user']['id'];
    
            // Validate input (basic validation)
            if (empty($name) || empty($start_date) || empty($end_date) || empty($budget)) {
                $_SESSION['error_message'] = "All fields are required.";
                header("Location: /user/create-trip");
                exit();
            }
    
            // Call the model function to create the trip
            $success = $this->trip->createTrip($name, $owner_id, $start_date, $end_date, $budget);
    
            // Respond with a success or error message
            if ($success) {
                $_SESSION['success_message'] = "Trip created successfully!";
                header("Location: /user/create-trip");
                exit();
            } else {
                $_SESSION['error_message'] = "Failed to create the trip. Please try again.";
                header("Location: /user/create-trip");
                exit();
            }
        }
    }
    
    
    



    public function updateTrip($id)
    {
        session_start();
        error_log("Session User Data: " . print_r($_SESSION, true));
        
    
        // Check if the session is valid
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Please log in to continue.']);
            exit();
        }


    
        $trip = $this->trip->getTripById($id);

        // Check if the trip exists
        
        if (!$trip) {
            error_log("Trip not found for ID: " . $id);
        }

        // Log the trip's user_id and the session's user_id
        error_log("Trip User ID: " . $trip['user_id']);
        error_log("Session User ID: " . $_SESSION['user']['id']);

        // Check if the current user is the owner of the trip
        if ($trip['user_id'] !== $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
            exit();
        }
    
        // Handle the POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Log the incoming data for debugging
            error_log(file_get_contents('php://input'));
        
            $inputData = json_decode(file_get_contents('php://input'), true);
        
            if (isset($inputData['name'], $inputData['start_date'], $inputData['end_date'], $inputData['budget'])) {
                $name = $inputData['name'];
                $start_date = $inputData['start_date'];
                $end_date = $inputData['end_date'];
                $budget = $inputData['budget'];
        
                // Log the data to ensure it's correct
                error_log("Updating Trip with data: " . print_r($inputData, true));
        
                $success = $this->trip->updateTrip($id, $name, $start_date, $end_date, $budget);
        
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Trip updated successfully!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update the trip.']);
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
        session_start();

        $trip = $this->trip->getTripById($id);

        if (!$trip) {
            echo json_encode(['success' => false, 'message' => 'Trip not found!']);
            return;
        }

        if ($trip['user_id'] !== $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
            return;
        }

        $success = $this->trip->deleteTrip($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Trip deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete the trip.']);
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
    
        // Step 2: Pass the trip_id to the view
        // You can pass the $trip_id directly to the view or fetch other details from the $trip variable
        
        // Optionally, check if the trip exists before displaying the form
        if (!$trip) {
            echo "Trip not found!";
            exit;
        }
    
        // Step 3: Include the form view and pass trip_id to it
        // You can pass the $trip_id (and any other necessary data) to the view
        include __DIR__ . "/../../resources/views/user/trip_itinerary_create.php";
    }

    // Store a new itinerary
    public function store($trip_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Fetch values from POST data
            $day_title = $_POST['day_title'];
            $description = $_POST['description'];
            $location = $_POST['location']; // Added location field
            $itinerary_date = $_POST['itinerary_date'];
    
            // Now use the trip_id and store the itinerary
            $this->itinerary->create($trip_id, $day_title, $description, $location, $itinerary_date); // Passed location
    
            // Redirect to the itineraries page
            header("Location: /trip/$trip_id/itinerary");
            exit();
        }
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
    public function update($trip_id, $id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $day_title = $_POST['day_title'] ?? null;
            $description = $_POST['description'] ?? null;
            $location = $_POST['location'] ?? null;
            $itinerary_date = $_POST['itinerary_date'] ?? null;
    
            if (!$day_title || !$description || !$location || !$itinerary_date) {
                die("Error: All fields are required.");
            }
    
            if ($this->itinerary->update($id, $day_title, $description, $location, $itinerary_date)) {
                header("Location: /trip/$trip_id/itinerary");
                exit();
            } else {
                die("Error: Could not update itinerary.");
            }
        }
    }
    

    // Delete an itinerary
    public function deleteItineraryById($id) {
        // Debugging: Ensure correct value
        if (!$id) {
            die("Error: Missing itinerary ID.");
        }
    
        // Check if itinerary exists
        $itineraryModel = new TripItinerary($this->pdo); // Instantiate the model
        $data = $itineraryModel->getById($id); // You'll need this getById method in your model
    
        if (!$data) {
            die("Error: Itinerary not found for ID ($id).");
        }
    
        // Proceed with deletion
        if ($itineraryModel->delete($id)) {
            header("Location: /trip/itinerary"); // Redirect on successful deletion
            exit();
        } else {
            // Only reach here if $itineraryModel->delete($id) returns false
            die("Error: Failed to delete itinerary.");
        }
    }



    
    
    


}

