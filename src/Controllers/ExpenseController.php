<?php
namespace App\Controllers;

use App\Models\TripExpense;
use App\Models\Trip;
use Core\Database;
use PDO;
use PDOException;
use Exception;
class ExpenseController {
    protected $expenseModel;
    private $tripModel;
    private $db;
    protected $userId;

    // Constructor adjusted to use the singleton database instance
  // Constructor adjusted to use the singleton database instance
    public function __construct()
    {
        // Use the Singleton to get the connection
        $database = Database::getInstance(); // Get the database connection instance
        $this->db = $database->getConnection(); // Retrieve the connection from Database class

        if (!$this->db) {
            throw new Exception("Database connection failed");
        }

        // Instantiate the models with the database connection
        $this->expenseModel = new TripExpense($this->db);
        $this->tripModel = new Trip($this->db);  // Pass the db connection to the Trip model
    }
    




    
    public function showExpenses() {
        session_start();  // Make sure the session is started
    
        // Ensure the user is logged in by checking if the session has the user information
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to view your expenses.";  // Set error message in session
            header("Location: /");  // Redirect to login page
            exit();  // Stop further execution
        }
    
        $user_id = $_SESSION['user']['id'];  // Get the logged-in user's ID from the session
    
        try {
            // Query to fetch expenses along with the trip name for the logged-in user
            $query = "
                SELECT trip_expenses.*, trips.name AS trip_name
                FROM trip_expenses
                JOIN trips ON trip_expenses.trip_id = trips.id
                WHERE trip_expenses.user_id = :user_id
            ";
    
            // Prepare and execute the query
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);  // Bind the user ID to the query
            $stmt->execute();
    
            // Fetch all expenses with the associated trip name
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Check if any expenses are found
            if (empty($expenses)) {
                $_SESSION['error'] = "No expenses found for your account.";  // Set error message if no expenses found
            }
    
            // Define the path to the view
            $viewPath = __DIR__ . '/../../resources/views/user/expense.php';
    
