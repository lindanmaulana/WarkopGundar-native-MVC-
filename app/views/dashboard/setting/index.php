<div class="mt-10 mb-4">
    <h2 class="text-3xl font-semibold text-royal-blue">Pengaturan Profile</h2>
    <p class="text-dark-blue mt-1">Perbarui informasi akun seperti nama atau data profil lainnya.</p>
</div>

<div class="">
    <h2 class="text-lg font-semibold text-dark-blue">Profile Setting</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div id="alert" class="bg-green-200 rounded p-4">
            <p class="text-green-700 font-semibold">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </p>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="w-full flex items-center gap-6 py-4">
        <form action="/update/profile" method="POST" class="w-full space-y-6">
            <div class="space-y-3">
                <!-- Nama -->
                <label for="name" class="flex flex-col gap-3">
                    <span class="text-dark-blue font-semibold">Nama:</span>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                        class="w-full border-2 border-dark-blue/20 px-4 py-1 rounded-sm">
                </label>
                <?php if (!empty($_SESSION['errors']['name'])): ?>
                    <p class="text-red-500 text-xs italic mt-1"><?= $_SESSION['errors']['name'] ?></p>
                <?php endif; ?>

                <!-- Email -->
                <label for="email" class="flex flex-col gap-3">
                    <span class="text-dark-blue font-semibold">Email:</span>
                    <input
                        type="text"
                        id="email"
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        readonly
                        class="w-full border-2 border-dark-blue/20 px-4 text-dark-blue/70 py-1 rounded-sm">
                </label>
            </div>

            <!-- Tombol -->
            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="px-4 py-1 rounded cursor-pointer bg-royal-blue text-white font-semibold text-sm">Update</button>
                <button type="reset" class="px-4 py-1 rounded cursor-pointer bg-red-500 text-white font-semibold text-sm">Batal</button>
            </div>
        </form>
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