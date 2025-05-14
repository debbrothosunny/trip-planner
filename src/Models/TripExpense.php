<?php

namespace App\Models;

use Core\Database;
use mysqli;
use PDO;  
use PDOException;

class TripExpense {
    private $conn;
    private $db;
    private $table = "trip_expenses";
  
  public function __construct($db) {
        $this->db = $db;
    }

     // Add a new expense to the database
     public function createExpense($tripId, $category, $amount, $currency, $description, $expenseDate, $userId) {
        $query = "INSERT INTO trip_expenses (trip_id, category, amount, currency, description, expense_date, user_id) 
                  VALUES (:trip_id, :category, :amount, :currency, :description, :expense_date, :user_id)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':trip_id', $tripId);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':currency', $currency);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':expense_date', $expenseDate);
        $stmt->bindParam(':user_id', $userId);
    
        return $stmt->execute();  // Return true if insertion was successful
    }
    

    public function getExpensesByTrip($trip_id, $userId) {
        $sql = "SELECT * FROM {$this->table} WHERE trip_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$trip_id, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    
    public function getAllTrips($userId) {
        $sql = "SELECT * FROM trips WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all trips for the user
    }
    

    public function getExpenseById($id, $userId) {
        $sql = "SELECT trip_expenses.*, trips.name AS trip_name 
                FROM trip_expenses 
                JOIN trips ON trip_expenses.trip_id = trips.id 
                WHERE trip_expenses.id = :id AND trip_expenses.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    

    public function updateExpense($id, $tripId, $category, $amount, $currency, $description, $expenseDate, $userId) {
        // First, check if the expense belongs to the user
        $sql = "SELECT * FROM trip_expenses WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$expense) {
            // If expense not found or doesn't belong to the user, return false
            return false;
        }
    
        // Proceed to update the expense
        $updateQuery = "UPDATE trip_expenses SET 
                        trip_id = :trip_id, 
                        category = :category, 
                        amount = :amount, 
                        currency = :currency, 
                        description = :description, 
                        expense_date = :expense_date 
                        WHERE id = :id AND user_id = :user_id";
    
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->bindParam(':user_id', $userId);
        $updateStmt->bindParam(':trip_id', $tripId);
        $updateStmt->bindParam(':category', $category);
        $updateStmt->bindParam(':amount', $amount);
        $updateStmt->bindParam(':currency', $currency);
        $updateStmt->bindParam(':description', $description);
        $updateStmt->bindParam(':expense_date', $expenseDate);
    
        return $updateStmt->execute();  // Return true if update was successful
    }
    

    


    public function deleteExpense($expense_id, $userId) {
        // SQL to delete the expense record if the user owns it
        $sql = "DELETE FROM trip_expenses WHERE id = :expense_id AND user_id = :user_id";
        
        // Prepare the SQL statement
        $stmt = $this->db->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':expense_id', $expense_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // Ensure the user is deleting their own expense
        
        // Execute the query
        return $stmt->execute(); // Return true if delete was successful
    }
    
    
}