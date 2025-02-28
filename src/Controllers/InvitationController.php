<?php

namespace App\Controllers;

use App\Models\TripInvitation;
use App\Models\Trip;
use Core\Database;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class InvitationController {
    private $tripInvitation;
    private $tripParticipants;
    private $trip;

    public function __construct() {
        // Get database instance
        $database = Database::getInstance();

        // Initialize the TripInvitation model
        $this->tripInvitation = new TripInvitation($database->getConnection());

        // Initialize the Trip model
        $this->trip = new Trip($database->getConnection());  // Ensure you initialize the Trip model here
    }

    // Show send invitation form
    public function showSendInvitationForm($trip_id) {
        // Fetch trip details
        $trip = $this->trip->getTripById($trip_id);
        
        // Check if the trip exists
        if (!$trip) {
            echo "Trip not found.";
            return;
        }
    
        // Include the view to show the send invitation form
        include __DIR__ . '/../../resources/views/user/send_invitation.php';
    }
    




    // Process sending invitation// Process sending invitation
    public function sendInvitation($trip_id)
    {
        $mail = new PHPMailer(true);
        try {
            // Fetch trip details
            $trip = $this->trip->getTripById($trip_id);

            // Check if the trip exists
            if (!$trip) {
                echo "Trip not found.";
                return;
            }

            // Get invitee email from POST request
            $invitee_email = $_POST['invitee_email'] ?? null; // Ensure this is set

            if (!$invitee_email) {
                echo "Invitee email is required.";
                return;
            }

            // Generate the invitation link, adjusting to localhost:8000/login for login
            $invitation_link = "http://localhost:8000/login?trip_id={$trip_id}&email=" . urlencode($invitee_email);

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'debnathsunny7852@gmail.com';
            $mail->Password = 'rwpmqwohjffydazg'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('debnathsunny7852@gmail.com', 'Trip Planner');
            $mail->addAddress($invitee_email); // Set recipient email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Trip Invitation';
            $mail->Body = 'You are invited to join the trip: ' . htmlspecialchars($trip['name']) . "<br>
            To accept the invitation, please log in and confirm your invitation by clicking the link below: <br>
            <a href='{$invitation_link}'>Accept Invitation</a>";

            $mail->send();
            echo 'Invitation has been sent!';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


    public function showLoginForm() {
        // Get the trip_id and email from the query string
        $trip_id = isset($_GET['trip_id']) ? $_GET['trip_id'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
    
        // If both trip_id and email are provided, show invitation logic
        if ($trip_id && $email) {
            // Fetch the trip details from the database (simulated with an array here)
            // Ideally, you would query your database to get the trip details.
            $trip = getTripById($trip_id);
    
            if (!$trip) {
                echo "Trip not found.";
                return;
            }
    
            // You can now pass the trip details and email to your login view
            require_once __DIR__ . '/../../resources/views/login.php'; // Include login view
        } else {
            // Normal login form if no trip_id and email
            require_once __DIR__ . '/../../resources/views/login.php'; // Include normal login view
        }
    }
    

    
    


    

    // List all invitations for the logged-in user
    public function listInvitations() {
        session_start();
        $email = $_SESSION['user']['email'];

        $invitations = $this->tripInvitation->getInvitationsByUserEmail($email);
        require_once __DIR__ . '/../views/trip_invitation/my_invitations.php';
    }

    // Accept invitation
    public function acceptInvitation($trip_id)
    {
        session_start();
        $invitee_email = $_GET['email'] ?? null;
    
        // If the user is already logged in, add them as a participant
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
    
            // Check if user is already a participant
            $existing_participant = $this->tripParticipants->findByUserAndTrip($user_id, $trip_id);
            if (!$existing_participant) {
                $this->tripParticipants->addParticipant($user_id, $trip_id, 'accepted');
                header("Location: http://localhost:8000/participant/dashboard"); // Redirect to dashboard
                exit;
            } else {
                echo "You are already a participant in this trip.";
            }
        } else {
            // Store trip details in session and redirect to login
            $_SESSION['pending_trip_id'] = $trip_id;
            $_SESSION['pending_email'] = $invitee_email;
    
            echo "<script>
                if(confirm('You need to log in to accept the invitation. Do you want to login now?')) {
                    window.location.href = 'http://localhost:8000/login';
                } else {
                    window.location.href = 'http://localhost:8000/register';
                }
            </script>";
        }
    }
    
    
    

    

    // Reject invitation
    public function rejectInvitation($invitation_id) {
        if ($this->tripInvitation->rejectInvitation($invitation_id)) {
            echo "Invitation rejected!";
        } else {
            echo "Error rejecting invitation.";
        }
    }
}
