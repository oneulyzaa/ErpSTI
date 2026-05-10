<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterAssetController;
use App\Http\Controllers\Admin\MasterClientController;
use App\Http\Controllers\QuotationController;


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

    // Generate PDF for a specific quotation
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])
    ->name('quotations.pdf');
    
    //quotation
    Route::resource('quotations', QuotationController::class);

    
});