<?php

namespace App\Controllers;

use App\Models\Transportation;
use Core\Database;
use App\Models\Trip;
use PDO;
use PDOException;
class TransportationController 
{
    private $db;
    private $tripModel;
    private $transportation;

    public function __construct()
    {
        // Use the Singleton to get the connection
        $database = Database::getInstance(); // Get the database connection instance
        $this->db = $database->getConnection(); // Retrieve the connection from Database class
    
        // Initialize models
        $this->tripModel = new Trip($this->db);
        $this->transportation = new Transportation($this->db);
    }

    // View all transportation records
    public function transportationList() {
        session_start();
    
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to view your transportation records.";
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user']['id']; // ✅ Correct session key
    
        try {
            // Fetch all trips for the logged-in user
            $trips = $this->tripModel->getTripsByUserId($user_id);
    
            // Query to fetch transportation records along with trip names
            $query = "
                SELECT transportation.*, trips.name AS trip_name
                FROM transportation
                JOIN trips ON transportation.trip_id = trips.id
                WHERE trips.user_id = :user_id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
    
            $transportations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Path to the view
            $viewPath = __DIR__ . '/../../resources/views/user/transportation.php';
    
            if (file_exists($viewPath)) {
                include($viewPath);
            } else {
                echo "View file not found: " . $viewPath;
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
    
    



    // Create new transportation entry
    public function create()
    {
        session_start(); // Ensure the session is started
    
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to add transportation.";
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user']['id']; // ✅ Correct session key for user ID
    
        try {
            // Fetch trip names for the dropdown
            $query = "SELECT id, name FROM trips WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Collect data from the form, including the amount
                $data = [
                    'trip_id' => $_POST['trip_id'],
                    'type' => $_POST['type'],
                    'company_name' => $_POST['company_name'],
                    'departure_location' => $_POST['departure_location'],
                    'arrival_location' => $_POST['arrival_location'],
                    'departure_date' => $_POST['departure_date'],
                    'arrival_date' => $_POST['arrival_date'],
                    'booking_reference' => $_POST['booking_reference'],
                    'user_id' => $user_id, // ✅ Ensure the correct user ID is used
                    'amount' => $_POST['amount'] // Add the amount field
                ];
    
                // Call the create method in the model
                $transportation_id = $this->transportation->create($data);
    
                // Redirect to the list or success page with a flash message
                $_SESSION['success'] = 'Transportation created successfully!';
                header('Location: /user/transportation');
                exit();
            }
    
            // Show the form for adding transportation
            $viewPath = __DIR__ . '/../../resources/views/user/transportation_create.php';
    
            if (file_exists($viewPath)) {
                include($viewPath);
            } else {
                echo "View file not found: " . $viewPath;
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
    
    
    
    


    
    

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'trip_id' => $_POST['trip_id'],
                'type' => $_POST['type'],
                'company_name' => $_POST['company_name'],
                'departure_location' => $_POST['departure_location'],
                'arrival_location' => $_POST['arrival_location'],
                'departure_date' => $_POST['departure_date'],
                'arrival_date' => $_POST['arrival_date'],
                'booking_reference' => $_POST['booking_reference'],
                'user_id' => $_SESSION['user']['id'],
                'amount' => $_POST['amount'] // Add the amount field here
            ];
    
            $transportation_id = $this->transportation->create($data);
    
            $_SESSION['sweetalert'] = [
                'title' => $transportation_id ? 'Success!' : 'Error!',
                'text' => $transportation_id ? 'Transportation created successfully!' : 'Failed to create transportation entry.',
                'icon' => $transportation_id ? 'success' : 'error'
            ];
    
            // Redirect immediately after setting session
            header('Location: /user/transportation');
            exit();
        }
    }
    
    
    
    



    // Edit a transportation entry
    public function edit($id)
    {
        session_start(); // Ensure session is started
    
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to edit transportation records.";
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user']['id']; // ✅ Correct session key for user ID
    
        try {
            // Retrieve the transportation entry by ID
            $transportation = $this->transportation->getById($id);
    
            // Check if the transportation entry exists and belongs to the logged-in user
            if (!$transportation || $transportation['user_id'] !== $user_id) {
                $_SESSION['error'] = 'Transportation not found or unauthorized!';
                header('Location: /user/transportation');
                exit();
            }
    
            // Fetch only the trips belonging to the logged-in user
            $query = "SELECT id, name FROM trips WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'trip_id' => $_POST['trip_id'],
                    'type' => $_POST['type'],
                    'company_name' => $_POST['company_name'],
                    'departure_location' => $_POST['departure_location'],
                    'arrival_location' => $_POST['arrival_location'],
                    'departure_date' => $_POST['departure_date'],
                    'arrival_date' => $_POST['arrival_date'],
                    'booking_reference' => $_POST['booking_reference'],
                    'user_id' => $user_id // ✅ Ensuring correct user ID
                ];
    
                // Attempt to update the transportation data
                $success = $this->transportation->update($id, $data);
    
                if ($success) {
                    $_SESSION['success'] = 'Transportation updated successfully!';
                    header('Location: /user/transportation');
                    exit(); // Prevent further execution after redirect
                } else {
                    $_SESSION['error'] = 'Failed to update transportation.';
                }
            }
    
            // Define the view path for the edit page
            $viewPath = __DIR__ . '/../../resources/views/user/transportation_edit.php';
    
            if (file_exists($viewPath)) {
                include($viewPath);
            } else {
                echo "View file not found: " . $viewPath;
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
    

    
    
    


    public function update($id)
    {
        session_start();
    
        // Retrieve form data from the POST request
        $data = [
            'trip_id' => $_POST['trip_id'],
            'type' => $_POST['type'],
            'company_name' => $_POST['company_name'],
            'departure_location' => $_POST['departure_location'],
            'arrival_location' => $_POST['arrival_location'],
            'departure_date' => $_POST['departure_date'],
            'arrival_date' => $_POST['arrival_date'],
            'booking_reference' => $_POST['booking_reference'],
            'user_id' => $_SESSION['user']['id'],
            'amount' => $_POST['amount'] // Add the amount field here
        ];
    
        // Perform the update operation
        $affectedRows = $this->transportation->update($id, $data);
    
        // Set session message based on update result
        if ($affectedRows > 0) {
            $_SESSION['sweetalert'] = [
                'title' => 'Success!',
                'text' => 'Transportation updated successfully!',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['sweetalert'] = [
                'title' => 'No Changes!',
                'text' => 'No updates were made.',
                'icon' => 'info'
            ];
        }
    
        // Redirect after updating
        header('Location: /user/transportation');
        exit();
    }
    
    
    
    

    

    // Delete a transportation entry
    public function delete($id)
    {
        session_start(); // Ensure session is started
    
        // Fetch transportation record by ID
        $transportation = $this->transportation->getById($id);
        
        if (!$transportation) {
            $_SESSION['sweetalert'] = [
                'title' => 'Error!',
                'text' => 'Transportation not found!',
                'icon' => 'error'
            ];
            header('Location: /user/transportation');
            exit();
        }
    
        // Perform deletion
        $success = $this->transportation->delete($id);
    
        // Set SweetAlert notification
        if ($success) {
            $_SESSION['sweetalert'] = [
                'title' => 'Deleted!',
                'text' => 'Transportation deleted successfully!',
                'icon' => 'success'
            ];
        } else {
            $_SESSION['sweetalert'] = [
                'title' => 'Error!',
                'text' => 'Failed to delete transportation.',
                'icon' => 'error'
            ];
        }
    
        // Redirect back to transportation list
        header('Location: /user/transportation');
        exit();
    }
    
}
