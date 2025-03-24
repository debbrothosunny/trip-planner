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
use App\Controllers\HotelController;

// Create a new router
    $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $router) {
        // Login Routes
        $router->addRoute('GET', '/', [AuthController::class, 'showLoginForm']); // Shows login form
        $router->addRoute('POST', '/login', [AuthController::class, 'login']); // Handles login submission



        $router->addRoute('GET', '/forgot_password', [AuthController::class, 'forgotPassword']); // Show forgot password form
        $router->addRoute('POST', '/forgot_password', [AuthController::class, 'handleForgotPassword']); // Handle form submission

    

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
        // ✅ Admin Dashboard
        $router->addRoute('GET', '/dashboard', [AdminController::class, 'dashboard']);

        // ✅ Delete a User
        $router->addRoute('GET', '/delete/{id}', [AdminController::class, 'deleteUser']);

        // ✅ View User's Trips
        $router->addRoute('GET', '/user/{id}/trips', [AdminController::class, 'viewUserTrips']);

        // ✅ Accept Participant Payment
        $router->addRoute('GET', '/accept-payment/{tripId}/{userId}', [AdminController::class, 'acceptPayment']);

        // ✅ View Payment Details
        $router->addRoute('GET', '/view-payment-details/{tripId}/{userId}', [AdminController::class, 'viewPaymentDetails']);

        // 🔹 Hotel Management Routes (Newly Added)
        

        // ✅ List all hotels
        $router->addRoute('GET', '/hotels', [HotelController::class, 'index']);

        // ✅ Create a new hotel (form page)
        $router->addRoute('GET', '/hotels/create', [HotelController::class, 'create']);


        // ✅ Store new hotel (form submission)
        $router->addRoute('POST', '/hotels/store', [HotelController::class, 'store']);

        // ✅ Edit hotel (form page)
        $router->addRoute('GET', '/hotels/edit/{id}', [HotelController::class, 'edit']);

        // ✅ Update hotel (form submission)
        $router->addRoute('POST', '/hotels/update/{id}', [HotelController::class, 'update']);

        // ✅ Delete hotel
        $router->addRoute('GET', '/hotels/delete/{id}', [HotelController::class, 'delete']);

        // ✅ List all hotels Rooms
        $router->addRoute('GET', '/hotels/rooms', [HotelController::class, 'roomIndex']);
        $router->addRoute('GET', '/hotels/rooms/create', [HotelController::class, 'createRoom']);
        $router->addRoute('POST', '/hotels/rooms/store', [HotelController::class, 'storeRoom']);
        $router->addRoute('GET', '/hotels/rooms/edit/{id}', [HotelController::class, 'editRoom']);
        $router->addRoute('POST', '/hotels/rooms/update/{id}', [HotelController::class, 'updateRoom']);
        $router->addRoute('GET', '/hotels/rooms/delete/{id}', [HotelController::class, 'deleteRoom']);



        // ✅ Hotel Bookings Management
        $router->addRoute('GET', '/hotel-bookings', [HotelController::class, 'bookingIndex']); // List all bookings
        $router->addRoute('GET', '/hotel-bookings/view/{id}', [HotelController::class, 'viewBooking']); // Optional: view single booking
        $router->addRoute('POST', '/hotel-bookings/confirm', [HotelController::class, 'confirmBooking']); // Confirm booking
   

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
        $router->addRoute('GET', '/create', [AccommodationController::class, 'accommodationCreate']);  // Show the create form
        
        // Update the route to include the {location} parameter
        $router->addRoute('GET', '/fetch-hotels/{location}', [AccommodationController::class, 'fetchHotelsByLocation']);

        $router->addRoute('GET', '/fetch-hotel-rooms/{hotelId}', [AccommodationController::class, 'fetchHotelRooms']);
    
        // Other routes
        $router->addRoute('POST', '/store', [AccommodationController::class, 'storeAccommodation']);  // Store new accommodation
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
    
    
    $router->addGroup('/user', function (RouteCollector $router) {
        // Other routes inside /user group
        $router->addRoute('GET', '/my_trip_participants', [UserController::class, 'myTripParticipants']);
    });

  
    $router->addGroup('/user/profile', function (RouteCollector $router) {
        $router->addRoute('GET', '', [UserController::class, 'showProfile']);  // Show user profile
        $router->addRoute('POST', '/update', [UserController::class, 'updateProfile']); // Update profile (name, email, password)
    });
    


    // Participant Routes
    $router->addGroup('/participant', function (RouteCollector $router) {
        // 📌 Dashboard - Show pending invitations & accepted trips
        $router->addRoute('GET', '/dashboard', [ParticipantController::class, 'dashboard']);

        // 📌 Trip Details - Show details for a specific trip
        $router->addRoute('GET', '/trip-details/{tripId}', [ParticipantController::class, 'viewTripDetails']);
            
        // 📌 Status Update - Accept/Decline trip
        $router->addRoute('POST', '/update-status', [ParticipantController::class, 'updateStatus']);

        // 📌 Submit Review for a trip
        $router->addRoute('POST', '/submitReview/{tripId}', [ParticipantController::class, 'submitReview']);

        // 📌 Payment Routes
        $router->addRoute('POST', '/make-payment', [ParticipantController::class, 'makePayment']);
    });



    $router->addGroup('/participant/profile', function (RouteCollector $router) {
        $router->addRoute('GET', '', [ParticipantController::class, 'showProfile']);  // Show user profile
        $router->addRoute('POST', '/update', [ParticipantController::class, 'updateProfile']); // Update profile (name, email, password)
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