            // Check if the view file exists and include it
            if (file_exists($viewPath)) {
                include($viewPath);  // Include the view file and pass the expenses data
            } else {
                // Handle the case where the view file is missing
                echo "View file not found: " . $viewPath;
            }
    
        } catch (PDOException $e) {
            // Handle any database errors and display a user-friendly message
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: /error");  // Redirect to an error page
            exit();
        }
    }
    
    
    
      
    public function createExpenseForm()
    {
        // Start the session to access the logged-in user's data
        session_start();

        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to create an expense.";  // Set error message
            header("Location: /login");  // Redirect to the login page
            exit();  // Exit to stop further execution
        }

        // Get the user ID from the session
        $userId = $_SESSION['user']['id'];

        try {
            // Fetch trips for the logged-in user
            $trips = $this->tripModel->getTripsByUserId($userId);

            // Check if any trips were found
            if (empty($trips)) {
                $_SESSION['error'] = "No trips found for the user.";  // Set error message for no trips
                header("Location: /user/trips");  // Redirect to trips page or another appropriate page
                exit();
            }

            // Set the view path dynamically
            $viewPath = __DIR__ . '/../../resources/views/user/expense_create.php';  // Adjusted path to the view file

            // Check if the view file exists
            if (file_exists($viewPath)) {
                // Include the view file and pass the trips data
                include($viewPath);
            } else {
                // Handle the case where the view file does not exist
                echo "View file not found: " . $viewPath;
            }
        } catch (Exception $e) {
            // Handle any unexpected errors
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: /user/trips");  // Redirect to trips page in case of an error
            exit();
        }
    }

    


    public function storeExpense()
    {
        // Start the session to access the logged-in user's data
        session_start();
    
        // Ensure the user is logged in by checking session user data
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = "Please login to store expenses.";  // Store error message
            header("Location: /login");  // Redirect to login page
            exit();  // Exit to stop further execution
        }
    
        $user_id = $_SESSION['user']['id'];  // Get the logged-in user's ID from the session
    
        // Check if all required form data is present
        if (isset($_POST['trip_id'], $_POST['category'], $_POST['amount'], $_POST['currency'], $_POST['description'], $_POST['expense_date'])) {
            // Sanitize the inputs to prevent SQL injection
            $trip_id = filter_var($_POST['trip_id'], FILTER_SANITIZE_NUMBER_INT);
            $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
            $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $currency = filter_var($_POST['currency'], FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $expense_date = filter_var($_POST['expense_date'], FILTER_SANITIZE_STRING); // Assuming format YYYY-MM-DD
    
            // Validate inputs (optional but recommended for further security)
            if (empty($trip_id) || empty($category) || empty($amount) || empty($currency) || empty($description) || empty($expense_date)) {
                $_SESSION['error'] = "All fields are required.";  // Set error message for missing data
                header("Location: /user/add-expense");  // Redirect to form page to try again
                exit();
            }
    
            // Check if amount is a valid number (optional but recommended)
            if (!is_numeric($amount) || $amount <= 0) {
                $_SESSION['error'] = "Amount must be a positive number.";  // Set error for invalid amount
                header("Location: /user/add-expense");  // Redirect to form page
                exit();
            }
    
            // Prepare SQL query to insert the expense
            $query = "
                INSERT INTO trip_expenses (user_id, trip_id, category, amount, currency, description, expense_date)
                VALUES (:user_id, :trip_id, :category, :amount, :currency, :description, :expense_date)
            ";
    
            try {
                // Prepare the SQL statement
                $stmt = $this->db->prepare($query);
    
                // Bind the parameters
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
                $stmt->bindParam(':currency', $currency, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':expense_date', $expense_date, PDO::PARAM_STR);
    
                // Execute the query
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Expense stored successfully.";  // Set success message
                    header("Location: /user/expense");  // Redirect to expenses list page
                    exit();
                } else {
                    $_SESSION['error'] = "Error: Unable to store the expense.";  // Set error for failure to store
                    header("Location: /user/add-expense");  // Redirect back to the form
                    exit();
                }
            } catch (PDOException $e) {
                // Handle database connection errors
                $_SESSION['error'] = "Database error: " . $e->getMessage();  // Store DB error message
                header("Location: /user/add-expense");  // Redirect back to form for retry
                exit();
            }
        } else {
            // Handle the case where required form fields are missing
            $_SESSION['error'] = "Error: Please fill in all fields.";  // Set error for missing fields
            header("Location: /user/add-expense");  // Redirect back to form page
            exit();
        }
    }
    
    

    public function editExpenseForm($id) {
        // Start the session to access the logged-in user's data
        session_start();
        
        // Retrieve the user ID from the session
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header("Location: /");
            exit();
        }
        $userId = $_SESSION['user']['id'];
        
        // Fetch the expense based on the ID and user ID
        $expense = $this->expenseModel->getExpenseById($id, $userId); // Pass userId to the model method
        
        // If no expense is found, redirect to the expense list with an error
        if (!$expense) {
            header("Location: /user/expense?error=expense_not_found");
            exit();
        }
        
        // Fetch all trips for the user
        $trips = $this->expenseModel->getAllTrips($userId);
        
        // Define the path to the edit expense view
        $viewPath = __DIR__ . '/../../resources/views/user/expense_edit.php';
        
        // Check if the view file exists, and include it
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // If the view file is not found, show an error message
            echo "View file not found: " . $viewPath;
        }
    }
    
    
    
    
    
    
    
    public function updateExpense($id) {
        // Start the session to access the logged-in user's data
        session_start();
        
        // Retrieve the user ID from the session
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header("Location: /");
            exit();
        }
        $userId = $_SESSION['user']['id'];
        
        // Assuming you get the form data and validate it
        $category = $_POST['category'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $description = $_POST['description'];
        $expense_date = $_POST['expense_date'];
        $trip_id = $_POST['trip_id'];
        
        // Update the expense
        if ($this->expenseModel->updateExpense($id, $trip_id, $category, $amount, $currency, $description, $expense_date, $userId)) {
            // After successful update, redirect to /user/expense
            header("Location: /user/expense");
            exit();
        } else {
            // Handle failure (optional)
            echo "Error updating expense.";
        }
    }
    
    
    public function delete($id) {
        // Start the session to access the logged-in user's data
        session_start();
        
        // Retrieve the user ID from the session
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            header("Location: /");
            exit();
        }
        $userId = $_SESSION['user']['id'];
        
        // Call the model's delete method with expense_id and user_id
        if ($this->expenseModel->deleteExpense($id, $userId)) {
            // Redirect with success message
            header("Location: /user/expense");
            exit();
        } else {
            // Redirect with error message
            header("Location: /user/expense?error=expense_delete_failed");
            exit();
        }
    }
    

}
