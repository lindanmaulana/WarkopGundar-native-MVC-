<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Categories extends Model
{
    protected $table = "categories";

    public function create(string $name, string $description): bool
    {
        $sql = "INSERT INTO {$this->table} (name, description) VALUES (:name, :description)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all categories: " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching category by ID: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, string $newName, ?string $newDescription): bool
    {
        $sql = "UPDATE {$this->table} SET name = :name, description = :description WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $newName, PDO::PARAM_STR);
            $stmt->bindParam(":description", $newDescription, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE name = :name";
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            if ($excludeId !== null) {
                $stmt->bindParam(":exclude_id", $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error checking category name existence: " . $e->getMessage());
            return true;
        }
    }
}
