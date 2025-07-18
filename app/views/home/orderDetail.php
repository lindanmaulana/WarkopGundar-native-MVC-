<?php $statusValue = $order['status']; ?>

<section class="mt-20">
    <div class="container max-w-6xl mx-auto">
        <div class="space-y-8 py-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-secondary">Detail Pesanan</h2>
                <a href="/order" class="bg-secondary px-4 rounded text-white text-sm py-1">Back</a>
            </div>

            <div class="relative max-w-1/2 mx-auto bg-peach px-2 py-6 rounded shadow-sm shadow-dark-blue/10">
                <div class="absolute translate-x-1/2 right-1/2 -top-9 size-16 flex items-center justify-center rounded-full bg-primary border-2 border-white">
                    <?php echo x_icon(['name' => 'coffee', 'class' => 'text-white text-2xl']); ?>
                </div>
                <h3 class="text-center text-3xl font-bold text-dark-blue">Warkop Gundar</h3>
                <p id="order-status" class="text-sm text-center py-4" data-order-status="<?= $statusValue ?>"></p>

                <div class="px-4 md:px-0 md:max-w-2/3 mx-auto py-10 space-y-4">
                    <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                        <li class="flex items-center justify-between">
                            <h4 class="font-semibold">Nama</h4>
                            <p class="text-dark-blue/60"><?= $order['user_name'] ?></p>
                        </li>
                        <li class="flex items-center justify-between">
                            <h4 class="font-semibold">Tanggal</h4>
                            <p class="text-dark-blue/60"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                        </li>
                        <li class="flex items-center justify-between">
                            <h4 class="font-semibold">Lokasi Warkop</h4>
                            <p class="text-dark-blue/60"><?= $order['branch'] ?></p>
                        </li>
                        <li class="flex items-center justify-between">
                            <h4 class="font-semibold">Lokasi Antar</h4>
                            <p class="text-dark-blue/60"><?= $order['delivery_location'] ?></p>
                        </li>
                    </ul>

                    <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                        <?php foreach ($order['order_items'] as $item): ?>
                            <li class="flex items-center justify-between">
                                <h4 class="font-semibold"><?= $item['product_name'] ?> <span class="text-green-500"><?= $item['qty'] ?>x</span></h4>
                                <p class="text-dark-blue/60">Rp<?= number_format($item['item_price'], 0, ',', '.') ?>/pcs</p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                        <li class="flex flex-col items-center justify-between">
                            <h4 class="font-semibold">Deskripsi</h4>
                            <p class="text-dark-blue/60"><?= $order['description'] ?></p>
                        </li>
                    </ul>

                    <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                        <li class="flex flex-col justify-between">
                            <h4 class="font-semibold">Bukti Pembayaran</h4>
                            <?php if ($paymentProofs): ?>
                                <figure class="max-h-60 overflow-y-auto">
                                    <img src="/storage/<?= $paymentProofs['image_url'] ?>" alt="<?= $order['user_name'] ?>">
                                </figure>
                            <?php else: ?>
                                <p class="text-center text-sm text-red-500">Belum melakukan pembayaran</p>
                            <?php endif; ?>
                        </li>
                    </ul>

                    <ul>
                        <li class="flex items-center justify-between">
                            <h4 class="font-semibold text-xl">Total</h4>
                            <p class="text-dark-blue font-semibold text-xl">Rp<?= number_format($order['total_price'], 0, ',', '.') ?></p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="relative max-w-1/2 mx-auto bg-pale-peach px-2 py-6 rounded shadow-sm shadow-dark-blue/10 text-center">
                <?php if ($statusValue === 'pending'): ?>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Pesanan berhasil dikirim, tunggu respons dari admin ya.</h3>
                    <p class="text-gray-600">Jangan lupa untuk segera lakukan pembayaran dan unggah bukti pembayarannya agar pesanan bisa segera diproses.</p>
                    <p class="text-sm text-gray-500 mt-2">Kami akan konfirmasi setelah pembayaran kamu kami terima.</p>
                <?php elseif ($statusValue === 'processing'): ?>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Pesananmu Sedang Diproses!</h3>
                    <p class="text-gray-600">Kami sedang meracik pesananmu dengan sepenuh hati. Sebentar lagi siap dinikmati!</p>
                    <p class="text-sm text-gray-500 mt-2">Tetap pantau statusnya ya.</p>
                <?php elseif ($statusValue === 'done'): ?>
                    <h3 class="text-xl font-semibold text-green-700 mb-2">Pesananmu Sudah Selesai!</h3>
                    <p class="text-gray-600">Terima kasih telah berbelanja di Warkop <?= $order['branch'] ?>. Semoga puas dengan pesananmu!</p>
                    <p class="text-sm text-gray-500 mt-2">Kami tunggu orderan selanjutnya ya, Kak!</p>
                <?php elseif ($statusValue === 'cancelled'): ?>
                    <h3 class="text-xl font-semibold text-red-700 mb-2">Pesanan Dibatalkan.</h3>
                    <p class="text-gray-600">Mohon maaf, pesanan ini telah dibatalkan.</p>
                    <p class="text-sm text-gray-500 mt-2">Jika ada pertanyaan, silakan hubungi kami.</p>
                <?php else: ?>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Terima Kasih atas Pesanan Anda!</h3>
                    <p class="text-gray-600">Status pesanan Anda saat ini adalah: <?= ucfirst($statusValue) ?>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    const showOrderStatus = () => {
        const orderStatus = document.getElementById('order-status');
        const status = orderStatus.dataset.orderStatus;

        const span = document.createElement('span');
        span.classList.add(
            status === "pending" ? "bg-yellow-600" :
            status === "processing" ? "bg-blue-800" :
            status === "done" ? "bg-green-800" : "bg-red-800",
            "rounded", "px-2", "py-1", "text-white"
        );
        span.innerHTML = status;
        orderStatus.appendChild(span);
    }
    showOrderStatus();
</script>