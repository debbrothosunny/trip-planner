<?php
namespace App\Controllers;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Trip;
use Core\Database;
use PDO;
use PDOException;
  
class AccommodationController {
    private $accommodation;  
    private $tripModel;    
    private $db;
    private $hotelModel;
    private $roomModel;

    // Constructor to initialize the Accommodation model
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    
        // Initialize models with DB connection
        $this->tripModel = new Trip($this->db);
        $this->accommodation = new Accommodation($this->db);
        $this->hotelModel = new Hotel($this->db);         
        $this->roomModel = new HotelRoom($this->db);       
    }
    


    public function accommodationList() {
        session_start();
    
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to view your accommodations.";
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user']['id']; 
    
        try {
            // Fetch accommodations from the model
            $accommodations = $this->accommodation->getAccommodationsByUserId($user_id);
    
            // Pass the data to the view
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

    // Create a new accommodation

    public function fetchHotelsByLocation($location) {
        $location = urldecode($location); // Decode the location to handle special characters
    
        // Fetch hotels based on location
        $hotels = $this->hotelModel->getHotelsByLocation($location);
    
        $hotelDetails = [];
    
        foreach ($hotels as $hotel) {
            // Fetch the rooms for each hotel
            $rooms = $this->hotelModel->getRoomsByHotel($hotel['id']);
    
            // Store hotel and room details in an array
            $hotelDetails[] = [
                'hotel_id' => $hotel['id'],
                'hotel_name' => $hotel['name'],
                'rooms' => $rooms
            ];
        }
    
        echo json_encode($hotelDetails); // Return the hotel details as JSON
    }

    // Fetch rooms for a specific hotel by hotel ID
    public function fetchHotelRooms($hotelId) {
        // Fetch the rooms for the given hotel ID
        $rooms = $this->roomModel->getRoomsByHotel($hotelId);

        if (empty($rooms)) {
            // If no rooms are found, return a JSON response with an error message
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No rooms available for this hotel.']);
            http_response_code(404); // HTTP status code 404
            exit;
        }

        // If rooms are found, return them as a JSON response
        header('Content-Type: application/json');
        echo json_encode($rooms); // Send the room data as JSON
        exit; // Stop further execution
    }
    
    

    
    


    public function accommodationCreate() {
        session_start();
    
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /");
            exit();
        }
    
        $userId = $_SESSION['user']['id']; // ✅ Define $userId from session
    
        // Load data from DB
        $locations = $this->hotelModel->getAllLocations();  // Fetch all locations
        $rooms = $this->roomModel->getAllRooms();           // Fetch all rooms
        $trips = $this->tripModel->getTripsByUserId($userId); // Fetch trips for the logged-in user
    
        // View
        $viewPath = __DIR__ . '/../../resources/views/user/accommodation_create.php';
        if (file_exists($viewPath)) {
            include($viewPath);
        } else {
            echo "View not found: $viewPath";
        }
    }
    
    
    
    // Store accommodation data in the database
    public function storeAccommodation()
    {
        session_start();
        error_log("StoreAccommodation method called");
    
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit();
        }
    
        // Validate input
        $errors = [];
    
        if (empty($_POST['hotel_id'])) {
            $errors[] = 'Hotel is required.';
        }
    
        if (empty($_POST['room_type'])) {  // Changed to room_type instead of room_id
            $errors[] = 'Room selection is required.';
        }
    
        if (empty($_POST['check_in_date']) || !strtotime($_POST['check_in_date'])) {
            $errors[] = 'Valid check-in date is required.';
        }
    
        if (empty($_POST['check_out_date']) || !strtotime($_POST['check_out_date'])) {
            $errors[] = 'Valid check-out date is required.';
        }
    
        if (!empty($_POST['check_in_date']) && !empty($_POST['check_out_date'])) {
            $checkIn = strtotime($_POST['check_in_date']);
            $checkOut = strtotime($_POST['check_out_date']);
            if ($checkOut < $checkIn) {
                $errors[] = 'Check-out date must be after or equal to check-in date.';
            }
        }
    
        if (empty($_POST['trip_id'])) {
            $errors[] = 'Trip selection is required.';
        }
    
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /user/accommodation/create");
            exit();
        }
    
        $userId = $_SESSION['user']['id'];
        $hotelId = $_POST['hotel_id'];
        $roomType = $_POST['room_type'];  // Store room_type instead of room_id
        $checkInDate = $_POST['check_in_date'];
        $checkOutDate = $_POST['check_out_date'];
        $status = 0;
        $tripId = $_POST['trip_id'];  // Get trip_id from the form
    
        // Check for overlapping bookings of the same room type in the selected dates
        $conflictQuery = $this->db->prepare("
            SELECT * FROM accommodations
            WHERE room_type = ? AND hotel_id = ? AND (
                (check_in_date <= ? AND check_out_date >= ?)
            )
        ");
        $conflictQuery->execute([$roomType, $hotelId, $checkOutDate, $checkInDate]);
    
        if ($conflictQuery->fetch()) {
            $_SESSION['sweetalert'] = [
                'title' => 'Booking Conflict!',
                'text' => 'This room type is already booked during the selected dates.',
                'icon' => 'error'
            ];
            header("Location: /user/accommodation/create");
            exit();
        }
    
        // Fetch the price based on the room type
        $stmt = $this->db->prepare("SELECT price FROM hotel_rooms WHERE room_type = ? AND hotel_id = ?");
        $stmt->execute([$roomType, $hotelId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $room ? $room['price'] : 0;
    
        // Insert the accommodation booking with room_type and trip_id
        $result = $this->accommodation->create($userId, $hotelId, $roomType, $checkInDate, $checkOutDate, $status, $tripId);

    
        if ($result) {
            $_SESSION['sweetalert'] = [
                'title' => 'Success!',
                'text' => 'Accommodation booked successfully!',
                'icon' => 'success'
            ];
            header("Location: /user/accommodation");
        } else {
            $_SESSION['sweetalert'] = [
                'title' => 'Error!',
                'text' => 'Failed to book accommodation. Please try again.',
                'icon' => 'error'
            ];
            header("Location: /user/accommodation/create");
        }
        exit();
    }
    
    
    
    
    
    
    
    
    
    
    
    


    

    // Edit accommodation
    public function accommodationEdit($id) {
        session_start();
    
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header("Location: /login"); 
            exit();
        }
    
        $userId = $_SESSION['user']['id'];
    
        // Fetch accommodation details by ID and ensure it belongs to the logged-in user
        $accommodation = $this->accommodation->getAccommodationByIdAndUser($id, $userId);
    
        // If accommodation is not found or doesn't belong to the user, redirect to the list with an error
        if (!$accommodation) {
            $_SESSION['sweetalert'] = [ 
                'title' => 'Error!',
                'text' => 'Accommodation not found or you do not have permission to edit it.',
                'icon' => 'error'
            ];
            header("Location: /user/accommodation/list");
            exit();
        }
    
        // Format check-in and check-out date if needed
        $check_in_date = date("Y-m-d H:i:s", strtotime($accommodation['check_in_date']));
        $check_out_date = date("Y-m-d H:i:s", strtotime($accommodation['check_out_date']));
    
        // Fetch trip, hotel, and room details for the select options
        $trips = $this->tripModel->getTripsByUserId($userId);
        $hotels = $this->hotelModel->getAllHotels();
        $rooms = $this->roomModel->getAllRooms();
    
        // Define the path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/accommodation_edit.php';
    
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }
    

    // Update accommodation
    public function update($id) {
        session_start();
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $trip_id = htmlspecialchars($_POST['trip_id']);
            $hotel_id = htmlspecialchars($_POST['hotel_id']);
            $room_id = htmlspecialchars($_POST['room_id']);
            $check_in_date = htmlspecialchars($_POST['check_in_date']);
            $check_out_date = htmlspecialchars($_POST['check_out_date']);
            $price = htmlspecialchars($_POST['price']);
            $status = htmlspecialchars($_POST['status']);
    
            // Validate required fields
            if (empty($trip_id) || empty($hotel_id) || empty($room_id) || empty($check_in_date) || empty($check_out_date) || empty($price)) {
                $_SESSION['error'] = "All fields are required!";
                header("Location: /user/accommodation");
                exit();
            }
    
            // Check for valid date format (Y-m-d H:i:s)
            if (!strtotime($check_in_date) || !strtotime($check_out_date)) {
                $_SESSION['error'] = "Invalid date format! Please use a valid date format.";
                header("Location: /user/accommodation");
                exit();
            }
    
            // Sanitize and format dates for saving to the database
            $check_in_date = date("Y-m-d H:i:s", strtotime($check_in_date));
            $check_out_date = date("Y-m-d H:i:s", strtotime($check_out_date));
    
            try {
                $query = "UPDATE accommodations 
                          SET trip_id = :trip_id, hotel_id = :hotel_id, room_id = :room_id, 
                              check_in_date = :check_in_date, check_out_date = :check_out_date, 
                              price = :price, status = :status
                          WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':trip_id', $trip_id);
                $stmt->bindParam(':hotel_id', $hotel_id);
                $stmt->bindParam(':room_id', $room_id);
                $stmt->bindParam(':check_in_date', $check_in_date);
                $stmt->bindParam(':check_out_date', $check_out_date);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':id', $id);
    
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
                    header("Location: /user/accommodation");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['sweetalert'] = [
                    'title' => 'Error!',
                    'text' => 'Database error: ' . $e->getMessage(),
                    'icon' => 'error'
                ];
                header("Location: /user/accommodation");
                exit();
            }
        }
    }
    
    
    // Delete accommodation
    public function delete($id) {
        session_start();
    
        // Check if the accommodation exists
        $query = "SELECT * FROM accommodations WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        $accommodation = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$accommodation) {
            echo json_encode(['success' => false, 'message' => 'Accommodation not found.']);
            exit();
        }
    
        // Delete the accommodation
        $query = "DELETE FROM accommodations WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Accommodation deleted successfully.']);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete accommodation. Please try again.']);
                exit();
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            exit();
        }
    }
}


