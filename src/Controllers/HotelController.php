<?php

namespace App\Controllers;

use App\Models\Hotel;
use App\Models\Country; // Assuming you have a Country model
use App\Models\HotelRoom;
use App\Models\RoomType;
use App\Models\Accommodation;
use App\Models\State;   // Assuming you have a State model
use Core\Database;
use PDO;
require_once __DIR__ . '/../../helpers/csrf_helper.php'; // Make sure this path is correct
class HotelController
{
    private $hotelModel;
    private $hotelRoomModel;
    private $accommodationModel;
    private $roomTypeModel;
    private $countryModel;
    private $stateModel;
    private $db;
    private $itemsPerPage = 5; // Number of hotels to display per page

    public function __construct()
    {
        // Get the database connection using the singleton method
        $this->db = Database::getInstance()->getConnection();

        // Instantiate the models and pass the DB connection
        $this->hotelModel = new Hotel($this->db);
        $this->hotelRoomModel = new HotelRoom($this->db);
        $this->accommodationModel = new Accommodation($this->db);
        $this->roomTypeModel = new RoomType($this->db);
        $this->countryModel = new Country($this->db); // Initialize the property
        $this->stateModel = new State($this->db);

    }


    public function hotelList()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
    
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $this->itemsPerPage = 10; // Or whatever your desired number per page is
        $offset = ($page - 1) * $this->itemsPerPage;
    
        // Use a model method to fetch ALL hotels with pagination
        $allHotels = $this->hotelModel->getAllHotelsWithLimit($this->itemsPerPage, $offset);
    
        // Use a model method to get the TOTAL count of ALL hotels for pagination
        $totalHotels = $this->hotelModel->getTotalAllHotels();
    
        $totalPages = ceil($totalHotels / $this->itemsPerPage);
    
        $activeCountries = $this->countryModel->findAllActive();
        $activeStates = $this->stateModel->findAllActive();
    
        $data = [
            'hotels' => $allHotels,
            'countries' => $activeCountries,
            'states' => $activeStates,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];
        extract($data);
    
