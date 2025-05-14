<?php

namespace App\Models;

use PDO;
use Exception;

class TripReview {

    private $db; 

    // Constructor to initialize the connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Save a trip review
    public function saveReview($tripId, $userId, $rating, $reviewText) {
        $query = "INSERT INTO trip_reviews (trip_id, user_id, rating, review_text) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);  // Use $this->conn to refer to the PDO connection
        return $stmt->execute([$tripId, $userId, $rating, $reviewText]);
    }

    // Get a review for a trip by a specific user
    public function getReviewByUser($tripId, $userId) {
        $query = "SELECT * FROM trip_reviews WHERE trip_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($query);  // Use $this->conn
        $stmt->execute([$tripId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    // Get all reviews for a trip, including the reviewer's name
    public function getReviewsByTrip($tripId, $excludeUserId = null) {
        $sql = "SELECT r.*, u.name FROM trip_reviews r INNER JOIN users u ON r.user_id = u.id WHERE r.trip_id = :trip_id";
        $params = [':trip_id' => $tripId];
    
        if ($excludeUserId !== null) {
            $sql .= " AND r.user_id != :user_id";
            $params[':user_id'] = $excludeUserId;
        }
    
        $sql .= " ORDER BY r.created_at DESC";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}


