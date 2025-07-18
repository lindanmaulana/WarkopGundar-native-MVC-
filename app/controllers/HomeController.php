<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Enums\OrderStatus;
use App\Core\Auth;
use App\Core\Database;
use App\Models\PaymentProofs;
use PDO;
use Exception;

class HomeController extends Controller
{
  protected $ProductModel;

  public function __construct()
  {
    $this->ProductModel = new Product();
  }

  public function index()
  {
    $allProducts = $this->ProductModel->all();

    $productsLatest = array_slice($allProducts, 0, 3);

    if (count($allProducts) <= 3) {
      $productsForYou = $allProducts;
    } else {
      $productsForYou = array_slice($allProducts, 3, 6);
    }

    $data = [
      'productsLatest' => $productsLatest,
      'productsForYou' => $productsForYou
    ];

    return $this->view("/home/index", $data, "/homeLayout");
  }

  public function showMenu()
  {
    // Ambil semua produk yang tersedia (stock > 0)
    $products = $this->ProductModel->getAvailableProducts();

    // Ambil produk kategori makanan
    $productsFood = $this->ProductModel->getProductsByCategory('makanan');

    // Ambil produk kategori minuman
    $productsCoffe = $this->ProductModel->getProductsByCategory('minuman');

    // Kirim ke view
    $data = [
      'products' => $products,
      'productsFood' => $productsFood,
      'productsCoffe' => $productsCoffe
    ];

    return $this->view("/home/menu", $data, "/homeLayout");
  }

  public function showCart()
  {
    return $this->view('/home/cart', [], '/homeLayout');
  }

  public function showCheckout()
  {
    $paymentModel = new Payment();
    $paymentsMethod = $paymentModel->getActive();

    return $this->view('/home/checkout', ['paymentsMethod' => $paymentsMethod], '/homeLayout');
  }

  public function showProfile()
  {
    $user = $_SESSION['user'] ?? null;

    return $this->view('home/profile', ['user' => $user]);
  }

  public function showOrder()
  {
    $user = [
      'id' => $_SESSION['user_id'] ?? null,
      'email' => $_SESSION['email'] ?? null,
      'name' => $_SESSION['name'] ?? null,
      'role' => $_SESSION['role'] ?? null,
    ];

    if (!$user) {
      header('Location: /login');
      exit;
    }

    $orderModel = new Order();
    $orders = $orderModel->getByUserId($user['id']);

    return $this->view('/home/order', ['orders' => $orders], '/homeLayout');
  }

  public function showDetailOrder($params)
  {
    $orderId = $params[0];
    $orderModel = new Order();
    $order = $orderModel->getOrderItems($orderId);

    if (!$order) {
      // handle error atau redirect jika order tidak ditemukan
    }

    $paymentProofModel = new PaymentProofs();
    $paymentProofs = $paymentProofModel->getByOrderId($orderId);

    return $this->view('/home/orderDetail', [
      'order' => $order,
      'paymentProofs' => $paymentProofs
    ], '/homeLayout');
  }


  public function showPayment($params)
  {
    $orderId = $params[0];

    $orderModel = new Order();
    $order = $orderModel->getById($orderId);

    if (!$order) {
      http_response_code(404);
      echo "Order tidak ditemukan";
      exit;
    }

    $paymentModel = new Payment();
    $payment = $paymentModel->getById($order['payment_id']); // ambil data payment terkait

    $order['payment'] = $payment; // tambahkan payment ke dalam array order

    $payments = $paymentModel->getActive();

    $paymentProofModel = new PaymentProofs();
    $paymentProof = $paymentProofModel->getByOrderId($orderId);

    return $this->view('/home/payment', [
      'order' => $order,
      'payment' => $payments,
      'paymentProof' => $paymentProof,
      'orderId' => $orderId
    ], '/homeLayout');
  }

