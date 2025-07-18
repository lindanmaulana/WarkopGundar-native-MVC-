<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\PaymentProofs;
use App\Models\Product;
use App\Models\User;

class OrderController extends Controller
{
    protected $orderModel;
    protected $productModel;
    protected $userModel;
    protected $paymentProofModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User(); // Sesuaikan jika Anda punya model User yang berbeda
        $this->paymentProofModel = new PaymentProofs();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Contoh sederhana untuk otorisasi: hanya admin yang bisa mengakses ini
        // Anda perlu mengganti ini dengan logika otorisasi yang sebenarnya
        // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        //     header('Location: /login'); // Arahkan ke halaman login
        //     exit;
        // }
    }

    public function index()
    {
        $orders = $this->orderModel->all();

        // Untuk setiap order, ambil detail produk yang terkait
        foreach ($orders as &$order) {
            $order['items'] = $this->orderModel->getOrderItems($order['id']);
        }
        unset($order); // Putuskan referensi terakhir

        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);

        $this->view('/dashboard/orders/index', [
            'orders' => $orders,
            'message' => $message
        ], '/dashboardLayout');
    }

    public function create()
    {
        $errorMessage = '';
        $products = $this->productModel->all(); // Ambil daftar produk yang tersedia
        $users = $this->userModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? 1; // Contoh: ambil dari sesi, atau default 1
            $customerName = trim($_POST['customer_name'] ?? '');
            $deliveryLocation = trim($_POST['delivery_location'] ?? '');
            $branch = trim($_POST['branch'] ?? '');
            $description = trim($_POST['description'] ?? null);
            $productIds = $_POST['product_ids'] ?? []; // Array of product IDs
            $quantities = $_POST['quantities'] ?? []; // Array of quantities for each product

            // Validasi Input
            if (empty($customerName) || empty($deliveryLocation) || empty($branch)) {
                $errorMessage = "Nama pelanggan, lokasi pengiriman, dan cabang tidak boleh kosong.";
            } elseif (empty($productIds)) {
                $errorMessage = "Setidaknya satu produk harus dipilih.";
            } else {
                $totalPrice = 0;
                $orderItemsData = [];
                $productStatusesToUpdate = [];

                // Ambil detail produk untuk menghitung total harga dan validasi
                foreach ($productIds as $index => $productId) {
                    $qty = (int)($quantities[$index] ?? 0);
                    $product = $this->productModel->getById((int)$productId);

                    if (!$product || $qty <= 0) {
                        $errorMessage = "Produk tidak valid atau kuantitas kurang dari 1.";
                        break;
                    }
                    // Pastikan produk tersedia
                    if ($product['status'] !== 'available') {
                        $errorMessage = "Produk '{$product['name']}' tidak tersedia untuk disewa.";
                        break;
                    }

                    $itemPrice = $product['price']; // Ambil harga dari produk
                    $totalPrice += ($qty * $itemPrice);
                    $orderItemsData[] = [
                        'product_id' => $product['id'],
                        'qty' => $qty,
                        'price' => $itemPrice
                    ];
                    $productStatusesToUpdate[] = $product['id'];
                }

                if (empty($errorMessage)) {
                    // Status awal order
                    $initialOrderStatus = 'pending'; // Sesuaikan dengan enum di DB

                    // Buat order utama
                    $orderId = $this->orderModel->create($userId, $customerName, $deliveryLocation, $branch, $totalPrice, $description, $initialOrderStatus);

                    if ($orderId) {
                        $allOrderItemsAdded = true;
                        foreach ($orderItemsData as $item) {
                            if (!$this->orderModel->addOrderItem($orderId, $item['product_id'], $item['qty'], $item['price'])) {
                                $allOrderItemsAdded = false;
                                // Anda mungkin ingin menghapus order yang sudah dibuat jika item gagal
                                // Atau setidaknya log error dan berikan pesan yang jelas
                                error_log("Failed to add order item for order ID: $orderId, Product ID: {$item['product_id']}");
                                $errorMessage = "Gagal menambahkan beberapa produk ke order. Order telah dibuat, tetapi mungkin tidak lengkap. Silakan cek manual.";
                                break;
                            } else {
                                // Update status produk menjadi 'rented' atau 'in use'
                                $this->productModel->updateProductStatus($item['product_id'], 'rented'); // Sesuaikan dengan enum di DB products
                            }
                        }

                        if ($allOrderItemsAdded) {
                            $_SESSION['message'] = "Order berhasil ditambahkan!";
                            header('Location: /dashboard/orders');
                            exit;
                        }
                    } else {
                        $errorMessage = "Gagal membuat order. Silakan coba lagi.";
                    }
                }
            }
        }

        $this->view('/dashboard/orders/create', [
            'errorMessage' => $errorMessage,
            'products' => $products, // Kirim data produk ke view untuk dropdown
            'users' => $users // Kirim data pengguna jika diperlukan di form
        ]);
    }

    public function edit(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['message'] = "ID order tidak valid.";
            header('Location: /dashboard/orders');
            exit;
        }

        $order = $this->orderModel->getByIdWithItems((int)$id);
        if (!$order) {
            $_SESSION['message'] = "Order tidak ditemukan.";
            header('Location: /dashboard/orders');
            exit;
        }

        $products = $this->productModel->all();
        $users = $this->userModel->getAll();

        $this->view("/dashboard/orders/update", [
            'order' => $order,
            'products' => $products,
            'users' => $users,
            'errorMessage' => '',
        ], '/dashboardLayout');
    }


    public function update(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id)) {
            $_SESSION['message'] = "ID order tidak valid.";
            header('Location: /dashboard/orders');
            exit;
        }

        $order = $this->orderModel->getById((int)$id);
        if (!$order) {
            $_SESSION['message'] = "Order tidak ditemukan.";
            header('Location: /dashboard/orders');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['message'] = "Metode tidak diizinkan.";
            header('Location: /dashboard/orders');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? 1; // Atau ambil dari sesi login
        $deliveryLocation = trim($_POST['delivery_location'] ?? '');
        $branch = trim($_POST['branch'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = trim($_POST['status'] ?? $order['status']);

        $currentTotalPrice = $order['total_price'];

        $updated = $this->orderModel->update(
            (int)$id,
            $userId,
            $deliveryLocation,
            $branch,
            $currentTotalPrice,
            $description,
            $status
        );

        if ($updated) {
            $_SESSION['message'] = "Order #$id berhasil diperbarui.";
            header("Location: /dashboard/orders");
            exit;
        } else {
            $_SESSION['message'] = "Gagal memperbarui order.";
            header("Location: /dashboard/orders/update/$id");
            exit;
        }
    }



    public function show(array $params)
    {
        $orderId = $params[0] ?? null;

        if (!$orderId || !is_numeric($orderId)) {
            $_SESSION['message'] = "ID pesanan tidak valid.";
            header('Location: /dashboard/orders');
            exit;
        }

        // Ambil detail order
        $order = $this->orderModel->getByIdWithItems((int)$orderId);
        if (!$order) {
            $_SESSION['message'] = "Pesanan tidak ditemukan.";
            header('Location: /dashboard/orders');
            exit;
        }

        // Ambil bukti pembayaran
        $paymentProof = $this->paymentProofModel->getByOrderId((int)$orderId);

        $this->view('/dashboard/orders/detail', [
            'order' => $order,
            'paymentProof' => $paymentProof
        ], '/dashboardLayout');
    }


    public function delete(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID order tidak valid.";
            header('Location: /dashboard/orders');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sebelum menghapus order, pastikan untuk menangani order_items terkait
            // Atau Anda bisa menggunakan CASCADE DELETE di database
            // Jika tidak ada CASCADE DELETE:
            // Hapus order_items terlebih dahulu
            // $this->orderModel->deleteOrderItemsByOrderId((int)$id); // Perlu method baru di model Order

            if ($this->orderModel->delete((int)$id)) {
                $_SESSION['message'] = "Order berhasil dihapus!";
            } else {
                $_SESSION['message'] = "Gagal menghapus order. Mungkin ada masalah database atau relasi.";
            }
        } else {
            $_SESSION['message'] = "Metode request tidak diizinkan untuk operasi ini.";
        }

        header('Location: /dashboard/orders');
        exit;
    }
}
