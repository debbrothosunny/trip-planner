<?php

namespace App\Models;
use PDO; // Use PDO for database operations
use Core\Database;

class TripInvitation {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Send an invitation
    public function sendInvitation($trip_id, $inviter_id, $invitee_email) {
        $stmt = $this->pdo->prepare("INSERT INTO trip_invitations (trip_id, inviter_id, invitee_email, status) 
                                     VALUES (:trip_id, :inviter_id, :invitee_email, 'pending')");
        return $stmt->execute([
            'trip_id' => $trip_id,
            'inviter_id' => $inviter_id,
            'invitee_email' => $invitee_email
        ]);  
    }

    

    // Get invitations for a specific user (invitee)
    public function getInvitationsByUserEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM trip_invitations WHERE invitee_email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Accept invitation
    public function acceptInvitation($invitation_id) {
        $stmt = $this->pdo->prepare("UPDATE trip_invitations SET status = 'accepted' WHERE id = :id");
        return $stmt->execute(['id' => $invitation_id]);
    }

    // Reject invitation
    public function rejectInvitation($invitation_id) {
        $stmt = $this->pdo->prepare("UPDATE trip_invitations SET status = 'rejected' WHERE id = :id");
        return $stmt->execute(['id' => $invitation_id]);
    }
}
