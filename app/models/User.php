<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class User extends Model
{
    protected $table = 'users';

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateName(int $id, string $name): bool
    {
        $sql = "UPDATE {$this->table} SET name = :name, updated_at = NOW() WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating user name: " . $e->getMessage());
            return false;
        }
    }


    public function register(string $name, string $email, string $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$this->table} (name, email, password) VALUES (:name, :email, :password)";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $err) {
            error_log("Error during user registration: " . $err->getMessage());
            return false;
        }
    }

    public function login(string $email, string $password)
    {
        $sql = "SELECT id, name, email, password, role FROM {$this->table} WHERE email = :email LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            } else {
                return false;
            }
        } catch (PDOException $err) {
            error_log("Error during user registration: " . $err->getMessage());
            return false;
        }
    }


    public function logout()
    {
        if (session_start() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = array();

        session_destroy();

        header('Location: /auth/login');
        exit;
    }

    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all users: " . $e->getMessage());
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
            error_log("Error fetching user by ID: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, string $name, string $email, string $role, string $status): bool
    {
        $sql = "UPDATE {$this->table} SET name = :name, email = :email, role = :role, status = :status WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":role", $role, PDO::PARAM_STR);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword(int $id, string $password): bool
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating user password: " . $e->getMessage());
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
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            if ($excludeId !== null) {
                $stmt->bindParam(":exclude_id", $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error checking email existence: " . $e->getMessage());
            return true;
        }
    }

    public function getPaginated(int $limit = 10, int $offset = 0, string $search = ''): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE name LIKE :search OR email LIKE :search";
            $params[':search'] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching paginated users: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalCount(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE name LIKE :search OR email LIKE :search";
            $params[':search'] = "%{$search}%";
        }

        try {
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total user count: " . $e->getMessage());
            return 0;
        }
    }
}
