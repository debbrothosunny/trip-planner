<?php

namespace App\Controllers;
use PDO;
use PDOException;
use App\Models\User;    // Ensure you have a User model
use Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class AuthController {
    private $userModel;
    protected $db;  

    // Constructor to initialize the database connection
    public function __construct()
    {
        // Get the database connection (assuming Database is a singleton class for PDO)
        $database = Database::getInstance();  // Get the instance of Database (singleton pattern)
        $this->db = $database->getConnection();  // Assign the PDO connection to $this->db

        // Initialize the User model (User already gets its own DB connection)
        $this->userModel = new User(); 
    }


    // Show login form

    public function showLoginForm() {
        // Load login view
        require_once __DIR__ . '/../../resources/views/login.php';
    }

    // Handle login request
    public function login() {
        session_start();  // Ensure session is started before accessing session variables
    
        // Check if the request method is POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);  // Method Not Allowed
            die("405 Method Not Allowed");
        }
    
        // Retrieve email and password from POST data
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
    
        // Validate the input
        if (!$email || !$password) {
            die("Email and Password are required!");
        }
    
        // Call the model to get the user by email
        $user = $this->userModel->login($email);
    
        // Check if user exists and if the password is correct
        if (!$user || !password_verify($password, $user['password'])) {
            die("Invalid credentials!");
        }
    
        // Store user details and role in session
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];  // Store the user ID in the session
        $_SESSION['role'] = $user['role'];
    
        // Redirect based on the user's role
        switch ($user['role']) {
            case 'admin':
                header("Location: /admin/dashboard");
                break;
            case 'user':
                header("Location: /user/dashboard");
                break;
            case 'participant':
                header("Location: /participant/dashboard");
                break;
            default:
                // In case of an unknown role, destroy the session for security
                session_destroy();
                die("Role not recognized! Please contact support.");
        }
        exit();
    }
    
    
    

    
    // Register method

 


    private function sendOtpEmail($email)
    {
        $otp = rand(100000, 999999);  // Generate a 6-digit OTP
        $expiryTime = time() + 300;  // OTP expires in 5 minutes (300 seconds)
    
        // Store OTP and expiry time in database (update user record)
        $stmt = $this->db->prepare("UPDATE users SET verification_token = ?, otp_expiry = ? WHERE email = ?");
        $stmt->execute([$otp, $expiryTime, $email]);
    
        // Send OTP to the user via email
        $mail = new PHPMailer(true);
        try {
            // Set up SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'debnathsunny7852@gmail.com';
            $mail->Password = 'rwpmqwohjffydazg'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Recipients
            $mail->setFrom('debnathsunny7852@gmail.com', 'Trip Planner');
            $mail->addAddress($email);  // Recipient's email
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Email Verification';
            $mail->Body    = 'Use the following OTP to verify your email: <strong>' . $otp . '</strong><br>This OTP is valid for 5 minutes.';
    
            // Send the email
            $mail->send();
            echo 'Registration successful! Please check your email to verify your account.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    
    

    public function register()
    {   
        session_start();  // Ensure the session is started
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);
            $role = $_POST['role'];
    
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email format.";
                exit();
            }
    
            try {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
                // Save email temporarily in the session to track the user until OTP verification
                $_SESSION['temp_user_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'role' => $role
                ];
    
                // Generate OTP and set expiration time
                $otp = rand(100000, 999999);  // Generate OTP
                $expiryTime = time() + (10 * 60);  // OTP expires in 10 minutes
    
                // Store OTP and expiry time temporarily in the session (for verification later)
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_expiry'] = $expiryTime;
    
                // Debugging session data
                echo "OTP: " . $otp . "<br>";
                echo "Expiry Time: " . date("Y-m-d H:i:s", $expiryTime) . "<br>";
    
                // Send OTP to the user's email
                $this->sendOtpEmail($email, $otp);  // Implement this method to send OTP to user's email
    
                // Redirect to OTP verification page
                header('Location: /verify-otp');
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
    
    
    

    


    public function verifyOtp()
    {
        session_start();
    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];  // User's email address
            $enteredOtp = $_POST['otp'];  // OTP entered by the user
    
            // Fetch OTP and expiry from the database
            $stmt = $this->db->prepare("SELECT verification_token, otp_expiry, id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
    
            // Check if the user exists
            if ($user) {
                $storedOtp = $user['verification_token'];
                $expiryTime = $user['otp_expiry'];
    
                // Check if OTP is correct and hasn't expired
                if ($enteredOtp == $storedOtp && time() <= $expiryTime) {
                    // OTP is valid, update user verification status
                    $stmt = $this->db->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
                    $stmt->execute([$email]);
    
                    // Optionally, you could log the user in or redirect them after successful verification
                    $_SESSION['user_email'] = $email;  // Storing user email in session
                    header('Location: /');  // Redirect to a dashboard or success page
                    exit();
                } else {
                    echo "Invalid or expired OTP!";
                }
            } else {
                // User not found, so we should create a new user
                // Use session data to create a new user since we stored it in the session during registration
                if (isset($_SESSION['temp_user_data'])) {
                    $name = $_SESSION['temp_user_data']['name'];
                    $password = $_SESSION['temp_user_data']['password'];
                    $role = $_SESSION['temp_user_data']['role'];
    
                    // Insert the new user into the database
                    $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, is_verified, verification_token, otp_expiry) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $password, $role, 1, $enteredOtp, time() + 300]); // 1 for verified
    
                    // Set the user as logged in and redirect
                    $_SESSION['user_email'] = $email;
                    header('Location: /');  // Redirect to dashboard or success page
                    exit();
                } else {
                    echo "Session expired or data missing. Please try again.";
                }
            }
        }
    }
    
    
    
    
    
    
    


    
        // Method to show OTP verification form // Method to show OTP verification form
    public function showOtpForm()
    {
        // Check if the user is already logged in (optional)
        if (isset($_SESSION['user_id'])) {
            // If user is logged in, redirect to a dashboard or home page
            header('Location: /dashboard');
            exit;
        }

        // Define the view path for OTP verification form
        $viewPath = __DIR__ . '/../../resources/views/otp_verify.php';

        // Check if the view exists
        if (file_exists($viewPath)) {
            // Include the OTP verification view
            include($viewPath);
        } else {
            // Handle the error if the view is not found
            echo "Error: OTP verification view not found.";
        }
    }


    

    




    

    


    public function showRegistrationForm()
    {
        // Make sure the path to the view is correct
        
        require __DIR__ . '/../../resources/views/register.php';
    }
    


    // public function dashboard() {
    //     require_once __DIR__ . '/../../resources/views/dashboard.php';
    // }


    public function logout() {
        // Destroy session or authentication token
        session_start();
        session_destroy();

        // Redirect to login page or home page after logout
        header('Location: /');
        exit();
    }
}


