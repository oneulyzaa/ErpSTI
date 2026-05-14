<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterAssetController;
use App\Http\Controllers\Admin\MasterClientController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SalesOrderController;


Route::get('/', function () {
    return view('welcome');
});

// Login
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Logout
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard
Route::middleware('auth')->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // master data aset using MasterAsetController
    Route::resource('master-assets', MasterAssetController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // master data aset using MasterClientController
    Route::resource('master-clients', MasterClientController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // ─── Quotation ───────────────────────────────────────────────
    // Generate PDF for a specific quotation
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
    // Quick-add client via modal
    Route::post('quotations/quick-add-client', [QuotationController::class, 'quickAddClient'])->name('quotations.quick-add-client');
    // CRUD
    Route::resource('quotations', QuotationController::class);

    // ─── Sales Order ─────────────────────────────────────────────
    // Generate PDF for a specific sales order
    Route::get('sales-orders/{salesOrder}/pdf', [SalesOrderController::class, 'pdf'])->name('sales-orders.pdf');
    // Copy from quotation
    Route::get('sales-orders/copy-from-quotation/{quotation}', [SalesOrderController::class, 'copyFromQuotation'])->name('sales-orders.copy-from-quotation');
    // CRUD
    Route::resource('sales-orders', SalesOrderController::class);

    
});