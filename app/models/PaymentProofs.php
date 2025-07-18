<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class PaymentProofs extends Model
{
    protected $table = 'payment_proofs';

    // Menyimpan bukti pembayaran baru
    public function create(int $orderId, ?string $imageUrl, bool $verified = false): int|false
    {
        $sql = "INSERT INTO {$this->table} (order_id, image_url, verified) VALUES (:order_id, :image_url, :verified)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);

            // Tangani jika imageUrl null
            if ($imageUrl === null) {
                $stmt->bindValue(":image_url", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(":image_url", $imageUrl, PDO::PARAM_STR);
            }

            $stmt->bindValue(":verified", $verified, PDO::PARAM_BOOL);

            if ($stmt->execute()) {
                return (int) $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error creating payment proof: " . $e->getMessage());
            return false;
        }
    }


    // Mengambil data bukti pembayaran berdasarkan order_id
    public function getByOrderId(int $orderId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = :order_id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching payment proof by order_id: " . $e->getMessage());
            return false;
        }
    }

    // Memperbarui status verifikasi bukti pembayaran
    public function updateVerification(int $orderId, bool $verified): bool
    {
        $sql = "UPDATE {$this->table} SET verified = :verified WHERE order_id = :order_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":verified", $verified, PDO::PARAM_BOOL);
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating verification: " . $e->getMessage());
            return false;
        }
    }

    // Hapus bukti pembayaran berdasarkan ID
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting payment proof: " . $e->getMessage());
            return false;
        }
    }
}
