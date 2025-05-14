<?php

namespace App\Models;

use PDO;
use Core\Database;

class State
{
    private $db;
    private $table = "states";

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function findAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 0"; // Assuming 0 is the value for 'Active'
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}