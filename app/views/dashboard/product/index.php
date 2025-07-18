<?php ob_start(); ?>

<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Manajemen Menu</h2>
    <p class="text-dark-blue mt-1">Atur dan kelola daftar makanan serta minuman yang tersedia di Warkop.</p>
</div>

<div class="space-y-4">
    <div class="p-2 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-royal-blue">Menu</h2>
        <div class="flex items-center gap-2">
            <input id="filter-search" type="text" placeholder="Cari menu..." class="border border-dark-blue/20 rounded-lg px-4 py-1">
            <form id="categoryFilterForm" method="GET" action="/dashboard/products">
                <select name="category" id="category_filter" class="bg-dark-blue text-white px-2 rounded py-1">
                    <option value="" <?= empty($selectedCategoryId) ? 'selected' : '' ?>>All</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $selectedCategoryId == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="/dashboard/menu/products/create" class="flex items-center rounded px-3 py-1 text-white bg-green-500 hover:bg-green-300 cursor-pointer">Tambah</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div id="alert" class="bg-green-200 rounded p-4">
            <p class="text-green-800 font-semibold"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto w-full bg-white p-2 rounded-lg shadow-sm shadow-dark-blue/10">
        <table class="w-full text-left rounded-md overflow-hidden">
            <thead class=" *:text-gray-400 *:border-b *:border-dark-blue/10">
                <tr>
                    <th class="font-normal py-2 px-6">No</th>
                    <th class="font-normal p-2">Gambar</th>
                    <th class="font-normal p-2">Nama</th>
                    <th class="font-normal p-2">Kategori</th>
                    <th class="font-normal p-2">Harga</th>
                    <th class="font-normal p-2">Stok</th>
                    <th class="font-normal p-2">Deskripsi</th>
                    <th class="font-normal p-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="menu-content">
                <?php if (!empty($products)): ?>
                    <?php $no = 1;
                    foreach ($products as $product): ?>
                        <tr class="menu-row hover:bg-dark-blue/20 divide-y divide-gray-200 text-gray-800 *:text-sm *:font-medium">
                            <td class="py-4 px-6"><?= $no++ ?></td>
                            <td class="px-2 py-4 text-dark-blue">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="/storage/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= $product['name'] ?>" class="w-24">
                                <?php else: ?>
                                    <img src="/images/image-placeholder.png" alt="<?= $product['name'] ?>" class="w-24">
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-4 text-dark-blue menu-name"><?= htmlspecialchars($product['name']) ?></td>
                            <td class="px-2 py-4 text-dark-blue menu-category"><?= htmlspecialchars($product['category_name']) ?></td>
                            <td class="px-2 py-4 text-dark-blue">Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                            <td class="px-2 py-4 text-dark-blue"><?= $product['stock'] ?></td>
                            <td class="px-2 py-4 text-dark-blue menu-description"><?= htmlspecialchars($product['description']) ?></td>
                            <td class="px-2 py-4 text-dark-blue">
                                <div class="flex items-center justify-center gap-3 *:text-sm">
                                    <a href="/dashboard/menu/products/update/<?= $product['id'] ?>" class="text-royal-blue font-medium cursor-pointer">Edit</a>
                                    <a href="/dashboard/menu/products/detail/<?= $product['id'] ?>" class="text-green-500 font-medium cursor-pointer">Detail</a>
                                    <form action="/products/delete/<?= $product['id'] ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="text-red-500 font-medium cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-red-500">
                            <p class="flex items-center justify-center gap-2">🗃️ Data Product tidak tersedia.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Limit and Pagination Controls -->
        <div class="flex items-center justify-between mt-4">
            <div>
                <label for="limitPerPage" class="mr-2 text-dark-blue">Tampilkan</label>
                <select id="limitPerPage" class="border border-dark-blue/20 rounded px-2 py-1">
                    <option value="5" selected>5</option>
                    <option value="10" >10</option>
                    <option value="20">20</option>
                </select>
                <span class="ml-2 text-dark-blue">data per halaman</span>
            </div>
            <div id="pagination" class="flex gap-1"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertComponent = document.getElementById('alert');
    if (alertComponent) {
        setTimeout(() => alertComponent.style.display = "none", 1500);
    }

    const filter = document.getElementById('category_filter');
    filter.addEventListener('change', function () {
        document.getElementById('categoryFilterForm').submit();
    });

    const searchInput = document.getElementById('filter-search');
    let currentSearch = '';
    searchInput.addEventListener('input', function () {
        currentSearch = this.value.toLowerCase();
        filterTable();
    });

    const rows = document.querySelectorAll('.menu-row');
    const paginationContainer = document.getElementById('pagination');
    const limitSelect = document.getElementById('limitPerPage');
    let currentPage = 1;
    let limit = parseInt(limitSelect.value);

    function filterTable() {
        const tbody = document.getElementById('menu-content');
        const searchTerm = currentSearch;
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.querySelector('.menu-name')?.textContent.toLowerCase() || '';
            const category = row.querySelector('.menu-category')?.textContent.toLowerCase() || '';
            const description = row.querySelector('.menu-description')?.textContent.toLowerCase() || '';
            const match = name.includes(searchTerm) || category.includes(searchTerm) || description.includes(searchTerm);
            row.dataset.visible = match ? "true" : "false";
        });

        showPage(1); // Reset ke halaman pertama saat search
    }

    function showPage(page) {
        currentPage = page;
        const visibleRows = Array.from(rows).filter(r => r.dataset.visible !== "false");
        const start = (page - 1) * limit;
        const end = start + limit;

        rows.forEach(row => row.style.display = 'none');
        visibleRows.slice(start, end).forEach(row => row.style.display = '');

        renderPagination(visibleRows.length);
    }

    function renderPagination(totalVisible) {
        const totalPages = Math.ceil(totalVisible / limit);
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
    rows.forEach(row => row.dataset.visible = "true");
    showPage(currentPage);
});
</script>
