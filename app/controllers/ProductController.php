<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Categories;

class ProductController extends Controller
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Categories();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $categoryId = $_GET['category'] ?? null;

        if ($categoryId) {
            $products = $this->productModel->getAllWithCategory((int)$categoryId);
        } else {
            $products = $this->productModel->all();
        }

        $categories = $this->categoryModel->all();

        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);

        $this->view('/dashboard/product/index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
            'message' => $message
        ], '/dashboardLayout');
    }

    public function create()
    {

        $categories = $this->categoryModel->all();
        $this->view('/dashboard/product/create', ['categories' => $categories], '/dashboardLayout');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['category_id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
            $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
            $imagePath = null;

            $errors = [];

            // Validasi input
            if (!$categoryId) $errors[] = "Kategori wajib dipilih.";
            if (!$name) $errors[] = "Nama produk wajib diisi.";
            if ($price < 0) $errors[] = "Harga tidak boleh negatif.";
            if ($stock < 0) $errors[] = "Stok tidak boleh negatif.";

            // Validasi file upload
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image_url'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'];
                if (!in_array($file['type'], $allowedTypes)) {
                    $_SESSION['error'] = "Tipe gambar tidak valid.";
                    header("Location: /dashboard/menu/products");
                    exit;
                }

                if ($file['size'] > 2 * 1024 * 1024) { // 2MB max
                    $_SESSION['error'] = "Ukuran gambar melebihi 2MB.";
                    header("Location: /dashboard/menu/products");
                    exit;
                }

                if (empty($errors)) {
                    $newName = 'product_' . bin2hex(random_bytes(5)) . '.' . $ext;
                    $targetDir = __DIR__ . '/../../public/storage/products/';
                    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                    $destination = $targetDir . $newName;
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $imagePath = 'products/' . $newName;
                    } else {
                        $errors[] = "Gagal mengunggah gambar.";
                    }
                }
            }

            // Kalau error, simpan ke session dan redirect
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: /dashboard/menu/products/create');
                exit;
            }

            // Simpan produk
            $saved = $this->productModel->create([
                'category_id' => $categoryId,
                'name' => $name,
                'image_url' => $imagePath,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
            ]);

            if ($saved) {
                $_SESSION['message'] = "Produk berhasil ditambahkan.";
                header("Location: /dashboard/menu/products");
                exit;
            } else {
                $_SESSION['errors'] = ["Gagal menyimpan produk ke database."];
                header("Location: /dashboard/menu/products/create");
                exit;
            }
        }
    }

    public function edit(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID produk tidak valid.";
            header('Location: /dashboard/products');
            exit;
        }

        $product = $this->productModel->getById((int)$id);
        if (!$product) {
            $_SESSION['message'] = "Produk tidak ditemukan.";
            header('Location: /dashboard/products');
            exit;
        }

        $categories = $this->categoryModel->all();

        $this->view("/dashboard/product/update", [
            'product' => $product,
            'categories' => $categories,
            'errorMessage' => $_SESSION['errorMessage'] ?? ''
        ], '/dashboardLayout');

        unset($_SESSION['errorMessage']);
    }

    public function update(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID produk tidak valid.";
            header('Location: /dashboard/products');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['message'] = "Metode tidak diizinkan.";
            header('Location: /dashboard/products');
            exit;
        }

        $product = $this->productModel->getById((int)$id);
        if (!$product) {
            $_SESSION['message'] = "Produk tidak ditemukan.";
            header('Location: /dashboard/products');
            exit;
        }

        $newCategoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT);
        $newName = trim($_POST['name'] ?? '');
        $newDescription = trim($_POST['description'] ?? null);
        $newPrice = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
        $newStock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);

        $errorMessage = '';

        // Validasi input
        if ($newCategoryId === false || $newCategoryId <= 0) {
            $errorMessage = "Kategori tidak valid.";
        } elseif (empty($newName)) {
            $errorMessage = "Nama produk tidak boleh kosong.";
        } elseif ($newPrice === false || $newPrice < 0) {
            $errorMessage = "Harga harus berupa angka positif.";
        } elseif ($newStock === false || $newStock < 0) {
            $errorMessage = "Stok tidak boleh negatif.";
        } elseif ($this->productModel->nameExists($newName, (int)$id)) {
            $errorMessage = "Nama produk sudah ada.";
        }

        // Handle upload image jika ada file baru
        $imagePath = $product['image_url'];
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image_url'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'];

            if (!in_array($file['type'], $allowedTypes)) {
                $errorMessage = "Tipe gambar tidak valid.";
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errorMessage = "Ukuran gambar melebihi 2MB.";
            } else {
                $newNameImage = 'product_' . bin2hex(random_bytes(5)) . '.' . $ext;
                $targetDir = __DIR__ . '/../../public/storage/products/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                $destination = $targetDir . $newNameImage;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if (!empty($product['image_url'])) {
                        $oldImagePath = __DIR__ . '/../../public/storage/' . $product['image_url'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $imagePath = 'products/' . $newNameImage;
                } else {
                    $errorMessage = "Gagal mengunggah gambar.";
                }
            }
        }

        // Kalau error, redirect balik ke form edit
        if (!empty($errorMessage)) {
            $_SESSION['errorMessage'] = $errorMessage;
            header("Location: /products/edit/$id");
            exit;
        }

        // Update ke DB
        $result = $this->productModel->updateWithImage((int)$id, [
            'category_id' => $newCategoryId,
            'name' => $newName,
            'description' => $newDescription,
            'price' => $newPrice,
            'stock' => $newStock,
            'image_url' => $imagePath,
        ]);

        if ($result) {
            $_SESSION['message'] = "Produk berhasil diperbarui.";
        } else {
            $_SESSION['message'] = "Gagal memperbarui produk.";
        }

        header('Location: /dashboard/menu/products');
        exit;
    }

    public function show(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = 'ID produk tidak valid.';
            header('Location: /dashboard/menu/products');
            exit;
        }

        $product = $this->productModel->getById((int) $id);

        if (!$product) {
            $_SESSION['message'] = 'Produk tidak ditemukan.';
            header('Location: /dashboard/menu/products');
            exit;
        }

        // Tampilkan halaman detail produk
        $this->view("/dashboard/product/show", [
            'product' => $product,
            'errorMessage' => $_SESSION['errorMessage'] ?? ''
        ], '/dashboardLayout');
    }


    public function delete(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID produk tidak valid.";
            header('Location: /dashboard/menu/products');
            exit;
        }

        // Ambil detail produk
        $product = $this->productModel->getById((int)$id);
        if (!$product) {
            $_SESSION['message'] = "Produk tidak ditemukan.";
            header('Location: /dashboard/menu/products');
            exit;
        }

        // Hapus gambar dari storage (jika ada)
        if (!empty($product['image_url'])) {
            $imagePath = __DIR__ . '/../../public/storage/' . $product['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Hapus produk dari database
        $result = $this->productModel->delete((int)$id);

        if ($result) {
            $_SESSION['message'] = "Produk berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Gagal menghapus produk.";
        }

        header('Location: /dashboard/menu/products');
        exit;
    }
}
