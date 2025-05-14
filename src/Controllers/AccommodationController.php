<?php

namespace App\Controllers;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\Trip;
use App\Models\HotelRoom;
use App\Models\RoomType;
use Core\Database; // Make sure the Database class is used
use \DateTime;
class AccommodationController // Assuming you have a BaseController
{
    private $accommodationModel;
    private $hotelModel;
    private $roomTypeModel;
    private $roomModel;
    private $tripModel;  
    private $db; // To store the database connection

    public function __construct()
    {
        $database = Database::getInstance(); // Get the singleton instance of the Database class
        $this->db = $database->getConnection(); // Get the PDO connection object

        $this->tripModel = new Trip($this->db);
        $this->accommodationModel = new Accommodation($this->db); // Pass the connection
        $this->hotelModel = new Hotel($this->db);          // Pass the connection
        $this->roomModel = new HotelRoom($this->db);        // Pass the connection
        $this->roomTypeModel = new RoomType($this->db); // Instantiate RoomTypeModel and pass the connection
        session_start(); // Using native PHP sessions
        // You might want to add middleware for authentication (check if user is logged in)
        if (!isset($_SESSION['user'])) {
            header("Location: /"); // Adjust login route as needed
            exit();
        }
    }

    /**
     * Displays the user's accommodations list page.
     *
     * @return void
    */

    public function accommodationList(): void
    {
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to view your accommodations.";
            header("Location: /"); // Redirect to login page
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $accommodations = $this->accommodationModel->findByUserId($userId);

        // Define the path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/accommodation.php';

        // Check if the view file exists and include it
        if (file_exists($viewPath)) {
            include($viewPath); // Include the view file, $accommodations is already available
        } else {
            // Handle the case where the view file is missing
            echo "View file not found: " . $viewPath;
        }
    }

   
    public function accommodationCreate(): void
    {
        // Ensure the user is logged in (you likely have this already)
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to create a booking.";
            header("Location: /"); // Redirect to login page
            exit();
            
        }

        $userId = $_SESSION['user']['id'];

        // Fetch data for dropdowns
        $countries = $this->hotelModel->findAllCountries(); // Assuming this works
        $trips = $this->tripModel->getTripsByUserId($userId); // Assuming this works
        $roomTypes = $this->roomTypeModel->findAllActive(); // Assuming you have a roomTypeModel and a method to fetch active room types
       
        
        // Define the path to the view
        $viewPath = __DIR__ . '/../../resources/views/user/accommodation_create.php';

