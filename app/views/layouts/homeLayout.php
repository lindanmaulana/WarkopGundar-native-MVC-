<?php
$userLoggedIn = isset($_SESSION['user']) && $_SESSION['user'] !== null;


$role = $_SESSION['role'] ?? 'Guest';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warkop Gundar</title>
    <link href="/assets/css/output.css" rel="stylesheet">
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="font-poppins-regular">
    <div class="flex flex-col min-h-screen ">
        <header class="w-full fixed top-0 right-0 z-50 transition-all duration-300 shadow-sm bg-peach">
            <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-5 flex items-center justify-between">
                <a href="/" class="flex-shrink-0">
                    <h2 class="text-primary flex items-center gap-1.5 font-bold text-2xl lg:text-3xl">
                        <?php echo x_icon(['name' => 'warkopgundar2', 'class' => 'size-7 lg:size-8']); ?> Warkop <span class="text-secondary">Gundar</span>
                    </h2>
                </a>

                <div class="hidden lg:flex items-center gap-8">
                    <ul class="flex items-center gap-6 xl:gap-8 font-medium">
                        <li>
                            <a href="/"
                                class="relative text-secondary text-lg hover:text-primary transition-colors duration-200 after:content-[''] after:absolute after:bottom-0 after:left-1/2 after:-translate-x-1/2 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full">Home</a>
                        </li>
                        <li>
                            <a href="/menu"
                                class="relative text-secondary text-lg hover:text-primary transition-colors duration-200
                                after:content-[''] after:absolute after:bottom-0 after:left-1/2 after:-translate-x-1/2 after:w-0 after:h-[2px] after:bg-primary after:transition-all after:duration-300 hover:after:w-full">Our Menu</a>
                        </li>
                    </ul>

                    <div class="relative flex items-center gap-3">
                        <a href="/cart" class="relative text-secondary hover:text-primary transition-colors duration-200">
                            <?php echo x_icon(['name' => 'shopping-cart', 'class' => 'size-6 lg:size-7']); ?>
                            <span id="totalCart" class="absolute -top-1 -right-2 size-5 bg-green-500 rounded-full text-white text-xs font-bold flex items-center justify-center p-0.5 leading-none transform scale-90">
                            </span>
                        </a>
                        <button onclick="handleMenu()" class="focus:outline-none rounded-full transition-all duration-200 hover:bg-gray-100 p-1">
                            <?php echo x_icon(['name' => 'profile', 'class' => 'size-8 lg:size-9 text-secondary cursor-pointer hover:text-secondary/70 transition-colors duration-200']); ?>
                        </button>

                        <ul id="menu" class="absolute top-12 right-0 min-w-44 bg-peach rounded-lg shadow-xl py-2 opacity-0 pointer-events-none transition-all duration-300 transform scale-95 origin-top-right">
                            <li>
                                <a href="/profile" class="block px-4 py-2 text-green-600 hover:bg-peach/80 hover:text-green-800 transition-colors duration-200 text-sm font-medium">Profile</a>
                            </li>
                            <li>
                                <a href="/order" class="block px-4 py-2 text-green-600 hover:bg-peach/80 hover:text-green-800 transition-colors duration-200 text-sm font-medium">Pesanan</a>
                            </li>
                            <li>
                                <form action="/logout" method="post">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-peach/80 hover:text-red-800 transition-colors duration-200 text-sm font-medium">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="lg:hidden flex items-center gap-3">
                    <?php if ($userLoggedIn): ?>
                        <a href="/cart" class="relative text-secondary hover:text-primary transition-colors duration-200">
                            <?php echo x_icon(['name' => 'shopping-cart', 'class' => 'size-6']); ?>
                            <span id="totalCartMobile" class="absolute -top-1 -right-2 size-5 bg-green-500 rounded-full text-white text-xs font-bold flex items-center justify-center p-0.5 leading-none transform scale-90">
                            </span>
                        </a>
                    <?php endif; ?>
                    <button id="mobileMenuButton" class="text-secondary focus:outline-none p-1 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-75 z-40 hidden transition-opacity duration-300 opacity-0"></div>
            <nav id="mobileMenu" class="fixed top-0 right-0 w-64 h-full bg-peach shadow-lg transform translate-x-full transition-transform duration-300 z-50">
                <div class="p-6">
                    <button id="closeMobileMenuButton" class="absolute top-4 right-4 text-secondary hover:text-primary transition-colors duration-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <ul class="flex flex-col gap-6 text-xl font-semibold mt-16">
                        <li>
                            <a href="/" class="text-secondary hover:text-primary transition-colors duration-200 >">Home</a>
                        </li>
                        <li>
                            <a href="#" class="text-secondary hover:text-primary transition-colors duration-200">About Us</a>
                        </li>
                        <li>
                            <a href="/menu" class="text-secondary hover:text-primary transition-colors duration-200 ">Our Menu</a>
                        </li>
                        <?php if ($userLoggedIn): ?>
                            <hr class="border-t border-gray-300 my-4">
                            <li>
                                <a href="/profile" class="text-green-600 hover:text-green-800 transition-colors duration-200">Profile</a>
                            </li>
                            <li>
                                <a href="/order" class="text-green-600 hover:text-green-800 transition-colors duration-200">Pesanan</a>
                            </li>
                            <li>
                                <form action="/logout" method="post">
                                    <button type="submit" class="block w-full text-left text-red-600 hover:text-red-800 transition-colors duration-200">Logout</button>
                                </form>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="flex-1 font-poppins-regular"> <?php
                                                    if (isset($content)) {
                                                        echo $content;
                                                    }
                                                    ?>
        </main>
        <footer>
            <div class="relative w-full py-16 md:py-20 overflow-hidden"
                style="background-image: url('/assets/images/bg-coffe-3.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">

                <span class="absolute inset-0 bg-black/70"></span>

                <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 md:gap-8 lg:gap-16 text-white text-center md:text-left">

                        <div class="flex flex-col items-center md:items-start space-y-4">
                            <h2 data-aos="fade-up" data-aos-duration="1000" class="text-primary flex items-center gap-2 font-bold text-3xl lg:text-4xl">
                                <?php echo x_icon(['name' => 'warkopgundar2', 'class' => 'size-8 lg:size-10']); ?> Warkop <span class="text-secondary">Gundar</span>
                            </h2>
                            <p data-aos="fade-up" data-aos-duration="1100" class="text-white/90 text-sm md:text-base max-w-xs leading-relaxed">
                                Tempat ngopi sederhana di jantung kota. Warkop Gundar hadir untuk jadi tempat istirahat, ngobrol, dan nikmati kopi dengan harga bersahabat.
                            </p>
                        </div>

                        <div class="flex flex-col items-center md:items-start space-y-5">
                            <h3 data-aos="fade-up" data-aos-duration="1000" class="relative text-2xl font-bold pb-3">
                                HUBUNGI KAMI
                                <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-16 h-1.5 bg-primary rounded lg:left-0 lg:transform-none"></span>
                            </h3>
                            <div data-aos="fade-up" data-aos-duration="1100" class="text-white/90 text-sm md:text-base font-light space-y-2">
                                <p>Gedung Pajak Sudirman<br>Jl. Jend. Sudirman Kav. 56, Senayan, Jakarta Selatan</p>
                                <div class="flex items-center gap-3">
                                    <?php echo x_icon(['name' => 'phone', 'class' => 'size-5 text-primary']); ?>
                                    <span>+62 878-7865-9892</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <?php echo x_icon(['name' => 'mail', 'class' => 'size-5 text-primary']); ?>
                                    <span>linmidofficial@gmail.com</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center md:items-start space-y-5">
                            <h3 data-aos="fade-up" data-aos-duration="1000" class="relative text-2xl font-bold pb-3">
                                IKUTI KAMI
                                <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-16 h-1.5 bg-primary rounded lg:left-0 lg:transform-none"></span>
                            </h3>
                            <div data-aos="fade-up" data-aos-duration="1100" class="flex gap-4">
                                <a href="https://facebook.com" target="_blank" class="text-white/80 hover:text-primary transition-colors duration-200">
                                    <?php echo x_icon(['name' => 'facebook', 'class' => 'size-8']); ?>
                                </a>
                                <a href="https://twitter.com" target="_blank" class="text-white/80 hover:text-primary transition-colors duration-200">
                                    <?php echo x_icon(['name' => 'twitter', 'class' => 'size-8']); ?>
                                </a>
                                <a href="https://instagram.com" target="_blank" class="text-white/80 hover:text-primary transition-colors duration-200">
                                    <?php echo x_icon(['name' => 'instagram', 'class' => 'size-8']); ?>
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="border-t border-white/20 mt-12 pt-8 text-center">
                        <p class="text-white/70 text-sm">© 2025 WarkopGundar. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.rawgit.com/michalsnik/aos/2.3.4/dist/aos.js"></script>

    <script>
        AOS.init(); // Initialize AOS library
    </script>
    <script>
        const Swall = Swal;

        let cartLocalStorage = localStorage.getItem('cart');
        let cart = cartLocalStorage ? JSON.parse(cartLocalStorage) : [];

        console.log({
            cart
        })

        const componentTotalCart = document.getElementById('totalCart');
        const componentTotalCartMobile = document.getElementById('totalCartMobile');

        function handleAddToCart(buttonElement) {
            const userId = buttonElement.dataset.userId;
            const productId = buttonElement.dataset.productId;
            const productName = buttonElement.dataset.productName;
            const productPrice = parseFloat(buttonElement.dataset.productPrice);
            const productImage = buttonElement.dataset.productImage;
            const productCategory = buttonElement.dataset.productCategory
            console.log({
                productCategory
            })

            const exisItem = cart.findIndex(item => item.userId === userId && item.productId === productId);

            if (exisItem > -1) {
                cart[exisItem].qty += 1;
                cart[exisItem].totalPrice += productPrice;
            } else {
                cart.push({
                    userId,
                    productId,
                    productName,
                    price: productPrice,
                    totalPrice: productPrice,
                    image_url: productImage,
                    category: productCategory,
                    qty: 1
                });
            }

            Swall.fire({
                title: "Berhasil!",
                text: `Menu ${productName} telah ditambahkan ke keranjang.`,
                icon: "success"
            });

            mainLocalStorage();
            showTotalCart();
        }

        const showTotalCart = () => {
            if (cart.length === 0) {
                if (componentTotalCart) componentTotalCart.style.display = "none";
                if (componentTotalCartMobile) componentTotalCartMobile.style.display = "none";
            } else {
                if (componentTotalCart) {
                    componentTotalCart.style.display = "flex"; // or "block"
                    componentTotalCart.innerHTML = cart.length;
                }
                if (componentTotalCartMobile) {
                    componentTotalCartMobile.style.display = "flex"; // or "block"
                    componentTotalCartMobile.innerHTML = cart.length;
                }
            }
        };

        function mainLocalStorage() {
            const cartNew = JSON.stringify(cart);
            localStorage.setItem('cart', cartNew);
        }

        const componentMenu = document.getElementById('menu');
        if (componentMenu) { // Ensure menu exists before trying to manipulate its classes
            componentMenu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
        }


        const handleMenu = () => {
            if (!componentMenu) return; // Exit if menu element not found

            if (componentMenu.classList.contains('opacity-0')) {
                componentMenu.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
                componentMenu.classList.add('opacity-100', 'scale-100');
            } else {
                componentMenu.classList.remove('opacity-100', 'scale-100');
                componentMenu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
            }
        };

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const closeMobileMenuButton = document.getElementById('closeMobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

        if (mobileMenuButton && mobileMenu && mobileMenuOverlay && closeMobileMenuButton) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('translate-x-full');
                mobileMenu.classList.add('translate-x-0');
                mobileMenuOverlay.classList.remove('hidden', 'opacity-0');
                mobileMenuOverlay.classList.add('block', 'opacity-100');
            });

            closeMobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.remove('translate-x-0');
                mobileMenu.classList.add('translate-x-full');
                mobileMenuOverlay.classList.remove('block', 'opacity-100');
                mobileMenuOverlay.classList.add('hidden', 'opacity-0');
            });

            mobileMenuOverlay.addEventListener('click', () => {
                mobileMenu.classList.remove('translate-x-0');
                mobileMenu.classList.add('translate-x-full');
                mobileMenuOverlay.classList.remove('block', 'opacity-100');
                mobileMenuOverlay.classList.add('hidden', 'opacity-0');
            });
        }


        document.addEventListener('click', (event) => {
            const profileButton = document.querySelector('button[onclick="handleMenu()"]');

            if (
                componentMenu &&
                !componentMenu.contains(event.target) &&
                profileButton && !profileButton.contains(event.target)
            ) {
                componentMenu.classList.remove('opacity-100', 'scale-100');
                componentMenu.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
            }
        });

        // Initial call to show total cart count on page load
        showTotalCart();
    </script>
</body>

</html>