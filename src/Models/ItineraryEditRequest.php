<?php

namespace App\Models;

use PDO;


class ItineraryEditRequest {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Create a new edit request

    public function createRequest($tripId, $userId, $notes) {
        $stmt = $this->db->prepare("
        INSERT INTO itinerary_edit_requests (trip_id, itinerary_id, user_id, notes, status)
        VALUES (?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$tripId, $userId, $notes]);
    }
    

    // 📜 Get pending edit requests for a trip (only trip owner can view)
    public function getPendingRequests($tripId) {
        $stmt = $this->db->prepare("SELECT * FROM itinerary_edit_requests WHERE trip_id = ? AND status = 'pending'");
        $stmt->execute([$tripId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅❌ Approve or reject a request
    public function updateRequestStatus($requestId, $status) {
        $stmt = $this->db->prepare("UPDATE itinerary_edit_requests SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $requestId]);
    }

    // 🏆 Check if a participant's request was approved
    public function isEditApproved($tripId, $userId) {
        $stmt = $this->db->prepare("SELECT status FROM itinerary_edit_requests WHERE trip_id = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$tripId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['status'] === 'approved';
    }
}

