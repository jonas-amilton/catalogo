<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/products', fn() => view('products.index'))->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/show', [CartController::class, 'show'])->name('cart.show');
    Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.index');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('products', fn() => view('admin.products.index'))->name('products.index');
    Route::get('products/create', fn() => view('admin.products.create'))->name('products.create');
    Route::get('products/{product}/edit', fn(\App\Models\Product $product) => view('admin.products.edit', compact('product')))->name('products.edit');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
});

require __DIR__ . '/auth.php';