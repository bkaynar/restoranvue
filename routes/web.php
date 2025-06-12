<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    $user = Auth::user()->load('roles'); // rolleri eager load ettik

    return Inertia::render('Dashboard', [
        'auth' => [
            'user' => $user,
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Masa ve Kategori Yönetimi Rotaları
Route::middleware(['auth', 'verified'])->group(function () {
    // Masa Yönetimi Rotaları
    Route::get('tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('tables/create', [TableController::class, 'create'])->name('tables.create');
    Route::post('tables', [TableController::class, 'store'])->name('tables.store');
    Route::get('tables/{table}', [TableController::class, 'show'])->name('tables.show');
    Route::get('tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
    Route::put('tables/{table}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');

    // Kategori Yönetimi Rotaları
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');

    // Ürün Yönetimi Rotaları
    Route::get('products', [ProductController::class, 'byCategory'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
