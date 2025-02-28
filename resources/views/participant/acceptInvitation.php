// acceptInvitation.php
if (isset($_GET['trip_id']) && isset($_GET['token'])) {
    $trip_id = $_GET['trip_id'];
    $token = $_GET['token'];

    // Validate the token by checking it in the database
    $invitation = $this->getInvitationByToken($trip_id, $token);

    if ($invitation) {
        // Token is valid, now add the user to the trip
        if ($this->acceptInvitation($invitation['id'])) {
            echo "Invitation accepted successfully! You are now a participant.";
            header("Location: participant_dashboard.php"); // Redirect to dashboard
            exit;
        } else {
            echo "Failed to accept invitation.";
        }
    } else {
        echo "Invalid invitation or token.";
    }
} else {
    echo "Invalid request.";
}

// Function to fetch the invitation based on token and trip_id
private function getInvitationByToken($trip_id, $token)
{
    $query = "SELECT * FROM trip_invitations WHERE trip_id = :trip_id AND token = :token AND status = 'pending'";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':trip_id', $trip_id);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
