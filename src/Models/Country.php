<?php

namespace App\Models;

use PDO;
use Core\Database;

class Country
{
    private $db;
    protected $table = 'countries';



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


    public function updateCountry(int $id, string $name, int $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET name = :name, status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }





}