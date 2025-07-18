<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Categories;


class CategoriesController extends Controller
{
    protected $CategoryModel;

    public function __construct()
    {
        $this->CategoryModel = new Categories(); // Menggunakan nama model singular
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $categories = $this->CategoryModel->all(); // Menggunakan method all()

        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);

        $this->view('/dashboard/categories/index', [
            'categories' => $categories,
            'message' => $message
        ], '/dashboardLayout');
    }

    public function create()
    {
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = filter_var($_POST['description'] ?? '', FILTER_VALIDATE_FLOAT);

            if (empty($name)) {
                $errorMessage = "Nama kategori tidak boleh kosong.";
            } elseif ($this->CategoryModel->nameExists($name)) {
                $errorMessage = "Nama kategori sudah ada.";
            } else {
                if ($this->CategoryModel->create($name, $description)) {
                    $_SESSION['message'] = "Kategori '{$name}' berhasil ditambahkan!";
                    header('Location: /dashboard/categories');
                    exit;
                } else {
                    $errorMessage = "Gagal menambahkan kategori. Silakan coba lagi.";
                }
            }
        }

        $this->view('/dashboard/categories/create', [
            'errorMessage' => $errorMessage,
        ], '/dashboardLayout');
    }

    public function edit(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID kategori tidak valid.";
            header('Location: /dashboard/categories');
            exit;
        }

        $category = $this->CategoryModel->getById((int)$id);

        if (!$category) {
            $_SESSION['message'] = "Kategori tidak ditemukan.";
            header('Location: /dashboard/categories');
            exit;
        }

        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newName = trim($_POST['name'] ?? '');
            $newDescription = trim($_POST['description'] ?? null); // Menambahkan deskripsi

            if (empty($newName)) {
                $errorMessage = "Nama kategori tidak boleh kosong.";
            } elseif ($this->CategoryModel->nameExists($newName, (int)$id)) {
                $errorMessage = "Nama kategori sudah ada.";
            } else {
                // Panggil method update di model yang sudah disesuaikan
                // Perlu ada method update di CategoryModel yang hanya menerima name dan description
                if ($this->CategoryModel->update((int)$id, $newName, $newDescription)) {
                    $_SESSION['message'] = "Kategori '{$newName}' berhasil diperbarui!";
                    header('Location: /dashboard/categories');
                    exit;
                } else {
                    $errorMessage = "Gagal memperbarui kategori. Silakan coba lagi.";
                }
            }
            // Perbarui data yang ditampilkan di form jika ada error validasi
            $category['name'] = $newName;
            $category['description'] = $newDescription;
        }

        return $this->view("/dashboard/categories/update", [ // Menggunakan return
            'category' => $category,
            'errorMessage' => $errorMessage,
        ], '/dashboardLayout');
    }

    public function delete(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID kategori tidak valid.";
            header('Location: /dashboard/categories');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->CategoryModel->delete((int)$id)) {
                $_SESSION['message'] = "Kategori berhasil dihapus!";
            } else {
                $_SESSION['message'] = "Gagal menghapus kategori. Mungkin kategori ini masih terhubung dengan konsol.";
            }
        } else {
            $_SESSION['message'] = "Metode request tidak diizinkan untuk operasi ini.";
        }

        header('Location: /dashboard/categories');
        exit;
    }
}
