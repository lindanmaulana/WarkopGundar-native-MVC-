<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Product extends Model
{
    protected $table = 'products';

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting products: " . $e->getMessage());
            return 0;
        }
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
        (category_id, name, image_url, description, price, stock) 
        VALUES (:category_id, :name, :image_url, :description, :price, :stock)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error inserting product: " . $e->getMessage());
            return false;
        }
    }



    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT p.*, c.name as category_name FROM {$this->table} p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all products: " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id)
    {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p JOIN categories c ON p.category_id = c.id WHERE p.id = :id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching product by ID: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, int $categoryId, string $name, ?string $description, float $price, string $status): bool
    {
        $sql = "UPDATE {$this->table} SET category_id = :category_id, name = :name, description = :description, price = :price, status = :status WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":price", $price, PDO::PARAM_STR);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    public function updateWithImage(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} 
            SET category_id = :category_id, 
                name = :name, 
                description = :description, 
                price = :price, 
                stock = :stock,
                image_url = :image_url
            WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);

            error_log('Updating product ID ' . $id . ' with data: ' . json_encode($data));

            $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':stock', $data['stock'], PDO::PARAM_INT);
            $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating product with image: " . $e->getMessage());
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
            error_log("Error deleting product: " . $e->getMessage());
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
            error_log("Error checking product name existence: " . $e->getMessage());
            return true;
        }
    }

    public function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = :status";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting products by status: " . $e->getMessage());
            return 0;
        }
    }

    public function updateProductStatus(int $productId, string $newStatus): bool
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":status", $newStatus, PDO::PARAM_STR);
            $stmt->bindParam(":id", $productId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating product status: " . $e->getMessage());
            return false;
        }
    }

    // Ambil semua produk yang stoknya masih tersedia
    public function getAvailableProducts(): array
    {
        $sql = "SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.stock > 0 
            ORDER BY p.id DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching available products: " . $e->getMessage());
            return [];
        }
    }

    // Ambil produk berdasarkan nama kategori (makanan / minuman)
    public function getProductsByCategory(string $categoryName): array
    {
        $sql = "SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            JOIN categories c ON p.category_id = c.id 
            WHERE c.name = :category_name AND p.stock > 0 
            ORDER BY p.id DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category_name', $categoryName, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching products by category: " . $e->getMessage());
            return [];
        }
    }

    public function getAllWithCategory(?int $categoryId = null): array
    {
        $sql = "SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            JOIN categories c ON p.category_id = c.id";

        if ($categoryId) {
            $sql .= " WHERE p.category_id = :category_id";
        }

        $sql .= " ORDER BY p.id DESC";

        try {
            $stmt = $this->db->prepare($sql);
            if ($categoryId) {
                $stmt->bindParam(":category_id", $categoryId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching products with category: " . $e->getMessage());
            return [];
        }
    }

    public function reduceStock(int $productId, int $qty): bool
    {
        $sql = "UPDATE {$this->table} SET stock = stock - :qty WHERE id = :id AND stock >= :qty";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":qty", $qty, PDO::PARAM_INT);
            $stmt->bindParam(":id", $productId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error reducing product stock: " . $e->getMessage());
            return false;
        }
    }
}
