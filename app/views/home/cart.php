<section class="pt-28 pb-20 bg-gray-50 min-h-screen">
    <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-gray-200">
                <h2 class="text-3xl font-extrabold text-secondary mb-2 sm:mb-0">Keranjang Belanja Anda</h2>
                <p id="totalItems" class="text-lg font-semibold text-gray-600"></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- CART TABLE -->
                <div class="lg:col-span-2 overflow-x-auto bg-white rounded-xl shadow-lg p-4">
                    <table class="w-full text-left table-auto">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-4 text-left rounded-tl-lg">Produk</th>
                                <th class="py-3 px-4 text-center">Harga</th>
                                <th class="py-3 px-4 text-center">Jumlah</th>
                                <th class="py-3 px-4 text-center">Total</th>
                                <th class="py-3 px-4 text-center rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="text-gray-600 text-sm font-light">
                            <!-- Akan diisi oleh JavaScript -->
                        </tbody>
                    </table>

                    <div id="empty-cart-message" class="hidden text-center py-16 text-gray-500">
                        <p class="mb-6 text-xl font-medium">Keranjang belanjamu masih kosong.</p>
                        <a href="/menu" class="inline-flex items-center px-8 py-3 bg-secondary text-white font-medium rounded-lg hover:bg-secondary/90 transition-colors duration-200 shadow-md">
                            Mulai Belanja Sekarang
                        </a>
                    </div>
                </div>

                <!-- SUMMARY -->
                <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-6 h-fit sticky top-28">
                    <h3 class="text-2xl font-bold text-secondary mb-6 border-b pb-4 border-gray-200">Ringkasan Pesanan</h3>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-gray-700">
                            <span>Biaya Pengiriman</span>
                            <span class="font-medium">Gratis</span>
                        </div>
                        <div class="flex justify-between items-center text-2xl font-bold text-primary border-t pt-4 border-gray-200">
                            <span>Total</span>
                            <span id="total-price">Rp 0</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="/checkout" id="btn-complete-order" class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg text-center hover:bg-green-700 transition-colors duration-200 shadow-md transform hover:scale-105">Lanjutkan ke Pembayaran</a>
                        <a href="/menu" class="w-full border border-secondary text-secondary font-semibold py-3 rounded-lg text-center hover:bg-secondary hover:text-white transition-colors duration-200 transform hover:scale-105">Kembali Belanja</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let cart = JSON.parse(localStorage.getItem('cart')) || []
    const componentTotalItems = document.getElementById('totalItems')

    const showMappingCart = () => {
        const tableBody = document.getElementById('table-body')
        const btnCompleteOrder = document.getElementById('btn-complete-order')
        tableBody.innerHTML = ""

        if (cart.length === 0) {
            btnCompleteOrder.style.display = "none"
            document.getElementById("total-price").innerText = "Rp 0"

            const row = document.createElement('tr')
            row.innerHTML = `<td colspan="5" class="py-4 text-center text-red-500">Keranjang kosong!</td>`
            tableBody.appendChild(row)
            return
        }

        cart.forEach((item, index) => {
            const row = document.createElement('tr')
            console.log({
                item
            })
            row.innerHTML = `
        <td class="py-4 px-4 flex items-center gap-3">
          <img src="${item.image_url ? `/storage.php?file=${item.image_url } ` : '/images/image-placeholder.png' }" alt="${item.productName}" class="w-16 h-16 object-cover rounded-md border border-gray-200">
          <div>
            <h3 class="font-medium text-secondary text-base">${item.productName}</h3>
            <p class="text-xs text-gray-500">${item.category}</p>
          </div>
        </td>
        <td class="py-4 px-4 text-center font-medium text-gray-700">Rp ${item.price}</td>
        <td class="py-4 px-4 text-center">
          <div class="flex items-center justify-center gap-2">
            <button onclick="handleQty(${item.productId}, 'dec')" class="text-primary">-</button>
            <span class="font-semibold text-gray-700">${item.qty}</span>
            <button onclick="handleQty(${item.productId}, 'inc')" class="text-primary">+</button>
          </div>
        </td>
        <td class="py-4 px-4 text-center font-semibold text-secondary">Rp ${item.totalPrice}</td>
        <td class="py-4 px-4 text-center">
          <button onclick="handleDeleteCart(${item.productId})" class="text-red-500">Hapus</button>
        </td>
      `
            tableBody.appendChild(row)
        })

        showTotalPrice()
        btnCompleteOrder.style.display = "block"
    }

    const handleQty = (productId, type) => {
        const index = cart.findIndex(item => item.productId == productId)
        if (index > -1) {
            const item = cart[index]
            if (type === 'inc') {
                item.qty += 1
                item.totalPrice = item.qty * item.price
            } else if (type === 'dec' && item.qty > 1) {
                item.qty -= 1
                item.totalPrice = item.qty * item.price
            }
        }
        localStorage.setItem('cart', JSON.stringify(cart))
        showMappingCart()
        showTotalItems()
    }

    const handleDeleteCart = (productId) => {
        cart = cart.filter(item => item.productId != productId)
        localStorage.setItem('cart', JSON.stringify(cart))
        showMappingCart()
        showTotalItems()
    }

    const showTotalPrice = () => {
        const total = cart.reduce((sum, item) => sum + Number(item.totalPrice), 0)
        document.getElementById("total-price").innerText = "Rp " + total.toLocaleString('id-ID')
    }

    const showTotalItems = () => {
        componentTotalItems.innerText = `${cart.length} Item${cart.length > 1 ? 's' : ''}`
    }

    showMappingCart()
    showTotalItems()
</script>