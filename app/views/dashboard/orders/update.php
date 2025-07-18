<?php $isActive = fn(string $routeName) => ($_SERVER['REQUEST_URI'] === $routeName ? 'bg-royal-blue' : 'bg-royal-blue/70'); ?>

<div class="py-10">
    <h2 class="text-2xl font-semibold text-dark-blue">Edit Pesanan</h2>
    <p class="text-sm text-dark-blue/70">Ubah status pesanan berdasarkan progres di Warkop.</p>
</div>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-dark-blue">Form Edit Order</h3>
        <a href="/dashboard/orders" class="flex items-center gap-2 text-sm bg-dark-blue hover:bg-dark-blue/80 text-white px-3 py-1 rounded">
            ← Kembali
        </a>
    </div>


    <div class="bg-white p-6 rounded-lg shadow-md shadow-dark-blue/10">
        <form action="/orders/update/<?= $order['id'] ?>" method="POST" class="space-y-5">
            <input type="hidden" name="_method" value="PATCH">

            <div>
                <label for="customer_name" class="block text-sm font-semibold text-dark-blue mb-1">Nama Pembeli</label>
                <input type="text" id="customer_name" name="customer_name"
                    value="<?= htmlspecialchars($order['user']['name'] ?? '') ?>"
                    class="w-full border border-dark-blue/20 bg-gray-100 text-dark-blue/80 px-4 py-2 rounded" readonly>
            </div>

            <div>
                <label for="branch" class="block text-sm font-semibold text-dark-blue mb-1">Lokasi Warkop</label>
                <input type="text" id="branch" name="branch"
                    value="<?= htmlspecialchars($order['branch']) ?>"
                    class="w-full border border-dark-blue/20 bg-gray-100 text-dark-blue/80 px-4 py-2 rounded" readonly>
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-dark-blue mb-1">Status Pesanan</label>
                <select id="status" name="status" class="w-full border border-dark-blue/20 px-4 py-2 rounded bg-white text-dark-blue">
                    <?php
                    $statuses = ['pending' => 'Pending', 'processing' => 'Processing', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                    foreach ($statuses as $value => $label) {
                        $selected = ($order['status'] === $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="submit" class="bg-royal-blue hover:bg-royal-blue/90 text-white font-semibold px-4 py-2 rounded text-sm">
                    Update
                </button>
                <button type="reset" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>