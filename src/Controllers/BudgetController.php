<?php

namespace App\Controllers;

use Core\Database; // Import the Database class
use PDOException;
use PDO;

class BudgetController
{
    private $db;

    public function __construct()
    {
        // Initialize the database connection using the Singleton pattern
        $this->db = Database::getInstance()->getConnection();  // Using the getConnection method to get the PDO instance
    }

    public function showBudgetView()
    {
        session_start();  // Start the session to access session data
    
        // Check if the user is logged in by verifying if userId exists in the session
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to view your budget.";
            header("Location: /");  // Redirect to login page or homepage
            exit();
        }
    
        $userId = $_SESSION['user']['id'];  // Correct session key to fetch logged-in user's ID
    
        try {
            // Fetch all trips for the user
            $query = "SELECT id, name, budget FROM trips WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Prepare an array to hold the expenses data for each trip
            $tripExpensesData = [];
    
            foreach ($trips as $trip) {
                // Fetch Total Accommodation cost for each trip
                $accommodationQuery = "SELECT SUM(price) AS totalAccommodation FROM accommodations WHERE trip_id = :trip_id";
                $stmt = $this->db->prepare($accommodationQuery);
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->execute();
                $accommodationData = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalAccommodation = $accommodationData['totalAccommodation'] ?? 0.00;
    
                // Fetch Total Transportation cost for each trip
                $transportationQuery = "SELECT SUM(amount) AS totalTransportation FROM transportation WHERE trip_id = :trip_id";
                $stmt = $this->db->prepare($transportationQuery);
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->execute();
                $transportationData = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalTransportation = $transportationData['totalTransportation'] ?? 0.00;
    
                // Fetch Total Trip Expenses for each trip
                $tripExpensesQuery = "SELECT SUM(amount) AS totalExpenses FROM trip_expenses WHERE trip_id = :trip_id";
                $stmt = $this->db->prepare($tripExpensesQuery);
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->execute();
                $tripExpensesDataQuery = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalExpenses = $tripExpensesDataQuery['totalExpenses'] ?? 0.00;
    
                // Calculate the Total Overall for each trip
                $totalOverall = $totalAccommodation + $totalTransportation + $totalExpenses;
    
                // Store the trip expenses data in the array
                $tripExpensesData[] = [
                    'trip_name' => $trip['name'],  // Name of the trip
                    'budget' => $trip['budget'],  // Trip budget
                    'totalAccommodation' => $totalAccommodation,
                    'totalTransportation' => $totalTransportation,
                    'totalExpenses' => $totalExpenses,
                    'totalOverall' => $totalOverall,
                ];
            }
    
            // Pass the necessary data to the view
            $viewPath = __DIR__ . '/../../resources/views/user/budget_view.php';  // Adjust the path based on your file structure
    
            if (file_exists($viewPath)) {
                include($viewPath);
            } else {
                echo "View file not found: " . $viewPath;
            }
        } catch (PDOException $e) {
            // Handle any database errors
            echo "Database error: " . $e->getMessage();
        }
    }
    
    

    // Method to fetch trips for the user (you can adjust this logic as per your database schema)
    private function getUserTrips($userId)
    {
        $query = "SELECT * FROM trips WHERE user_id = :user_id";  // Sample query, adjust as needed
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();  // Returns the trips associated with the user
    }

    // Method to fetch overall expenses for the user (based on trip_expenses, accommodations, and transportation tables)

// Method to fetch expenses per trip for the user (based on trip_expenses, accommodations, and transportation tables)
    public function getUserExpensesPerTrip($userId)
    {
        // Initialize an array to hold expenses per trip
        $tripExpensesData = [];

        try {
            // Query for trips
            $tripQuery = "
                SELECT id, name  -- Assuming you have 'name' in the trips table to identify the trip
                FROM trips
                WHERE user_id = :user_id
            ";
            $stmt = $this->db->prepare($tripQuery);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Loop through trips to get expenses for each trip
            foreach ($trips as $trip) {
                // Query for accommodations per trip
                $accommodationQuery = "
                    SELECT SUM(price) AS totalAccommodation
                    FROM accommodations
                    WHERE trip_id = :trip_id AND user_id = :user_id
                ";
                $stmt = $this->db->prepare($accommodationQuery);
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $accommodationData = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalAccommodation = $accommodationData['totalAccommodation'] ?? 0.00;

                // Query for transportation per trip
                $transportationQuery = "
                    SELECT SUM(amount) AS totalTransportation
                    FROM transportation
                    WHERE trip_id = :trip_id AND user_id = :user_id
                ";
                $stmt = $this->db->prepare($transportationQuery);
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $transportationData = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalTransportation = $transportationData['totalTransportation'] ?? 0.00;

                // Query for trip expenses per trip
                $tripExpensesQuery = "
                    SELECT SUM(amount) AS totalExpenses
                    FROM trip_expenses
                    WHERE trip_id = :trip_id AND user_id = :user_id
                ";
                $stmt = $this->db->prepare($tripExpensesQuery);
                $stmt->bindParam(':trip_id', $trip['id'], PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $tripExpensesData = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalExpenses = $tripExpensesData['totalExpenses'] ?? 0.00;

                // Store the trip expenses data in the array
                $tripExpensesData[] = [
                    'trip_name' => $trip['name'],  // Assuming 'name' is the trip name
                    'totalAccommodation' => $totalAccommodation,
                    'totalTransportation' => $totalTransportation,
                    'totalExpenses' => $totalExpenses,
                    'totalOverall' => $totalAccommodation + $totalTransportation + $totalExpenses,
                ];
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        // Return the expenses data for each trip
        return $tripExpensesData;
    }





}
