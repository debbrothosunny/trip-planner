<?php

namespace App\Models;

use PDO;
use Exception;

class TripReview {
    private $conn; // This will hold the PDO connection

    // Constructor to initialize the connection
    public function __construct($db) {
        $this->conn = $db; // Assign the provided database connection to $this->conn
    }

    // Save a trip review
    public function saveReview($tripId, $userId, $rating, $reviewText) {
        $query = "INSERT INTO trip_reviews (trip_id, user_id, rating, review_text) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);  // Use $this->conn to refer to the PDO connection
        return $stmt->execute([$tripId, $userId, $rating, $reviewText]);
    }

    // Get a review for a trip by a specific user
    public function getReviewByUser($tripId, $userId) {
        $query = "SELECT * FROM trip_reviews WHERE trip_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);  // Use $this->conn
        $stmt->execute([$tripId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    // Get all reviews for a trip, including the reviewer's name
    public function getReviewsByTrip($tripId) {
        // Join the trip_reviews table with the users table to get the reviewer's name
        $query = "SELECT tr.*, u.name
                FROM trip_reviews tr
                JOIN users u ON tr.user_id = u.id
                WHERE tr.trip_id = ?";
        
        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tripId]);
        
        // Return all reviews along with the reviewer's name
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}


