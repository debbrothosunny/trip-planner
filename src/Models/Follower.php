<?php

namespace App\Models; // Adjust the namespace to match your project

use PDOException;
use PDO;

class Follower
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Creates a new follow relationship.
     *
     * @param int $followerId The ID of the user who is following.
     * @param int $followingId The ID of the user being followed.
     * @return bool True on success, false on failure.
     */
    public function create(int $followerId, int $followingId): bool
    {
        $sql = "INSERT INTO followers (follower_id, following_id, created_at)
                VALUES (:follower_id, :following_id, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':follower_id', $followerId, PDO::PARAM_INT);
            $stmt->bindParam(':following_id', $followingId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error creating follow relationship: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a follow relationship.
     *
     * @param int $followerId The ID of the user who is unfollowing.
     * @param int $followingId The ID of the user being unfollowed.
     * @return bool True on success, false on failure.
     */
    public function delete(int $followerId, int $followingId): bool
    {
        $sql = "DELETE FROM followers
                WHERE follower_id = :follower_id AND following_id = :following_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':follower_id', $followerId, PDO::PARAM_INT);
            $stmt->bindParam(':following_id', $followingId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error deleting follow relationship: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a user is already following another user.
     *
     * @param int $followerId The ID of the potential follower.
     * @param int $followingId The ID of the potential followed user.
     * @return bool True if they are already following, false otherwise.
     */
    public function isFollowing(int $followerId, int $followingId): bool
    {
        $sql = "SELECT COUNT(*) FROM followers
                WHERE follower_id = :follower_id AND following_id = :following_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':follower_id', $followerId, PDO::PARAM_INT);
            $stmt->bindParam(':following_id', $followingId, PDO::PARAM_INT);
            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error checking follow relationship: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets a list of users that a specific user is following.
     *
     * @param int $followerId The ID of the user to retrieve the following list for.
     * @return array An array of user IDs being followed.
     */
    public function getFollowing(int $followerId): array
    {
        $sql = "SELECT following_id FROM followers WHERE follower_id = :follower_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':follower_id', $followerId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting following list: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Gets a list of users who are following a specific user.
     *
     * @param int $followingId The ID of the user to retrieve the followers list for.
     * @return array An array of user IDs who are followers.
     */
    public function getFollowers(int $followingId): array
    {
        $sql = "SELECT follower_id FROM followers WHERE following_id = :following_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':following_id', $followingId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting followers list: " . $e->getMessage());
            return [];
        }
    }


    public function getFollowerCount(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM  followers  WHERE following_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Error (Follower::getFollowerCount): " . $e->getMessage());
            return 0;
        }
    }
}