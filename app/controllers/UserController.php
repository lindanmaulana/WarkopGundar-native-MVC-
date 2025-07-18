<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    protected $UserModel;

    public function __construct()
    {
        $this->UserModel = new User();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $users = $this->UserModel->all();

        $success = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);

        $this->view('/dashboard/users/index', [
            'users' => $users,
            'success' => $success
        ], '/dashboardLayout');
    }

    public function create()
    {
        $errorMessage = '';
        $successMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');
            $role = trim($_POST['role'] ?? 'user');

            // Validasi input
            if (empty($name)) {
                $errorMessage = "Nama tidak boleh kosong.";
            } elseif (empty($email)) {
                $errorMessage = "Email tidak boleh kosong.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = "Format email tidak valid.";
            } elseif (empty($password)) {
                $errorMessage = "Password tidak boleh kosong.";
            } elseif (strlen($password) < 6) {
                $errorMessage = "Password minimal 6 karakter.";
            } elseif ($password !== $confirmPassword) {
                $errorMessage = "Konfirmasi password tidak cocok.";
            } elseif ($this->UserModel->emailExists($email)) {
                $errorMessage = "Email sudah digunakan.";
            } else {
                if ($this->UserModel->register($name, $email, $password, $role)) {
                    $_SESSION['success'] = "Pengguna '{$name}' berhasil ditambahkan!";
                    header('Location: /dashboard/users');
                    exit;
                } else {
                    $errorMessage = "Gagal menambahkan pengguna. Silakan coba lagi.";
                }
            }
        }

        $this->view('/dashboard/users/create', [
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
        ], '/dashboardLayout');
    }

    public function edit(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['success'] = "ID pengguna tidak valid.";
            header('Location: /dashboard/users');
            exit;
        }

        $user = $this->UserModel->getById((int)$id);

        if (!$user) {
            $_SESSION['success'] = "Pengguna tidak ditemukan.";
            header('Location: /dashboard/users');
            exit;
        }

        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newName = trim($_POST['name'] ?? '');
            $newEmail = trim($_POST['email'] ?? '');
            $newRole = trim($_POST['role'] ?? 'user');
            $newStatus = trim($_POST['status'] ?? 'active');

            // Validasi input
            if (empty($newName)) {
                $errorMessage = "Nama tidak boleh kosong.";
            } elseif (empty($newEmail)) {
                $errorMessage = "Email tidak boleh kosong.";
            } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = "Format email tidak valid.";
            } elseif ($this->UserModel->emailExists($newEmail, (int)$id)) {
                $errorMessage = "Email sudah digunakan.";
            } else {
                if ($this->UserModel->update((int)$id, $newName, $newEmail, $newRole, $newStatus)) {
                    $_SESSION['success'] = "Pengguna '{$newName}' berhasil diperbarui!";
                    header('Location: /dashboard/users');
                    exit;
                } else {
                    $errorMessage = "Gagal memperbarui pengguna. Silakan coba lagi.";
                }
            }
            
            // Update data untuk ditampilkan di form jika ada error
            $user['name'] = $newName;
            $user['email'] = $newEmail;
            $user['role'] = $newRole;
            $user['status'] = $newStatus;
        }

        $this->view("/dashboard/users/update", [
            'user' => $user,
            'errorMessage' => $errorMessage,
        ], '/dashboardLayout');
    }

    public function delete(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['success'] = "ID pengguna tidak valid.";
            header('Location: /dashboard/users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->UserModel->getById((int)$id);
            
            if (!$user) {
                $_SESSION['success'] = "Pengguna tidak ditemukan.";
            } elseif ($this->UserModel->delete((int)$id)) {
                $_SESSION['success'] = "Pengguna '{$user['name']}' berhasil dihapus!";
            } else {
                $_SESSION['success'] = "Gagal menghapus pengguna. Silakan coba lagi.";
            }
        } else {
            $_SESSION['success'] = "Metode request tidak diizinkan untuk operasi ini.";
        }

        header('Location: /dashboard/users');
        exit;
    }

    public function changePassword(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['success'] = "ID pengguna tidak valid.";
            header('Location: /dashboard/users');
            exit;
        }

        $user = $this->UserModel->getById((int)$id);

        if (!$user) {
            $_SESSION['success'] = "Pengguna tidak ditemukan.";
            header('Location: /dashboard/users');
            exit;
        }

        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            if (empty($newPassword)) {
                $errorMessage = "Password baru tidak boleh kosong.";
            } elseif (strlen($newPassword) < 6) {
                $errorMessage = "Password minimal 6 karakter.";
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = "Konfirmasi password tidak cocok.";
            } else {
                if ($this->UserModel->updatePassword((int)$id, $newPassword)) {
                    $_SESSION['success'] = "Password pengguna '{$user['name']}' berhasil diubah!";
                    header('Location: /dashboard/users');
                    exit;
                } else {
                    $errorMessage = "Gagal mengubah password. Silakan coba lagi.";
                }
            }
        }

        $this->view("/dashboard/users/change-password", [
            'user' => $user,
            'errorMessage' => $errorMessage,
        ], '/dashboardLayout');
    }

    // API endpoint untuk AJAX (jika diperlukan)
    public function api()
    {
        header('Content-Type: application/json');
        
        $limit = (int)($_GET['limit'] ?? 10);
        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        
        $offset = ($page - 1) * $limit;
        
        $users = $this->UserModel->getPaginated($limit, $offset, $search);
        $totalCount = $this->UserModel->getTotalCount($search);
        $totalPages = ceil($totalCount / $limit);
        
        echo json_encode([
            'users' => $users,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit
        ]);
        exit;
    }
}