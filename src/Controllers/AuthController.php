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
        session_start();
    
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "405 Method Not Allowed"]);
            exit();
        }
    
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
    
        if (!$email || !$password) {
            echo json_encode(["status" => "error", "message" => "Email and Password are required!"]);
            exit();
        }
    
        $user = $this->userModel->login($email);
    
        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode(["status" => "error", "message" => "Invalid credentials!"]);
            exit();
        }
    
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    
        // Return a JSON response instead of redirecting
        switch ($user['role']) {
            case 'admin':
                echo json_encode(["status" => "success", "redirect" => "/admin/dashboard"]);
                break;
            case 'user':
                echo json_encode(["status" => "success", "redirect" => "/user/dashboard"]);
                break;
            case 'participant':
                echo json_encode(["status" => "success", "redirect" => "/participant/dashboard"]);
                break;
            default:
                session_destroy();
                echo json_encode(["status" => "error", "message" => "Role not recognized!"]);
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



    // ✅ 1. Forgot Password Request
    public function forgotPassword() {
        // Define the correct view path
        $viewPath = __DIR__ . '/../../resources/views/forgot_password.php';
    
        // Check if the file exists before including it
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }
    



     // ✅ 2. Send Reset Link

     public function sendResetLink() {
        global $conn; // Use global DB connection
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST["email"]);
    
            // Check if email exists in the database
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
    
            if ($stmt->num_rows > 0) {
                // Generate a reset token and expiry time
                $token = bin2hex(random_bytes(50));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
    
                // Update user with reset token and expiry time
                $stmt = $conn->prepare("UPDATE users SET verification_token=?, otp_expiry=? WHERE email=?");
                $stmt->bind_param("sss", $token, $expiry, $email);
                $stmt->execute();
    
                // Prepare the reset link
                $reset_link = "http://localhost/reset_password.php?token=" . $token;
    
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);
                try {
                    // Set up SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'debnathsunny7852@gmail.com'; // Your SMTP email
                    $mail->Password = 'rwpmqwohjffydazg'; // Your SMTP App password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
    
                    // Set email headers
                    $mail->setFrom('debnathsunny7852@gmail.com', 'Trip Planner');
                    $mail->addAddress($email); // Recipient's email
    
                    // Set email content
                    $mail->isHTML(true);
                    $mail->Subject = "Password Reset Request";
                    $mail->Body    = "Click the link below to reset your password:<br><br><a href=\"$reset_link\">Reset Password</a><br><br>This link expires in 1 hour.";
    
                    // Send email
                    $mail->send();
    
                    // Set success message
                    $_SESSION['message'] = "A reset link has been sent to your email.";
                } catch (Exception $e) {
                    // Handle PHPMailer exception
                    $_SESSION['error'] = "Failed to send email. Try again. Error: " . $mail->ErrorInfo;
                }
            } else {
                $_SESSION['error'] = "Email not found.";
            }
    
            // Redirect back to the forgot password page
            header("Location: forgot_password.php");
            exit();
        }
    }
    // ✅ 3. Show Reset Password Form
    public function showResetForm() {
        global $conn;
        if (!isset($_GET["token"])) {
            die("Invalid request.");
        }

        $token = $_GET["token"];

        // Validate token
        $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND otp_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            die("Invalid or expired token.");
        }

        include "views/reset_password.php"; // Load the reset form view
    }


    // ✅ 4. Update Password
    public function updatePassword() {
        global $conn;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $token = $_POST["token"];
            $password = $_POST["password"];
            $confirm_password = $_POST["confirm_password"];

            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Passwords do not match.";
                header("Location: reset_password.php?token=$token");
                exit();
            }

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update password and clear token
            $stmt = $conn->prepare("UPDATE users SET password=?, verification_token=NULL, otp_expiry=NULL WHERE verification_token=?");
            $stmt->bind_param("ss", $hashed_password, $token);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Password updated. You can now log in.";
                header("Location: login.php");
            } else {
                $_SESSION['error'] = "Something went wrong. Try again.";
                header("Location: reset_password.php?token=$token");
            }
            exit();
        }
    }









    
}


