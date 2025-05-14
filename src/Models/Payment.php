<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Payment
{
    private $db;
    private $table = 'payments';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createPayment(array $data): int
    {
        $sql = "INSERT INTO payments (user_id, trip_id, payment_gateway, transaction_id, amount, currency, payment_status, payment_date, payer_id, payment_method)
                VALUES (:user_id, :trip_id, :payment_gateway, :transaction_id, :amount, :currency, :payment_status, NOW(), :payer_id, :payment_method)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function updatePaymentDetails(int $localPaymentId, array $details): bool
    {
        $sql = "UPDATE payments SET
                        payment_status = :payment_status,
                        payer_id = :payer_id,
                        transaction_id = :transaction_id,
                        amount = :amount,
                        currency = :currency,
                        payment_method = :payment_method,
                        updated_at = NOW()
                    WHERE id = :local_payment_id";
        $stmt = $this->db->prepare($sql);
        $executed = $stmt->execute([
            ':payment_status' => $details['payment_status'],
            ':payer_id' => $details['payer_id'],
            ':transaction_id' => $details['transaction_id'],
            ':amount' => $details['amount'],
            ':currency' => $details['currency'],
            ':payment_method' => $details['payment_method'],
            ':local_payment_id' => $localPaymentId,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function updatePaymentId(int $localPaymentId, string $paypalPaymentId): bool
    {
        $sql = "UPDATE payments SET transaction_id = :paypal_paymentId WHERE id = :localPaymentId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':paypalPaymentId' => $paypalPaymentId, ':localPaymentId' => $localPaymentId]);
    }

    public function getPaymentByPaymentId(string $transactionId): ?array
    {
        $sql = "SELECT * FROM payments WHERE transaction_id = :transaction_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':transaction_id', $transactionId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("getPaymentByPaymentId result: " . print_r($result, true)); // Add this
        return $result ?: null;
    }

    public function getPaymentsByTripId(int $tripId): array
    {
        $sql = "SELECT * FROM payments WHERE trip_id = :trip_id ORDER BY payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the payment status for a specific user and trip.
     *
     * @param int $userId
     * @param int $tripId
     * @return string|null Returns the payment status (e.g., 'pending', 'completed', 'failed') or null if no payment found.
     */
    
     public function getPaymentStatus(int $userId, int $tripId): ?string
     {
         $sql = "SELECT payment_status FROM payments WHERE user_id = :user_id AND trip_id = :trip_id ORDER BY payment_date DESC LIMIT 1";
         $stmt = $this->db->prepare($sql);
         $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
         $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
         $stmt->execute();
         $result = $stmt->fetch(PDO::FETCH_ASSOC);
         return $result ? $result['payment_status'] : null;
     }

    /**
     * Gets a specific payment record for a given user and trip.
     *
     * @param int $userId
     * @param int $tripId
     * @return array|null
     */
    public function getPaymentByUserAndTrip(int $userId, int $tripId): ?array
    {
        $sql = "SELECT 
                    id,
                    user_id,
                    trip_id,
                    payment_gateway,
                    transaction_id,
                    amount,
                    currency,
                    payment_status,
                    payment_date,
                    payer_id,
                    payment_method
                FROM payments 
                WHERE user_id = :user_id 
                  AND trip_id = :trip_id 
                ORDER BY payment_date DESC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null; // Explicitly return null if no result
    }


     // And a function in your Payment model to update the status by trip_id
     public function markAsPaidByTripId(int $tripId): bool
     {
         $sql = "UPDATE payments SET payment_status = 'paid' WHERE trip_id = :trip_id";
         $stmt = $this->db->prepare($sql);
         $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
         return $stmt->execute();
     }


     public function updatePayerId(string $paymentId, string $payerId): int
    {
        $sql = "UPDATE payments SET payer_id = :payer_id WHERE payment_id = :payment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':payer_id', $payerId);
        $stmt->bindParam(':payment_id', $paymentId);
        $stmt->execute();
        return $stmt->rowCount();
    }


    public function getPaymentById(int $id): ?array
    {
        $sql = "SELECT * FROM payments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    public function updatePaymentStatus(int $localPaymentId, string $status): bool
    {
        $sql = "UPDATE payments SET payment_status = :status WHERE id = :local_payment_id";
        $stmt = $this->db->prepare($sql);
        $executed = $stmt->execute([
            ':status' => $status,
            ':local_payment_id' => $localPaymentId,
        ]);
        return $stmt->rowCount() > 0;
    }






    //  payment store database

    public function insertPayment($data)
    {
        // Assuming you have a PDO connection called $this->db
        $sql = "INSERT INTO payments 
            (user_id, trip_id, payment_gateway, transaction_id, amount, currency, payment_status, payment_date, payer_id, payment_method) 
            VALUES 
            (:user_id, :trip_id, :payment_gateway, :transaction_id, :amount, :currency, :payment_status, :payment_date, :payer_id, :payment_method)";

        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':trip_id', $data['trip_id']);
        $stmt->bindParam(':payment_gateway', $data['payment_gateway']);
        $stmt->bindParam(':transaction_id', $data['transaction_id']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':currency', $data['currency']);
        $stmt->bindParam(':payment_status', $data['payment_status']);
        $stmt->bindParam(':payment_date', $data['payment_date']);
        $stmt->bindParam(':payer_id', $data['payer_id']);
        $stmt->bindParam(':payment_method', $data['payment_method']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId(); // Return inserted ID
        } else {
            return false; // Insert failed
        }
    }



    // In Payment model (or Trip model)
    public function getTripNameById(int $tripId): ?string
    {
        $stmt = $this->db->prepare("SELECT name FROM trips WHERE id = :id");
        $stmt->bindParam(':id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : null;
    }

    // In Payment model (or User model)
    public function getUserNameById(int $userId): ?string
    {
        $stmt = $this->db->prepare("SELECT name FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : null;
    }


    

     
}