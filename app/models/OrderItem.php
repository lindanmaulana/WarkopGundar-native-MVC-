<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class OrderItem extends Model
{
    protected $table = 'order_items';

    public function create(int $orderId, int $productId, int $qty, float $price): int|false
    {
        $sql = "INSERT INTO {$this->table} (order_id, product_id, qty, price) VALUES (:order_id, :product_id, :qty, :price)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->bindParam(":product_id", $productId, PDO::PARAM_INT);
            $stmt->bindParam(":qty", $qty, PDO::PARAM_INT);
            $stmt->bindParam(":price", $price, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating order item: " . $e->getMessage());
            return false;
        }
    }

    public function all(): array
    {
        try {
            // Mengambil semua order items dengan detail produk dan order
            $stmt = $this->db->query("
                SELECT 
                    oi.id AS order_item_id, 
                    oi.order_id, 
                    oi.product_id, 
                    oi.qty, 
                    oi.price AS item_price,
                    p.name AS product_name,
                    o.customer_name AS order_customer_name
                FROM 
                    {$this->table} oi
                JOIN 
                    products p ON oi.product_id = p.id
                JOIN
                    orders o ON oi.order_id = o.id
                ORDER BY oi.id DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all order items: " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id)
    {
        $sql = "
            SELECT 
                oi.id AS order_item_id, 
                oi.order_id, 
                oi.product_id, 
                oi.qty, 
                oi.price AS item_price,
                p.name AS product_name,
                o.customer_name AS order_customer_name
            FROM 
                {$this->table} oi
            JOIN 
                products p ON oi.product_id = p.id
            JOIN
                orders o ON oi.order_id = o.id
            WHERE oi.id = :id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching order item by ID: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, int $orderId, int $productId, int $qty, float $price): bool
    {
        $sql = "UPDATE {$this->table} SET order_id = :order_id, product_id = :product_id, qty = :qty, price = :price WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->bindParam(":product_id", $productId, PDO::PARAM_INT);
            $stmt->bindParam(":qty", $qty, PDO::PARAM_INT);
            $stmt->bindParam(":price", $price, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating order item: " . $e->getMessage());
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
            error_log("Error deleting order item: " . $e->getMessage());
            return false;
        }
    }

    public function deleteByOrderId(int $orderId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE order_id = :order_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting order items by order ID: " . $e->getMessage());
            return false;
        }
    }
}