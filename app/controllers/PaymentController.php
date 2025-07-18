<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = (new Payment())->all();
        $this->view('/dashboard/payment/index', ['payments' => $payments], '/dashboardLayout');
    }

    public function create()
    {
        $this->view('/dashboard/payment/create', [], '/dashboardLayout');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /dashboard/payments");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $is_active = in_array($_POST['is_active'] ?? null, ['1', '0']) ? (int)$_POST['is_active'] : 0;

        if (empty($name)) {
            $_SESSION['message'] = 'Nama pembayaran wajib diisi.';
            header("Location: /dashboard/payments/create");
            exit;
        }

        $qr_code_url = null;
        if (isset($_FILES['qr_code_url']) && $_FILES['qr_code_url']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['qr_code_url'];
            $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'];

            if (!in_array($image['type'], $allowed)) {
                $_SESSION['message'] = 'File QR tidak valid.';
                header("Location: /dashboard/payments/create");
                exit;
            }

            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = 'payment_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $uploadPath = __DIR__ . '/../../public/storage/payments/' . $fileName;

            move_uploaded_file($image['tmp_name'], $uploadPath);
            $qr_code_url = 'payments/' . $fileName;
        }

        $payment = new Payment();
        $payment->create([
            'name' => $name,
            'qr_code_url' => $qr_code_url,
            'is_active' => $is_active
        ]);

        $_SESSION['message'] = "Tipe Pembayaran berhasil dibuat.";
        header("Location: /dashboard/payments");
        exit;
    }

    public function show(array $params)
    {
        $id = $params[0] ?? null;

        if (!$id) {
            $_SESSION['message'] = "ID tidak valid.";
            header("Location: /dashboard/payments");
            exit;
        }

        $payment = (new Payment())->getById((int)$id);

        if (!$payment) {
            $_SESSION['message'] = "Data tidak ditemukan.";
            header("Location: /dashboard/payments");
            exit;
        }

        $this->view('/dashboard/payment/show', ['payment' => $payment], '/dashboardLayout');
    }


    public function edit(array $params)
    {
        $id = $params[0] ?? null;
        $payment = (new Payment())->find((int)$id);

        if (!$payment) {
            $_SESSION['message'] = "Data tidak ditemukan.";
            header("Location: /dashboard/payments");
            exit;
        }

        $this->view('/dashboard/payment/update', ['payment' => $payment], '/dashboardLayout');
    }

    public function update(array $params)
    {
        $id = $params[0] ?? null;
        $paymentModel = new Payment();
        $payment = $paymentModel->find((int)$id);

        if (!$payment) {
            $_SESSION['message'] = "Data tidak ditemukan.";
            header("Location: /dashboard/payments");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $is_active = in_array($_POST['is_active'] ?? null, ['1', '0']) ? (int)$_POST['is_active'] : 0;
        $delete_qr = isset($_POST['delete_qr_code_url']);

        if (empty($name)) {
            $_SESSION['message'] = 'Nama pembayaran wajib diisi.';
            header("Location: /dashboard/payments/edit/$id");
            exit;
        }

        $qr_code_url = $payment['qr_code_url'];

        // Hapus gambar lama jika diminta
        if ($delete_qr && $qr_code_url && file_exists(__DIR__ . '/../../public/' . $qr_code_url)) {
            unlink(__DIR__ . '/../../public/' . $qr_code_url);
            $qr_code_url = null;
        }

        // Upload gambar baru
        if (isset($_FILES['qr_code_url']) && $_FILES['qr_code_url']['error'] === UPLOAD_ERR_OK) {
            if ($qr_code_url && file_exists(__DIR__ . '/../../public/' . $qr_code_url)) {
                unlink(__DIR__ . '/../../public/' . $qr_code_url);
            }

            $image = $_FILES['qr_code_url'];
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = 'payment_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $uploadPath = __DIR__ . '/../../public/storage/payments/' . $fileName;
            move_uploaded_file($image['tmp_name'], $uploadPath);
            $qr_code_url = 'storage/payments/' . $fileName;
        }

        $paymentModel->update((int)$id, [
            'name' => $name,
            'qr_code_url' => $qr_code_url,
            'is_active' => $is_active
        ]);


        $_SESSION['message'] = "Tipe Pembayaran berhasil diperbarui.";
        header("Location: /dashboard/payments");
        exit;
    }

    public function destroy(array $params)
    {
        $id = $params[0] ?? null;
        $paymentModel = new Payment();
        $payment = $paymentModel->find((int)$id);

        if ($payment && $payment['qr_code_url'] && file_exists(__DIR__ . '/../../public/storage/' . $payment['qr_code_url'])) {
            unlink(__DIR__ . '/../../public/storage/' . $payment['qr_code_url']);
        }

        $paymentModel->delete((int)$id);
        $_SESSION['message'] = "Tipe Pembayaran berhasil dihapus.";
        header("Location: /dashboard/payments");
        exit;
    }
}
