<?php

namespace App\Controllers;

use PDO;
use PDOException;
use App\Models\User;          // Ensure you have a User model
use Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../../helpers/csrf_helper.php'; // Make sure this path is correct

class AuthController
{
    private $userModel;
    protected $db;

    private $otp_lifetime = 600;       // OTP expiry time in seconds (10 minutes)
    private $resend_cooldown = 60;     // Cooldown time before resending OTP in seconds
    private $max_resend_attempts = 3;

    // Constructor to initialize the database connection
    public function __construct()
    {
        // Get the database connection (assuming Database is a singleton class for PDO)
        $database = Database::getInstance();  // Get the instance of Database (singleton pattern)
        $this->db = $database->getConnection();  // Assign the PDO connection to $this->db

        // Initialize the User model (User already gets its own DB connection)
        $this->userModel = new User($this->db);
    }



    // Show login form

    public function showLoginForm() {
        // Load login view
        require_once __DIR__ . '/../../resources/views/login.php';
    }

    // Handle login request
    public function login() {
      

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

        if (!$user) {
            echo json_encode(["status" => "error", "message" => "check your email or password!"]);
            exit();
        }

        // Check if the user is active (status == 0)
        if ($user['status'] == 1) {
            echo json_encode(["status" => "error", "message" => "Your account is deactivated. Please contact the administrator."]);
            exit();
        }

        // Verify the password only if the user is active
        if (!password_verify($password, $user['password'])) {
            echo json_encode(["status" => "error", "message" => "check your email or password!"]);
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

    private function sendOtpEmail($email, $otp, $isForPasswordReset = false) {
        $expiryTime = time() + 300;   // OTP expires in 5 minutes (300 seconds)

        // Store OTP and expiry time in database (update user record)
        $stmt = $this->db->prepare("UPDATE users SET verification_token = ?, otp_expiry = ? WHERE email = ?");
        $stmt->execute([$otp, $expiryTime, $email]);

        // Send OTP to the user via email
        $mail = new PHPMailer(true);
        try {
            // Set up SMTP (adjust to your configuration)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'debnathsunny7852@gmail.com';
            $mail->Password = 'rwpmqwohjffydazg'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('debnathsunny7852@gmail.com', 'Trip Planner');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $subject = $isForPasswordReset ? 'Your OTP for Password Reset' : 'Your OTP for Email Verification';
            $body = 'Use the following OTP to ' . ($isForPasswordReset ? 'reset your password' : 'verify your email') . ': <strong>' . $otp . '</strong><br>This OTP is valid for 5 minutes.';
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true; // Email sent successfully
        } catch (Exception $e) {
            error_log("OTP Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false; // Email sending failed
        }
    }
    
    



    public function register()
    {
        
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            error_log("Phone value received BEFORE trim (PHP): '" . $_POST['phone'] . "'");
            $phone = trim($_POST['phone']);
            error_log("Phone value received AFTER trim (PHP): '" . $phone . "'");

            $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $country = $_POST['country'] ?? null; // Capture country
            $city = $_POST['city'] ?? null;       // Capture city
            $language = $_POST['language'] ?? null; // Capture language
            $currency = $_POST['currency'] ?? null; // Capture currency
            $gender = $_POST['gender'] ?? null;     // Capture gender

            // Handle profile photo upload
            $profilePhotoPath = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['profile_photo']['type'], $allowedMimeTypes)) {

                    // This points to trip_planner/public/image/profile_photos/
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/image/profile_photos/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $uniqueFilename = uniqid() . '_' . basename($_FILES['profile_photo']['name']);

                    // Path for storing in DB or session (relative to public)
                    $profilePhotoPath = 'image/profile_photos/' . $uniqueFilename;

                    if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $uniqueFilename)) {
                        echo "Error uploading profile photo.";
                        return;
                    }

                    if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) {
                        echo "Profile photo size exceeds the limit (2MB).";
                        return;
                    }

                } else {
                    echo "Invalid profile photo format. Only JPEG, PNG, and GIF are allowed.";
                    return;
                }


                if ($_FILES['profile_photo']['size'] > 2 * 1024 * 1024) { // Example: 2MB limit
                    echo "Profile photo size exceeds the limit (2MB).";
                    return;
                }
            }

            $_SESSION['temp_user_data'] = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT), // Hash password here
                'role' => $role,
                'phone' => $phone,
                'otp' => rand(100000, 999999),
                'otp_expiry' => time() + (10 * 60), // 10 minutes
                'profile_photo_path' => $profilePhotoPath, // Store the file path (can be null)
                'country' => $country, // Store country in session
                'city' => $city,       // Store city in session
                'language' => $language, // Store language in session
                'currency' => $currency, // Store currency in session
                'gender' => $gender      // Store gender in session
            ];

            $_SESSION['otp_email'] = $email; // Store email for verification page
            $_SESSION['otp_lifetime'] = 600; // Store lifetime for resend calculation

            $this->sendOtpEmail($email, $_SESSION['temp_user_data']['otp']);

            echo "/verify-otp";
            return;

        } 
    }
    



    public function checkEmail() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
                $stmt->bindParam(":email", $email);
                $stmt->execute();
                $emailExists = $stmt->fetchColumn();
    
                echo json_encode(['exists' => (bool)$emailExists]);
                return;
            }
        }
        echo json_encode(['exists' => false]); // Default if invalid or not found
    }
    
    public function checkPhone() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['phone'])) {
            $phone = trim($_POST['phone']);
            if (preg_match("/^[0-9]{5,15}$/", $phone)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE phone = :phone");
                $stmt->bindParam(":phone", $phone);
                $stmt->execute();
                $phoneExists = $stmt->fetchColumn();
    
                echo json_encode(['exists' => (bool)$phoneExists]);
                return;
            }
        }
        echo json_encode(['exists' => false]); // Default if invalid or not found
    }


    public function verifyOtp()
    {
       

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $enteredOtp = trim($_POST['otp']);

            try {
                // Check if user already exists
                $stmt = $this->db->prepare("SELECT verification_token, otp_expiry, id, is_verified, profile_photo FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    $storedOtp = trim($user['verification_token']);
                    $expiryTime = (int)$user['otp_expiry'];

                    if ($enteredOtp === $storedOtp && time() <= $expiryTime) {
                        // OTP is valid for existing user (you might have a different logic here)
                        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, otp_expiry = NULL WHERE email = ?");
                        $stmt->execute([$email]);

                        $_SESSION['user_email'] = $email;
                        header('Location: /');
                        exit();
                    } else {
                        echo "Invalid or expired OTP!";
                        exit();
                    }
                } else {
                    // If user doesn't exist yet (new registration flow)
                    if (isset($_SESSION['temp_user_data'])) {
                        $name = $_SESSION['temp_user_data']['name'];
                        $password = $_SESSION['temp_user_data']['password'];
                        $role = $_SESSION['temp_user_data']['role'];
                        $phone = $_SESSION['temp_user_data']['phone'];
                        $storedOtp = trim($_SESSION['temp_user_data']['otp'] ?? '');
                        $otpExpiry = (int)($_SESSION['temp_user_data']['otp_expiry'] ?? time());
                        $profilePhotoPath = $_SESSION['temp_user_data']['profile_photo_path'] ?? null;
                        // Retrieve from session!
                        $country = $_SESSION['temp_user_data']['country'] ?? null;
                        $city = $_SESSION['temp_user_data']['city'] ?? null;
                        $language = $_SESSION['temp_user_data']['language'] ?? null;
                        $currency = $_SESSION['temp_user_data']['currency'] ?? null;
                        $gender = $_SESSION['temp_user_data']['gender'] ?? null;

                        if ($enteredOtp === $storedOtp && time() <= $otpExpiry) {
                            // Create and verify user
                            $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, phone, is_verified, verification_token, otp_expiry, profile_photo, country, city, language, currency, gender)
                                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$name, $email, $password, $role, $phone, 1, $storedOtp, $otpExpiry, $profilePhotoPath, $country, $city, $language, $currency, $gender]);

                            $_SESSION['user_email'] = $email;
                            unset($_SESSION['temp_user_data']); // Clean up session
                            header('Location: /');
                            exit();
                        } else {
                            echo "Invalid or expired OTP!";
                            exit();
                        }
                    } else {
                        echo "Session expired or data missing. Please try again.";
                        exit();
                    }
                }
            } catch (PDOException $e) {
                echo "Database Error: " . $e->getMessage();
            }
        }
    }
    
    // Method to show OTP verification form // Method to show OTP verification form
    public function showOtpForm()
    {
        // Check if the user is already logged in (optional)
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
    
        // Pass the email to the view if it's available in the session
        $email = $_SESSION['otp_email'] ?? '';
    
        // Define the view path for OTP verification form
        $viewPath = __DIR__ . '/../../resources/views/otp-verify.php';
    
        // Check if the view exists
        if (file_exists($viewPath)) {
            include($viewPath);
        } else {
            echo "Error: OTP verification view not found.";
        }
    }
        

    public function resendOtp()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST['email']);
    
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
                return;
            }
    
            try {
                $stmt = $this->db->prepare("SELECT id, otp_expiry, resend_attempt FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
    
                if ($user) {
                    $currentTime = time();
                    $otpLifetime = $_SESSION['otp_lifetime'] ?? 600; // 10 min
                    $resendCooldown = $this->resend_cooldown ?? 60; // 1 min
    
                    // Convert otp_expiry (Y-m-d H:i:s) to timestamp
                    $otpExpiryTimestamp = strtotime($user['otp_expiry']);
                    
                    // Calculate last OTP generated time
                    $lastOtpGeneratedAt = $otpExpiryTimestamp - $otpLifetime;
    
                    if ($currentTime - $lastOtpGeneratedAt < $resendCooldown) {
                        $waitSeconds = $resendCooldown - ($currentTime - $lastOtpGeneratedAt);
                        echo json_encode(['success' => false, 'message' => "Please wait {$waitSeconds} seconds before resending OTP."]);
                        return;
                    }
    
                    if ($user['resend_attempt'] >= $this->max_resend_attempts) {
                        echo json_encode(['success' => false, 'message' => 'Max resend attempts reached.']);
                        return;
                    }
    
                    $newOtp = sprintf("%06d", rand(100000, 999999));
                    $newExpiryDatetime = date('Y-m-d H:i:s', $currentTime + $otpLifetime);
    
                    $stmt = $this->db->prepare("UPDATE users SET verification_token = ?, otp_expiry = ?, resend_attempt = resend_attempt + 1 WHERE email = ?");
                    $stmt->execute([$newOtp, $newExpiryDatetime, $email]);
    
                    if ($this->sendOtpEmail($email, $newOtp)) {
                        echo json_encode(['success' => true, 'message' => 'New OTP sent successfully.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to send OTP email.']);
                    }
    
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email not found.']);
                }
    
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
            }
    
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
    
    


    

    




    

    


    public function showRegistrationForm()
    {
        // Make sure the path to the view is correct
        
        require __DIR__ . '/../../resources/views/register.php';
    }
    




    public function logout() {
        // Destroy session or authentication token
       
        session_destroy();

        // Redirect to login page or home page after logout
        header('Location: /');
        exit();
    }



    // ✅ 1. Forgot Password Request
    public function forgotPassword() {
        // Define the correct view path
        $viewPath = __DIR__ . '/../../resources/views/forgot-password.php';
    
        // Check if the file exists before including it
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }


    private function generateOtp($length = 6) {
        return rand(pow(10, $length - 1), pow(10, $length) - 1);
    }

    public function handleForgotPassword() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
    
            // 1. Check if the email exists in the database
            $stmt = $this->db->prepare("SELECT id, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            $stmt->closeCursor();
    
            if ($user) {
                // 2. Generate a new OTP
                $otp = $this->generateOtp();
    
                // 3. Store the OTP and expiry in the database and send email
                if ($this->sendOtpEmail($email, $otp)) {
                    // 4. ✅ Redirect the user to the OTP verification page (CORRECT ROUTE)
                    header("Location: /verify-otp-reset");  // <-- Fixed here
                    exit();
                } else {
                    // Failed to send OTP email
                    header("Location: /forgot-password?error=Failed to send OTP to your email. Please try again.");
                    exit();
                }
            } else {
                // Email not found
                header("Location: /forgot-password?error=The provided email address was not found.");
                exit();
            }
        } else {
            // If the request method is not POST, redirect to the forgot password form
            header("Location: /forgot-password");
            exit();
        }
    }
    




    public function showVerifyOtpForm() {
        // Define the correct view path for the verify OTP form
        $viewPath = __DIR__ . '/../../resources/views/verify-otp-reset.php';

        // Check if the file exists before including it
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "View file not found: " . $viewPath;
        }
    }
    
    public function verifyOtpReset() {
        $email = trim($_POST['email']);
        $otp = trim($_POST['otp']);
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            return;
        }
    
        try {
            $stmt = $this->db->prepare("SELECT id, verification_token, otp_expiry FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
    
            if ($user) {
                echo "Submitted OTP: " . $otp . "<br>";
                echo "Stored OTP: " . $user['verification_token'] . "<br>";
                echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
                echo "OTP Expiry: " . $user['otp_expiry'] . "<br>";
    
                if ($otp === $user['verification_token']) {
                    if (strtotime($user['otp_expiry']) > time()) {
                        // OTP valid
                        echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'OTP expired.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Email not found.']);
            }
    
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
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