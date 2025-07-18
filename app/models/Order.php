<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Order extends Model
{
    protected $table = 'orders';

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting orders: " . $e->getMessage());
            return 0;
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
            error_log("Error counting orders by status: " . $e->getMessage());
            return 0;
        }
    }

    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting orders by user ID: " . $e->getMessage());
            return 0;
        }
    }


    public function create(int $userId, string $customerName, string $deliveryLocation, string $branch, float $totalPrice, ?string $description, string $status): int|false
    {
        $sql = "INSERT INTO {$this->table} (user_id, customer_name, delivery_location, branch, total_price, description, status) VALUES (:user_id, :customer_name, :delivery_location, :branch, :total_price, :description, :status)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $stmt->bindParam(":customer_name", $customerName, PDO::PARAM_STR);
            $stmt->bindParam(":delivery_location", $deliveryLocation, PDO::PARAM_STR);
            $stmt->bindParam(":branch", $branch, PDO::PARAM_STR);
            $stmt->bindParam(":total_price", $totalPrice, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating order: " . $e->getMessage());
            return false;
        }
    }

    public function all(): array
    {
        try {
            $sql = "SELECT 
            o.id, o.user_id, o.branch, o.delivery_location,
            o.total_price, o.status, o.description, o.created_at,
            u.name AS user_name
            FROM {$this->table} o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.id DESC";


            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all orders: " . $e->getMessage());
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
            error_log("Error fetching order by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderItemsOnly(int $orderId): array
    {
        try {
            $sql = "
            SELECT
                oi.id as order_item_id,
                oi.qty,
                oi.price as item_price,
                p.id as product_id,
                p.name as product_name,
                p.description as product_description,
                p.image_url
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching order items only: " . $e->getMessage());
            return [];
        }
    }


    public function getByIdWithItems(int $id): ?array
    {
        try {
            $sql = "
            SELECT 
                o.*, 
                u.name as user_name, 
                u.email as user_email,
                oi.id as order_item_id,
                oi.qty,
                oi.price as item_price,
                p.id as product_id,
                p.name as product_name,
                p.image_url as product_image_url,
                p.price as product_price
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE o.id = :order_id
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($rows)) return null;

            // Data order dari baris pertama
            $firstRow = $rows[0];
            $order = [
                'id' => $firstRow['id'],
                'user_id' => $firstRow['user_id'],
                'branch' => $firstRow['branch'],
                'delivery_location' => $firstRow['delivery_location'],
                'description' => $firstRow['description'],
                'status' => $firstRow['status'],
                'total_price' => $firstRow['total_price'],
                'created_at' => $firstRow['created_at'],
                'user' => [
                    'name' => $firstRow['user_name'],
                    'email' => $firstRow['user_email'],
                ],
                'orderItems' => []
            ];

            foreach ($rows as $row) {
                if ($row['order_item_id']) {
                    $order['orderItems'][] = [
                        'id' => $row['order_item_id'],
                        'qty' => $row['qty'],
                        'price' => $row['item_price'],
                        'product' => [
                            'id' => $row['product_id'],
                            'name' => $row['product_name'],
                            'image_url' => $row['product_image_url'],
                            'price' => $row['product_price'],
                        ]
                    ];
                }
            }

            return $order;
        } catch (PDOException $e) {
            error_log("Error in getByIdWithItems: " . $e->getMessage());
            return null;
        }
    }



    public function update(int $id, int $userId, string $deliveryLocation, string $branch, float $totalPrice, ?string $description, string $status): bool
    {
        $sql = "UPDATE {$this->table} SET user_id = :user_id =delivery_location = :delivery_location, branch = :branch, total_price = :total_price, description = :description, status = :status WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $stmt->bindParam(":delivery_location", $deliveryLocation, PDO::PARAM_STR);
            $stmt->bindParam(":branch", $branch, PDO::PARAM_STR);
            $stmt->bindParam(":total_price", $totalPrice, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating order: " . $e->getMessage());
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
            error_log("Error deleting order: " . $e->getMessage());
            return false;
        }
    }

    public function countActiveOrders(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status IN ('pending', 'processing')";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting active orders: " . $e->getMessage());
            return 0;
        }
    }

    // ... di dalam class Order extends Model

    public function getRecentOrdersWithDetails(int $limit = 3, ?int $userId = null): array
    {
        $sql = "
        SELECT
            o.id AS order_id,
            o.user_id,
            o.payment_id,
            o.delivery_location,
            o.branch,
            o.total_price,
            o.description,
            o.status,
            u.name AS user_name,
            u.email AS user_email,
            oi.id AS item_id,
            oi.product_id,
            oi.qty,
            oi.price AS item_price,
            p.name AS product_name,
            p.image_url AS product_image_url,
            p.price AS product_original_price
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
    ";

        $conditions = [];
        $params = [];

        if ($userId !== null) {
            $conditions[] = "o.user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY o.id DESC LIMIT :limit";

        try {
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $orders = [];

            foreach ($rawResults as $row) {
                $orderId = $row['order_id'];
                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = [
                        'id' => $row['order_id'],
                        'user_id' => $row['user_id'],
                        'payment_id' => $row['payment_id'],
                        'delivery_location' => $row['delivery_location'],
                        'branch' => $row['branch'],
                        'total_price' => $row['total_price'],
                        'description' => $row['description'],
                        'status' => $row['status'],
                        'user' => [ // Data user terkait
                            'id' => $row['user_id'], // Tambahkan ID user juga
                            'name' => $row['user_name'],
                            'email' => $row['user_email'],
                        ],
                        'order_items' => []
                    ];
                }

                // Tambahkan item_order hanya jika ada (order_item_id tidak null)
                if ($row['item_id'] !== null) {
                    $orders[$orderId]['order_items'][] = [
                        'id' => $row['item_id'],
                        'order_id' => $row['order_id'],
                        'product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'price' => $row['item_price'],
                        'product' => [ // Data produk terkait
                            'id' => $row['product_id'],
                            'name' => $row['product_name'],
                            'image_url' => $row['product_image_url'] ?? null, // Mungkin tidak semua produk punya image_url
                            'price' => $row['product_original_price'] ?? null,
                        ]
                    ];
                }
            }

            return array_values($orders); // Kembalikan sebagai array terindeks
        } catch (PDOException $e) {
            error_log("Error fetching recent orders with details: " . $e->getMessage());
            return [];
        }
    }

    public function addOrderItem(int $orderId, int $productId, int $qty, float $price): bool
    {
        $sql = "INSERT INTO order_items (order_id, product_id, qty, price) VALUES (:order_id, :product_id, :qty, :price)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->bindParam(":product_id", $productId, PDO::PARAM_INT);
            $stmt->bindParam(":qty", $qty, PDO::PARAM_INT);
            $stmt->bindParam(":price", $price, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding order item: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderItems(int $orderId): array|null
    {
        try {
            // Ambil detail utama dari order
            $orderSql = "
            SELECT
                o.*,
                u.name as user_name
            FROM
                orders o
            JOIN
                users u ON o.user_id = u.id
            WHERE
                o.id = :order_id
        ";
            $stmt = $this->db->prepare($orderSql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return null;
            }

            // Ambil item-item pesanan
            $itemsSql = "
            SELECT
                oi.id as order_item_id,
                oi.qty,
                oi.price as item_price,
                p.id as product_id,
                p.name as product_name,
                p.description as product_description
            FROM
                order_items oi
            JOIN
                products p ON oi.product_id = p.id
            WHERE
                oi.order_id = :order_id
        ";
            $stmtItems = $this->db->prepare($itemsSql);
            $stmtItems->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmtItems->execute();
            $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            // Gabungkan order + items
            $order['order_items'] = $orderItems;

            return $order;
        } catch (PDOException $e) {
            error_log("Error fetching order and items: " . $e->getMessage());
            return null;
        }
    }


    public function updateStatus(int $orderId, string $newStatus): bool
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":status", $newStatus, PDO::PARAM_STR);
            $stmt->bindParam(":id", $orderId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }

    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching orders by user: " . $e->getMessage());
            return [];
        }
    }
}
