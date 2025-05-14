<?php
namespace App\Controllers;

use PDO;
use App\Models\User;
use App\Models\Trip;
use App\Models\Payment;
use Core\Database; // ✅ Correct the namespace

class AdminController {
    private $db;
    private $userModel;
    private $tripModel;
    private $paymentModel;
    private $itemsPerPage = 3; 

    public function __construct() {
        $database = Database::getInstance(); // Use the singleton instance
        $this->db = $database->getConnection(); // Get the connection

        $this->userModel = new User($this->db);
        $this->tripModel = new Trip($this->db);
        $this->paymentModel = new Payment($this->db);
    }
   
    // ✅ Dashboard: View System Analytics & Users
    public function dashboard() {
            session_start();

            if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
                header("Location: /");
                exit();
            }

            $year = $_GET['year'] ?? null;
            $month = $_GET['month'] ?? null;

            $analytics = $this->systemAnalytics($year, $month);

            // Fetch all users, including the created_at column
            $stmtAllUsers = $this->db->query("SELECT id, name, email, role, created_at FROM users");
            $users = $stmtAllUsers->fetchAll(PDO::FETCH_ASSOC);

            // Fetch administrator's profile photo and name, including created_at
            $stmtAdmin = $this->db->prepare("SELECT name, profile_photo, created_at FROM users WHERE id = ?");
            $stmtAdmin->execute([$_SESSION['user']['id']]);
            $adminData = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
            $userName = $adminData['name'] ?? 'Administrator';
            $profilePhoto = $adminData['profile_photo'] ?? null;
            $adminActiveSince = $adminData['created_at'] ?? null; // Get admin's active since

            // Fetch pending bookings (no change needed)
            $pendingBookings = $this->getPendingBookings();

            // Set notification if there are pending bookings (no change needed)
            if (!empty($pendingBookings)) {
                $_SESSION['booking_notification'] = [
                    'type' => 'warning',
                    'message' => 'You have ' . count($pendingBookings) . ' pending hotel booking(s)!'
                ];
            }

            // Fetch user counts by country (no change needed)
            $stmtCountryCounts = $this->db->query("SELECT country, COUNT(*) AS user_count FROM users WHERE country IS NOT NULL AND country != '' GROUP BY country ORDER BY user_count DESC");
            $userCountsByCountry = $stmtCountryCounts->fetchAll(PDO::FETCH_ASSOC);

            // Fetch user counts by city (no change needed)
            $stmtCityCounts = $this->db->query("SELECT city, COUNT(*) AS user_count FROM users WHERE city IS NOT NULL AND city != '' GROUP BY city ORDER BY user_count DESC");
            $userCountsByCity = $stmtCityCounts->fetchAll(PDO::FETCH_ASSOC);

            // Fetch ongoing trips (no change needed)
            $stmtOngoingTrips = $this->db->query("SELECT t.* FROM trips t WHERE t.start_date <= CURDATE() AND t.end_date >= CURDATE()");
            $ongoingTrips = $stmtOngoingTrips->fetchAll(PDO::FETCH_ASSOC);

            // Fetch completed trips (no change needed)
            $stmtCompletedTrips = $this->db->query("SELECT * FROM trips WHERE end_date < CURDATE()");
            $completedTrips = $stmtCompletedTrips->fetchAll(PDO::FETCH_ASSOC);

            $data = [
                'total_users' => $analytics['total_users'] ?? 0,
                'total_trips' => $analytics['total_trips'] ?? 0,
                'total_payment' => $analytics['total_payment'] ?? 0.00,
                'total_trip_participants' => $analytics['total_trip_participants'] ?? 0,
                'total_accepted' => $analytics['total_accepted'] ?? 0,
                'profilePhoto' => $profilePhoto,
                'userName' => $userName, // Include userName for the greeting
                'monthly_growth' => $analytics['monthly_growth'] ?? [],
                'selected_month_growth' => $analytics['selected_month_growth'] ?? null,
                'users' => $users,
                'booking_notification' => $_SESSION['booking_notification'] ?? null,
                'userCountsByCountry' => $userCountsByCountry,
                'userCountsByCity' => $userCountsByCity,
                'ongoingTrips' => $ongoingTrips,
                'completedTrips' => $completedTrips,
                'adminActiveSince' => $adminActiveSince, // Add admin's active since to the data
            ];

