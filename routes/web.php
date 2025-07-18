<?php

require_once __DIR__ . "/../vendor/autoload.php";

use App\Core\Router;
use App\controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\BukuController;
use App\controllers\CategoriesController;
use App\controllers\ConsolesController;
use App\Controllers\DashboardController;
use App\Controllers\OrderController;
use App\Controllers\PaymentController;
use App\Controllers\ProductController;
use App\controllers\RentalsController;
use App\Controllers\UserController;

Router::add('GET', '/', HomeController::class, 'index');
Router::add("GET", "/auth/login", AuthController::class, 'showAuthLogin');
Router::add("GET", "/auth/register", AuthController::class, 'showAuthRegister');
Router::add("POST", "/register", AuthController::class, 'register');
Router::add("POST", "/login", AuthController::class, 'login');
Router::add("POST", "/logout", AuthController::class, 'logout');

Router::add('GET', '/menu', HomeController::class, 'showMenu');
Router::add('GET', '/cart', HomeController::class, 'showCart');
Router::add('GET', '/checkout', HomeController::class, 'showCheckout');
Router::add('GET', '/order', HomeController::class, 'showOrder');
Router::add('GET', '/order/{id}/payment', HomeController::class, 'showPayment');
Router::add('GET', '/order/{id}/detail', HomeController::class, 'showDetailOrder');
Router::add('POST', '/checkout', HomeController::class, 'createOrder');
Router::add('POST', '/upload/{id}/payment', HomeController::class, 'uploadPaymentProof');

// Dashboard

Router::add('GET', '/dashboard', DashboardController::class, 'index');
Router::add("GET", "/dashboard/categories", CategoriesController::class, 'index');
Router::add("GET", "/dashboard/categories/create", CategoriesController::class, 'create');
Router::add("POST", "/categories/create", CategoriesController::class, 'create');
Router::add("GET", "/dashboard/categories/update/{id}", CategoriesController::class, 'edit');
Router::add("POST", "/categories/update/{id}", CategoriesController::class, 'edit');
Router::add("POST", "/categories/delete/{id}", CategoriesController::class, 'delete');

// User Routes
Router::add('GET', '/dashboard/users', UserController::class, 'index');
Router::add('GET', '/dashboard/users/create', UserController::class, 'create');
Router::add('POST', '/users/create', UserController::class, 'create');
Router::add('GET', '/dashboard/users/update/{id}', UserController::class, 'edit');
Router::add('POST', '/users/update/{id}', UserController::class, 'edit');
Router::add('POST', '/users/delete/{id}', UserController::class, 'delete');
Router::add('GET', '/dashboard/users/change-password/{id}', UserController::class, 'changePassword');
Router::add('POST', '/users/change-password/{id}', UserController::class, 'changePassword');
Router::add('GET', '/dashboard/users/api', UserController::class, 'api');

Router::add("GET", "/dashboard/menu/products", ProductController::class, 'index');
Router::add("GET", "/dashboard/menu/products/create", ProductController::class, 'create');
Router::add("POST", "/products/create", ProductController::class, 'store');
Router::add("GET", "/dashboard/menu/products/update/{id}", ProductController::class, 'edit');
Router::add("POST", "/products/update/{id}", ProductController::class, 'update');
Router::add("GET", "/dashboard/menu/products/detail/{id}", ProductController::class, 'show');
Router::add("POST", "/products/delete/{id}", ProductController::class, 'delete');

Router::add("GET", "/dashboard/orders", OrderController::class, 'index');
Router::add("GET", "/dashboard/orders/create", OrderController::class, 'create');
Router::add("POST", "/dashboard/orders/create", OrderController::class, 'create');
Router::add("GET", "/dashboard/orders/update/{id}", OrderController::class, 'edit');
Router::add("POST", "/orders/update/{id}", OrderController::class, 'update');
Router::add("GET", "/dashboard/orders/detail/{id}", OrderController::class, 'show');
Router::add("POST", "/dashboard/orders/delete/{id}", OrderController::class, 'delete');

Router::add("GET", "/dashboard/payments", PaymentController::class, 'index');
Router::add("GET", "/dashboard/payments/create", PaymentController::class, 'create');
Router::add("POST", "/payments/store", PaymentController::class, 'store');
Router::add("GET", "/dashboard/payments/detail/{id}", PaymentController::class, 'show');
Router::add("GET", "/dashboard/payments/update/{id}", PaymentController::class, 'edit');
Router::add("POST", "/payments/update/{id}", PaymentController::class, 'update');
Router::add("GET", "/dashboard/payments/detail/{id}", PaymentController::class, 'show');
Router::add("POST", "/payments/delete/{id}", PaymentController::class, 'destroy');

Router::add("GET", '/dashboard/setting', DashboardController::class, 'showSetting');
Router::add("POST", '/update/profile', DashboardController::class, 'updateProfile');

Router::run();
