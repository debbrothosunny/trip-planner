<?php

namespace App\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Models\TripItinerary;
use \Core\Database; // Make sure this is correct
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

        // Check if user is logged in
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
            header("Location: /login");
            exit();
        }

        $user_id = $_SESSION['user']['id'];

        // Ensure database connection exists
        if (!$this->pdo) {
            die("Database connection is not initialized.");
        }

        // Fetch user's name
        $stmt = $this->pdo->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_name = $user ? $user['name'] : 'Guest';

        // Fetch user's trips
        $trips = $this->trip->getTripsByUserId($user_id);

        // ✅ Check if user is new (i.e., no trips)
        $isNewUser = empty($trips);

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
    
        // Fetch trips where the logged-in user (Purna) is the creator (creator_id or similar)
        $stmt = $this->pdo->prepare("
        SELECT trips.id AS trip_id, trips.name AS trip_name
        FROM trips
        WHERE trips.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch participants for each trip that Purna created, including the email
        $participants = [];
        foreach ($trips as $trip) {
            $trip_id = $trip['trip_id'];
            $stmt = $this->pdo->prepare("
                SELECT users.id AS user_id, users.name AS user_name, users.email AS user_email, trip_participants.status
                FROM trip_participants
                JOIN users ON trip_participants.user_id = users.id
                WHERE trip_participants.trip_id = :trip_id
            ");
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->execute();
            $participants[$trip_id] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        // Load the view
        $participantsViewPath = __DIR__ . '/../../resources/views/user/my_trip_participants.php';
        if (file_exists($participantsViewPath)) {
            include $participantsViewPath;
        } else {
            echo "Trip participants view not found!";
        }
    }
    
    
    
    


    
    
    



    // Show the create trip form
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
            header("Location: /login");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $budget = $_POST['budget'];
            $owner_id = $_SESSION['user']['id'];
    
            // Use the model to create the trip
            $success = $this->trip->createTrip($name, $owner_id, $start_date, $end_date, $budget);
    
            if ($success) {
                // Store a success message in session
                $_SESSION['success_message'] = "Trip created successfully!";
                
                // Redirect to the user's dashboard after success
                header("Location: /user/create-trip");
                exit();
            } else {
                echo "Failed to create the trip.";
            }
        }
    }
    

    public function editTrip($id)
    {
        session_start();

        // Ensure the user is logged in
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        // Fetch the trip details from the database
        $trip = $this->trip->getTripById($id);

        if (!$trip) {
            echo "Trip not found!";
            exit();
        }

        // Ensure the logged-in user owns this trip
        if ($trip['user_id'] !== $_SESSION['user']['id']) {
            echo "Unauthorized access!";
            exit();
        }

        // Define the correct path to the edit view
        $viewPath = __DIR__ . '/../../resources/views/user/edit_trip.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Edit trip view not found!";
        }
    }



    public function updateTrip($id)
    {
        session_start();
    
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Please log in to continue.";
            header("Location: /login");
            exit();
        }
    
        $trip = $this->trip->getTripById($id);
    
        if (!$trip) {
            $_SESSION['error'] = "Trip not found!";
            header("Location: /user/dashboard");
            exit();
        }
    
        if ($trip['user_id'] !== $_SESSION['user']['id']) {
            $_SESSION['error'] = "Unauthorized access!";
            header("Location: /user/dashboard");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $budget = $_POST['budget'];
    
            $success = $this->trip->updateTrip($id, $name, $start_date, $end_date, $budget);
    
            if ($success) {
                $_SESSION['success'] = "Trip updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update the trip.";
            }
    
            // Redirect to the dashboard with the message
            header("Location: /user/dashboard");
            exit();
        }
    }
    
    
    

    public function deleteTrip($id)
    {
        // Fetch the trip by its ID using the model method
        $trip = $this->trip->getTripById($id);
    
        // Check if the trip exists
        if (!$trip) {
            $_SESSION['error'] = "Trip not found!";
            header("Location: /user/dashboard");
            exit();
        }
    
        // Attempt to delete the trip using the model's deleteTrip method
        $success = $this->trip->deleteTrip($id);
    
        // Check if the deletion was successful
        if ($success) {
            $_SESSION['success'] = "Trip deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete the trip.";
        }
    
        // Redirect back to the dashboard
        header("Location: /user/dashboard");
        exit();
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
    public function delete($trip_id, $id) {
        // Debugging: Ensure correct values
        if (!$trip_id || !$id) {
            die("Error: Missing trip ID ($trip_id) or itinerary ID ($id).");
        }
    
        // Check if itinerary exists
        $data = $this->itinerary->getById($id);
        
        if (!$data) {
            die("Error: Itinerary not found for ID ($id).");
        }
    
        // Proceed with deletion
        if ($this->itinerary->delete($id)) {
            header("Location: /trip/$trip_id/itinerary");
            exit();
        } else {
            die("Error: Failed to delete itinerary.");
        }
    }



    
    
    


}

