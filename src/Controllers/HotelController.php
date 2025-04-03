<?php

namespace App\Controllers;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Accommodation; // You need this
use Core\Database; 
class HotelController {
    private $hotelModel;
    private $hotelRoomModel;
    private $accommodationModel;
    private $db;
    public function __construct() {
        // Get the database connection using the singleton method
        $this->db = Database::getInstance()->getConnection();

        // Instantiate the models and pass the DB connection
        $this->hotelModel = new Hotel($this->db);
        $this->hotelRoomModel = new HotelRoom($this->db);
        $this->accommodationModel = new Accommodation($this->db);
    }

    // Show all hotels
    public function index() {
        $hotels = $this->hotelModel->getAllHotels();
        $viewPath = __DIR__ . '/../../resources/views/admin/hotel/hotel.php';
        include $viewPath;
    }

    // Show create hotel form
    public function create() {
        $viewPath = __DIR__ . '/../../resources/views/admin/hotel/create.php';
        include $viewPath;
    }

    // Handle hotel creation
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $location = $_POST['location'];
            $description = $_POST['description'];

            if ($this->hotelModel->createHotel($name, $location, $description)) {
                $_SESSION['success'] = "Hotel added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add hotel.";
            }
            header("Location: /admin/hotel");
            exit();
        }
    }

    // Show edit form
    public function edit($id) {
        $hotel = $this->hotelModel->getHotelById($id);
        $viewPath = __DIR__ . '/../../resources/views/admin/hotel/edit.php';
        include $viewPath;
    }

    // Handle hotel update
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $location = $_POST['location'];
            $description = $_POST['description'];

            if ($this->hotelModel->updateHotel($id, $name, $location, $description)) {
                $_SESSION['success'] = "Hotel updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update hotel.";
            }
            header("Location: /admin/hotels");
            exit();
        }
    }

    // Handle hotel deletion
    public function delete($id) {
        if ($this->hotelModel->deleteHotel($id)) {
            $_SESSION['success'] = "Hotel deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete hotel.";
        }
        header("Location: /admin/hotels");
        exit();
    }






    // Fetch and display all rooms
    public function roomIndex() {
        $rooms = $this->hotelRoomModel->getAllRooms();
        include __DIR__ . '/../../resources/views/admin/hotel/room/room.php';
    }

    // Show form to create a new room 
    public function createRoom() {
        // Fetch all hotels from the database
        $hotels = $this->hotelRoomModel->getAllHotels();  // Assuming this function is implemented in your model
        
        // Include the view and pass hotels data to it
        include __DIR__ . '/../../resources/views/admin/hotel/room/create.php';
    }

    // Store a new room
    public function storeRoom() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hotel_id = $_POST['hotel_id'];
            $room_type = $_POST['room_type'];
            $price = $_POST['price'];
            $total_rooms = $_POST['total_rooms'];
            $available_rooms = $_POST['available_rooms'];
            $description = $_POST['description'];

            if ($this->hotelRoomModel->createRoom($hotel_id, $room_type, $price, $total_rooms, $available_rooms, $description)) {
                $_SESSION['success'] = "Room added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add room.";
            }
            header("Location: /admin/hotels/rooms");
            exit();
        }
    }

    // Show form to edit an existing room
    public function editRoom($id) {
        // Fetch the room details by ID
        $room = $this->hotelRoomModel->getRoomById($id);
    
        // Fetch all hotels to populate the hotel dropdown in the form
        $hotels = $this->hotelRoomModel->getAllHotels();
    
        if ($room) {
            // Pass the room data and hotel list to the view
            include __DIR__ . '/../../resources/views/admin/hotel/room/edit.php';
        } else {
            $_SESSION['error'] = "Room not found.";
            header("Location: /admin/hotel-rooms");
            exit();
        }
    }

    // Update an existing room
    public function updateRoom($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hotel_id = $_POST['hotel_id'];
            $room_type = $_POST['room_type'];
            $price = $_POST['price'];
            $total_rooms = $_POST['total_rooms'];
            $available_rooms = $_POST['available_rooms'];
            $description = $_POST['description'];

            if ($this->hotelRoomModel->updateRoom($id, $hotel_id, $room_type, $price, $total_rooms, $available_rooms, $description)) {
                $_SESSION['success'] = "Room updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update room.";
            }
            header("Location: /admin/hotels/rooms");
            exit();
        }
    }


    // Delete a room
    public function deleteRoom($id) {
        if ($this->hotelRoomModel->deleteRoom($id)) {
            $_SESSION['success'] = "Room deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete room.";
        }
        header("Location: /admin/hotels/rooms");
        exit();
    }




    public function bookingIndex() {
        $bookings = $this->accommodationModel->getAllPendingBookingsWithHotelRoomDetails(); // You implement this method in model
        include __DIR__ . '/../../resources/views/admin/hotel/hotel_bookings.php';
    }
    

    public function confirmBooking()
    {
        // Step 1: Get the necessary data from the POST request
        $accommodationId = $_POST['accommodation_id'];
        $hotelId = $_POST['hotel_id'];
        $roomType = $_POST['room_type'];
        $totalRooms = $_POST['total_rooms'];  // Number of rooms booked
    
        // Step 2: Update the accommodation status to '1' (confirmed)
        $this->accommodationModel->confirmAccommodation($accommodationId);
    
        // Step 3: Decrease the available and total rooms in the hotel_rooms table
        $this->hotelRoomModel->decreaseAvailableAndTotalRooms($hotelId, $roomType, $totalRooms);
    
        // Step 4: Set a success message in the session for SweetAlert
        $_SESSION['sweetalert'] = [
            'title' => 'Booking Confirmed',
            'text' => 'Booking has been confirmed and rooms updated.',
            'icon' => 'success'
        ];
    
        // Step 5: Redirect the user back to the hotel bookings page
        header("Location: /admin/hotel-bookings");
        exit();
    }
    
    









}

