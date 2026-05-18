<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterAssetController;
use App\Http\Controllers\Admin\MasterClientController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\ProductionController;

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
    // AJAX: get quotation data (items + labors) for auto-load
    Route::get('sales-orders/quotation-data/{quotation}', [SalesOrderController::class, 'getQuotationData'])->name('sales-orders.quotation-data');
    Route::get('sales-orders/client-data/{client}', [SalesOrderController::class, 'getClientData'])->name('sales-orders.client-data');
    // CRUD
    Route::resource('sales-orders', SalesOrderController::class);

    // ─── Delivery Order ────────────────────────────────────────────
    Route::get('delivery-orders/{deliveryOrder}/pdf', [DeliveryOrderController::class, 'pdf'])->name('delivery-orders.pdf');
    Route::get('delivery-orders/so-data/{salesOrder}', [DeliveryOrderController::class, 'getSoData'])->name('delivery-orders.so-data');
    Route::get('delivery-orders/client-data/{client}', [DeliveryOrderController::class, 'getClientData'])->name('delivery-orders.client-data');
    Route::resource('delivery-orders', DeliveryOrderController::class);

    // ─── Production Plan ─────────────────────────────────────────
    Route::get('productions/{production}/pdf', [ProductionController::class, 'pdf'])->name('productions.pdf');
    Route::get('productions/sales-order-items/{salesOrder}', [ProductionController::class, 'getSoItems'])->name('productions.so-items');
    Route::resource('productions', ProductionController::class);

    // ─── Invoice ─────────────────────────────────────────────────
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('invoices/so-data/{salesOrder}', [InvoiceController::class, 'getSoData'])->name('invoices.so-data');
    Route::get('invoices/client-data/{client}', [InvoiceController::class, 'getClientData'])->name('invoices.client-data');
    Route::resource('invoices', InvoiceController::class);

    // ─── Receipt (Tanda Terima) ──────────────────────────────────
    Route::get('receipts/{receipt}/pdf', [ReceiptController::class, 'pdf'])->name('receipts.pdf');
    Route::get('receipts/invoice-data/{invoice}', [ReceiptController::class, 'getInvoiceData'])->name('receipts.invoice-data');
    Route::get('receipts/client-data/{client}', [ReceiptController::class, 'getClientData'])->name('receipts.client-data');
    Route::resource('receipts', ReceiptController::class);
});