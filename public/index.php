<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Ensure autoload is loaded

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\TransportationController;
use App\Controllers\AccommodationController;
use App\Controllers\ExpenseController;
use App\Controllers\InvitationController;
use App\Controllers\ParticipantController;
use App\Controllers\BudgetController;

// Create a new router
    $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $router) {
        // Login Routes
        $router->addRoute('GET', '/', [AuthController::class, 'showLoginForm']); // Shows login form
        $router->addRoute('POST', '/login', [AuthController::class, 'login']); // Handles login submission

        
    

    // Registration Routes
    $router->addRoute('GET', '/register', [AuthController::class, 'showRegistrationForm']);
    $router->addRoute('POST', '/register', [AuthController::class, 'register']);
    // OTP Verification Routes
    $router->addRoute('GET', '/verify-otp', [AuthController::class, 'showOtpForm']); // Shows OTP form
    $router->addRoute('POST', '/verify-otp', [AuthController::class, 'verifyOtp']); // Handles OTP verification 

   
    // Logout Route
    $router->addRoute('POST', '/logout', [AuthController::class, 'logout']);
    
    // Admin Routes
    $router->addGroup('/admin', function (RouteCollector $router) {
        // ✅ Admin Dashboard (System Analytics & User Management)
        $router->addRoute('GET', '/dashboard', [AdminController::class, 'dashboard']);

        // ✅ Delete a User
        $router->addRoute('GET', '/delete/{id}', [AdminController::class, 'deleteUser']);

        // ✅ View User's Trips
        $router->addRoute('GET', '/user/{id}/trips', [AdminController::class, 'viewUserTrips']);
    });

    


    // User Routes
    $router->addGroup('/user', function (RouteCollector $router) {
        $router->addRoute('GET', '/dashboard', [UserController::class, 'dashboard']);
        $router->addRoute('GET', '/create-trip', [UserController::class, 'showCreateTripForm']);
        $router->addRoute('POST', '/create-trip', [UserController::class, 'createTrip']);
        $router->addRoute('GET', '/my-trips', [UserController::class, 'myTrips']);
        $router->addRoute('GET', '/trip/{id}', [UserController::class, 'viewTrip']);
        $router->addRoute('GET', '/trip/{id}/edit', [UserController::class, 'editTrip']);
        $router->addRoute('POST', '/trip/{id}', [UserController::class, 'updateTrip']);
        $router->addRoute('GET','/trip/delete/{id}', [UserController::class, 'deleteTrip']);

    });



    // Add trip itinerary routes
    $router->addGroup('/trip/{trip_id}/itinerary', function (RouteCollector $router) {
        $router->addRoute('GET', '', [UserController::class, 'showItineraries']);
        $router->addRoute('GET', '/create', [UserController::class, 'create']);  // Show form to create itinerary
        $router->addRoute('POST', '/create', [UserController::class, 'store']);  // Accept POST request for itinerary creation
    
        $router->addRoute('GET', '/{id}/edit', [UserController::class, 'edit']);
        $router->addRoute('POST', '/{id}/update', [UserController::class, 'update']);
        $router->addRoute('GET', '/{id}/delete', [UserController::class, 'delete']);
    });
    


    $router->addGroup('/user/trip/{trip_id}/invitation', function (RouteCollector $router) {
        // Show the send invitation form
        $router->addRoute('GET', '/send', [InvitationController::class, 'showSendInvitationForm']);  // Show form to send invitation
        
        // Handle sending the invitation
        $router->addRoute('POST', '/send', [InvitationController::class, 'sendInvitation']);  // Process and send the invitation
        
        // Show all sent invitations (optional)
        $router->addRoute('GET', '/my-invitations', [InvitationController::class, 'showMyInvitations']);  // Show all user's invitations
        
        // Handle accepting the invitation (new route)
        $router->addRoute('GET', '/accept', [UserController::class, 'acceptInvitation']);  // Handle accepting invitation
    });
    



    $router->addGroup('/user/transportation', function (RouteCollector $router) {
        $router->addRoute('GET', '', [TransportationController::class, 'transportationList']);  // Show all transportation records
        $router->addRoute('GET', '/create', [TransportationController::class, 'create']);  // Show the create transportation form
        $router->addRoute('POST', '/store', [TransportationController::class, 'store']);  // Store new transportation record
        $router->addRoute('GET', '/edit/{id}', [TransportationController::class, 'edit']);  // Show edit form for a specific transportation
        $router->addRoute('POST', '/update/{id}', [TransportationController::class, 'update']); // Update specific transportation record
        $router->addRoute('GET', '/delete/{id}', [TransportationController::class, 'delete']);  // Delete specific transportation record
    });
    



    $router->addGroup('/user/accommodation', function (RouteCollector $router) {
        $router->addRoute('GET', '', [AccommodationController::class, 'accommodationList']);  // Show all accommodations
        $router->addRoute('GET', '/create', [AccommodationController::class, 'create']);  // Show the create form
        $router->addRoute('POST', '/store', [AccommodationController::class, 'store']);  // Store new accommodation
        $router->addRoute('GET', '/{id}/edit', [AccommodationController::class, 'accommodationEdit']);  // Show edit form
        $router->addRoute('POST', '/update/{id}', [AccommodationController::class, 'update']); // Update accommodation
        $router->addRoute('GET', '/delete/{id}', [AccommodationController::class, 'delete']);
    });
    


    $router->addGroup('/user/expense', function (RouteCollector $router) {
        // Show all expenses
        $router->addRoute('GET', '', [ExpenseController::class, 'showExpenses']);  
    
        // Show the form to add a new expense
        $router->addRoute('GET', '/create', [ExpenseController::class, 'createExpenseForm']);
    
        // Store a new expense
        $router->addRoute('POST', '/store', [ExpenseController::class, 'storeExpense']);  
    
        // Show edit form
        $router->addRoute('GET', '/edit/{id}', [ExpenseController::class, 'editExpenseForm']);
    
        // Update expense
        $router->addRoute('POST', '/update/{id}', [ExpenseController::class, 'updateExpense']);
    
        // Delete expense
        $router->addRoute('GET', '/delete/{id}', [ExpenseController::class, 'delete']);
    
        // View budget for a specific user and trip

    });

    $router->addGroup('/user/budget-view', function (RouteCollector $router) {
        $router->addRoute('GET', '', [BudgetController::class, 'showBudgetView']);
    });
    
    


    // Participant Routes

    $router->addGroup('/participant', function (RouteCollector $router) {
        // 📌 Dashboard - Show pending invitations & accepted trips
        $router->addRoute('GET', '/dashboard', [ParticipantController::class, 'dashboard']);
    
        // 📌 View trip details
        $router->addRoute('GET', '/trip/{id}', [ParticipantController::class, 'viewTrip']);
    
        // 📌 Accept or Decline trip invitations
        $router->addRoute('POST', '/trip/{id}/accept', [ParticipantController::class, 'acceptInvitation']);
        $router->addRoute('POST', '/trip/{id}/decline', [ParticipantController::class, 'declineInvitation']);
    
        // 📌 View itinerary for a trip
        $router->addRoute('GET', '/trip/{id}/itinerary', [ParticipantController::class, 'viewItinerary']);
    
        // 📌 Add personal notes for a trip
        $router->addRoute('POST', '/trip/{id}/notes', [ParticipantController::class, 'addNotes']);
    
        // 📌 Add expenses for a trip
        $router->addRoute('POST', '/trip/{id}/expenses', [ParticipantController::class, 'addExpenses']);
    });
    



    
    
    


});

// Dispatch the request
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "404 Not Found";
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Instantiate the controller and call the method
        [$controller, $method] = $handler;
        $controllerInstance = new $controller();
        call_user_func_array([$controllerInstance, $method], $vars);
        break;
}