        // Check if the view file exists and include it
        if (file_exists($viewPath)) {
            include($viewPath); // Include the view file and pass the data ($countries, $trips, $roomTypes)
        } else {
            // Handle the case where the view file is missing
            echo "View file not found: " . $viewPath;
        }
    }


    public function getStatesByCountry(int $countryId): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->hotelModel->findStatesByCountryId($countryId));
    }

    public function getHotelsByCountryAndState(int $countryId, int $stateId): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->hotelModel->findHotelsByCountryAndState($countryId, $stateId));
    }

    public function getRoomTypesByHotel(int $hotelId): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->roomModel->findRoomTypesByHotelId($hotelId));
    }

   
    public function storeAccommodation(): void
    {
        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = 'Please log in to make a booking.';
            header("Location: /"); // Redirect to login page
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $hotelId = $_POST['hotel_id'] ?? null;
        $roomTypeId = $_POST['room_type_id'] ?? null;
        $checkInDate = $_POST['check_in_date'] ?? null;
        $checkOutDate = $_POST['check_out_date'] ?? null;
        $tripId = $_POST['trip_id'] ?? null;

        // Basic validation
        if (empty($hotelId) || empty($roomTypeId) || empty($checkInDate) || empty($checkOutDate) || empty($tripId)) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            header("Location: /user/accommodation/create");
            exit();
        }

        // Validate date formats
        if (!strtotime($checkInDate) || !strtotime($checkOutDate)) {
            $_SESSION['error'] = 'Invalid date format.';
            header("Location: /user/accommodation/create");
            exit();
        }

        // Ensure check-out date is after check-in date
        if (strtotime($checkOutDate) <= strtotime($checkInDate)) {
            $_SESSION['error'] = 'Check-out date must be after check-in date.';
            header("Location: /user/accommodation/create");
            exit();
        }

        // Adjust date formats for database storage
        $checkInDate = (new DateTime($checkInDate))->format('Y-m-d H:i:s');
        $checkOutDate = (new DateTime($checkOutDate))->format('Y-m-d H:i:s');

        // Fetch the price from the hotel_rooms table
        $hotelRoomPrice = $this->roomModel->getPriceByHotelAndType($hotelId, $roomTypeId);

        if (!$hotelRoomPrice || $hotelRoomPrice['price'] === null) {
            $_SESSION['error'] = 'Selected room type not found or price not set for this hotel.';
            header("Location: /user/accommodation/create");
            exit();
        }

        $numberOfNights = (new DateTime($checkInDate))->diff(new DateTime($checkOutDate))->days;
        $totalPrice = floatval($hotelRoomPrice['price']) * $numberOfNights;

        // Create the booking (the model will handle finding an available room)
        $bookingResult = $this->accommodationModel->create(
            $userId,
            $tripId,
            $hotelId,
            $roomTypeId, // Pass the roomTypeId so the model can find an available room
            $checkInDate,
            $checkOutDate,
            $totalPrice,
            '1' // Default status: Pending
        );

        if ($bookingResult) {
            $_SESSION['success'] = 'Booking created successfully!';
            header("Location: /user/accommodation"); // Redirect to the accommodations list page
            exit();
        } else {
            $_SESSION['error'] = 'Failed to create booking. No available room of the selected type for the chosen dates.';
            header("Location: /user/accommodation/create");
            exit();
        }
    }

   


    public function checkRoomAvailability(): void
    {
        // Check if it's an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            header('HTTP/1.1 403 Forbidden');
            echo 'Direct access not allowed.';
            exit();
        }

        // Get the JSON data from the request body
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        $roomTypeId = $data['roomTypeId'] ?? null; // Changed from $roomId
        $checkInDate = $data['checkInDate'] ?? null;
        $checkOutDate = $data['checkOutDate'] ?? null;
        $hotelId = $data['hotelId'] ?? null; // Assuming you need hotelId for availability check

        // Basic validation
        if (empty($roomTypeId) || empty($checkInDate) || empty($checkOutDate) || empty($hotelId)) {
            $this->sendJsonResponse(['available' => false, 'error' => 'Missing parameters']);
            return;
        }

        // Log the received parameters for debugging
        error_log("checkRoomAvailability - Received Hotel ID: " . $hotelId);
        error_log("checkRoomAvailability - Received Room Type ID: " . $roomTypeId);
        error_log("checkRoomAvailability - Received Check-in: " . $checkInDate);
        error_log("checkRoomAvailability - Received Check-out: " . $checkOutDate);

        // Call the model to check availability
        // Assuming you have a method in your Accommodation model to check availability
        // based on hotel ID, room type ID, and dates.
        $available = $this->accommodationModel->isRoomAvailableByTypeAndHotel(
            $hotelId,
            $roomTypeId,
            $checkInDate,
            $checkOutDate
        );

        $this->sendJsonResponse(['available' => $available]);
    }

    private function sendJsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }


    
    
    // public function confirmation(int $id): void
    // {
    //     $booking = $this->accommodationModel->find($id);

    //     if (!$booking || $booking['user_id'] !== ($_SESSION['user']['id'] ?? null)) {
    //         // Handle booking not found or unauthorized access
    //         header("Location: /user/accommodation");
    //         exit();
    //     }

    //     $hotel = $this->hotelModel->find($booking['hotel_id']);
    //     $room = $this->roomModel->find($booking['room_id']);

    //     echo $this->view('booking/confirmation', [
    //         'booking' => $booking,
    //         'hotel' => $hotel,
    //         'room' => $room,
    //     ]);
    // }

    // You can add more actions here for editing, deleting, etc.
}