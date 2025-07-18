<?php
$category = $category ?? ['id' => '', 'name' => '', 'description' => ''];
?>

<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Edit Kategori</h2>
    <p class="text-dark-blue mt-1">Perbarui nama kategori jika diperlukan</p>
</div>

<div class="space-y-4">
    <div class="p-2 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-royal-blue">Kategori</h2>
        <a href="/dashboard/categories" class="bg-dark-blue hover:bg-dark-blue/70 px-3 rounded py-1 text-white flex items-center gap-1 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div id="alert" class="bg-red-200 rounded p-4">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p class="text-red-700 text-sm italic"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col gap-4 bg-white p-2 rounded-lg shadow-sm shadow-dark-blue/10">
        <form action="/categories/update/<?= $category['id'] ?>" method="POST" class="space-y-6">
            <input type="hidden" name="_method" value="PATCH">

            <div class="space-y-3">
                <label for="name" class="flex flex-col gap-3">
                    <span class="text-dark-blue font-semibold">Nama:</span>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" class="w-full border border-dark-blue/20 px-4 py-1 rounded-sm">
                </label>

                <label for="description" class="flex flex-col gap-3 rounded-md">
                    <span class="text-dark-blue font-semibold">Deskripsi:</span>
                    <input type="text" id="description" name="description" value="<?= htmlspecialchars($category['description']) ?>" class="w-full border border-dark-blue/20 px-4 py-1 rounded-sm">
                </label>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="px-4 py-1 rounded cursor-pointer bg-royal-blue text-white font-semibold text-sm">Update</button>
                <button type="reset" class="px-4 py-1 rounded cursor-pointer bg-red-500 text-white font-semibold text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    const alertComponent = document.getElementById('alert')
    if (alertComponent) {
        setTimeout(() => alertComponent.style.display = "none", 1500)
    }
</script>
