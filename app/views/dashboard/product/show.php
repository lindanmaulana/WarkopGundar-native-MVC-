<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Detail Menu</h2>
    <p class="text-dark-blue mt-1 max-w-96">Berikut adalah rincian dari menu yang telah dipilih, termasuk harga, deskripsi, dan stok ketersediaan.</p>
</div>

<section>
    <div class="container max-w-6xl mx-auto">
        <div class="flex items-center justify-between">
            <h3 class="text-2xl text-dark-blue font-semibold"><?= htmlspecialchars($product['name']) ?></h3>
            <a href="/dashboard/menu/products" class="flex items-center gap-2 text-sm bg-dark-blue hover:bg-dark-blue/80 text-white p-2 rounded">
                ← Kembali
            </a>
        </div>

        <div class="flex gap-8 py-8">
            <div>
                <?php if (!empty($product['image_url'])): ?>
                    <figure class="w-full h-100 rounded overflow-hidden group cursor-pointer shadow-md">
                        <img src="/storage/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-125 transition-global">
                    </figure>
                <?php else: ?>
                    <img src="/images/image-placeholder.png" alt="Tidak ada gambar">
                <?php endif; ?>
            </div>

            <div class="bg-white w-full p-6 rounded-lg shadow-md space-y-4">
                <label class="block space-y-1">
                    <span class="block text-dark-blue font-semibold">Nama Produk</span>
                    <input type="text" value="<?= htmlspecialchars($product['name']) ?>" class="w-full px-4 border border-dark-blue/30 py-2 rounded-lg" readonly>
                </label>

                <label class="block space-y-1">
                    <span class="block text-dark-blue font-semibold">Kategori</span>
                    <input type="text" value="<?= htmlspecialchars($product['category_name']) ?>" class="w-full px-4 border border-dark-blue/30 py-2 rounded-lg" readonly>
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <label class="block space-y-1">
                        <span class="block text-dark-blue font-semibold">Harga</span>
                        <input type="text" value="<?= number_format($product['price'], 0, ',', '.') ?>" class="w-full px-4 border border-dark-blue/30 py-2 rounded-lg" readonly>
                    </label>

                    <label class="block space-y-1">
                        <span class="block text-dark-blue font-semibold">Stok</span>
                        <input type="text" value="<?= $product['stock'] ?>" class="w-full px-4 border border-dark-blue/30 py-2 rounded-lg" readonly>
                    </label>
                </div>

                <label class="block space-y-1">
                    <span class="block text-dark-blue font-semibold">Deskripsi</span>
                    <textarea class="w-full px-4 border border-dark-blue/30 py-2 rounded-lg" rows="4" readonly><?= htmlspecialchars($product['description']) ?></textarea>
                </label>
            </div>
        </div>
    </div>
</section>