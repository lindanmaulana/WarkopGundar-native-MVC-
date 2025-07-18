<section class="min-h-[400px] pt-28 pb-20 bg-no-repeat" style="background-image: url('/assets/images/bg-menu.png');">
    <div class="container max-w-6xl mx-auto py-24">
        <h2 class="text-secondary text-5xl font-semibold text-center tracking-widest">MENU</h2>

        <article class="flex justify-center gap-10 mt-18">
            <article class="w-full">
                <h3 class="text-3xl font-medium tracking-widest border-t border-t-black border-b border-b-black/30 py-1">MINUMAN</h3>

                <ul class="p-9 space-y-4">
                    <?php foreach ($productsCoffe as $product): ?>
                        <li class="flex items-center justify-between">
                            <div>
                                <h4 class="text-secondary text-base italic"><?= htmlspecialchars($product['name']); ?></h4>
                                <p><?= htmlspecialchars($product['description']); ?></p>
                            </div>
                            <span class="font-semibold text-secondary">Rp <?= number_format($product['price'], 0, ',', '.'); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <div class="block w-[1px] min-h-96 bg-black"></div>

            <article class="w-full">
                <h3 class="text-3xl font-medium tracking-widest border-t border-t-black border-b border-b-black/30 py-1">MAKANAN</h3>

                <ul class="p-9 space-y-4">
                    <?php foreach ($productsFood as $product): ?>
                        <li class="flex items-center justify-between">
                            <div>
                                <h4 class="text-secondary text-base italic"><?= htmlspecialchars($product['name']); ?></h4>
                                <p><?= htmlspecialchars($product['description']); ?></p>
                            </div>
                            <span class="font-semibold text-secondary">Rp <?= number_format($product['price'], 0, ',', '.'); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </article>
    </div>
</section>

<section class="my-20">
    <div class="container max-w-6xl mx-auto">
        <h2 class="text-secondary text-5xl font-semibold text-center tracking-widest">MENU TERSEDIA</h2>

        <!-- Search & Filter Controls -->
        <div class="flex flex-wrap justify-between items-center gap-4 mt-8">
            <input
                type="text"
                id="searchInput"
                placeholder="Cari menu..."
                class="w-full md:w-1/2 p-2 border border-primary/40 rounded-md shadow-sm">
            <select id="categoryFilter" class="w-full md:w-1/4 p-2 border border-primary/40 rounded-md shadow-sm">
                <option value="">Semua Kategori</option>
                <?php
                $categories = array_unique(array_column($products, 'category_name'));
                foreach ($categories as $category):
                ?>
                    <option value="<?= htmlspecialchars($category); ?>"><?= htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Product Cards -->
        <article id="menuContainer" class="grid grid-cols-4 gap-4 py-10"></article>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            <button id="prevPage" class="px-4 py-2 mx-1 bg-primary text-white rounded disabled:opacity-50">Sebelumnya</button>
            <span id="pageInfo" class="px-4 py-2 text-secondary font-semibold"></span>
            <button id="nextPage" class="px-4 py-2 mx-1 bg-primary text-white rounded disabled:opacity-50">Berikutnya</button>
        </div>
    </div>
</section>

<!-- JavaScript -->
<script>
    const allProducts = <?= json_encode($products); ?>;
    const menuContainer = document.getElementById('menuContainer');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const prevPage = document.getElementById('prevPage');
    const nextPage = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');

    let currentPage = 1;
    const itemsPerPage = 8;

    function renderProducts(products) {
        menuContainer.innerHTML = '';
        const start = (currentPage - 1) * itemsPerPage;
        const paginated = products.slice(start, start + itemsPerPage);

        if (paginated.length === 0) {
            menuContainer.innerHTML = '<p class="col-span-4 text-center text-gray-500">Menu tidak ditemukan.</p>';
            return;
        }

        paginated.forEach((product, index) => {
            const delay = 500 + (index * 100);
            menuContainer.innerHTML += `
                <article
                    data-aos="fade-up"
                    data-aos-duration="${delay}"
                    class="flex flex-col h-auto bg-white border border-primary/20 p-4 rounded-xl shadow-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02] hover:shadow-xl hover:border-primary/50 space-y-3">
                    <div class="relative w-full h-60 overflow-hidden rounded-lg">
                        <figure class="w-full h-full">
                            <img
                                src="${product.image_url ? `/storage.php?file=${encodeURIComponent(product.image_url)}` : '/images/image-placeholder.png'}"
                                alt="${product.name}"
                                class="w-full h-full object-cover transition-transform duration-300 ease-in-out hover:scale-105">
                        </figure>
                        <span class="absolute top-2 left-2 bg-primary/80 text-white text-xs font-semibold px-2 py-0.5 rounded-full z-10">
                            ${product.category_name}
                        </span>
                    </div>

                    <div class="flex flex-col flex-grow justify-between gap-2 pt-1">
                        <h3 class="text-xl text-secondary font-extrabold line-clamp-2 leading-tight">${product.name}</h3>
                        <span class="text-2xl font-bold text-primary">Rp ${parseInt(product.price).toLocaleString('id-ID')}</span>
                    </div>

                    <div class="flex items-center justify-between mt-auto">
                        <span class="bg-gray-100 text-secondary font-semibold px-3 py-1.5 rounded-full text-sm shadow-inner">
                            Stok ${product.stock}
                        </span>
                        <button
                            onclick="handleAddToCart(this)"
                            data-user-id="<?= isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : ''; ?>"
                            data-product-id="${product.id}"
                            data-product-category="${product.category_name}"
                            data-product-name="${product.name}"
                            data-product-price="${product.price}"
                            data-product-image="${product.image_url}"
                            class="bg-primary text-white rounded-full p-2.5 cursor-pointer shadow-md transition-all duration-300 ease-in-out hover:bg-royal-blue/90 hover:scale-110">
                            <?= x_icon(['name' => 'shopping-cart', 'class' => 'size-5 lg:size-5']); ?>
                        </button>
                    </div>
                </article>
            `;
        });

        const totalPages = Math.ceil(products.length / itemsPerPage);
        pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;
        prevPage.disabled = currentPage === 1;
        nextPage.disabled = currentPage === totalPages;
    }

    function applyFilters(resetPage = true) {
        const query = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        const filtered = allProducts.filter(product => {
            const nameMatch = product.name.toLowerCase().includes(query);
            const categoryMatch = !selectedCategory || product.category_name === selectedCategory;
            return nameMatch && categoryMatch;
        });

        if (resetPage) {
            currentPage = 1;
        }

        renderProducts(filtered);
    }


    searchInput.addEventListener('input', applyFilters);
    categoryFilter.addEventListener('change', applyFilters);

    prevPage.addEventListener('click', () => {
        currentPage--;
        applyFilters(false); // jangan reset halaman
    });

    nextPage.addEventListener('click', () => {
        currentPage++;
        applyFilters(false); // jangan reset halaman
    });

    // Initial render
    applyFilters();


    const alertComponent = document.getElementById('alert')

    const handleHideAlert = (alert) => {
        if (alert) {
            setTimeout(() => {
                alert.style.display = "none"
            }, 1500);
        }
    }

    handleHideAlert(alertComponent)
</script>