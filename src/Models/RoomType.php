<?php

namespace App\Models;

use PDO;
use Core\Database; // Assuming your Database connection class

class RoomType
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

   // In your RoomTypeModel.php

   public function findAllActive(): array
   {
       $sql = "SELECT id, name FROM room_types WHERE status = 0";
       $stmt = $this->db->prepare($sql);
       $stmt->execute();
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function find(int $id): array|false
   {
       $sql = "SELECT * FROM room_types WHERE id = :id";
       $stmt = $this->db->prepare($sql);
       $stmt->bindParam(':id', $id, PDO::PARAM_INT);
       $stmt->execute();
       return $stmt->fetch(PDO::FETCH_ASSOC);
   }

    // You can add other methods as needed (e.g., findById, etc.)
}