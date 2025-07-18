<!-- Header Section -->
<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Manajemen Pengguna</h2>
    <p class="text-dark-blue mt-1">Kelola data pengguna yang memiliki akses ke sistem, termasuk admin dan staf yang bertugas.</p>
</div>

<!-- Content Section -->
<div class="space-y-4">
    <div class="p-2 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-royal-blue">Daftar Pengguna</h2>
        <div class="flex items-center gap-4">
            <input id="filter-search" type="text" placeholder="Cari..." class="border border-dark-blue/20 rounded-lg px-4 py-1">
        </div>
    </div>

    <!-- Success Message -->
    <?php if (!empty($success)): ?>
        <div id="alert-success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <p class="text-green-700">
                <strong class="bold">Success!</strong> <?= htmlspecialchars($success) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="bg-white p-2 rounded-lg shadow-md shadow-dark-blue/10 py-4">
        <table class="w-full text-left rounded-md overflow-hidden">
            <thead class="*:text-gray-400 *:border-b *:border-dark-blue/10">
                <tr>
                    <th class="font-normal py-2 px-6">No</th>
                    <th class="font-normal p-2">Nama</th>
                    <th class="font-normal p-2">Email</th>
                    <th class="font-normal p-2">Role</th>
                    <th class="font-normal p-2">Status Akun</th>
                    <th class="font-normal p-2">Tgl Daftar</th>
                    <th class="font-normal p-2">Aksi</th>
                </tr>
            </thead>
            <tbody id="user-content">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr class="border-b border-dark-blue/10 hover:bg-gray-50">
                            <td class="py-3 px-6"><?= $index + 1 ?></td>
                            <td class="p-2 font-medium"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="p-2 text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                </span>
                            </td>
                            <td class="p-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['is_email_verified'] == '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $user['is_email_verified'] == '1' ? 'Terverifikasi' : 'Belum Verifikasi' ?>
                                </span>
                            </td>
                            <td class="p-2 text-gray-600">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="p-2">
                                <div class="flex items-center gap-2">
                                    <a href="/dashboard/users/edit/<?= $user['id'] ?>"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            Tidak ada data pengguna ditemukan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination (Static for now, can be made dynamic) -->
        <div class="flex items-center justify-between py-6 px-4">
            <div class="flex items-center gap-2">
                <select name="" id="filter-limit" class="border border-dark-blue/20 rounded-md px-3 py-1 text-sm font-semibold">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <p class="text-sm font-semibold opacity-50">data per halaman</p>
            </div>

            <div id="filter-page" class="flex items-center gap-2">
                <button class="px-3 py-1 border border-dark-blue/20 rounded-md text-sm font-medium hover:bg-gray-50">
                    Sebelumnya
                </button>
                <span class="px-3 py-1 bg-royal-blue text-white rounded-md text-sm font-medium">1</span>
                <button class="px-3 py-1 border border-dark-blue/20 rounded-md text-sm font-medium hover:bg-gray-50">
                    Selanjutnya
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Search and Pagination -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('filter-search');
        const limitSelect = document.getElementById('filter-limit');
        let currentPage = 1;
        let currentLimit = 10;
        let currentSearch = '';

        // Auto-hide success message after 5 seconds
        const successAlert = document.getElementById('alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 5000);
        }

        // Search functionality
        searchInput.addEventListener('input', function() {
            currentSearch = this.value.toLowerCase();
            filterTable();
        });

        // Limit change functionality
        limitSelect.addEventListener('change', function() {
            currentLimit = parseInt(this.value);
            currentPage = 1;
            // In a real implementation, you would reload the page or make an AJAX call
            // For now, we'll just filter the visible table
            filterTable();
        });

        function filterTable() {
            const tbody = document.getElementById('user-content');
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const nameCell = row.getElementsByTagName('td')[1];
                const emailCell = row.getElementsByTagName('td')[2];

                if (nameCell && emailCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const email = emailCell.textContent.toLowerCase();

                    if (name.includes(currentSearch) || email.includes(currentSearch)) {
                        if (visibleCount < currentLimit) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        }
    });
</script>