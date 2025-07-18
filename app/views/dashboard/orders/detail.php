<div class="space-y-4 pb-10 md:pb-0">
    <div class="w-full min-h-[200px] flex items-center justify-between bg-royal-blue px-6 md:px-12 -mb-14">
        <h2 class="text-lg font-semibold text-white">Order Detail</h2>
        <a href="/dashboard/orders" class="flex items-center justify-start max-w-20 gap-1 bg-dark-blue text-sm px-4 py-1 text-white rounded">
            <?php echo x_icon(['name' => 'arrow-left', 'class' => '']); ?> Back
        </a>
    </div>

    <div class="relative max-w-[90%] mx-auto bg-white px-2 py-6 rounded shadow-sm shadow-dark-blue/10">
        <div class="absolute translate-x-1/2 right-1/2 -top-9 size-16 flex items-center justify-center rounded-full bg-royal-blue border-2 border-white">
            <?php echo x_icon(['name' => 'coffee', 'class' => '']); ?>
        </div>
        <h3 class="text-center text-3xl font-bold text-dark-blue">Warkop Gundar</h3>
        <p id="order-status" class="text-sm text-center py-4" data-order-status="<?= $order['status'] ?>"></p>

        <div class="px-4 md:px-0 md:max-w-2/3 mx-auto py-10 space-y-4">
            <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                <li class="flex items-center justify-between">
                    <h4 class="font-semibold">Nama</h4>
                    <p class="text-dark-blue/60"><?= htmlspecialchars($order['user']['name']) ?></p>
                </li>
                <li class="flex items-center justify-between">
                    <h4 class="font-semibold">Tanggal</h4>
                    <p class="text-dark-blue/60"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                </li>
                <li class="flex items-center justify-between">
                    <h4 class="font-semibold">Lokasi Warkop</h4>
                    <p class="text-dark-blue/60"><?= htmlspecialchars($order['branch']) ?></p>
                </li>
                <li class="flex items-center justify-between">
                    <h4 class="font-semibold">Lokasi Antar</h4>
                    <p class="text-dark-blue/60"><?= htmlspecialchars($order['delivery_location']) ?></p>
                </li>
            </ul>

            <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                <?php foreach ($order['orderItems'] as $item): ?>
                    <li class="flex items-center justify-between">
                        <h4 class="font-semibold"><?= htmlspecialchars($item['product']['name']) ?> <span class="text-royal-blue"><?= $item['qty'] ?>x</span></h4>
                        <p class="text-dark-blue/60">Rp<?= number_format($item['product']['price'], 0, ',', '.') ?>/ <span class="text-xs">pcs</span></p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                <li class="flex flex-col items-center justify-between">
                    <h4 class="font-semibold">Deskripsi</h4>
                    <p class="text-dark-blue/60"><?= htmlspecialchars($order['description']) ?></p>
                </li>
            </ul>

            <ul class="w-full space-y-2 pb-4 border-b border-dark-blue/20">
                <li class="flex flex-col justify-between">
                    <h4 class="font-semibold">Bukti Pembayaran</h4>
                    <?php if (!empty($paymentProof)): ?>
                        <figure class="max-h-60 overflow-y-auto">
                            <img src="/storage/<?= htmlspecialchars($paymentProof['image_url']) ?>" alt="<?= htmlspecialchars($order['user']['name']) ?>">
                        </figure>
                    <?php else: ?>
                        <p class="text-center text-sm text-red-500">Pelanggan belum melakukan pembayaran</p>
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
</div>

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
    };

    showOrderStatus();
</script>