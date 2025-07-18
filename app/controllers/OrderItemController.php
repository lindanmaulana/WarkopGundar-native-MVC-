<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;

class OrderItemController extends Controller
{
    protected $orderItemModel;
    protected $orderModel;
    protected $productModel;

    public function __construct()
    {
        $this->orderItemModel = new OrderItem();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Contoh otorisasi: Mungkin hanya admin yang bisa mengakses
        // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        //     header('Location: /login');
        //     exit;
        // }
    }

    public function index()
    {
        $orderItems = $this->orderItemModel->all();

        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);

        $this->view('/dashboard/orderitems/index', [
            'orderItems' => $orderItems,
            'message' => $message
        ]);
    }

    public function create()
    {
        $errorMessage = '';
        $orders = $this->orderModel->all(); // Untuk memilih order
        $products = $this->productModel->all(); // Untuk memilih produk

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = filter_var($_POST['order_id'] ?? '', FILTER_VALIDATE_INT);
            $productId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT);
            $qty = filter_var($_POST['qty'] ?? '', FILTER_VALIDATE_INT);
            // Harga item biasanya diambil dari produk, bukan input langsung
            $itemPrice = 0; 

            // Validasi input
            if ($orderId === false || $orderId <= 0 || !$this->orderModel->getById($orderId)) {
                $errorMessage = "Order ID tidak valid.";
            } elseif ($productId === false || $productId <= 0 || !$this->productModel->getById($productId)) {
                $errorMessage = "Produk ID tidak valid.";
            } elseif ($qty === false || $qty <= 0) {
                $errorMessage = "Kuantitas harus berupa angka positif.";
            } else {
                $product = $this->productModel->getById($productId);
                if ($product) {
                    $itemPrice = $product['price']; // Ambil harga dari produk
                } else {
                    $errorMessage = "Produk tidak ditemukan.";
                }

                if (empty($errorMessage)) {
                    if ($this->orderItemModel->create($orderId, $productId, $qty, $itemPrice)) {
                        // Setelah menambahkan item, Anda mungkin perlu memperbarui total_price di order terkait
                        // Ini akan melibatkan pengambilan semua item untuk orderId tersebut, menjumlahkan harganya,
                        // dan memanggil orderModel->updateTotal($orderId, $newTotalPrice)
                        $_SESSION['message'] = "Item order berhasil ditambahkan!";
                        header('Location: /dashboard/orderitems');
                        exit;
                    } else {
                        $errorMessage = "Gagal menambahkan item order. Silakan coba lagi.";
                    }
                }
            }
        }
        
        $this->view('/dashboard/orderitems/create', [
            'errorMessage' => $errorMessage,
            'orders' => $orders,
            'products' => $products
        ]);
    }

    public function edit(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID item order tidak valid.";
            header('Location: /dashboard/orderitems');
            exit;
        }

        $orderItem = $this->orderItemModel->getById((int)$id);
        if (!$orderItem) {
            $_SESSION['message'] = "Item order tidak ditemukan.";
            header('Location: /dashboard/orderitems');
            exit;
        }

        $orders = $this->orderModel->all();
        $products = $this->productModel->all();
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newOrderId = filter_var($_POST['order_id'] ?? '', FILTER_VALIDATE_INT);
            $newProductId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT);
            $newQty = filter_var($_POST['qty'] ?? '', FILTER_VALIDATE_INT);
            $newItemPrice = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT); // Atau ambil dari produk

            // Validasi input
            if ($newOrderId === false || $newOrderId <= 0 || !$this->orderModel->getById($newOrderId)) {
                $errorMessage = "Order ID tidak valid.";
            } elseif ($newProductId === false || $newProductId <= 0 || !$this->productModel->getById($newProductId)) {
                $errorMessage = "Produk ID tidak valid.";
            } elseif ($newQty === false || $newQty <= 0) {
                $errorMessage = "Kuantitas harus berupa angka positif.";
            } elseif ($newItemPrice === false || $newItemPrice < 0) {
                $errorMessage = "Harga harus berupa angka positif.";
            } else {
                if ($this->orderItemModel->update((int)$id, $newOrderId, $newProductId, $newQty, $newItemPrice)) {
                    // Setelah update item, Anda mungkin perlu memperbarui total_price di order terkait
                    $_SESSION['message'] = "Item order berhasil diperbarui!";
                    header('Location: /dashboard/orderitems');
                    exit;
                } else {
                    $errorMessage = "Gagal memperbarui item order. Silakan coba lagi.";
                }
            }
            // Perbarui data yang ditampilkan di form jika ada error validasi
            $orderItem['order_id'] = $newOrderId;
            $orderItem['product_id'] = $newProductId;
            $orderItem['qty'] = $newQty;
            $orderItem['item_price'] = $newItemPrice;
        }
        
        $this->view("/dashboard/orderitems/edit", [
            'orderItem' => $orderItem,
            'orders' => $orders,
            'products' => $products,
            'errorMessage' => $errorMessage,
        ]);
    }

    public function delete(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id || !is_numeric($id) || $id < 1) {
            $_SESSION['message'] = "ID item order tidak valid.";
            header('Location: /dashboard/orderitems');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Dapatkan order_id sebelum dihapus untuk update total_price jika diperlukan
            $orderItem = $this->orderItemModel->getById((int)$id);
            if ($orderItem) {
                $orderId = $orderItem['order_id'];
            }
            
            if ($this->orderItemModel->delete((int)$id)) {
                // Jika orderId tersedia, hitung ulang dan update total_price order
                // $this->orderModel->recalculateTotalPrice($orderId); // Method ini perlu dibuat di Order Model
                $_SESSION['message'] = "Item order berhasil dihapus!";
            } else {
                $_SESSION['message'] = "Gagal menghapus item order.";
            }
        } else {
            $_SESSION['message'] = "Metode request tidak diizinkan untuk operasi ini.";
        }
        
        header('Location: /dashboard/orderitems');
        exit;
    }
}