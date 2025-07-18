<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Categories;

class DashboardController extends Controller
{
    protected $CategoryModel;
    protected $ProductModel;
    protected $OrderModel;

    public function __construct()
    {
        $this->CategoryModel = new Categories();
        $this->ProductModel = new Product();
        $this->OrderModel = new Order();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'];
        $userName = $_SESSION['name'];

        // Inisialisasi Model
        $ProductModel = new \App\Models\Product();
        $PaymentModel = new \App\Models\Payment();
        $OrderModel   = new \App\Models\Order();

        // Ambil data
        $totalProducts = $ProductModel->count(); // Pastikan kamu punya method count()
        $totalOrders = $OrderModel->count();
        $totalPayments = $PaymentModel->count(); // Pastikan kamu punya method count()
        $totalOrderPending = $OrderModel->countByStatus('pending');
        $totalOrderByCustomer = 0;
        $latestOrdersData = [];

        if ($userRole === 'Customer') {
            $totalOrderByCustomer = $OrderModel->countByUserId($userId);
            $latestOrdersData = $OrderModel->getRecentOrdersWithDetails(3, $userId);
        }

        if ($userRole === 'Admin') {
            $latestOrdersData = $OrderModel->getRecentOrdersWithDetails(3);
        }

        $data = [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalPayments' => $totalPayments,
            'totalOrderPending' => $totalOrderPending,
            'totalOrderByCustomer' => $totalOrderByCustomer,
            'latestOrdersData' => $latestOrdersData,
            'user' => [
                'name' => $userName,
                'role' => $userRole
            ]
        ];

        return $this->view('/dashboard/index', $data, '/dashboardLayout');
    }

    public function markRentalAsCompleted(array $params)
    {
        $orderId = $params[0] ?? null;

        if (!$orderId || !is_numeric($orderId) || $orderId < 1) {
            $_SESSION['message'] = "ID rental tidak valid.";
            return $this->view("/auth/login", []);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateOrderSuccess = $this->OrderModel->updateStatus((int)$orderId, 'done');

            if ($updateOrderSuccess) {
                $orderItems = $this->OrderModel->getOrderItems((int)$orderId);
                foreach ($orderItems as $item) {
                    $this->ProductModel->updateProductStatus((int)$item['product_id'], 'available');
                }
                $_SESSION['message'] = "Rental berhasil diselesaikan.";
            } else {
                $_SESSION['message'] = "Gagal memperbarui status rental.";
            }
        } else {
            $_SESSION['message'] = "Metode request tidak diizinkan.";
        }
        return $this->view("/auth/login", []);
    }

    public function showSetting()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }

        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'],
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email']
        ];

        return $this->view('/dashboard/setting/index', ['user' => $user], '/dashboardLayout');
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /dashboard/settings");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $userId = $_SESSION['user_id'];

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Nama tidak boleh kosong.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /dashboard/settings");
            exit;
        }

        $UserModel = new \App\Models\User();
        $UserModel->updateName($userId, $name);
        $_SESSION['name'] = $name;
        $_SESSION['message'] = "Profil berhasil diperbarui.";
        header("Location: /dashboard/setting");
        exit;
    }
}
