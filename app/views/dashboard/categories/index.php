<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Manajemen Kategori</h2>
    <p class="text-dark-blue mt-1">Kelola kategori produk untuk memudahkan pengelompokan menu makanan dan minuman.</p>
</div>

<div class="space-y-4">
    <div class="p-2 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-royal-blue">Daftar Kategori</h2>
        <a href="/dashboard/categories/create" class="flex items-center rounded px-3 py-1 text-white bg-green-500 hover:bg-green-300 cursor-pointer">
            Tambah
        </a>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div id="alert" class="bg-green-200 rounded p-4">
            <p class="text-green-700 font-semibold">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="bg-white p-2 rounded-lg shadow-md shadow-dark-blue/10 py-4">
        <table class="w-full text-left rounded-md overflow-hidden">
            <thead class="*:text-gray-400  *:border-b *:border-dark-blue/10">
                <tr>
                    <th class="font-normal py-2 px-6">No</th>
                    <th class="font-normal p-2">Nama</th>
                    <th class="font-normal p-2">Deskripsi</th>
                    <th class="font-normal p-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($categories as $category): ?>
                        <tr class="hover:bg-dark-blue/20 divide-y divide-gray-200 text-gray-800 *:text-sm *:font-medium">
                            <td class="py-4 px-6"><?= $no++ ?></td>
                            <td class="px-2 py-4 text-dark-blue"><?= htmlspecialchars($category['name']) ?></td>
                            <td class="px-2 py-4 text-dark-blue"><?= htmlspecialchars($category['description']) ?></td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-3 *:text-sm">
                                    <a href="/dashboard/categories/update/<?= $category['id'] ?>" class="text-royal-blue font-medium cursor-pointer">Edit</a>
                                    <form action="/categories/delete/<?= $category['id'] ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                        <button type="submit" class="text-red-500 font-medium cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-red-500">
                            <p class="flex items-center justify-center gap-2">🗃️ Data Category tidak tersedia.</p>
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