            unset($_SESSION['booking_notification']);

            extract($data);

            $dashboardViewPath = __DIR__ . '/../../resources/views/admin/dashboard.php';
            if (file_exists($dashboardViewPath)) {
                include $dashboardViewPath;
            } else {
                echo "Dashboard view not found!";
            }
    }

    

    private function getPendingBookings() { // Function defined as class method
        $pendingBookingsStmt = $this->db->query("SELECT * FROM accommodations WHERE status = 0");
        return $pendingBookingsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    
    public function systemAnalytics($year = null, $month = null) { // Added parameters
        // Count total users
        $userCountStmt = $this->db->query("SELECT COUNT(*) AS count FROM users");
        $userCount = $userCountStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
        // Count total trips
        $tripCountStmt = $this->db->query("SELECT COUNT(*) AS count FROM trips");
        $tripCount = $tripCountStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
        // Count total payment (assuming a `payments` table and `amount` field)
        $paymentStmt = $this->db->query("SELECT SUM(amount) AS total FROM payments");
        $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
    
        // Count trip participants by status
        $acceptedStmt = $this->db->query("SELECT COUNT(*) AS count FROM trip_participants WHERE status = 'accepted'");
        $accepted = $acceptedStmt->fetch(PDO::FETCH_ASSOC)['count'];

    
        $declinedStmt = $this->db->query("SELECT COUNT(*) AS count FROM trip_participants WHERE status = 'declined'");
        $declined = $declinedStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
        // Monthly New User Growth (Only Monthly Growth)
        $monthlyGrowthStmt = $this->db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count
            FROM users
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");
        $monthlyGrowth = $monthlyGrowthStmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Selected Month Growth (if year and month are provided)
        if ($year !== null && $month !== null) {
            $selectedMonthStmt = $this->db->query("
                SELECT COUNT(*) AS count
                FROM users
                WHERE DATE_FORMAT(created_at, '%Y-%m') = '$year-$month'
            ");
            $selectedMonthCount = $selectedMonthStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
            $previousMonth = ($month == 1) ? 12 : $month - 1;
            $previousYear = ($month == 1) ? $year - 1 : $year;
    
            $previousSelectedMonthStmt = $this->db->query("
                SELECT COUNT(*) AS count
                FROM users
                WHERE DATE_FORMAT(created_at, '%Y-%m') = '$previousYear-$previousMonth'
            ");
            $previousSelectedMonthCount = $previousSelectedMonthStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
            $selectedMonthGrowth = $selectedMonthCount - $previousSelectedMonthCount;
        } else {
            $selectedMonthGrowth = null; // No growth if no month is selected
        }
    
        return [
            'total_users' => $userCount,
            'total_trips' => $tripCount,
            'total_payment' => $payment,
            'total_trip_participants' => $accepted  + $declined,
            'total_accepted' => $accepted,
            'total_declined' => $declined,
            'monthly_growth' => $monthlyGrowth,
            'selected_month_growth' => $selectedMonthGrowth,
        ];
    }


    public function tripParticipant() {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
    
        // Fetch trip names from the trips table (using the 'name' column)
        $tripStmt = $this->db->query("SELECT id, name FROM trips");
        $trips = $tripStmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch participants for each trip
        $participants = [];
        foreach ($trips as $trip) {
            $tripId = $trip['id'];
            $participantStmt = $this->db->prepare("
                SELECT users.id AS user_id, 
                       users.name AS user_name, 
                       trip_participants.status AS trip_status, 
                       payments.payment_status, 
                       payments.amount 
                FROM trip_participants
                JOIN users ON trip_participants.user_id = users.id
                LEFT JOIN payments ON payments.user_id = users.id AND payments.trip_id = :trip_id
                WHERE trip_participants.trip_id = :trip_id
            ");
            $participantStmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $participantStmt->execute();
            $participants[$tripId] = $participantStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        // Pass data to the view
        $data = [
            'trips' => $trips,
            'participants' => $participants
        ];
    
        // Load the trip participant view
        $tripParticipantViewPath = __DIR__ . '/../../resources/views/admin/trip_participant.php';
        if (file_exists($tripParticipantViewPath)) {
            include $tripParticipantViewPath;
        } else {
            echo "Trip participant view not found!";
        }
    }

    public function tripPaymentDetails($tripId, $userId)
    {
         // Sanitize and validate input (as before)
         $tripId = filter_var($tripId, FILTER_VALIDATE_INT);
         $userId = filter_var($userId, FILTER_VALIDATE_INT);
         if (!$tripId || !$userId) {
             http_response_code(400);
             echo json_encode(['error' => 'Invalid Trip ID or User ID']);
             return;
         }
 
         // Instantiate your Payment model
         $paymentModel = new Payment($this->db);
 
         // Fetch payment details
         $paymentDetails = $paymentModel->getPaymentByUserAndTrip($userId, $tripId);
 
         if ($paymentDetails) {
             // Fetch trip name and user name
             $tripName = $paymentModel->getTripNameById($tripId);
             $userName = $paymentModel->getUserNameById($userId);
 
             // Add trip name and user name to the response data
             $paymentDetails['trip_name'] = $tripName;
             $paymentDetails['user_name'] = $userName;
 
             header('Content-Type: application/json');
             echo json_encode($paymentDetails);
         } else {
             http_response_code(404);
             echo json_encode(['error' => 'Payment details not found']);
         }
    }


    public function acceptPayment($tripId, $userId)
    {

        // Sanitize and validate input
        $tripId = filter_var($tripId, FILTER_VALIDATE_INT);
        $userId = filter_var($userId, FILTER_VALIDATE_INT);
    
        if (!$tripId || !$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid Trip ID or User ID']);
            return;
        }
    
        // Load your Payment model (already done in the constructor)
    
        // Find the relevant payment record (you might want to verify payment status is currently 'pending')
        $payment = $this->paymentModel->getPaymentByUserAndTrip($userId, $tripId);
    
        if (!$payment || $payment['payment_status'] != 1) { // Assuming 1 is 'pending'
            http_response_code(400);
            echo json_encode(['error' => 'No pending payment found for this user and trip.']);
            return;
        }
    
        // Update the payment status to 'completed' (assuming 0 is 'completed' in your database)
        $updated = $this->paymentModel->updatePaymentStatus($payment['id'], 0);
    
        if ($updated) {
            // Optionally, you might want to:
            // - Update a status in the trip_participants table
            // - Send an email notification to the user
            // - Log the action
    
            echo json_encode(['success' => true, 'message' => 'Payment accepted and status updated to completed.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update the payment status.']);
        }
    }
    



    public function viewUserTrips($id) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header("Location: /");
            exit();
        }
    
        // Fetch the user's details by ID
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$user) {
            echo "User not found!";
            return;
        }
    
        // ✅ Fetch trips with accommodations, hotels, and hotel room details
        $tripStmt = $this->db->prepare("
        SELECT 
            t.id AS trip_id, 
            t.name AS trip_name, 
            t.start_date, 
            t.end_date, 
            t.budget,
    
            a.room_type,
            a.check_in_date,
            a.check_out_date,
    
            h.name AS hotel_name,
            h.location,
    
            hr.price,
            hr.description AS room_description
    
        FROM trips t
        LEFT JOIN accommodations a ON a.user_id = t.user_id
        LEFT JOIN hotels h ON a.hotel_id = h.id
        LEFT JOIN hotel_rooms hr ON hr.hotel_id = h.id AND hr.room_type = a.room_type
        WHERE t.user_id = ?
    ");
    
    
        $tripStmt->execute([$id]);
        $userTrips = $tripStmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Pass data to the view
        $data = [
            'user' => $user,
            'userTrips' => $userTrips,
        ];
    
        // Load the view to display the user's trips and accommodations
        $viewPath = __DIR__ . '/../../resources/views/admin/user_trips.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View for user trips not found!";
        }
    }
    



    public function users(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->itemsPerPage;

        $totalUsers = $this->userModel->getTotalUsers(); // Implement this in your UserModel
        $totalPages = ceil($totalUsers / $this->itemsPerPage);

        $users = $this->userModel->getUsersWithLimit($this->itemsPerPage, $offset); // Implement this in your UserModel

        $userDataWithTrips = [];
        foreach ($users as $user) {
            $trips = $this->tripModel->getTripsByUserId($user['id']);
            $user['trips'] = $trips;
            $userDataWithTrips[] = $user;
        }  

        $data['users'] = $userDataWithTrips;
        $data['currentPage'] = $page;
        $data['totalPages'] = $totalPages;
        $viewPath = __DIR__ . '/../../resources/views/admin/user.php';

        if (file_exists($viewPath)) {
            extract($data);
            include($viewPath);
        } else {
            echo "View file not found: " . $viewPath;
        }
    }


    public function deactivateUser(int $id): void
    {
        if ($this->userModel->updateUserStatus($id, 1)) { // 1 for inactive
            $_SESSION['success'] = 'User deactivated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to deactivate user.';
        }
        header("Location: /admin/user");
        exit();
    }

    public function activateUser(int $id): void
    {
        if ($this->userModel->updateUserStatus($id, 0)) { // 0 for active
            $_SESSION['success'] = 'User activated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to activate user.';
        }
        header("Location: /admin/user");
        exit();
    }


    public function deleteUser(int $id): void
    {
        if ($this->userModel->deleteUser($id)) {
            $_SESSION['success'] = 'User deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete user.';
        }
        header("Location: /admin/user");
        exit();
    }



    // user Profile Upade Logic
    public function showProfile() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
    
        // Fetch user details from the database
        $user = $this->userModel->getUser($user_id);
    
        if (!$user) {
            $_SESSION['error'] = "User not found!";
            header("Location: /dashboard.php");
            exit();
        }
    
        // ✅ Define the profile view path
        $profileViewPath = __DIR__ . '/../../resources/views/admin/profile.php';
    
        // ✅ Check if view file exists before including
        if (file_exists($profileViewPath)) {
            include $profileViewPath;
        } else {
            echo "User profile view not found!";
        }
    }
    
    
    public function updateProfile()
    {
        session_start();

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

        // Check if any changes were actually made
        $existingUser = $this->userModel->getUser($user_id);
        if ($existingUser) {
            $hasChanges = false;
            if ($name !== $existingUser['name']) $hasChanges = true;
            if ($email !== $existingUser['email']) $hasChanges = true;
            if ($phone !== $existingUser['phone']) $hasChanges = true;
            if ($country !== $existingUser['country']) $hasChanges = true;
            if ($language !== $existingUser['language']) $hasChanges = true;
            if ($currency !== $existingUser['currency']) $hasChanges = true;
            if ($gender !== $existingUser['gender']) $hasChanges = true;
            if (!empty($_FILES['profile_photo']['name'])) $hasChanges = true;
            if ($password !== null) $hasChanges = true;

            if (!$hasChanges) {
                echo json_encode(['success' => false, 'message' => 'No changes were made.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit();
        }

        // Handle profile photo upload
        $profilePhotoPath = $existingUser['profile_photo'] ?? null; // Keep existing if no new upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_photo']['type'], $allowedMimeTypes)) {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/image/profile_photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $uniqueFilename = uniqid() . '_' . basename($_FILES['profile_photo']['name']);
                $newProfilePhotoPath = 'image/profile_photos/' . $uniqueFilename;
                if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $uniqueFilename)) {
                    echo json_encode(['success' => false, 'message' => 'Error uploading profile photo.']);
                    exit();
                }
                if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
                    echo json_encode(['success' => false, 'message' => 'Profile photo size exceeds the limit (2MB).']);
                    exit();
                }
                // Delete the old profile photo if a new one is uploaded
                if ($profilePhotoPath && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $profilePhotoPath)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $profilePhotoPath);
                }
                $profilePhotoPath = $newProfilePhotoPath;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid profile photo format. Only JPEG, PNG, and GIF are allowed.']);
                exit();
            }
        }

        // Update user profile
        if ($this->userModel->updateProfile(
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
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
            // Optionally, update session user data if needed
            $updatedUser = $this->userModel->getUser($user_id);
            if ($updatedUser) {
                $_SESSION['user'] = $updatedUser; // Assuming you store user data in $_SESSION['user']
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile!']);
        }
        exit();
    }

}