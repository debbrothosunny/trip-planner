<?php
namespace App\Models;
use PDO;
class PollModel {
    private $db; // Your database connection object

    public function __construct($db) {
        $this->db = $db;
    }

    public function createPoll(int $tripId, int $itineraryId, int $userId, string $question, ?string $endDate): ?int {
        $sql = "INSERT INTO polls (trip_id, itinerary_id, user_id, question, likes) VALUES (?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$tripId, $itineraryId, $userId, $question]);
    
        return $result ? $this->db->lastInsertId() : null;
    }

    public function getPollForItinerary(int $itineraryId): ?array {
        $sql = "SELECT * FROM polls WHERE itinerary_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$itineraryId]);
        $poll = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($poll && $poll['options']) {
            $poll['options'] = json_decode($poll['options'], true);
        }

        return $poll;
    }

    public function getPollsByTripId(int $tripId): array {
        $stmt = $this->db->prepare("SELECT * FROM polls WHERE trip_id = ?");
        $stmt->execute([$tripId]);
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Since there's no 'options' column, you don't need to do any decoding here.
    
        return $polls;  
    }
    public function getPollById(int $pollId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM polls WHERE id = ?");
        $stmt->execute([$pollId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function incrementLike(int $pollId): void {
        $stmt = $this->db->prepare("UPDATE polls SET likes = likes + 1 WHERE id = ?");
        $stmt->execute([$pollId]);
    }

    public function incrementDislike(int $pollId): void {
        $stmt = $this->db->prepare("UPDATE polls SET dislikes = dislikes + 1 WHERE id = ?");
        $stmt->execute([$pollId]);
    }

    public function updateLikedBy(int $pollId, string $likedBy): void {
        $stmt = $this->db->prepare("UPDATE polls SET liked_by = ? WHERE id = ?");
        $stmt->execute([$likedBy, $pollId]);
    }

    public function updateDislikedBy(int $pollId, string $dislikedBy): void {
        $stmt = $this->db->prepare("UPDATE polls SET disliked_by = ? WHERE id = ?");
        $stmt->execute([$dislikedBy, $pollId]);
    }


    public function getPollsByTripIds(array $tripIds): array
    {
        if (empty($tripIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($tripIds), '?'));
        $sql = "SELECT p.*, u.name AS creator_name, i.day_title, t.name AS trip_name
                FROM polls p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN trip_itineraries i ON p.itinerary_id = i.id
                JOIN trips t ON p.trip_id = t.id
                WHERE p.trip_id IN ($placeholders)
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($tripIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
