<div class="w-full h-auto sticky top-0 inset-0 flex items-center justify-between py-10">
    <header class="w-full h-full flex justify-between items-center border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Penyewaan Baru</h1>
        <a href="/dashboard/rentals" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200 shadow-sm">
            Kembali ke Daftar Penyewaan
        </a>
    </header>
</div>
<div class="w-full flex-1 overflow-y-auto py-4">
    <?php if (!empty($errorMessage)): ?>
        <div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($errorMessage); ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Form Tambah Penyewaan</h2>
        <form action="/dashboard/rentals/create" method="POST">
            <div class="mb-4">
                <label for="console_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Konsol:</label>
                <select name="console_id" id="console_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">-- Pilih Konsol Tersedia --</option>
                    <?php if (!empty($availableConsoles)): ?>
                        <?php foreach ($availableConsoles as $console): ?>
                            <option 
                                value="<?= htmlspecialchars($console['id']); ?>" 
                                data-hourly-rate="<?= htmlspecialchars($console['category_hourly_rate']); ?>"
                                <?= (isset($oldInput['console_id']) && $oldInput['console_id'] == $console['id']) ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($console['serial_code']); ?> (<?= htmlspecialchars($console['category_name']); ?> - Rp<?= number_format($console['category_hourly_rate'], 0, ',', '.'); ?>/jam)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Tidak ada konsol tersedia</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="customer_name" class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan:</label>
                <input type="text" name="customer_name" id="customer_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($oldInput['customer_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-4">
                <label for="duration_hours" class="block text-gray-700 text-sm font-bold mb-2">Durasi (Jam):</label>
                <input type="number" name="duration_hours" id="duration_hours" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($oldInput['duration_hours'] ?? ''); ?>" min="1" required>
            </div>
            <div class="mb-6">
                <label for="total_cost" class="block text-gray-700 text-sm font-bold mb-2">Total Biaya (Rp):</label>
                <input type="number" name="total_cost" id="total_cost" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($oldInput['total_cost'] ?? ''); ?>" step="0.01" min="0" required readonly>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors duration-200">
                    Tambah Penyewaan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consoleSelect = document.getElementById('console_id');
    const durationInput = document.getElementById('duration_hours');
    const totalCostInput = document.getElementById('total_cost');

    function calculateTotalCost() {
        const selectedOption = consoleSelect.options[consoleSelect.selectedIndex];
        const hourlyRate = parseFloat(selectedOption.dataset.hourlyRate || '0');
        const duration = parseFloat(durationInput.value || '0');

        if (!isNaN(hourlyRate) && !isNaN(duration) && duration > 0) {
            const totalCost = hourlyRate * duration;
            totalCostInput.value = totalCost.toFixed(2); // Format ke 2 desimal
        } else {
            totalCostInput.value = ''; // Kosongkan jika input tidak valid
        }
    }

    // Panggil fungsi saat nilai input berubah
    consoleSelect.addEventListener('change', calculateTotalCost);
    durationInput.addEventListener('input', calculateTotalCost);

    // Panggil fungsi saat halaman pertama kali dimuat (untuk kasus oldInput)
    calculateTotalCost();
});
</script>
