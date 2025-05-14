<?php

namespace App\Controllers;

use App\Models\Transportation;
use App\Models\Trip;
use \Core\Database;
use PDOException;
use PDO;
include_once __DIR__ . '/../../helpers/csrf_helper.php';
class TransportationController
{
    private $db;
    private $tripModel;
    private $transportation;
    private const HTTP_OK = 200;
    private const HTTP_CREATED = 201;
    private const HTTP_BAD_REQUEST = 400;
    private const HTTP_UNAUTHORIZED = 401;
    private const HTTP_NOT_FOUND = 404;
    private const HTTP_METHOD_NOT_ALLOWED = 405;
    private const HTTP_INTERNAL_SERVER_ERROR = 500;

    public function __construct()
    {
        // Use the Singleton to get the connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();

        // Initialize models
        $this->tripModel = new Trip($this->db);
        $this->transportation = new Transportation($this->db);
    }

    // View all transportation records
    public function transportationList()
    {
      

        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please log in to view your transportation records.";
            header("Location: /");
            exit();
        }

        $userId = $_SESSION['user']['id'];

        try {
            // Fetch all trips for the logged-in user (you might need this in the view)
            $trips = $this->tripModel->getTripsByUserId($userId);

            // Query to fetch transportation records along with trip names
            $query = "
                    SELECT transportations.*, trips.name AS trip_name
                    FROM transportations
                    JOIN trips ON transportations.trip_id = trips.id
                    WHERE trips.user_id = :user_id
                ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $transportations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Encode the transportation data as JSON
            $transportations_json = json_encode($transportations);

            // Path to the view
            $viewPath = __DIR__ . '/../../resources/views/user/transportation.php';

            if (file_exists($viewPath)) {
                // Include the view and pass the $transportations_json variable
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
       

        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to add transportation.";
            header("Location: /");
            exit();
        }

        $user_id = $_SESSION['user']['id'];

        try {
            $query = "SELECT id, name FROM trips WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        

        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            http_response_code(self::HTTP_UNAUTHORIZED);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyCsrfToken()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                exit();
            }

            // Read and decode JSON data from the request body
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(self::HTTP_BAD_REQUEST);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data received']);
                exit();
            }

            if (empty($data['trip_id']) || empty($data['type']) || empty($data['company_name']) || empty($data['departure_location']) || empty($data['arrival_location']) || empty($data['departure_date']) || empty($data['arrival_date']) || empty($data['booking_reference']) || !isset($data['amount'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit();
            }

            $data['user_id'] = $_SESSION['user']['id'];

            try {
                error_log("Data being passed to create: " . json_encode($data));
                $transportation_id = $this->transportation->create($data);
                error_log("Transportation create result: " . var_export($transportation_id, true));

                if ($transportation_id) {
                    $_SESSION['success_message'] = 'Transportation created successfully!';
                    echo json_encode(['success' => true, 'message' => 'Transportation created successfully!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to create transportation entry.']);
                }
                exit();

            } catch (Exception $e) {
                http_response_code(500);
                error_log("Error creating transportation: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error creating transportation: ' . $e->getMessage()]);
                exit();
            }

        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
    }

  

    public function update($id)
    {
        
    
        // Retrieve JSON data from the request body
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);
    
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decoding error
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
            return;
        }
    
        // Ensure all required fields are present
        $requiredFields = [
            'trip_id',
            'type',
            'company_name',
            'departure_location',
            'arrival_location',
            'departure_date',
            'arrival_date',
            'booking_reference',
            'amount'
        ];
    
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
                return;
            }
        }
    
        // Add user_id from the session
        $data['user_id'] = $_SESSION['user']['id'];
    
        // Fetch the existing transportation data from the database
        $existingData = $this->transportation->getById($id); // Assuming you have a getById method in your Transportation model
    
        if (!$existingData) {
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'Transportation record not found.']);
            return;
        }
    
        // Check if any changes were actually made
        $changesMade = false;
        foreach ($requiredFields as $field) {
            if ($data[$field] != $existingData[$field]) {
                $changesMade = true;
                break; // Exit the loop as soon as a change is detected
            }
        }
    
        if (!$changesMade) {
            echo json_encode(['success' => true, 'message' => 'No changes were made.']);
            return;
        }
    
        // Perform the update operation
        $success = $this->transportation->update($id, $data);
    
        // Set JSON response based on update result
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Transportation updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update transportation.']);
        }
        return; // Important: Don't redirect, just return the JSON response
    }



    // Delete a transportation entry
    public function delete($id)
    {
         // Ensure session is started
        //check method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(self::HTTP_METHOD_NOT_ALLOWED);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Fetch transportation record by ID
        $transportation = $this->transportation->getById($id);

        if (!$transportation) {
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'Transportation not found!']);
            return;
        }

        // Perform deletion
        $success = $this->transportation->delete($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Transportation deleted successfully!']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to delete transportation.']);
        }
        return; // Important: Don't redirect, just return the JSON response
    }


    private function verifyCsrfToken() {
        $csrf_token = null;

        // 1. Check standard form POST
        if (isset($_POST['csrf_token'])) {
            $csrf_token = $_POST['csrf_token'];
        } else {
            // 2. Check JSON body
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (is_array($data) && isset($data['csrf_token'])) {
                $csrf_token = $data['csrf_token'];
            }
        }

        if (!$csrf_token || !isset($_SESSION['csrf_token'])) {
            return false;
        }

        if ($csrf_token !== $_SESSION['csrf_token']) {
            return false;
        }

        // Optionally regenerate token after use (recommended)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        return true;
    }

    protected function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
