<div class="shadow-md shadow-gray-300 px-4 py-6 w-[400px] min-h-[400px] flex flex-col items-center justify-center rounded-md">
    <h2 class="text-lg font-semibold">Warkop Gundar</h2>
    <form method="POST" action="/register" class="w-full rounded-md py-10 px-8">
        <h3 class="text-xl font-semibold">Wellcome Back!</h3>
        <p class="text-sm text-black/60">Warkop favoritmu, selalu ada untukmu.</p>
        <div class="space-y-6 w-full py-8">
            <div class="space-y-4">
                <label for="name" class="block w-full text-sm">
                    <input type="text" placeholder="name..." name="name" class="w-full outline-none bg-soft-blue-gray border-b active:border-black focus:border-black border-black/50 px-2 pb-2">
                </label>
                <label for="phoone" class="block w-full text-sm">
                    <input type="text" placeholder="08xxx" name="phone" class="w-full outline-none bg-soft-blue-gray border-b active:border-black focus:border-black border-black/50 px-2 pb-2">
                </label>
                <label for="email" class="block w-full text-sm">
                    <input type="email" placeholder="example@gmail.com" name="email" class="w-full outline-none bg-soft-blue-gray border-b active:border-black focus:border-black border-black/50 px-2 pb-2">
                </label>
                <label for="password" class="block w-full text-sm">
                    <input type="password" placeholder="password***" name="password" class="w-full outline-none bg-soft-blue-gray border-b active:border-black focus:border-black border-black/50 px-2 pb-2">
                </label>
                <label for="password-confirmation" class="block w-full text-sm">
                    <input type="password" placeholder="confirm password***" name="password_confirmation" class="w-full outline-none bg-soft-blue-gray border-b active:border-black focus:border-black border-black/50 px-2 pb-2">
                </label>
                <?php if (!empty($message)): ?>
                    <p class="bg-red-200 py-2 text-center text-sm text-red-500 rounded"><?= $error_user; ?></p>
                <?php endif; ?>
            </div>
            <div class="space-y-1">
                <button class="w-full bg-black rounded-md text-white py-2 text-xs font-semibold">Register</button>
                <p class="text-sm text-black/60">Sudah punya akun?, <a href="/auth/login" class="text-royal-blue">Masuk disini</a></p>
            </div>
        </div>
    </form>
</div>