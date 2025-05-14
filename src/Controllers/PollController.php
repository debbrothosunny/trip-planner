<?php
namespace App\Controllers;
use Core\Database;
use App\Models\PollModel; 
use App\Models\Trip;
use App\Models\TripParticipant; 

use \DateTime;
class PollController {
    private $pollModel;
    private $trip;
    private $db; // Assuming your controller might also need direct access
    protected $tripParticipantModel;
    public function __construct() {
        $database = Database::getInstance(); // Get the singleton instance
        $this->db = $database->getConnection(); // Get the database connection
        $this->pollModel = new PollModel($this->db); // Pass the connection to the model
        $this->tripParticipantModel = new TripParticipant($this->db);
        $this->trip = new Trip($this->db);
        session_start();
    }


    private function getUserSessionId(): ?int {
        return $_SESSION['user_id'] ?? null; // Adapt to your session management
    }

    private function isAcceptedParticipant(int $tripId, int $userId): bool {
        $status = $this->tripParticipantModel->getParticipantStatus($tripId, $userId);
        return $status === 'accepted';
    }

    public function storePoll() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'], $_POST['itinerary_id'], $_POST['question'])) {
            $tripId = filter_var($_POST['trip_id'], FILTER_VALIDATE_INT);
            $itineraryId = filter_var($_POST['itinerary_id'], FILTER_VALIDATE_INT);
            $question = trim($_POST['question']);
            $userId = $this->getUserSessionId();

            if (!$userId || !$this->isAcceptedParticipant($tripId, $userId)) {
                header("Location: /participant/trip-details/" . $tripId . "?poll_error=not_accepted");
                exit();
            }

            if ($tripId && $itineraryId && $userId && !empty($question)) {
                // Use the existing Trip model instance with the database connection
                $trip = $this->trip->getTripById($tripId);

                if ($trip && isset($trip['start_date'])) {
                    $tripStartDate = new DateTime($trip['start_date']);
                    $pollEndDate = $tripStartDate->modify('-2 days')->format('Y-m-d H:i:s');

                    $pollId = $this->pollModel->createPoll(
                        $tripId,
                        $itineraryId,
                        $userId,
                        $question,
                        $pollEndDate
                    );

                    if ($pollId) {
                        header("Location: /participant/trip-details/" . $tripId . "?poll_created=1");
                        exit();
                    } else {
                        header("Location: /participant/trip-details/" . $tripId . "?poll_error=create_failed");
                        exit();
                    }
                } else {
                    header("Location: /participant/trip-details/" . $tripId . "?poll_error=trip_data_missing");
                    exit();
                }
            } else {
                header("Location: /participant/trip-details/" . $tripId . "?poll_error=invalid_poll_data");
                exit();
            }
        } else {
            header("Location: /participant/trip-details/" . ($_POST['trip_id'] ?? '') . "?poll_error=form_submission_error");
            exit();
        }
    }

    public function likePoll(int $pollId) {
        $userId = $this->getUserSessionId();
        if (!$userId) {
            echo $this->unauthorizedResponse();
            return;
        }

        $poll = $this->pollModel->getPollById($pollId);
        if (!$poll) {
            echo $this->notFoundResponse();
            return;
        }

        // Get the trip ID associated with the poll
        $tripId = $poll['trip_id'];

        if (!$this->isAcceptedParticipant($tripId, $userId)) {
            echo $this->forbiddenResponse('You must be an accepted participant to like polls.');
            return;
        }

        if ($poll['user_id'] == $userId) {
            echo $this->alreadyVotedResponse('You cannot vote on your own poll.');
            return;
        }

        $likedBy = explode(',', $poll['liked_by'] ?? '');
        if (!in_array($userId, $likedBy)) {
            $likedBy[] = $userId;
            $this->pollModel->updateLikedBy($pollId, implode(',', array_filter(array_unique($likedBy))));
            $this->pollModel->incrementLike($pollId);
            $updatedPoll = $this->pollModel->getPollById($pollId);
            echo $this->successResponse($updatedPoll['likes'], true); // Indicate liked
            return;
        } else {
            echo $this->alreadyVotedResponse('You have already liked this poll.', true); // Indicate liked
            return;
        }
    }

    private function unauthorizedResponse() {
        http_response_code(401);
        return json_encode(['error' => 'Unauthorized']);
    }

    private function forbiddenResponse(string $message) {
        http_response_code(403);
        return json_encode(['error' => $message]);
    }

    private function notFoundResponse() {
        http_response_code(404);
        return json_encode(['error' => 'Poll not found']);
    }

    private function alreadyVotedResponse(string $message, bool $liked = false) {
        return json_encode(['message' => $message, 'liked' => $liked]);
    }

    private function successResponse(int $likes, bool $liked = false) {
        return json_encode(['likes' => $likes, 'liked' => $liked]);
    }

    public function checkVote(int $pollId) {
        $userId = $this->getUserSessionId();
        if (!$userId) {
            echo json_encode(['liked' => false]);
            return;
        }

        $poll = $this->pollModel->getPollById($pollId);
        if (!$poll) {
            echo json_encode(['liked' => false]);
            return;
        }

        $tripId = $poll['trip_id'];
        if (!$this->isAcceptedParticipant($tripId, $userId)) {
            echo json_encode(['liked' => false]); // Or handle differently if needed
            return;
        }

        $likedBy = explode(',', $poll['liked_by'] ?? '');

        echo json_encode(['liked' => in_array($userId, $likedBy)]);
        return;
    }

    

}

