<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Daftar Pesanan</h2>
    <p class="text-dark-blue mt-1">Pantau dan kelola semua pesanan pelanggan yang masuk di Warkop.</p>
</div>

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-royal-blue">Order</h2>
    </div>

    <div class="overflow-x-auto w-full bg-white p-2 rounded-lg shadow-sm shadow-dark-blue/10">
        <table class="w-full text-left rounded-md overflow-hidden">
            <thead class="*:text-gray-500">
                <tr>
                    <th class="font-normal py-2 px-6">No</th>
                    <th class="font-normal px-2 py-4">Pelanggan</th>
                    <th class="font-normal px-2 py-4">Tempat</th>
                    <th class="font-normal px-2 py-4">Lokasi Antar</th>
                    <th class="font-normal px-2 py-4">Total</th>
                    <th class="font-normal px-2 py-4">Status</th>
                    <th class="font-normal px-2 py-4">Deskripsi</th>
                    <th class="font-normal px-2 py-4">Waktu</th>
                    <th class="font-normal px-2 py-4"></th>
                </tr>
            </thead>
            <tbody id="order-content">
                <?php if (!empty($orders)): $no = 1; ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="order-row hover:bg-dark-blue/20 divide-y divide-gray-200 text-gray-800">
                            <td class="px-6 py-2"><?= $no++; ?></td>
                            <td class="px-2 py-4"><?= htmlspecialchars($order['user_name']); ?></td>
                            <td class="px-2 py-4"><?= htmlspecialchars($order['branch']); ?></td>
                            <td class="px-2 py-4"><?= htmlspecialchars($order['delivery_location']); ?></td>
                            <td class="px-2 py-4">Rp <?= number_format($order['total_price'], 0, ',', '.'); ?></td>
                            <td class="px-2 py-4">
                                <?php $statusOrder = $order['status']; ?>
                                <?php if ($statusOrder === 'pending'): ?>
                                    <p class="text-sm rounded px-2 py-1 text-center bg-yellow-600 text-white">Pending</p>
                                <?php elseif ($statusOrder === 'processing'): ?>
                                    <p class="text-sm rounded px-2 py-1 text-center bg-blue-800 text-white">Processing</p>
                                <?php elseif ($statusOrder === 'done'): ?>
                                    <p class="text-sm rounded px-2 py-1 text-center bg-green-800 text-white">Done</p>
                                <?php else: ?>
                                    <p class="text-sm rounded px-2 py-1 text-center bg-red-800 text-white">Cancelled</p>
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-4 line-clamp-1 truncate max-w-[160px]"><?= htmlspecialchars($order['description']); ?></td>
                            <td><?= date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                            <td class="px-2 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="/dashboard/orders/detail/<?= $order['id']; ?>" class="text-green-500 cursor-pointer">
                                        <?= x_icon(['name' => 'receipt-text', 'class' => '']); ?>
                                    </a>
                                    <a href="/dashboard/orders/update/<?= $order['id']; ?>" class="text-royal-blue cursor-pointer">
                                        <?= x_icon(['name' => 'pencil', 'class' => '']); ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-red-500">
                            <p class="flex items-center justify-center gap-2">
                                <?= x_icon(['name' => 'package-open', 'class' => '']); ?>
                                Pesanan kosong.
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination and Limit Control -->
        <div class="flex items-center justify-between mt-4">
            <div>
                <label for="limitOrderPage" class="mr-2 text-dark-blue">Tampilkan</label>
                <select id="limitOrderPage" class="border border-dark-blue/20 rounded px-2 py-1">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                </select>
                <span class="ml-2 text-dark-blue">pesanan per halaman</span>
            </div>
            <div id="order-pagination" class="flex gap-1"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.order-row');
    const paginationContainer = document.getElementById('order-pagination');
    const limitSelect = document.getElementById('limitOrderPage');
    let currentPage = 1;
    let limit = parseInt(limitSelect.value);

    function showPage(page) {
        currentPage = page;
        const start = (page - 1) * limit;
        const end = start + limit;

        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });

        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(rows.length / limit);
        paginationContainer.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = 'px-3 py-1 border rounded ' + (i === currentPage ? 'bg-royal-blue text-white' : 'bg-white text-royal-blue');
            btn.addEventListener('click', () => showPage(i));
            paginationContainer.appendChild(btn);
        }
    }

    limitSelect.addEventListener('change', function () {
        limit = parseInt(this.value);
        showPage(1);
    });

    // Inisialisasi
    showPage(currentPage);
});
</script>
