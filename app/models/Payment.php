<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Payment extends Model
{
    protected $table = 'payments';

    // Hitung total pembayaran
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting payments: " . $e->getMessage());
            return 0;
        }
    }

    public function find(int $id): array|false
    {
        return $this->getById($id);
    }


    // Ambil semua metode pembayaran aktif
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY id ASC";

        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching active payments: " . $e->getMessage());
            return [];
        }
    }

    // Ambil semua metode pembayaran
    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all payments: " . $e->getMessage());
            return [];
        }
    }

    // Ambil metode pembayaran berdasarkan ID
    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching payment by ID: " . $e->getMessage());
            return false;
        }
    }

    // Tambah metode pembayaran baru
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO {$this->table} (name, qr_code_url, is_active)
                VALUES (:name, :qr_code_url, :is_active)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":qr_code_url", $data['qr_code_url']);
            $stmt->bindValue(":is_active", $data['is_active'], PDO::PARAM_BOOL);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error creating payment: " . $e->getMessage());
            return false;
        }
    }

    // Update metode pembayaran
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} 
                SET name = :name, qr_code_url = :qr_code_url, is_active = :is_active 
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":qr_code_url", $data['qr_code_url']);
            $stmt->bindValue(":is_active", $data['is_active'], PDO::PARAM_BOOL);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating payment: " . $e->getMessage());
            return false;
        }
    }

    // Hapus metode pembayaran
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting payment: " . $e->getMessage());
            return false;
        }
    }
}
