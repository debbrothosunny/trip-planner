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

    public function getExpensesData()
    {
        session_start();

        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $user_id = $_SESSION['user']['id'];

        try {
            $query = "
                SELECT trip_expenses.*, trips.name AS trip_name
                FROM trip_expenses
                JOIN trips ON trip_expenses.trip_id = trips.id
                WHERE trip_expenses.user_id = :user_id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'expenses' => $expenses]);
            return;

        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            return;
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
        // Start the session (if not already started)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure the user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            $response = ['success' => false, 'message' => "Please login to store expenses."];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }

        $user_id = $_SESSION['user']['id'];

        // Check if all required data is present (assuming JSON body)
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        if (isset($data['trip_id'], $data['category'], $data['amount'], $data['currency'], $data['description'], $data['expense_date'])) {
            // Sanitize inputs
            $trip_id = filter_var($data['trip_id'], FILTER_SANITIZE_NUMBER_INT);
            $category = filter_var($data['category'], FILTER_SANITIZE_STRING);
            $amount = filter_var($data['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $currency = filter_var($data['currency'], FILTER_SANITIZE_STRING);
            $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
            $expense_date = filter_var($data['expense_date'], FILTER_SANITIZE_STRING);

            // Validate inputs
            $errors = [];
            if (empty($trip_id)) $errors[] = "Trip ID is required.";
            if (empty($category)) $errors[] = "Category is required.";
            if (empty($amount) || !is_numeric($amount) || $amount <= 0) $errors[] = "Amount must be a positive number.";
            if (empty($currency)) $errors[] = "Currency is required.";
            if (empty($expense_date)) $errors[] = "Expense Date is required.";

            if (!empty($errors)) {
                $response = ['success' => false, 'errors' => $errors];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }

            $query = "
                INSERT INTO trip_expenses (user_id, trip_id, category, amount, currency, description, expense_date)
                VALUES (:user_id, :trip_id, :category, :amount, :currency, :description, :expense_date)
            ";

            try {
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':trip_id', $trip_id, PDO::PARAM_INT);
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
                $stmt->bindParam(':currency', $currency, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':expense_date', $expense_date, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => "Expense stored successfully."];
                } else {
                    $response = ['success' => false, 'message' => "Error: Unable to store the expense."];
                }
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => "Database error: " . $e->getMessage()];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit();

        } else {
            $response = ['success' => false, 'message' => "Error: Please provide all required data in JSON format."];
            header('Content-Type: application/json');
            echo json_encode($response);
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
    

    public function updateExpense($id)
    {
        session_start();

        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            // Return JSON for unauthorized access
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];

        // Read the raw JSON data from the request body
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // Invalid JSON data
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
            exit();
        }

        // Assuming you get and validate the form data from the JSON payload
        $category = $data['category'] ?? '';
        $amount = $data['amount'] ?? null;
        $currency = $data['currency'] ?? '';
        $description = $data['description'] ?? '';
        $expense_date = $data['expense_date'] ?? '';
        $trip_id = $data['trip_id'] ?? null;

        // Basic validation (you should implement more robust validation)
        if (empty($category) || !is_numeric($amount) || empty($currency) || empty($expense_date) || empty($trip_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields.']);
            exit();
        }

        if ($this->expenseModel->updateExpense($id, $trip_id, $category, $amount, $currency, $description, $expense_date, $userId)) {
            // Return JSON for successful update
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Expense updated successfully!']);
            exit();
        } else {
            // Return JSON for update failure
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to update expense. Please try again.']);
            exit();
        }
    }
    
    
    public function delete($id) {
        session_start();
    
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
    
        if ($this->expenseModel->deleteExpense($id, $userId)) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Expense deleted successfully.']);
            exit();
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Failed to delete expense.']);
            exit();
        }
    }
    

}
