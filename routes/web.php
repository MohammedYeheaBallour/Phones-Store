<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    $products = Product::latest()->take(12)->get();
    $bestSellers = Product::inRandomOrder()->take(4)->get();
    return view('home', compact('products', 'bestSellers'));
});

// Admin Auth
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin routes
Route::middleware(['web', 'ensure.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminProductController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class)->except(['show']);
});

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/api/cart', [CartController::class, 'apiIndex'])->name('cart.api.index');
