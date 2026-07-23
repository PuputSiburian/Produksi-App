<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\ProduksiCuttingController;
use App\Http\Controllers\ProduksiCrimpingController;
use App\Http\Controllers\ProduksiLineController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Produk (Kelola Produk)
|--------------------------------------------------------------------------
*/
Route::resource('produk', ProdukController::class);

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Route Test
|--------------------------------------------------------------------------
*/
Route::get('/test-route', function() {
    return 'Route bekerja dengan baik!';
});

/*
|--------------------------------------------------------------------------
| Route untuk Semua User yang Login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Halaman Index (melihat data)
    Route::get('/produksi-cutting', [ProduksiCuttingController::class, 'index'])->name('produksi-cutting.index');
    Route::get('/produksi-crimping', [ProduksiCrimpingController::class, 'index'])->name('produksi-crimping.index');
    Route::get('/produksi-line', [ProduksiLineController::class, 'index'])->name('produksi-line.index');
    
    // ============================================================
    // ========== EXPORT CUTTING ==========
    // ============================================================
    // Export Semua Data
    Route::get('/produksi-cutting/export/pdf', [ProduksiCuttingController::class, 'exportPdf'])->name('produksi-cutting.export.pdf');
    
    // Export Harian & Mingguan (Halaman Filter)
    Route::get('/produksi-cutting/export', [ProduksiCuttingController::class, 'exportPage'])->name('produksi-cutting.export.page');
    Route::get('/produksi-cutting/export/harian', [ProduksiCuttingController::class, 'exportHarian'])->name('produksi-cutting.export.harian');
    Route::get('/produksi-cutting/export/mingguan', [ProduksiCuttingController::class, 'exportMingguan'])->name('produksi-cutting.export.mingguan');
    
    // Export Mingguan (Lama - untuk kompatibilitas)
    Route::get('/produksi-cutting-mingguan', [ProduksiCuttingController::class, 'exportWeekly'])->name('produksi-cutting.mingguan');
    Route::get('/produksi-cutting-mingguan-download', [ProduksiCuttingController::class, 'downloadWeeklyPDF'])->name('produksi-cutting.mingguan.download');
    // ========== END EXPORT CUTTING ==========
    
    // ============================================================
    // ========== EXPORT CRIMPING ==========
    // ============================================================
    // Export Semua Data
    Route::get('/produksi-crimping/export/pdf', [ProduksiCrimpingController::class, 'exportPdf'])->name('produksi-crimping.export.pdf');
    
    // Export Harian & Mingguan (Halaman Filter)
    Route::get('/produksi-crimping/export', [ProduksiCrimpingController::class, 'exportPage'])->name('produksi-crimping.export.page');
    Route::get('/produksi-crimping/export/harian', [ProduksiCrimpingController::class, 'exportHarian'])->name('produksi-crimping.export.harian');
    Route::get('/produksi-crimping/export/mingguan', [ProduksiCrimpingController::class, 'exportMingguan'])->name('produksi-crimping.export.mingguan');
    
    // Export Mingguan (Lama - untuk kompatibilitas)
    Route::get('/produksi-crimping-mingguan', [ProduksiCrimpingController::class, 'exportWeekly'])->name('produksi-crimping.mingguan');
    Route::get('/produksi-crimping-mingguan-download', [ProduksiCrimpingController::class, 'downloadWeeklyPDF'])->name('produksi-crimping.mingguan.download');
    // ========== END EXPORT CRIMPING ==========
    
    // ============================================================
    // ========== EXPORT LINE ==========
    // ============================================================
    // Export Semua Data
    Route::get('/produksi-line/export/pdf', [ProduksiLineController::class, 'exportPdf'])->name('produksi-line.export.pdf');
    
    // Export Harian & Mingguan (Halaman Filter)
    Route::get('/produksi-line/export', [ProduksiLineController::class, 'exportPage'])->name('produksi-line.export.page');
    Route::get('/produksi-line/export/harian', [ProduksiLineController::class, 'exportHarian'])->name('produksi-line.export.harian');
    Route::get('/produksi-line/export/mingguan', [ProduksiLineController::class, 'exportMingguan'])->name('produksi-line.export.mingguan');
    
    // Export Mingguan (Lama - untuk kompatibilitas)
    Route::get('/produksi-line-mingguan', [ProduksiLineController::class, 'exportWeekly'])->name('produksi-line.mingguan');
    Route::get('/produksi-line-mingguan-download', [ProduksiLineController::class, 'downloadWeeklyPDF'])->name('produksi-line.mingguan.download');
    // ========== END EXPORT LINE ==========
    
    // MESIN - CRUD lengkap dengan resource
    Route::resource('mesin', MesinController::class);
    
    // ========== ROUTE MANAGER ==========
    // Dashboard Manager
    Route::get('/manager-dashboard', [ManagerDashboardController::class, 'index'])->name('manager.dashboard');
    
    // Export PDF Manager
    Route::get('/manager-export-pdf', [ManagerDashboardController::class, 'exportPdf'])->name('manager.export.pdf');
    // ========== END ROUTE MANAGER ==========
    
    // ========== KELOLA USER (MANAGE USERS) ==========
    Route::resource('users', UserController::class);
    Route::get('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
});

/*
|--------------------------------------------------------------------------
| Route Khusus Admin (Create, Edit, Delete) untuk Produksi
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // PRODUKSI CUTTING
    Route::get('/produksi-cutting/create', [ProduksiCuttingController::class, 'create'])->name('produksi-cutting.create');
    Route::post('/produksi-cutting', [ProduksiCuttingController::class, 'store'])->name('produksi-cutting.store');
    Route::get('/produksi-cutting/{produksi_cutting}/edit', [ProduksiCuttingController::class, 'edit'])->name('produksi-cutting.edit');
    Route::put('/produksi-cutting/{produksi_cutting}', [ProduksiCuttingController::class, 'update'])->name('produksi-cutting.update');
    Route::delete('/produksi-cutting/{produksi_cutting}', [ProduksiCuttingController::class, 'destroy'])->name('produksi-cutting.destroy');
    Route::get('/produksi-cutting/{produksi_cutting}/history', [ProduksiCuttingController::class, 'history'])->name('produksi-cutting.history');
    
    // PRODUKSI CRIMPING
    Route::get('/produksi-crimping/create', [ProduksiCrimpingController::class, 'create'])->name('produksi-crimping.create');
    Route::post('/produksi-crimping', [ProduksiCrimpingController::class, 'store'])->name('produksi-crimping.store');
    Route::get('/produksi-crimping/{produksi_crimping}/edit', [ProduksiCrimpingController::class, 'edit'])->name('produksi-crimping.edit');
    Route::put('/produksi-crimping/{produksi_crimping}', [ProduksiCrimpingController::class, 'update'])->name('produksi-crimping.update');
    Route::delete('/produksi-crimping/{produksi_crimping}', [ProduksiCrimpingController::class, 'destroy'])->name('produksi-crimping.destroy');
    Route::get('/produksi-crimping/{produksi_crimping}/history', [ProduksiCrimpingController::class, 'history'])->name('produksi-crimping.history');
    
    // PRODUKSI LINE
    Route::get('/produksi-line/create', [ProduksiLineController::class, 'create'])->name('produksi-line.create');
    Route::post('/produksi-line', [ProduksiLineController::class, 'store'])->name('produksi-line.store');
    Route::get('/produksi-line/{produksi_line}/edit', [ProduksiLineController::class, 'edit'])->name('produksi-line.edit');
    Route::put('/produksi-line/{produksi_line}', [ProduksiLineController::class, 'update'])->name('produksi-line.update');
    Route::delete('/produksi-line/{produksi_line}', [ProduksiLineController::class, 'destroy'])->name('produksi-line.destroy');
    Route::get('/produksi-line/{produksi_line}/history', [ProduksiLineController::class, 'history'])->name('produksi-line.history');
    
    // MESIN
    Route::get('/mesin/create', [MesinController::class, 'create'])->name('mesin.create');
    Route::post('/mesin', [MesinController::class, 'store'])->name('mesin.store');
    Route::get('/mesin/{mesin}/edit', [MesinController::class, 'edit'])->name('mesin.edit');
    Route::put('/mesin/{mesin}', [MesinController::class, 'update'])->name('mesin.update');
    Route::delete('/mesin/{mesin}', [MesinController::class, 'destroy'])->name('mesin.destroy');
});