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
use App\Controllers\PaymentController;
use App\Controllers\PollController;

// Create a new router
    $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $router) {


        // Login Routes
        $router->addRoute('GET', '/', [AuthController::class, 'showLoginForm']); // Shows login form
        $router->addRoute('POST', '/login', [AuthController::class, 'login']); // Handles login submission



        $router->addRoute('GET', '/forgot-password', [AuthController::class, 'forgotPassword']); // Show forgot password form
        $router->addRoute('POST', '/forgot-password', [AuthController::class, 'handleForgotPassword']); // Handle form submission
        
        $router->addRoute('GET', '/reset-password', [AuthController::class, 'showResetForm']); // Show reset password form with token
        $router->addRoute('POST', '/reset-password', [AuthController::class, 'updatePassword']); // Handle new password submission

        $router->addRoute('GET', '/verify-otp-reset', [AuthController::class, 'showVerifyOtpForm']);

        $router->addRoute('POST', '/verify-otp-reset', [AuthController::class, 'verifyOtpReset']);
  



        // Registration Routes

        $router->addRoute('POST','/check-email', [AuthController::class, 'checkEmail']);
        $router->addRoute('POST','/check-phone', [AuthController::class, 'checkPhone']);
        $router->addRoute('GET', '/register', [AuthController::class, 'showRegistrationForm']);
        $router->addRoute('POST', '/register', [AuthController::class, 'register']);
        // OTP Verification Routes
        $router->addRoute('GET', '/verify-otp', [AuthController::class, 'showOtpForm']); // Shows OTP form
        $router->addRoute('POST', '/verify-otp', [AuthController::class, 'verifyOtp']); // Handles OTP verification 
        $router->addRoute('POST', '/resend-otp', [AuthController::class, 'resendOtp']);
    
        // Logout Route
        $router->addRoute('POST', '/logout', [AuthController::class, 'logout']);
    


        // Admin Routes
        $router->addGroup('/admin', function (RouteCollector $router) {

            

        $router->addRoute('GET', '/get-payment-details', [PaymentController::class, 'getPaymentDetails']);
        $router->addRoute('GET', '/payment-details/{tripId:\d+}/{userId:\d+}', [AdminController::class, 'tripPaymentDetails']);
        $router->addRoute('POST', '/accept-payment/{tripId:\d+}/{userId:\d+}', [AdminController::class, 'acceptPayment']);

        //  Admin Dashboard
        $router->addRoute('GET', '/dashboard', [AdminController::class, 'dashboard']);

        $router->addRoute('GET', '/profile', [AdminController::class, 'showProfile']);

        $router->addRoute('POST', '/profile/update', [AdminController::class, 'updateProfile']); 

        $router->addRoute('GET', '/user', [AdminController::class, 'users']);

        $router->addRoute('GET', '/deactivate/{id}', [AdminController::class, 'deactivateUser']);

        $router->addRoute('GET', '/activate/{id}', [AdminController::class, 'activateUser']);


        $router->addRoute('POST', '/delete/{id}', [AdminController::class, 'deleteUser']);


        $router->addRoute('GET', '/trip-participant', [AdminController::class, 'tripParticipant']);

        //  Delete a User
        $router->addRoute('GET', '/delete/{id}', [AdminController::class, 'deleteUser']);

        //  View User's Trips
        $router->addRoute('GET', '/user/{id}/trips', [AdminController::class, 'viewUserTrips']);

        // Get Payment Details (AJAX)
        

        // Update Payment Status (Form Submission)
        $router->addRoute('POST', '/update-payment-status', [PaymentController::class, 'updatePaymentStatus']);

        


        // Country Route

        $router->addRoute('GET', '/country', [HotelController::class, 'countryList']);

        //  Create a new hotel (form page)
        $router->addRoute('GET', '/country/create', [HotelController::class, 'createCountry']);

        //  Store new hotel (form submission)
        $router->addRoute('POST', '/country/store', [HotelController::class, 'storeCountry']);

        //  Update hotel (form submission)
        $router->addRoute('POST', '/country/update/{id}', [HotelController::class, 'updateCountry']);

        //  Delete hotel
        $router->addRoute('GET', '/country/delete/{id}', [HotelController::class, 'deleteCountry']);


         // State Routes
        $router->addRoute('GET', '/country/state/{countryId}', [HotelController::class, 'showStates']);
        $router->addRoute('GET', '/country/state/create/{countryId}', [HotelController::class, 'createStateForm']);
        $router->addRoute('POST', '/state/store', [HotelController::class, 'storeState']);
        $router->addRoute('POST', '/state/update/{id}', [HotelController::class, 'updateState']); // You'll need this method in your controller
        $router->addRoute('GET', '/state/delete/{id}', [HotelController::class, 'deleteState']); // You'll need this method in your controller

        

        // Room Type Route

         $router->addRoute('GET', '/room-type', [HotelController::class, 'roomTypeList']);


         $router->addRoute('GET', '/room-type/create', [HotelController::class, 'createRoomType']);
 

         $router->addRoute('POST', '/room-type/store', [HotelController::class, 'storeRoomType']);
 

         $router->addRoute('POST', '/room-type/update/{id}', [HotelController::class, 'updateRoomType']);
 

         $router->addRoute('GET', '/room-type/delete/{id}', [HotelController::class, 'deleteRoomType']);








        // Hotel Room Routes
        $router->addRoute('GET', '/hotel-room', [HotelController::class, 'hotelRoomList']);
        $router->addRoute('GET', '/hotel-room/create', [HotelController::class, 'createHotelRoom']);
        $router->addRoute('POST', '/hotel-room/store', [HotelController::class, 'storeHotelRoom']);
        $router->addRoute('POST', '/hotel-room/update/{id}', [HotelController::class, 'updateHotelRoom']);
        $router->addRoute('GET', '/hotel-room/delete/{id}', [HotelController::class, 'deleteHotelRoom']);



        //  List all hotels
        $router->addRoute('GET', '/hotel', [HotelController::class, 'hotelList']);
        $router->addRoute('GET', '/hotel/create', [HotelController::class, 'createHotel']);
        $router->addRoute('POST', '/hotel/store', [HotelController::class, 'storeHotel']);
        $router->addRoute('POST', '/hotel/update/{id}', [HotelController::class, 'updateHotel']);
        $router->addRoute('GET', '/hotel/delete/{id}', [HotelController::class, 'deleteHotel']);




        //  Hotel Bookings Management
        $router->addRoute('GET', '/hotel-bookings', [HotelController::class, 'bookingIndex']); // List all bookings
        $router->addRoute('GET', '/hotel-bookings/view/{id}', [HotelController::class, 'viewBooking']); // Optional: view single booking
        $router->addRoute('POST', '/hotel-bookings/confirm', [HotelController::class, 'confirmBooking']); // Confirm booking
   

    });

 
    // User Routes
    $router->addGroup('/user', function (RouteCollector $router) {
        $router->addRoute('GET', '/create-trip', [UserController::class, 'showCreateTripForm']);
        $router->addRoute('POST', '/trip/store', [UserController::class, 'storeTrip']);
        $router->addRoute('GET', '/dashboard', [UserController::class, 'dashboard']);
        $router->addRoute('GET', '/view-trip', [UserController::class, 'viewTrips']);
       
        $router->addRoute('POST', '/trip/update/{id}', [UserController::class, 'updateTrip']);
       $router->addRoute('GET', '/trip/delete/{id}', [UserController::class, 'deleteTrip']);

    });



    // Add trip itinerary routes

    $router->addGroup('/trip/{trip_id}/itinerary', function (RouteCollector $router) {
        // View all itineraries
        $router->addRoute('GET', '', [UserController::class, 'showItineraries']);

        // Trip Owner: Create new itinerary
        $router->addRoute('GET', '/create', [UserController::class, 'create']);  
        $router->addRoute('POST', '/create', [UserController::class, 'store']);  

        // Trip Owner: Edit, Update, Delete itinerary
        $router->addRoute('GET', '/{id}/edit', [UserController::class, 'edit']);
        $router->addRoute('POST', '/{id}/update', [UserController::class, 'update']);
     
    });

    $router->addGroup('/itinerary', function (RouteCollector $router) {
        $router->addRoute('GET', '/{id}/delete', [UserController::class, 'deleteItineraryById']);
    });
  
    


    // $router->addGroup('/user/trip/{trip_id}/invitation', function (RouteCollector $router) {
    //     // Show the send invitation form
    //     $router->addRoute('GET', '/send', [InvitationController::class, 'showSendInvitationForm']);  // Show form to send invitation
        
    //     // Handle sending the invitation
    //     $router->addRoute('POST', '/send', [InvitationController::class, 'sendInvitation']);  // Process and send the invitation
        
    //     // Show all sent invitations (optional)
    //     $router->addRoute('GET', '/my-invitations', [InvitationController::class, 'showMyInvitations']);  // Show all user's invitations
        
    //     // Handle accepting the invitation (new route)
    //     $router->addRoute('GET', '/accept', [UserController::class, 'acceptInvitation']);  // Handle accepting invitation
    // });
    


    $router->addGroup('/user/transportation', function (RouteCollector $router) {
        $router->addRoute('GET', '', [TransportationController::class, 'transportationList']);  // Show all transportation records
        $router->addRoute('GET', '/create', [TransportationController::class, 'create']);  // Show the create transportation form
        $router->addRoute('POST', '/store', [TransportationController::class, 'store']);  // Store new transportation record
        $router->addRoute('POST', '/update/{id}', [TransportationController::class, 'update']); // Update specific transportation record
        $router->addRoute('GET', '/delete/{id}', [TransportationController::class, 'delete']);  // Delete specific transportation record
    });
    



    $router->addGroup('/user/accommodation', function (RouteCollector $router) {

        $router->addRoute('GET', '', [AccommodationController::class, 'accommodationList']);   // Show all accommodations
        $router->addRoute('GET', '/create', [AccommodationController::class, 'accommodationCreate']);   // Show the create form
    
        // AJAX routes for fetching dependent dropdown data
        $router->addRoute('GET', '/states/{countryId}', [AccommodationController::class, 'getStatesByCountry']);
        $router->addRoute('GET', '/hotels/{countryId}/{stateId}', [AccommodationController::class, 'getHotelsByCountryAndState']);
        $router->addRoute('GET', '/room-types/{hotelId}', [AccommodationController::class, 'getRoomTypesByHotel']);
    
        $router->addRoute('POST', '/check-room-availability', [AccommodationController::class, 'checkRoomAvailability']);
    
        // Payment initiation route
        $router->addRoute('POST', '/payment/initiate', [PaymentController::class, 'initiateAccommodationPayment']);
    
        // Other routes
        $router->addRoute('POST', '/store', [AccommodationController::class, 'storeAccommodation']);   // Store new accommodation
        $router->addRoute('GET', '/{id:\d+}/edit', [AccommodationController::class, 'accommodationEdit']);   // Show edit form
        $router->addRoute('POST', '/update/{id:\d+}', [AccommodationController::class, 'update']); // Update accommodation
        $router->addRoute('GET', '/delete/{id:\d+}', [AccommodationController::class, 'delete']);
    });


    
       
    

    $router->addGroup('/user/expense', function (RouteCollector $router) {
        // Show all expenses
        $router->addRoute('GET', '', [ExpenseController::class, 'showExpenses']);  
    

        $router->addRoute('GET', '/data', [ExpenseController::class, 'getExpensesData']);

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

    });

    

    $router->addGroup('/user/budget-view', function (RouteCollector $router) {
        $router->addRoute('GET', '', [BudgetController::class, 'showBudgetView']);
    });
    
    
    $router->addGroup('/user', function (RouteCollector $router) {
        // Other routes inside /user group
        $router->addRoute('GET', '/my_trip_participants', [UserController::class, 'myTripParticipants']);

        // Routes for the follow system
        $router->addRoute('POST', '/{followingId:\d+}/follow', [UserController::class, 'followUser']);
        $router->addRoute('POST', '/{followingId:\d+}/unfollow', [UserController::class, 'unfollowUser']);

        $router->addRoute('GET', '/profile/details/{userId:\d+}', [ParticipantController::class, 'userProfileDetails']);
    });

  
    $router->addGroup('/user/profile', function (RouteCollector $router) {
        $router->addRoute('GET', '', [UserController::class, 'showProfile']);  
        $router->addRoute('POST', '/update', [UserController::class, 'updateProfile']); 
      
    });
    


    $router->addGroup('/participant', function (RouteCollector $router) {
        $router->addRoute('GET', '/dashboard', [ParticipantController::class, 'dashboard']);
        $router->addRoute('GET', '/trips', [ParticipantController::class, 'Trips']);
        $router->addRoute('GET', '/archived-trips', [ParticipantController::class, 'archivedTrips']);
    
        // 📌 Trip Details - Show details for a specific trip
        $router->addRoute('GET', '/trip-details/{tripId}', [ParticipantController::class, 'viewTripDetails']);
        $router->addRoute('POST', '/generate-invite-link', [ParticipantController::class, 'generateInviteLink']);
    
        // 📌 Status Update - Accept/Decline trip
        $router->addRoute('POST', '/update-status', [ParticipantController::class, 'updateStatus']);
    
        // 📌 Submit Review for a trip
        $router->addRoute('POST', '/submitReview/{tripId}', [ParticipantController::class, 'submitReview']);
    
        // 📌 Payment Routes
        $router->addRoute('POST', '/initiate-payment', [PaymentController::class, 'initiatePayment']);
        // Optional route to show a payment confirmation form
        $router->addRoute('GET', '/pay/{tripId}', [ParticipantController::class, 'showPaymentForm']);
    

        $router->addRoute('POST', '/cancel-trip', [ParticipantController::class, 'cancelTrip']);
    
        // 📌 Poll Creation
        $router->addRoute('POST', '/poll/create', [PollController::class, 'storePoll']);
    
        // 📌 Poll Like/Dislike (AJAX)
        $router->addRoute('POST', '/poll/like/{pollId}', [PollController::class, 'likePoll']);
        $router->addRoute('POST', '/poll/dislike/{pollId}', [PollController::class, 'dislikePoll']);
    });
    
    $router->addRoute('GET', '/invite/{code:[a-zA-Z0-9_.-]+}', [ParticipantController::class, 'handleInviteLink']);

    

    $router->addRoute('POST', '/handle-payment-success', [PaymentController::class, 'handlePaymentSuccess']);
    $router->addRoute('POST', '/payment/cancel', [PaymentController::class, 'handlePaymentCancel']);
    

    



    $router->addGroup('/participant/profile', function (RouteCollector $router) {
        $router->addRoute('GET', '', [ParticipantController::class, 'showProfile']);  
        $router->addRoute('POST', '/update', [ParticipantController::class, 'updateProfile']); 

    });


});

$uri = $_SERVER['REQUEST_URI'];

// Remove query string
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Dispatch the request using the URI without the query string
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);

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