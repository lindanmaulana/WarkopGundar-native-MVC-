<section class="pt-28 pb-20 bg-gray-50 min-h-screen">
    <div class="container max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-8">
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h2 class="text-3xl font-extrabold text-secondary">Checkout</h2>
                <a href="/menu" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg shadow hover:bg-secondary/90 transition-colors duration-200">
                    ← <span>Kembali Belanja</span>
                </a>
            </div>

            <div class="w-full flex flex-col gap-6">
                <div class="bg-white p-6 rounded-xl shadow-lg space-y-3">
                    <h3 class="text-xl font-bold text-secondary mb-2">Informasi Akun</h3>
                    <label for="account_email" class="block text-secondary text-sm font-medium mb-1">Email:</label>
                    <input type="text" id="account_email"
                        value="<?= htmlspecialchars($_SESSION['email'] ?? 'user@example.com') ?>"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-700 text-sm cursor-not-allowed"
                        readonly>
                    <p class="mt-2 text-xs text-gray-500">Pastikan email yang Anda masukkan benar.</p>
                </div>

                <div id="checkout-form-container">
                    <form onsubmit="handleSubmit(event)" class="space-y-6">
                        <div class="w-full bg-white space-y-5 p-6 shadow-lg rounded-xl">
                            <h3 class="text-xl font-bold text-secondary mb-2">Informasi Pembeli & Pengiriman</h3>

                            <label class="flex flex-col gap-2">
                                <span class="text-secondary text-sm font-medium">Nama:</span>
                                <input type="text" name="customer_name"
                                    value="<?= htmlspecialchars($_SESSION['name'] ?? 'Nama Pelanggan') ?>"
                                    class="w-full border border-gray-300 text-gray-800 px-4 py-2 rounded-lg"
                                    readonly>
                            </label>

                            <label class="flex flex-col gap-2">
                                <span class="text-secondary text-sm font-medium">Lokasi Warkop:</span>
                                <select name="branch" id="branch" class="w-full border border-gray-300 text-gray-800 px-4 py-2 rounded-lg">
                                    <option value="wg-sudirman">WG-Sudirman</option>
                                    <option value="wg-tebet">WG-Tebet</option>
                                    <option value="wg-depok">WG-Depok</option>
                                </select>
                            </label>

                            <label class="flex flex-col gap-2">
                                <span class="text-secondary text-sm font-medium">Lokasi Pengantaran:</span>
                                <input type="text" name="delivery_location" placeholder="Cth: Lantai 5, Depan Lift"
                                    class="w-full border border-gray-300 text-gray-800 px-4 py-2 rounded-lg" required>
                            </label>

                            <label class="flex flex-col gap-2">
                                <span class="text-secondary text-sm font-medium">Tipe Pembayaran:</span>
                                <?php if (!empty($paymentsMethod)) : ?>
                                    <select name="payment_id" id="payment_id" class="w-full border border-gray-300 px-4 py-2 rounded-lg">
                                        <?php foreach ($paymentsMethod as $payment) : ?>
                                            <option value="<?= $payment['id'] ?>"><?= htmlspecialchars($payment['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input type="text" disabled value="Belum ada metode pembayaran aktif untuk saat ini."
                                        class="text-red-500 bg-gray-100 px-4 py-2 rounded-lg border border-gray-300 cursor-not-allowed" />
                                <?php endif; ?>
                            </label>

                            <label class="flex flex-col gap-2">
                                <span class="text-secondary text-sm font-medium">Catatan Tambahan:</span>
                                <textarea name="description" rows="3"
                                    class="w-full border border-gray-300 text-gray-800 px-4 py-2 rounded-lg"></textarea>
                            </label>
                        </div>

                        <div class="w-full bg-white p-6 rounded-xl shadow-lg space-y-4">
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h3 class="text-xl font-bold text-secondary">Rincian Pesanan</h3>
                                <ul id="order-list" class="space-y-3 pt-4"></ul>
                            </div>
                            <div>
                                <div class="flex items-center justify-between text-secondary">
                                    <h4 class="text-lg font-semibold">Total Pembayaran</h4>
                                    <p class="font-bold text-3xl">Rp <span id="total-price">0</span></p>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition">
                            Pesan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');

    const mappingCart = () => {
        const orderList = document.getElementById('order-list')
        orderList.innerHTML = ""

        cart.forEach(item => {
            const row = document.createElement('li')
            const totalPrice = Intl.NumberFormat('id-ID', {
                currency: "idr",
                maximumFractionDigits: 0
            }).format(item.totalPrice)

            row.innerHTML = `
                <div class="flex items-center justify-between *:text-dark-blue/80">
                    <h4>${item.productName}</h4>
                    <p>Rp ${totalPrice}</p>
                </div>
            `

            orderList.appendChild(row)
        });

        showTotalPrice()
    }

    const showTotalPrice = () => {
        let total = 0
        const totalPrice = document.getElementById("total-price")
        cart.forEach(item => total += Number(item.totalPrice))

        totalPrice.innerHTML = Intl.NumberFormat('id-ID', {
            currency: "idr",
            maximumFractionDigits: 0
        }).format(total)
    }

    const handleSubmit = async (e) => {
        e.preventDefault()
        const form = e.target

        const customer_information = {
            delivery_location: form.delivery_location.value,
            branch: form.branch.value,
            payment_id: form.payment_id.value,
            description: form.description.value
        }

        const orders = {
            cart: cart,
            customer_information
        }

        try {
            const response = await fetch('/checkout', {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(orders)
            })

            const result = await response.json()

            if (!response.ok) throw result

            alert('Pesanan berhasil dibuat!')
            localStorage.removeItem('cart')
            window.location.href = `/order/${result.order_id}/payment`
        } catch (err) {
            alert('Gagal membuat pesanan: ' + err.error)
        }
    }

    mappingCart()
</script>