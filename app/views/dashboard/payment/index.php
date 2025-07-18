<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Jenis Pembayaran</h2>
    <p class="text-dark-blue mt-1">Kelola daftar metode pembayaran yang tersedia, lengkap dengan gambar QR Code.</p>
</div>

<div class="space-y-4">
    <div class="p-2 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-royal-blue">Jenis Pembayaran</h2>
        <a href="/dashboard/payments/create" class="flex items-center rounded px-3 py-1 text-white bg-green-500 hover:bg-green-300 cursor-pointer">
            Tambah
        </a>
    </div>

    <?php if (!empty($_SESSION['message'])): ?>
        <div id="alert" class="bg-green-200 rounded p-4">
            <p class="text-green-700 font-semibold">
                <?= htmlspecialchars($_SESSION['message']); ?>
            </p>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="overflow-x-auto w-full bg-white p-2 rounded-lg shadow-sm shadow-dark-blue/10">
        <table class="w-full text-left rounded-md overflow-hidden">
            <thead class="*:text-gray-500">
                <tr>
                    <th class="font-normal py-2 px-6">No</th>
                    <th class="font-normal px-2 py-4">Image</th>
                    <th class="font-normal px-2 py-4">Payment</th>
                    <th class="font-normal px-2 py-4">Status</th>
                    <th class="font-normal px-2 py-4">Waktu</th>
                    <th class="font-normal px-2 py-4"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-dark-blue/20 divide-y divide-gray-200 text-gray-800">
                            <td class="px-6 py-2"><?= $no++; ?></td>
                            <td class="py-2">
                                <?php if (!empty($payment['qr_code_url'])): ?>
                                    <img src="/storage/<?= htmlspecialchars($payment['qr_code_url']) ?>" alt="<?= htmlspecialchars($payment['name']) ?>" class="h-24">
                                <?php else: ?>
                                    <img src="/images/image-placeholder.png" alt="<?= htmlspecialchars($payment['name']) ?>" class="h-24">
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-4"><?= htmlspecialchars($payment['name']) ?></td>
                            <td class="px-2 py-4">
                                <?php if ($payment['is_active'] == 1): ?>
                                    <span class="bg-royal-blue px-2 text-white text-sm">Aktif</span>
                                <?php else: ?>
                                    <span class="bg-red-500 px-2 text-white text-sm">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-4">
                                <?= isset($payment['created_at']) ? date('d M Y H:i', strtotime($payment['created_at'])) : '-' ?>
                            </td>
                            <td class="px-2 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="/dashboard/payments/detail/<?= $payment['id'] ?>" class="text-green-500 cursor-pointer">
                                        <?php echo x_icon(['name' => 'receipt-text', 'class' => 'size-5']); ?>
                                    </a>
                                    <a href="/dashboard/payments/update/<?= $payment['id'] ?>" class="text-royal-blue cursor-pointer">
                                        <?php echo x_icon(['name' => 'pencil', 'class' => 'size-5']); ?>
                                    </a>
                                    <form action="/payments/delete/<?= $payment['id'] ?>" method="post" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        <button type="submit" class="text-red-500 cursor-pointer">
                                            <?php echo x_icon(['name' => 'trash', 'class' => 'size-5']); ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-red-500">
                            <p class="flex items-center justify-center gap-2">👜 Payment Kosong.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const alertComponent = document.getElementById('alert');

    if (alertComponent) {
        setTimeout(() => {
            alertComponent.style.display = "none";
        }, 1500);
    }
</script>