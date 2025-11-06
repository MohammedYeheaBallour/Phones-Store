<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NewsletterController;

Route::get('/', function () {
    $products = Product::latest()->take(12)->get();
    $bestSellers = Product::inRandomOrder()->take(4)->get();
    return view('home', compact('products', 'bestSellers'));
});

// Favorites page
Route::get('/favorites', function () {
    return view('favorites.index');
})->name('favorites.index');

// Account page
Route::get('/account', function () {
    return view('account.profile');
})->name('account.profile');

// Account edit
Route::get('/account/edit', function () {
    return view('account.edit');
})->name('account.edit');

Route::post('/account/update', [\App\Http\Controllers\AccountController::class, 'update'])->name('account.update');
Route::post('/account/password', [\App\Http\Controllers\AccountController::class, 'updatePassword'])->name('account.password.update');

// Favorites API for authenticated users
Route::get('/api/user/favorites', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('api.user.favorites.index');
Route::post('/api/user/favorites', [\App\Http\Controllers\FavoriteController::class, 'store'])->name('api.user.favorites.store');
Route::delete('/api/user/favorites/{product}', [\App\Http\Controllers\FavoriteController::class, 'destroy'])->name('api.user.favorites.destroy');

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

// Reviews API and submission
Route::get('/api/reviews', [ReviewController::class, 'apiIndex'])->name('reviews.api.index');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Newsletter subscription
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
