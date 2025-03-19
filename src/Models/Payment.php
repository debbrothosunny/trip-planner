<?php

namespace App\Models;

use PDO;
use PDOException;
use Core\Database; // Import the Database class

class Payment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); // Singleton DB connection
    }

    /**
     * Store payment details when a participant makes a payment
     */public function addPayment($userId, $tripId, $amount, $paymentMethod, $transactionId, $paymentStatus) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO payments (user_id, trip_id, amount, payment_method, transaction_id, payment_status, created_at)
                VALUES (:user_id, :trip_id, :amount, :payment_method, :transaction_id, :payment_status, NOW())
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':payment_method', $paymentMethod, PDO::PARAM_STR);
            $stmt->bindParam(':transaction_id', $transactionId, PDO::PARAM_STR);
            $stmt->bindParam(':payment_status', $paymentStatus, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * Get all payments made by a participant for a trip
     */
    public function getPaymentsByUser($userId, $tripId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payments WHERE user_id = :user_id AND trip_id = :trip_id ORDER BY created_at DESC
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Check if the participant has already paid for the trip
     */
    public function hasPaid($userId, $tripId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) AS total FROM payments WHERE user_id = :user_id AND trip_id = :trip_id AND payment_status = 'completed'
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getPaymentStatus($userId, $tripId) {
        try {
            // Prepare the SQL query to fetch the latest payment status for the user and trip
            $stmt = $this->db->prepare("
                SELECT payment_status 
                FROM payments 
                WHERE user_id = :user_id AND trip_id = :trip_id 
                ORDER BY created_at DESC LIMIT 1
            ");
            
            // Bind parameters to prevent SQL injection
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
            $stmt->execute();
        
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            // If no result found, return 'unpaid'
            if ($result === false) {
                return 'unpaid'; // Default status when no payment record is found
            }
        
            return $result['payment_status']; // Return the payment status
        } catch (PDOException $e) {
            // Log the error for debugging purposes
            error_log("Error in getPaymentStatus: " . $e->getMessage());
            return 'unpaid'; // Return 'unpaid' in case of an exception
        }
    }
    
    
}
