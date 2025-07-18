<?php
$orderId = $order['id'];
$paymentName = $order['payment']['name'];
$qrCodeUrl = $order['payment']['qr_code_url'];
$paymentProofExists = !empty($paymentProof);
?>

<section class="mt-24">
    <div class="container max-w-4xl mx-auto min-h-[400px]">
        <div class="w-full h-full grid grid-cols-2 gap-4 py-10">
            <!-- Metode Pembayaran -->
            <div class="h-full bg-peach shadow rounded p-6 space-y-6">
                <label for="" class="block rounded">
                    <span class="block text-lg text-secondary font-semibold">Metode Pembayaran</span>
                    <input type="text" value="<?= htmlspecialchars($paymentName) ?>" class="bg-green-500 text-white border-none outline-none rounded p-2" readonly>
                    <input type="text" id="paymentMethod" value="<?= htmlspecialchars($qrCodeUrl) ?>" hidden>
                </label>

                <figure class="min-h-62 flex items-center justify-center" id="selectedPaymentImage"></figure>
            </div>

            <!-- Upload Bukti -->
            <div class="h-full">
                <form action="/upload/<?= $orderId ?>/payment" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">

                    <?php if ($paymentProofExists): ?>
                        <span class="block text-secondary font-semibold mb-4">Bukti Pembayaran</span>
                        <p class="bg-green-500 p-2 text-white rounded">Pesananmu sudah diproses</p>
                    <?php else: ?>
                        <div class="flex items-center">
                            <div id="image-preview" class="relative w-full min-h-80 flex items-center justify-center rounded-lg overflow-hidden" style="display:none;">
                                <label for="image_url" class="absolute inset-0 flex items-center justify-center cursor-pointer text-center z-10 group"></label>
                                <span class="block text-secondary font-semibold mb-4">Bukti Pembayaran</span>
                                <figure class="rounded-md overflow-hidden h-80">
                                    <img src="/images/qrcode-default.png" id="image-product" alt="" class="w-full h-full object-contain">
                                </figure>
                            </div>

                            <div id="image-upload" class="w-full h-full self-end space-y-2">
                                <span class="block text-dark-blue font-semibold">Upload Bukti Pembayaran</span>
                                <label for="image_url" class="border-2 rounded-lg border-gray-200 flex items-center justify-center gap-1 cursor-pointer bg-peach h-[200px]">
                                    <span class="text-gray-500">Upload Image Here</span>
                                </label>
                            </div>

                            <input type="file" name="image_url" id="image_url" accept="image/*" class="hidden">
                        </div>

                        <button type="submit" class="bg-green-500 w-full py-2 rounded text-white">Kirim Bukti Pembayaran</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    const imagePreview = document.getElementById('image-preview');
    const imageUpload = document.getElementById('image-upload');
    const imageProduct = document.getElementById("image-product");

    document.getElementById('image_url').addEventListener('change', function(event) {
        if (event.target.files && event.target.files[0]) {
            const selectedFile = event.target.files[0];

            if (selectedFile.type.startsWith('image/')) {
                const objUrl = URL.createObjectURL(selectedFile);
                imageProduct.src = objUrl;

                imagePreview.style.display = "block";
                imageUpload.style.display = "none";

                imageProduct.onload = () => {
                    URL.revokeObjectURL(objUrl);
                };
            }
        }
    });

    const paymentMethodSelect = document.getElementById('paymentMethod');
    const displayDiv = document.getElementById('selectedPaymentImage');

    function updateQrCodeImage() {
        const selectedImageUrl = paymentMethodSelect.value;
        if (displayDiv) {
            const img = document.createElement('img');
            img.src = `/storage/${selectedImageUrl}`;
            img.alt = 'QR Code';
            img.classList.add('max-w-[400px]');
            displayDiv.innerHTML = '';
            displayDiv.appendChild(img);
        }
    }

    document.addEventListener('DOMContentLoaded', updateQrCodeImage);
    paymentMethodSelect.addEventListener('change', updateQrCodeImage);
</script>