  public function createOrder()
  {
    header('Content-Type: application/json');

    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $raw = file_get_contents("php://input");
    $request = json_decode($raw, true);

    if (!$request || !isset($request['cart'], $request['customer_information'])) {
      http_response_code(422);
      echo json_encode([
        'message' => 'Data order tidak valid.',
      ]);
      return;
    }

    $cartItems = $request['cart'];
    $info = $request['customer_information'];
    $user = [
      'id' => $_SESSION['user_id'] ?? null,
      'email' => $_SESSION['email'] ?? null,
      'name' => $_SESSION['name'] ?? null,
      'role' => $_SESSION['role'] ?? null,
    ];

    if (!$user) {
      http_response_code(401);
      echo json_encode(['message' => 'Unauthorized']);
      return;
    }

    $db = Database::getInstance();
    $db->beginTransaction();

    try {
      $totalOrderPrice = 0;
      $orderItemsData = [];
      $productModel = new Product();

      foreach ($cartItems as $item) {
        $product = $productModel->getById($item['productId']);

        if (!$product) {
          throw new Exception("Produk ID {$item['productId']} tidak ditemukan.");
        }

        if ($product['stock'] < $item['qty']) {
          throw new Exception("Stok produk '{$product['name']}' tidak mencukupi. Sisa: {$product['stock']}");
        }

        $itemTotalPrice = $product['price'] * $item['qty'];
        $totalOrderPrice += $itemTotalPrice;

        $orderItemsData[] = [
          'product_id' => $product['id'],
          'qty' => $item['qty'],
          'price' => $product['price'],
        ];

        $productModel->reduceStock($product['id'], $item['qty']);
      }

      $stmt = $db->prepare("INSERT INTO orders (user_id, payment_id, delivery_location, branch, total_price, description, status) 
                VALUES (:user_id, :payment_id, :location, :branch, :total_price, :description, :status)");

      $stmt->execute([
        ':user_id' => $user['id'],
        ':payment_id' => $info['payment_id'],
        ':location' => $info['delivery_location'],
        ':branch' => $info['branch'],
        ':total_price' => $totalOrderPrice,
        ':description' => $info['description'] ?? null,
        ':status' => OrderStatus::Pending->value
      ]);

      $orderId = $db->lastInsertId();

      $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (:order_id, :product_id, :qty, :price)");

      foreach ($orderItemsData as $item) {
        $itemStmt->execute([
          ':order_id' => $orderId,
          ':product_id' => $item['product_id'],
          ':qty' => $item['qty'],
          ':price' => $item['price'],
        ]);
      }

      $db->commit();

      echo json_encode([
        'message' => 'Order berhasil dibuat!',
        'order_id' => $orderId,
        'payment_id' => $info['payment_id'],
        'delivery_location' => $info['delivery_location'],
        'branch' => $info['branch'],
        'total_price' => $totalOrderPrice,
        'description' => $info['description'],
        'status' => OrderStatus::Pending->value
      ]);
    } catch (Exception $e) {
      $db->rollBack();
      http_response_code(500);
      echo json_encode([
        'message' => 'Gagal membuat order.',
        'error' => $e->getMessage()
      ]);
    }
  }

  public function uploadPaymentProof()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $orderId = $_POST['order_id'] ?? null;
      $verified = isset($_POST['is_active']) ? 1 : 0;

      // Validasi dasar
      if (!$orderId) {
        $_SESSION['error'] = "ID pesanan tidak ditemukan.";
        header("Location: /order/$orderId/payment");
        exit;
      }

      // Validasi order ID
      $orderModel = new Order();
      $order = $orderModel->getById($orderId);

      if (!$order) {
        $_SESSION['error'] = "Order tidak valid.";
        header("Location: /order/$orderId/payment");
        exit;
      }

      $imagePath = null;

      if (!empty($_FILES['image_url']['name'])) {
        $image = $_FILES['image_url'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!in_array($image['type'], $allowedTypes)) {
          $_SESSION['error'] = "Tipe gambar tidak valid.";
          header("Location: /order/$orderId/payment");
          exit;
        }

        if ($image['size'] > 2 * 1024 * 1024) { // 2MB max
          $_SESSION['error'] = "Ukuran gambar melebihi 2MB.";
          header("Location: /order/$orderId/payment");
          exit;
        }

        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $newFileName = 'payment_proof_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/storage/payments_proofs/';
        $uploadPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
          $_SESSION['error'] = "Gagal mengunggah file.";
          header("Location: /order/$orderId/payment");
          exit;
        }

        $imagePath = "payments_proofs/" . $newFileName;
      }

      // Simpan ke database
      $model = new PaymentProofs();
      $model->create($orderId, $imagePath, true);

      $_SESSION['message'] = "Bukti pembayaran berhasil dikirim.";
      header("Location: /order");
      exit;
    }
  }
}
