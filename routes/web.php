<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RepackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOpnameController;
use Illuminate\Support\Facades\Route;

// Redirect Home
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('pos.index');
    }

    return redirect()->route('login');
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rute Kasir & POS (Admin & Kasir)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    // Sesi Cash Register
    Route::prefix('cash-register')->name('cash-register.')->group(function () {
        Route::get('/open', [CashRegisterController::class, 'openForm'])->name('open-form');
        Route::post('/open', [CashRegisterController::class, 'open'])->name('open');
        Route::get('/close', [CashRegisterController::class, 'closeForm'])->name('close-form');
        Route::post('/close', [CashRegisterController::class, 'close'])->name('close');
        Route::get('/{id}/report', [CashRegisterController::class, 'shiftReport'])->name('shift-report');
    });

    // POS Layar Kasir
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::post('/hold', [PosController::class, 'holdBill'])->name('hold');
        Route::post('/recall/{id}', [PosController::class, 'recallBill'])->name('recall');
        Route::get('/receipt/{id}', [PosController::class, 'printReceipt'])->name('receipt');
    });
});

// Rute Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Produk & Kategori
    Route::resource('products', ProductController::class)->except(['show', 'destroy']);
    Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleActive'])->name('products.toggle');

    // Repack Karung -> Eceran
    Route::get('/repack', [RepackController::class, 'index'])->name('repack.index');
    Route::post('/repack', [RepackController::class, 'store'])->name('repack.store');

    // Penerimaan Barang (Stock In & FIFO Batch)
    Route::get('/stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::post('/stock-in', [StockInController::class, 'store'])->name('stock-in.store');

    // Audit Mutasi & Stock Opname
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::get('/stock-opname/excel', [StockOpnameController::class, 'exportExcel'])->name('stock-opname.excel');
    Route::post('/stock-opname/adjust', [StockOpnameController::class, 'adjust'])->name('stock-opname.adjust');

    // Laporan Penjualan & Laba Rugi
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/sales/excel', [ReportController::class, 'exportExcel'])->name('reports.sales.excel');
    Route::get('/reports/sales/pdf', [ReportController::class, 'exportPdf'])->name('reports.sales.pdf');
});
