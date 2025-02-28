<?php

namespace App\Models;

use PDO;
use Core\Database;

class Budget
{
    private $db;

    private $conn;

    public function __construct() {
        // Get PDO connection using Singleton pattern
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTripBudget(int $userId, int $tripId): array
    {
        // Get the trip budget from the trips table
        $query = "SELECT * FROM trips WHERE user_id = :user_id AND id = :trip_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTripExpenses(int $tripId): array
    {
        // Get all expenses for the trip from the trip_expenses table
        $query = "SELECT * FROM trip_expenses WHERE trip_id = :trip_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalExpenses(int $tripId): float
    {
        // Calculate total expenses for a specific trip
        $query = "SELECT SUM(amount) as total_expenses FROM trip_expenses WHERE trip_id = :trip_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$result['total_expenses'];
    }
    
    public function getRemainingBudget(int $tripId, float $tripBudget): float
    {
        // Calculate the remaining budget by subtracting total expenses from the trip's budget
        $totalExpenses = $this->getTotalExpenses($tripId);
        return $tripBudget - $totalExpenses;
    }

} 