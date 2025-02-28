<?php
namespace App\Controllers;

use App\Models\Accommodation;
use Core\Database;
use App\Models\Trip;
use PDO;
use PDOException;
class AccommodationController {
    private $accommodation;  
    private $tripModel;    

     // Constructor to initialize the Accommodation model
      // Constructor to initialize the Accommodation model
      public function __construct() {

        // Use the Singleton to get the connection
        $database = Database::getInstance(); // Get the database connection instance
        $this->db = $database->getConnection(); // Retrieve the connection from Database class
    
        // Instantiate the models with the database connection
        $this->tripModel = new Trip($this->db);
        $this->accommodation = new Accommodation($this->db);  // Initialize accommodation model
    }

    // Show all accommodations

    public function accommodationList() {
        session_start();
        
    
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to view your accommodations.";
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user']['id']; // ✅ Corrected session key
    
        try {
            // Fetch all trips for the logged-in user
            $trips = $this->tripModel->getTripsByUserId($user_id);
    
            // Query to fetch accommodations along with the trip name
            $query = "
                SELECT accommodations.*, trips.name AS trip_name
                FROM accommodations
                JOIN trips ON accommodations.trip_id = trips.id
                WHERE accommodations.user_id = :user_id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
    
            $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Path to the view
            $viewPath = __DIR__ . '/../../resources/views/user/accommodation.php';
    
            if (file_exists($viewPath)) {
                include($viewPath);
            } else {
                echo "View file not found: " . $viewPath;
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
      
    

    public function create() {
        session_start();  // Start the session to access the logged-in user’s data
    
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            die("Error: Please login to create accommodations.");
        }
    
        $user_id = $_SESSION['user']['id']; // ✅ Corrected session key
    
        // Fetch all trips for the logged-in user
        $trips = $this->tripModel->getTripsByUserId($user_id); 
    
        // Path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/accommodation_create.php';
    
        // Check if the view exists
        if (file_exists($viewPath)) {
            include($viewPath);
        } else {
            echo "View file not found: " . $viewPath;
        }
    }
    
    
      

    

    public function store() {
        session_start();  
    
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['sweetalert'] = [
                'title' => 'Error!',
                'text' => 'Please login to add accommodations.',
                'icon' => 'error'
            ];
            header("Location: /login");
            exit();
        }
    
        $user_id = $_SESSION['user']['id']; // ✅ Corrected session key
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trip_id = htmlspecialchars($_POST['trip_id']);
            $name = htmlspecialchars($_POST['name']);
            $location = htmlspecialchars($_POST['location']);
            $price = htmlspecialchars($_POST['price']);
            $amenities = htmlspecialchars($_POST['amenities']);
            $check_in_time = htmlspecialchars($_POST['check_in_time']);
            $check_out_time = htmlspecialchars($_POST['check_out_time']);
    
            if (empty($trip_id) || empty($name) || empty($location) || empty($price)) {
                $_SESSION['sweetalert'] = [
                    'title' => 'Error!',
                    'text' => 'Please fill in all required fields.',
                    'icon' => 'error'
                ];
                header("Location: /user/accommodation/create");
                exit();
            }
    
            $query = "INSERT INTO accommodations (user_id, trip_id, name, location, price, amenities, check_in_time, check_out_time) 
                      VALUES (:user_id, :trip_id, :name, :location, :price, :amenities, :check_in_time, :check_out_time)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':amenities', $amenities, PDO::PARAM_STR);
            $stmt->bindParam(':check_in_time', $check_in_time, PDO::PARAM_STR);
            $stmt->bindParam(':check_out_time', $check_out_time, PDO::PARAM_STR);
    
            if ($stmt->execute()) {
                $_SESSION['sweetalert'] = [
                    'title' => 'Success!',
                    'text' => 'Accommodation created successfully.',
                    'icon' => 'success'
                ];
                header("Location: /user/accommodation");
                exit();
            } else {
                $_SESSION['sweetalert'] = [
                    'title' => 'Error!',
                    'text' => 'Failed to create accommodation.',
                    'icon' => 'error'
                ];
                header("Location: /user/accommodation/create");
                exit();
            }
        }
    }



    public function accommodationEdit($id) {
        // Start the session to access the logged-in user's data
        session_start();
    
        // Retrieve the user ID from the session
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header("Location: /login"); // Redirect to login if no user is logged in
            exit();
        }
        $userId = $_SESSION['user']['id'];
    
        // Fetch accommodation details by ID and ensure it belongs to the logged-in user
        $accommodation = $this->accommodation->getAccommodationByIdAndUser($id, $userId); // Corrected method
    
        // If accommodation is not found or doesn't belong to the user, redirect to the list with an error
        if (!$accommodation) {
            header("Location: /user/accommodation/list?error=not_found");
            exit();
        }
    
        // Fetch all trips for the user (assuming you have a method like getTripsByUser)
        $trips = $this->accommodation->getTripsByUser($userId); // Fetch trips for the user
    
        // Define the path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/accommodation_edit.php';
    
        // Include the view if it exists
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }
    
    
    
    
    public function update($id) {
        session_start();
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $trip_id = htmlspecialchars($_POST['trip_id']);
            $name = htmlspecialchars($_POST['name']);
            $location = htmlspecialchars($_POST['location']);
            $price = htmlspecialchars($_POST['price']);
            $amenities = htmlspecialchars($_POST['amenities']);
            $check_in_time = htmlspecialchars($_POST['check_in_time']);
            $check_out_time = htmlspecialchars($_POST['check_out_time']);
    
            if (empty($trip_id) || empty($name) || empty($location) || empty($price)) {
                header("Location: /user/accommodation/edit/{$id}?error=required_fields_missing");
                exit();
            }
    
            $query = "UPDATE accommodations 
                      SET trip_id = :trip_id, name = :name, location = :location, 
                          price = :price, amenities = :amenities, check_in_time = :check_in_time, check_out_time = :check_out_time
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':amenities', $amenities, PDO::PARAM_STR);
            $stmt->bindParam(':check_in_time', $check_in_time, PDO::PARAM_STR);
            $stmt->bindParam(':check_out_time', $check_out_time, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            if ($stmt->execute()) {
                $_SESSION['sweetalert'] = [
                    'title' => 'Success!',
                    'text' => 'Accommodation updated successfully!',
                    'icon' => 'success'
                ];
                header("Location: /user/accommodation");
                exit();
            } else {
                $_SESSION['sweetalert'] = [
                    'title' => 'Error!',
                    'text' => 'Failed to update the accommodation. Please try again.',
                    'icon' => 'error'
                ];
                header("Location: /user/accommodation/edit/{$id}");
                exit();
            }
        }
    }
    
    
    


    public function delete($id) {
        // Check if the accommodation exists using a direct query
        $query = "SELECT * FROM accommodations WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $accommodation = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$accommodation) {
            // Set an error message in session if the accommodation does not exist
            $_SESSION['sweetalert'] = [
                'title' => 'Error!',
                'text' => 'Accommodation not found.',
                'icon' => 'error'
            ];
            // Redirect to accommodation list if not found
            header("Location: /user/accommodation/list?error=not_found");
            exit;
        }
    
        // Execute the delete query
        $query = "DELETE FROM accommodations WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            // Set a success message in session after successful deletion
            $_SESSION['sweetalert'] = [
                'title' => 'Success!',
                'text' => 'Accommodation deleted successfully.',
                'icon' => 'success'
            ];
            // Redirect to accommodation list after successful deletion
            header("Location: /user/accommodation");
            exit;
        } else {
            // Set an error message in session if deletion fails
            $_SESSION['sweetalert'] = [
                'title' => 'Error!',
                'text' => 'Failed to delete accommodation. Please try again.',
                'icon' => 'error'
            ];
            // Redirect back to accommodation list if deletion fails
            header("Location: /user/accommodation/list?error=delete_failed");
            exit;
        }
    }
    
    
    
    

}