        $hotelIndexViewPath = __DIR__ . '/../../resources/views/admin/hotel/hotel_list.php';
        if (file_exists($hotelIndexViewPath)) {
            include $hotelIndexViewPath;
        } else {
            echo "Hotel list view not found!";
        }
    }


    public function createHotel()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
    
        // Fetch active countries and states
        $countries = $this->countryModel->findAllActive();
        $states = $this->stateModel->findAllActive();
    
        // Get the CSRF token
        $csrfToken = getCsrfToken();
    
        $data = [
            'countries' => $countries,
            'states' => $states,
            'csrf_token' => $csrfToken, // Pass the token to the view
        ];
        extract($data);
    
        $createHotelViewPath = __DIR__ . '/../../resources/views/admin/hotel/hotel_create.php';
        if (file_exists($createHotelViewPath)) {
            include $createHotelViewPath;
        } else {
            echo "Create hotel view not found!";
        }
    }


    
    
    public function storeHotel()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verifyCsrfToken()) {
                $_SESSION['error'] = 'CSRF token validation failed.';
                header("Location: /admin/hotel/create");
                exit();
            }

            // Sanitize and validate input data for all hotel columns
            $country_id = filter_input(INPUT_POST, 'country_id', FILTER_SANITIZE_NUMBER_INT);
            $state_id = filter_input(INPUT_POST, 'state_id', FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $star_rating = filter_input(INPUT_POST, 'star_rating', FILTER_SANITIZE_NUMBER_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);

            // Perform basic validation for all required fields
            if (empty($country_id) || empty($state_id) || empty($name) || empty($address) || !is_numeric($star_rating) || !is_numeric($status)) {
                $_SESSION['error'] = 'Please fill in all required fields.';
                header("Location: /admin/hotel/create");
                exit();
            }

            // Call the createHotel method in your Hotel model
            if ($this->hotelModel->createHotel($country_id, $state_id, $name, $address, $description, $star_rating, $status)) {
                $_SESSION['success'] = 'Hotel created successfully.';
                header("Location: /admin/hotel");
                exit();
            } else {
                $_SESSION['error'] = 'Error creating hotel.';
                header("Location: /admin/hotel/create");
                exit();
            }
        } else {
            // If the request method is not POST, redirect to the create form
            header("Location: /admin/hotel/create"); // Corrected redirect
            exit();
        }
    }


    public function updateHotel($id) // Receive the $id from the URL segment
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $country_id = filter_input(INPUT_POST, 'country_id', FILTER_SANITIZE_NUMBER_INT);
            $state_id = filter_input(INPUT_POST, 'state_id', FILTER_SANITIZE_NUMBER_INT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $star_rating = filter_input(INPUT_POST, 'star_rating', FILTER_SANITIZE_NUMBER_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);
    
            // Perform basic validation
            if (empty($country_id) || empty($state_id) || empty($name) || empty($address) || !is_numeric($star_rating) || !is_numeric($status)) {
                $_SESSION['error'] = 'Please fill in all required fields.';
                header("Location: /admin/hotel");
                exit();
            }
    
            // Call the updateHotel method in your Hotel model, now passing the $id
            if ($this->hotelModel->updateHotel($country_id, $state_id, $name, $address, $description, $star_rating, $status, $id)) {
                $_SESSION['success'] = 'Hotel updated successfully.';
                header("Location: /admin/hotel"); // Redirect back to the list with success
                exit();
            } else {
                $_SESSION['error'] = 'Error updating hotel.';
                header("Location: /admin/hotel"); // Redirect back to the list with error
                exit();
            }
        } else {
            // If the request method is not POST, redirect back to the hotel list page.
            header("Location: /admin/hotel");
            exit();
        }
    }


    public function deleteHotel($id)
    {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        // Sanitize the ID to prevent potential security issues
        $hotelId = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        if ($hotelId === false || $hotelId === null) {
            $_SESSION['error'] = 'Invalid hotel ID.';
            header("Location: /admin/hotel");
            exit();
        }

        // Call the deleteHotel method in your Hotel model
        if ($this->hotelModel->deleteHotel($hotelId)) {
            $_SESSION['success'] = 'Hotel deleted successfully.';
        } else {
            $_SESSION['error'] = 'Error deleting hotel.';
        }

        header("Location: /admin/hotel");
        exit();
    }




    
    // ✅ Country List
    public function countryList() {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $stmt = $this->db->query("SELECT id, name, status FROM countries");
        $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = ['countries' => $countries];
        extract($data);

        $countryListViewPath = __DIR__ . '/../../resources/views/admin/country/country.php';
        if (file_exists($countryListViewPath)) {
            include $countryListViewPath;
        } else {
            echo "Country list view not found!";
        }
    }

    // ✅ Show Create Country Form
    public function createCountry() {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $countryCreateViewPath = __DIR__ . '/../../resources/views/admin/country/country_create.php';
        if (file_exists($countryCreateViewPath)) {
            include $countryCreateViewPath;
        } else {
            echo "Create country view not found!";
        }
    }
    public function storeCountry() {

        // Start session if not already started (though your helper likely handles this)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!verifyCsrfToken()) {
                $_SESSION['error'] = 'CSRF token validation failed.';
                header("Location: /admin/country/create"); // Redirect back to the form
                exit();
            }
    
            $name = $_POST['name'] ?? '';
            $status = $_POST['status'] ?? 1; // Default status to Inactive if not set
    
            if (!empty($name)) {
                $stmt = $this->db->prepare("INSERT INTO countries (name, status) VALUES (:name, :status)");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Country created successfully.'; // Optional success message
                    header("Location: /admin/country"); // Redirect to country list
                    exit();
                } else {
                    $_SESSION['error'] = 'Error creating country.'; // Optional error message
                    header("Location: /admin/country/create"); // Redirect back to the form
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Country name cannot be empty.'; // Optional error message
                header("Location: /admin/country/create"); // Redirect back to the form
                exit();
            }
        } else {
            // Handle non-POST requests
            header("Location: /admin/country");
            exit();
        }
    }



    // ✅ Update Country
    public function updateCountry($id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $status = $_POST['status'] ?? 0;

            if (!empty($name)) {
                // Call the updateCountry method in the MODEL
                if ($this->countryModel->updateCountry($id, $name, $status)) {
                    header("Location: /admin/country"); // Redirect to country list
                    exit();
                } else {
                    echo "Error updating country.";
                }
            } else {
                echo "Country name cannot be empty.";
            }
        } else {
            // Handle non-POST requests (optional)
            header("Location: /admin/country");
            exit();
        }
    }


    // ✅ Delete Country
    public function deleteCountry($id) {
      
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $stmt = $this->db->prepare("DELETE FROM countries WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: /admin/country"); // Redirect to country list
            exit();
        } else {
            echo "Error deleting country.";
        }
    }
  

   // ✅ Show States for a Specific Country

    public function showStates($countryId) {
      
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        // Fetch the country details
        $countryStmt = $this->db->prepare("SELECT id, name FROM countries WHERE id = :id");
        $countryStmt->bindParam(':id', $countryId, PDO::PARAM_INT);
        $countryStmt->execute();
        $country = $countryStmt->fetch(PDO::FETCH_ASSOC);

        if (!$country) {
            echo "Country not found!";
            return;
        }

        // Fetch states for the given country
        $stateStmt = $this->db->prepare("SELECT id, name, status FROM states WHERE country_id = :country_id");
        $stateStmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        $stateStmt->execute();
        $states = $stateStmt->fetchAll(PDO::FETCH_ASSOC);

        $data = ['country' => $country, 'states' => $states];
        extract($data);

        $stateListViewPath = __DIR__ . '/../../resources/views/admin/state/state.php';
        if (file_exists($stateListViewPath)) {
            include $stateListViewPath;
        } else {
            echo "State list view not found!";
        }
    }



    // ✅ Show Create State Form for a Specific Country
    public function createStateForm($countryId) {
        
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        // Fetch the country details to display in the form
        $countryStmt = $this->db->prepare("SELECT id, name FROM countries WHERE id = :id");
        $countryStmt->bindParam(':id', $countryId, PDO::PARAM_INT);
        $countryStmt->execute();
        $country = $countryStmt->fetch(PDO::FETCH_ASSOC);

        if (!$country) {
            echo "Country not found!";
            return;
        }

        $data = ['country' => $country];
        extract($data);

        $stateCreateViewPath = __DIR__ . '/../../resources/views/admin/state/state_create.php';
        if (file_exists($stateCreateViewPath)) {
            include $stateCreateViewPath;
        } else {
            echo "Create state view not found!";
        }
    }

    // ✅ Store New State
    public function storeState() {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $countryId = $_POST['country_id'] ?? null;
            $name = $_POST['name'] ?? '';
            $status = $_POST['status'] ?? 0;

            if ($countryId && !empty($name)) {
                $stmt = $this->db->prepare("INSERT INTO states (country_id, name, status) VALUES (:country_id, :name, :status)");
                $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    header("Location: /admin/country/state/" . $countryId); // Redirect back to the states list for the country
                    exit();
                } else {
                    echo "Error creating state.";
                }
            } else {
                echo "Country ID and state name are required.";
            }
        } else {
            header("Location: /admin/country"); // Redirect if not a POST request
            exit();
        }
    }

    // ✅ Update State
    public function updateState($id) {
      
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $status = $_POST['status'] ?? 0;

            if (!empty($name)) {
                $stmt = $this->db->prepare("UPDATE states SET name = :name, status = :status WHERE id = :id");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Redirect back to the states list for the specific country
                    $stateStmt = $this->db->prepare("SELECT country_id FROM states WHERE id = :id");
                    $stateStmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stateStmt->execute();
                    $state = $stateStmt->fetch(PDO::FETCH_ASSOC);

                    if ($state && $state['country_id']) {
                        header("Location: /admin/country/state/" . $state['country_id']);
                        exit();
                    } else {
                        header("Location: /admin/country"); // Fallback if country ID not found
                        exit();
                    }
                } else {
                    echo "Error updating state.";
                }
            } else {
                echo "State name cannot be empty.";
            }
        } else {
            header("Location: /admin/country"); // Redirect if not a POST request
            exit();
        }
    }

    // ✅ Delete State
    public function deleteState($id) {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        // Get the country_id before deleting to redirect correctly
        $stateStmt = $this->db->prepare("SELECT country_id FROM states WHERE id = :id");
        $stateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stateStmt->execute();
        $state = $stateStmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("DELETE FROM states WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($state && $state['country_id']) {
                header("Location: /admin/country/state/" . $state['country_id']);
                exit();
            } else {
                header("Location: /admin/country"); // Fallback if country ID not found
                exit();
            }
        } else {
            echo "Error deleting state.";
        }
    }

   

    // ✅ Display list of room types
    public function roomTypeList() {
        
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $stmt = $this->db->prepare("SELECT id, name, status FROM room_types");
        $stmt->execute();
        $roomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = ['roomTypes' => $roomTypes];
        extract($data);

        $roomTypeListViewPath = __DIR__ . '/../../resources/views/admin/room_type/room_type.php';
        if (file_exists($roomTypeListViewPath)) {
            include $roomTypeListViewPath;
        } else {
            echo "Room type list view not found!";
        }
    }



    // ✅ Show form to create a new room type
    public function createRoomType() {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $roomTypeCreateViewPath = __DIR__ . '/../../resources/views/admin/room_type/room_type_create.php';
        if (file_exists($roomTypeCreateViewPath)) {
            include $roomTypeCreateViewPath;
        } else {
            echo "Create room type view not found!";
        }
    }

    // ✅ Store a new room type
    public function storeRoomType() {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $status = $_POST['status'] ?? 0;

            if (!empty($name)) {
                $stmt = $this->db->prepare("INSERT INTO room_types (name, status) VALUES (:name, :status)");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    header("Location: /admin/room-type"); // Redirect to room type list
                    exit();
                } else {
                    echo "Error creating room type.";
                }
            } else {
                echo "Room type name cannot be empty.";
            }
        } else {
            header("Location: /admin/room-type");
            exit();
        }
    }



    // ✅ Update an existing room type
    public function updateRoomType($id) {
     
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $status = $_POST['status'] ?? 0;

            if (!empty($name)) {
                $stmt = $this->db->prepare("UPDATE room_types SET name = :name, status = :status WHERE id = :id");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    header("Location: /admin/room-type"); // Redirect to room type list
                    exit();
                } else {
                    echo "Error updating room type.";
                }
            } else {
                echo "Room type name cannot be empty.";
            }
        } else {
            header("Location: /admin/room-type");
            exit();
        }
    }

    // ✅ Delete a room type
    public function deleteRoomType($id) {
      
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $stmt = $this->db->prepare("DELETE FROM room_types WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: /admin/room-type"); // Redirect to room type list
            exit();
        } else {
            echo "Error deleting room type.";
        }
    }
 



    public function hotelRoomList()
    {
      
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $hotelRooms = $this->hotelRoomModel->getAllRooms();
        $roomTypes = $this->roomTypeModel->findAllActive();
        $hotels = $this->hotelModel->findAllActive(); // Assuming findAll() exists in your Hotel model

        $data = ['hotelRooms' => $hotelRooms, 'roomTypes' => $roomTypes, 'hotels' => $hotels];
        extract($data);


        $hotelRoomListViewPath = __DIR__ . '/../../resources/views/admin/hotel_room/hotel_room.php';
        if (file_exists($hotelRoomListViewPath)) {
            include $hotelRoomListViewPath;
        } else {
            echo "Hotel room list view not found!";
        }
    }

    public function createHotelRoom()
    {
        
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        $roomTypes = $this->roomTypeModel->findAllActive();
        $hotels = $this->hotelModel->findAllActive(); // Assuming findAll() exists in your Hotel model

        $data = ['roomTypes' => $roomTypes, 'hotels' => $hotels];
        extract($data);

        $createHotelRoomViewPath = __DIR__ . '/../../resources/views/admin/hotel_room/create_hotel_room.php';
        if (file_exists($createHotelRoomViewPath)) {
            include $createHotelRoomViewPath;
        } else {
            echo "Create hotel room view not found!";
        }
    }

    public function storeHotelRoom()
    {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hotel_id = filter_input(INPUT_POST, 'hotel_id', FILTER_SANITIZE_NUMBER_INT);
            $room_type_id = filter_input(INPUT_POST, 'room_type_id', FILTER_SANITIZE_NUMBER_INT);
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_NUMBER_INT);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $total_rooms = filter_input(INPUT_POST, 'total_rooms', FILTER_SANITIZE_NUMBER_INT);
            $available_rooms = filter_input(INPUT_POST, 'available_rooms', FILTER_SANITIZE_NUMBER_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);
            $amenities = filter_input(INPUT_POST, 'amenities', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (empty($room_type_id) || empty($hotel_id) || empty($capacity) || !is_numeric($price) || empty($available_rooms) || !is_numeric($status) || empty($total_rooms)) {
                $_SESSION['error'] = 'Please fill in all required fields.';
                header("Location: /admin/hotel-room/create");
                exit();
            }

            if ($this->hotelRoomModel->createRoom($hotel_id, $room_type_id, $capacity, $price, $description, $total_rooms, $available_rooms, $status, $amenities)) {
                $_SESSION['success'] = 'Hotel room created successfully.';
                header("Location: /admin/hotel-room");
                exit();
            } else {
                $_SESSION['error'] = 'Error creating hotel room.';
                header("Location: /admin/hotel-room/create");
                exit();
            }
        } else {
            header("Location: /admin/hotel-room/create");
            exit();
        }
    }

    public function updateHotelRoom($id)
    {
       
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hotel_id = filter_input(INPUT_POST, 'hotel_id', FILTER_SANITIZE_NUMBER_INT);
            $room_type_id = filter_input(INPUT_POST, 'room_type_id', FILTER_SANITIZE_NUMBER_INT);
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_NUMBER_INT);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $total_rooms = filter_input(INPUT_POST, 'total_rooms', FILTER_SANITIZE_NUMBER_INT);
            $available_rooms = filter_input(INPUT_POST, 'available_rooms', FILTER_SANITIZE_NUMBER_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);
            $amenities = filter_input(INPUT_POST, 'amenities', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (empty($room_type_id) || empty($hotel_id) || empty($capacity) || !is_numeric($price) || empty($available_rooms) || !is_numeric($status) || empty($total_rooms)) {
                $_SESSION['error'] = 'Please fill in all required fields.';
                header("Location: /admin/hotel-rooms/edit/" . $id);
                exit();
            }

            if ($this->hotelRoomModel->updateRoom($id, $hotel_id, $room_type_id, $capacity, $price, $description, $total_rooms, $available_rooms, $status, $amenities)) {
                $_SESSION['success'] = 'Hotel room updated successfully.';
                header("Location: /admin/hotel-room");
                exit();
            } else {
                $_SESSION['error'] = 'Error updating hotel room.';
                header("Location: /admin/hotel-rooms/edit/" . $id);
                exit();
            }
        } else {
            header("Location: /admin/hotel-rooms/edit/" . $id);
            exit();
        }
    }

    public function deleteHotelRoom($id)
    {
      
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }

        if ($this->hotelRoomModel->deleteRoom($id)) {
            $_SESSION['success'] = 'Hotel room deleted successfully.';
        } else {
            $_SESSION['error'] = 'Error deleting hotel room.';
        }
        header("Location: /admin/hotel-room");
        exit();
    }




    public function bookingIndex() {
        $bookings = $this->accommodationModel->getAllPendingBookingsWithHotelRoomDetails();
        
        $viewPath = __DIR__ . '/../../resources/views/admin/hotel/hotel_bookings.php';
        include $viewPath;
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

