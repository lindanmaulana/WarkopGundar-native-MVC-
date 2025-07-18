<?php
// Contoh deteksi route aktif manual:
function isActive($routeName)
{
    $current = $_GET['page'] ?? 'dashboard';
    return $current === $routeName ? 'text-royal-blue' : 'text-royal-blue/50';
}

function isActiveMobile($routeName)
{
    $current = $_GET['page'] ?? 'dashboard';
    return $current === $routeName ? 'text-white' : 'text-white/50';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem Antrian Rumah Sakit' ?></title>
    <!-- CSS -->
    <link href="/assets/css/output.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-track {
            background-color: #f7fafc;
        }
    </style>
</head>

<body>
    <!-- Main -->
    <main class="bg-soft-blue-gray">
        <section class="w-full flex gap-4">
            <div class="w-full hidden md:max-w-[250px] md:flex h-screen lg:relative translate-x-0 bg-white">
                <div class="relative w-full px-4 py-8">
                    <h2 class="flex items-center gap-2 text-xl font-semibold text-dark-blue">
                        <?php echo x_icon(['name' => 'warkopgundar2', 'class' => 'w-10 h-10']); ?>
                        Warkop Gundar
                    </h2>

                    <ul class="w-full py-14 flex flex-col items-center">
                        <li class="w-full group">
                            <a href="/dashboard" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('dashboard') ?>">
                                🏠 Dashboard
                            </a>
                        </li>

                        <li class="w-full group">
                            <a href="/dashboard/users" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('users') ?>">
                                👥 Users
                            </a>
                        </li>

                        <li class="w-full group">
                            <a href="/dashboard/categories" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('categories') ?>">
                                📂 Category
                            </a>
                        </li>

                        <li class="w-full group">
                            <a href="/dashboard/menu/products" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('products') ?>">
                                🍴 Menu
                            </a>
                        </li>

                        <li class="w-full group">
                            <a href="/dashboard/orders" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('orders') ?>">
                                🛒 Order
                            </a>
                        </li>

                        <li class="w-full group">
                            <a href="/dashboard/payments" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('payments') ?>">
                                💳 Payment
                            </a>
                        </li>

                        <li class="w-full group">
                            <a href="/dashboard/setting" class="w-full flex items-center gap-4 text-lg font-semibold pl-6 py-2 rounded-md group-hover:bg-royal-blue/20 transition-all <?= isActive('setting') ?>">
                                ⚙️ Setting
                            </a>
                        </li>
                    </ul>

                    <div class="w-full absolute bottom-4 left-0 px-4">
                        <form action="/logout" method="POST">
                            <button type="submit" class="w-full flex items-center gap-4 text-base text-red-400 hover:text-red-500 font-semibold pl-6 py-2 rounded-md hover:bg-red-100 transition-all">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Navbar Mobile -->
            <div class="fixed md:hidden bottom-2 translate-x-1/2 right-1/2 w-full max-w-[96%] z-50 mx-auto px-4 bg-royal-blue rounded">
                <ul class="w-full flex items-center justify-between gap-2">
                    <li><a href="?page=dashboard" class="py-2 px-3 <?= isActiveMobile('dashboard') ?>">🏠</a></li>
                    <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                        <li><a href="?page=categories" class="py-2 px-3 <?= isActiveMobile('categories') ?>">📂</a></li>
                    <?php endif; ?>
                    <li><a href="?page=products" class="py-2 px-3 <?= isActiveMobile('products') ?>">🍴</a></li>
                    <li><a href="?page=orders" class="py-2 px-3 <?= isActiveMobile('orders') ?>">🛒</a></li>
                    <li><a href="?page=setting" class="py-2 px-3 <?= isActiveMobile('setting') ?>">⚙️</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col w-full h-screen max-h-screen px-4">
                <div class="w-full h-auto sticky top-0 inset-0 flex items-center justify-between">
                    <?php if (isset($header)) echo $header; ?>
                </div>
                <div class="w-full flex-1 overflow-y-auto py-4">
                    <?php if (isset($content)) echo $content; ?>
                </div>
            </div>
        </section>
    </main>
</body>

</html>