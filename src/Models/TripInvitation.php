<?php

namespace App\Models;
use PDO; // Use PDO for database operations
use Core\Database;
use PDOException;
class TripInvitation {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO invitations (trip_id, inviter_user_id, invitation_code, created_at, expires_at, status, invited_user_id)
                VALUES (:trip_id, :inviter_user_id, :invitation_code, :created_at, :expires_at, :status, :invited_user_id)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':trip_id', $data['trip_id'], PDO::PARAM_INT);
            $stmt->bindParam(':inviter_user_id', $data['inviter_user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':invitation_code', $data['invitation_code'], PDO::PARAM_STR);
            $stmt->bindParam(':created_at', $data['created_at'], PDO::PARAM_STR);
            $stmt->bindParam(':expires_at', $data['expires_at'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':invited_user_id', $data['invited_user_id'], PDO::PARAM_INT);
            $stmt->execute();

            return $this->db->lastInsertId(); // Return the ID of the newly inserted row
        } catch (PDOException $e) {
            // Log the error for debugging purposes
            error_log("Error creating invitation: " . $e->getMessage());
            return false;
        }
    }

    public function findByCode(string $code)
    {
        $sql = "SELECT * FROM invitations WHERE invitation_code = :code";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding invitation by code: " . $e->getMessage());
            return false;
        }
    }


}
