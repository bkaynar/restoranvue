<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    $user = Auth::user()->load('roles');

    // Gerçek veriler
    $totalTables = \App\Models\Table::count();
    $occupiedTables = \App\Models\Table::whereHas('activeOrder')->count();
    $activeOrders = \App\Models\Order::whereNotIn('status', ['kapandı', 'ödendi', 'iptal'])->count();
    $dailyRevenue = \App\Models\Payment::whereDate('created_at', today())->where('status', 'ödendi')->sum('amount');
    $lastOrders = \App\Models\Order::with('table')->orderBy('created_at', 'desc')->take(5)->get();

    return Inertia::render('Dashboard', [
        'auth' => [
            'user' => $user,
        ],
        'stats' => [
            'totalTables' => $totalTables,
            'occupiedTables' => $occupiedTables,
            'activeOrders' => $activeOrders,
            'dailyRevenue' => $dailyRevenue,
        ],
        'lastOrders' => $lastOrders,
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

    // Sipariş Yönetimi Rotaları
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    // Sipariş ürün işlemleri (isteğe bağlı, API tarzı)
    Route::post('orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
    Route::put('order-items/{orderItem}', [OrderController::class, 'updateItem'])->name('orders.items.update');
    Route::delete('order-items/{orderItem}', [OrderController::class, 'removeItem'])->name('orders.items.remove');

    // Ödeme Yönetimi Rotaları
    Route::get('payments/create', [PaymentController::class, 'quickPayment'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
