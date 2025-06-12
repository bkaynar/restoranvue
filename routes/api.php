<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

// Table Routes
Route::prefix('tables')->name('tables.')->group(function () {
    Route::get('/', [TableController::class, 'index'])->name('index');
    Route::get('/list', [TableController::class, 'list'])->name('list');
    Route::get('/create', [TableController::class, 'create'])->name('create');
    Route::post('/', [TableController::class, 'store'])->name('store');
    Route::get('/{table}', [TableController::class, 'show'])->name('show');
    Route::get('/{table}/detail', [TableController::class, 'detail'])->name('detail');
    Route::get('/{table}/edit', [TableController::class, 'edit'])->name('edit');
    Route::put('/{table}', [TableController::class, 'update'])->name('update');
    Route::delete('/{table}', [TableController::class, 'destroy'])->name('destroy');
});

// Category Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/list', [CategoryController::class, 'list'])->name('list');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
    Route::get('/{category}/products', [CategoryController::class, 'products'])->name('products');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::put('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
});

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/list', [ProductController::class, 'list'])->name('list');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/detail', [ProductController::class, 'detail'])->name('detail');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    Route::put('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
});

// Order Routes
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/active', [OrderController::class, 'activeOrders'])->name('active');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::post('/{order}/items', [OrderController::class, 'addItem'])->name('add-item');
    Route::put('/items/{orderItem}', [OrderController::class, 'updateItem'])->name('update-item');
    Route::delete('/items/{orderItem}', [OrderController::class, 'removeItem'])->name('remove-item');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/detail', [OrderController::class, 'detail'])->name('detail');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::put('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});

// Payment Routes
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/create/{order}', [PaymentController::class, 'create'])->name('create');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
    Route::get('/{payment}/detail', [PaymentController::class, 'detail'])->name('detail');
    Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
    Route::put('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
});
