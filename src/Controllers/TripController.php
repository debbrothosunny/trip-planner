<?php
use App\Models\TripItinerary; 
use App\Models\ItineraryEditRequest; 
class TripController {
    private $db;
    private $editRequestModel;
    private $itineraryModel; // Add a property for the Itinerary model

    public function __construct($database) {
        $this->db = $database;
        $this->editRequestModel = new ItineraryEditRequest($database);
        $this->itineraryModel = new TripItinerary($database); // Instantiate Itinerary model in the constructor
    }

    // public function acceptItineraryRequest($requestId) {
    //     session_start();

    //     // Ensure user is logged in and has admin role (adjust as needed)
    //     if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    //         header("Location: /login");
    //         exit();
    //     }

    //     // Ensure database connection exists
    //     if (!$this->db) {
    //         die("Database connection is not initialized.");
    //     }

    //     $request = $this->editRequestModel->find($requestId);

    //     if (!$request) {
    //         $_SESSION['error'] = "Edit request not found.";
    //         header("Location: /admin/my-trip-participants"); // Redirect with error
    //         exit();
    //     }

    //     // Check if the current user is the owner of the trip (optional security check)
    //     $stmt = $this->db->prepare("SELECT user_id FROM trips WHERE id = ?");
    //     $stmt->execute([$request['trip_id']]);
    //     $trip = $stmt->fetch(PDO::FETCH_ASSOC);

    //     if (!$trip || $trip['user_id'] != $_SESSION['user']['id']) {
    //         $_SESSION['error'] = "You are not authorized to accept this request.";
    //         header("Location: /admin/my-trip-participants"); // Redirect with error
    //         exit();
    //     }

    //     // **Implement the logic to update the itinerary item**
    //     // This will depend on how the participant described the changes in the 'notes' field.

    //     // Example: If 'notes' contains the new description for the itinerary item
    //     $newDescription = $request['notes'];
    //     $itineraryIdToUpdate = $request['itinerary_id'];

    //     $updated = $this->itineraryModel->update($itineraryIdToUpdate, ['description' => $newDescription]);

    //     if ($updated) {
    //         // Update the status of the edit request to 'accepted' in the database
    //         $updatedRequest = $this->editRequestModel->updateRequestStatusAndGet($requestId, 'accepted');
    //         if ($updatedRequest) {
    //             $_SESSION['success'] = "Itinerary edit request for '{$updatedRequest['itinerary_name']}' by {$updatedRequest['user_name']} accepted.";
    //         } else {
    //             $_SESSION['success'] = "Itinerary edit request accepted.";
    //         }
    //     } else {
    //         $_SESSION['error'] = "Failed to update the itinerary item.";
    //     }

    //     // Redirect back to the page displaying the requests
    //     header("Location: /admin/my-trip-participants");
    //     exit();
    // }
}
