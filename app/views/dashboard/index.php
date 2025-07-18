<div class="mt-10 mb-4 w-full flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-semibold text-royal-blue">Ringkasan Dashboard</h2>
        <p class="text-dark-blue mt-1">Selamat datang, <?= htmlspecialchars($user['name']) ?>! Ini ringkasan operasional warkop hari ini.</p>
    </div>
    <div class="hidden md:flex items-center gap-3">
        <a href="/dashboard/orders" class="relative">
            <!-- Ganti icon dengan inline SVG atau file image -->
            <?php echo x_icon(['name' => 'shopping-cart', 'class' => 'size-5']); ?>
            <div data-total-order="<?= $totalOrderPending ?>" class="totalOrder absolute -top-3 right-0 rounded-full size-4 flex items-center justify-center bg-red-500">
                <span class="text-sm text-white"><?= $totalOrderPending ?></span>
            </div>
        </a>
        <h4 class="text-lg text-royal-blue font-semibold capitalize"><?= htmlspecialchars($user['role']) ?></h4>
        <div class="bg-royal-blue/40 rounded-full size-10 flex items-center justify-center">
            <span id="initialName" data-profile-name="<?= htmlspecialchars($user['name']) ?>" class="text-base font-bold text-royal-blue"></span>
        </div>
    </div>
</div>

<div class="mb-6">
    <h2 class="text-lg font-semibold text-royal-blue">Overview</h2>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-3 py-4 md:py-4">
        <div class="hover:-translate-y-2 transition-global duration-300 flex gap-2 max-h-[120px] bg-white border border-dark-blue/10 shadow-md p-5 rounded-md space-y-5 *:text-royal-blue">
            <div class="bg-royal-blue/30 size-10 rounded-full flex items-center justify-center">
                <?php echo x_icon(['name' => 'toolskitchen', 'class' => '']); ?>
            </div>
            <div>
                <h3 class="text-dark-blue/80 font-semibold text-sm">Total Menu</h3>
                <p class="text-xl font-bold text-dark-blue"><?= $totalProducts ?></p>
            </div>
        </div>
        <div class="hover:-translate-y-2 transition-global duration-300 flex gap-2 max-h-[120px] bg-white border border-dark-blue/10 shadow-md p-5 rounded-md space-y-5 *:text-royal-blue">
            <div class="bg-dark-blue/30 size-10 rounded-full flex items-center justify-center">
                <?php echo x_icon(['name' => 'shopping-cart', 'class' => '']); ?>
            </div>
            <div>
                <h3 class="text-dark-blue/80 font-semibold text-sm">Total Pesanan</h3>
                <p class="text-xl font-bold text-dark-blue"><?= $totalOrders ?></p>
            </div>
        </div>
        <div class="hover:-translate-y-2 transition-global duration-300 flex gap-2 max-h-[120px] bg-white border border-dark-blue/10 shadow-md p-5 rounded-md space-y-5 *:text-royal-blue">
            <div class="bg-green-200 size-10 rounded-full flex items-center justify-center">
                <?php echo x_icon(['name' => 'credit-card', 'class' => '']); ?>
            </div>
            <div>
                <h3 class="text-dark-blue/80 font-semibold text-sm">Total Tipe Pembayaran</h3>
                <p class="text-xl font-bold text-dark-blue"><?= $totalPayments ?></p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow-md p-5 rounded">
    <h2 class="text-xl font-semibold text-royal-blue">Pesanan Terbaru</h2>

    <div class="overflow-x-auto py-8 min-w-full">
        <table class="w-full">
            <thead class="*:text-xs text-gray-400 uppercase">
                <tr>
                    <th class="font-medium py-4">No</th>
                    <th class="font-medium py-4">Pelanggan</th>
                    <th class="font-medium py-4">Tempat</th>
                    <th class="font-medium py-4">Lokasi Antar</th>
                    <th class="font-medium py-4">Total</th>
                    <th class="font-medium py-4">Status</th>
                    <th class="font-medium py-4">Deskripsi</th>
                    <th class="font-medium py-4">Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($latestOrdersData)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($latestOrdersData as $order): ?>
                        <tr class="border-b border-dark-blue/20 hover:bg-dark-blue/20 *:text-sm">
                            <td class="text-gray-500 px-6 py-2"><?= $no++ ?></td>
                            <td class="text-gray-500 px-2 py-3"><?= htmlspecialchars($order['user']['name']) ?></td>
                            <td class="text-gray-500 px-2 py-3"><?= htmlspecialchars($order['branch']) ?></td>
                            <td class="text-gray-500 px-2 py-3"><?= htmlspecialchars($order['delivery_location']) ?></td>
                            <td class="text-gray-500 px-2 py-3">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></td>
                            <td class="text-gray-500 px-2 py-3">
                                <?php
                                $statusOrder = $order['status'];
                                $statusLabel = '';
                                $statusColor = '';
                                if ($statusOrder === 'pending') {
                                    $statusLabel = 'Pending';
                                    $statusColor = 'bg-yellow-600';
                                } elseif ($statusOrder === 'processing') {
                                    $statusLabel = 'Processing';
                                    $statusColor = 'bg-blue-800';
                                } elseif ($statusOrder === 'done') {
                                    $statusLabel = 'Done';
                                    $statusColor = 'bg-green-800';
                                } else {
                                    $statusLabel = 'Cancelled';
                                    $statusColor = 'bg-red-800';
                                }
                                ?>
                                <p class="text-sm rounded px-2 py-1 text-center text-white <?= $statusColor ?>"><?= $statusLabel ?></p>
                            </td>
                            <td class="text-gray-500 px-2 py-3 line-clamp-1 truncate max-w-[160px]"><?= htmlspecialchars($order['description'] ?? '-') ?></td>
                            <td class="text-gray-500">
                                <?= isset($order['created_at']) ? date('d M Y H:i', strtotime($order['created_at'])) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-red-500">
                            <p class="flex items-center justify-center gap-2">
                                <!-- icon -->
                                Pesanan kosong.
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="/dashboard/orders" class="flex items-center justify-end text-sm text-royal-blue">Lihat semua pesanan -></a>
</div>

<script>
    const showInitialNameUser = () => {
        const inisial = document.getElementById('initialName')
        const username = inisial.dataset.profileName;
        inisial.innerHTML = username.slice(0, 1);
    }

    const showTotalOrder = () => {
        const totalOrder = document.querySelector(".totalOrder")
        const totalOrderView = totalOrder.dataset.totalOrder;

        if (Number(totalOrderView) === 0) totalOrder.style.display = "none";
    }

    showTotalOrder();
    showInitialNameUser();
</script>