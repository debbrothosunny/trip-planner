<?php
// Start the session to check for user login
session_start();

// Include necessary files for database connection and model
include 'Database.php'; // Your database connection class
include 'TripInvitation.php'; // The TripInvitation model

use Core\Database;

// Check if the required parameters are passed
$trip_id = $_GET['trip_id'] ?? null;
$invitee_email = $_GET['email'] ?? null;

if (!$trip_id || !$invitee_email) {
    echo "Invalid invitation link. Missing parameters.";
    exit;
}

// Check if the user is logged in (for example, check user_id in session)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Create a new TripInvitation instance with the PDO connection
    $pdo = Database::getPDO();
    $tripInvitation = new TripInvitation($pdo);

    // Find the invitation for the given trip and invitee email
    $invitations = $tripInvitation->getInvitationsByUserEmail($invitee_email);
    $invitation = null;

    foreach ($invitations as $inv) {
        if ($inv['trip_id'] == $trip_id && $inv['status'] == 'pending') {
            $invitation = $inv;
            break;
        }
    }

    if ($invitation) {
        // Accept the invitation
        if ($tripInvitation->acceptInvitation($invitation['id'])) {
            echo "Invitation accepted successfully! You are now a participant.";
            // Redirect to the participant dashboard or trip page
            header("Location: participant_dashboard.php"); // Change to your dashboard
            exit;
        } else {
            echo "Failed to accept invitation.";
        }
    } else {
        echo "No pending invitation found for this trip.";
    }
} else {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit;
}

