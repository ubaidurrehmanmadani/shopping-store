<?php

use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('store.home');
Route::get('/categories/{category:slug}', [StorefrontController::class, 'category'])->name('store.categories.show');
Route::get('/products/{product:slug}', [StorefrontController::class, 'product'])->name('store.products.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationNotification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/cart', [CartController::class, 'index'])->name('store.cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('store.cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('store.cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('store.cart.destroy');
Route::get('/checkout', [CartController::class, 'checkout'])->name('store.checkout');

Route::middleware('auth')->group(function (): void {
    Route::post('/checkout', [CartController::class, 'placeOrder'])->middleware('verified')->name('store.checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('store.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('store.orders.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('store.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('store.profile.update');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('products', AdminProductController::class)->except('show');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
