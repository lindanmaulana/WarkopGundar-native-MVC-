<section class="pt-28 pb-20">
    <div class="container max-w-6xl mx-auto">
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-black/10 pb-6">
                <h2 class="text-2xl font-semibold text-secondary">Pesanan</h2>
                <p class="text-xl font-semibold text-secondary"><?= count($orders) ?> pesanan</p>
            </div>

            <div class="overflow-x-auto w-full bg-white p-2 rounded-lg shadow-sm shadow-dark-blue/10">
                <table class="w-full text-left rounded-md overflow-hidden">
                    <thead class="*:text-gray-500">
                        <tr>
                            <th class="font-normal py-2 px-6">No</th>
                            <th class="font-normal px-2 py-4">Tempat</th>
                            <th class="font-normal px-2 py-4">Lokasi Antar</th>
                            <th class="font-normal px-2 py-4">Total</th>
                            <th class="font-normal px-2 py-4">Status</th>
                            <th class="font-normal px-2 py-4">Deskripsi</th>
                            <th class="font-normal px-2 py-4">Waktu</th>
                            <th class="font-normal px-2 py-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)) :
                            $no = 1;
                            foreach ($orders as $order) : ?>
                                <tr class="hover:bg-dark-blue/20 divide-y divide-gray-200 text-gray-800">
                                    <td class="px-6 py-2"><?= $no++ ?></td>
                                    <td class="px-2 py-4"><?= htmlspecialchars($order['branch']) ?></td>
                                    <td class="px-2 py-4"><?= htmlspecialchars($order['delivery_location']) ?></td>
                                    <td class="px-2 py-4">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></td>
                                    <td class="px-2 py-4">
                                        <?php
                                        $status = $order['status'];
                                        $statusText = ucfirst($status);
                                        $bgColor = match ($status) {
                                            'pending' => 'bg-yellow-600',
                                            'processing' => 'bg-blue-800',
                                            'done' => 'bg-green-800',
                                            default => 'bg-red-800',
                                        };
                                        ?>
                                        <p class="text-sm rounded px-2 py-1 text-center text-white <?= $bgColor ?>"><?= $statusText ?></p>
                                    </td>
                                    <td class="px-2 py-4"><?= htmlspecialchars($order['description']) ?: '-' ?></td>
                                    <td class="px-2 py-4"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td class="px-2 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="/order/<?= $order['id'] ?>/detail" class="text-green-500 cursor-pointer">Detail</a>
                                            <a href="/order/<?= $order['id'] ?>/payment" class="text-royal-blue cursor-pointer">Pembayaran</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else : ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-red-500">
                                    <p class="flex items-center justify-center gap-2">Belum ada pesanan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>