<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CsController;
use App\Http\Controllers\SloController;
use App\Http\Controllers\AdminLegalController;
use App\Http\Controllers\DireksiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaManagerController;
use App\Http\Controllers\ManagerBisnisController;

// =====================
// PUBLIC
// =====================
Route::prefix('/')->group(function () {
    // Route::get('/', fn() => view('welcome'))->name('home');
    Route::get('/', fn() => redirect()->route('login'))->name('home');

    // Cek status berkas (mirip cek tiket WBS)
    Route::get('/tracking', [TrackingController::class, 'checkForm'])->name('tracking');
    Route::post('/tracking', [TrackingController::class, 'checkStatus'])->name('tracking.check');
});

// =====================
// PROFILE
// =====================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================
// ADMIN
// =====================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');


    Route::get('/berkas', [AdminController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [AdminController::class, 'berkasShow'])->name('berkas.show');
    Route::get('/berkas/{id}/edit', [AdminController::class, 'berkasEdit'])->name('berkas.edit');
    Route::put('/berkas/{id}', [AdminController::class, 'berkasUpdate'])->name('berkas.update');
    Route::delete('/berkas/{id}', [AdminController::class, 'berkasDestroy'])->name('berkas.destroy');

    // Kantor
    Route::get('/kantor', [AdminController::class, 'kantorIndex'])->name('kantor.index');
    Route::post('/kantor', [AdminController::class, 'kantorStore'])->name('kantor.store');
    Route::put('/kantor/{id}', [AdminController::class, 'kantorUpdate'])->name('kantor.update');
    Route::delete('/kantor/{id}', [AdminController::class, 'kantorDestroy'])->name('kantor.destroy');

    // User
    Route::get('/users', [AdminController::class, 'userIndex'])->name('user.index');
    Route::post('/users', [AdminController::class, 'userStore'])->name('user.store');
    Route::put('/users/{id}', [AdminController::class, 'userUpdate'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'userDestroy'])->name('user.destroy');

    // Laporan/Export
    Route::get('/laporan', [AdminController::class, 'laporanIndex'])->name('laporan.index');
    Route::post('/laporan/export', [AdminController::class, 'exportProses'])->name('laporan.export');


    });

// =====================
// CS
// =====================
Route::middleware(['auth', 'role:cs'])
    ->prefix('cs')->name('cs.')->group(function () {

    Route::get('/dashboard', [CsController::class, 'dashboard'])->name('dashboard');

    // Berkas
    Route::get('/berkas', [CsController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/tambah', [CsController::class, 'berkasCreate'])->name('berkas.create');
    Route::post('/berkas', [CsController::class, 'berkasStore'])->name('berkas.store');
    Route::get('/berkas/{id}', [CsController::class, 'berkasShow'])->name('berkas.show');
    Route::post('/berkas/{id}/verifikasi', [CsController::class, 'verifikasi'])->name('berkas.verifikasi');
    Route::post('/berkas/{id}/batalkan', [CsController::class, 'batalkan'])->name('berkas.batalkan');

    Route::get('/berkas/{id}/edit', [CsController::class, 'edit'])->name('berkas.edit');
    Route::put('/berkas/{id}', [CsController::class, 'update'])->name('berkas.update');

    Route::get('/laporan/cair', [CsController::class, 'laporanCair'])->name('laporan.cair');
    Route::get('/laporan/batal', [CsController::class, 'laporanBatal'])->name('laporan.batal');
    Route::get('/laporan/tolak', [CsController::class, 'laporanTolak'])->name('laporan.tolak');
    Route::get('/laporan', [CsController::class, 'laporanIndex'])->name('laporan.index');
    Route::post('/laporan/export', [CsController::class, 'laporanExport'])->name('laporan.export');


    });

// =====================
// SENIOR LOAN OFFICER
// =====================
Route::middleware(['auth', 'role:slo'])
    ->prefix('slo')->name('slo.')->group(function () {

    Route::get('/dashboard', [SloController::class, 'dashboard'])->name('dashboard');

    Route::get('/berkas', [SloController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [SloController::class, 'berkasShow'])->name('berkas.show');

    Route::post('/berkas/{id}/screening', [SloController::class, 'updateScreening'])->name('berkas.screening');
    Route::post('/berkas/{id}/slik', [SloController::class, 'updateSlik'])->name('berkas.slik');
    Route::post('/berkas/{id}/survey', [SloController::class, 'updateSurvey'])->name('berkas.survey');
    Route::post('/berkas/{id}/komite', [SloController::class, 'updateKomite'])->name('berkas.komite');
    Route::post('/berkas/{id}/realisasi', [SloController::class, 'updateRealisasi'])->name('berkas.realisasi');
    Route::post('/berkas/{id}/cair', [SloController::class, 'updateCair'])->name('berkas.cair');

    Route::post('/berkas/{id}/pending', [SloController::class, 'pending'])->name('berkas.pending');
    Route::post('/berkas/{id}/tolak', [SloController::class, 'tolak'])->name('berkas.tolak');
    Route::post('/berkas/{id}/batalkan', [SloController::class, 'batalkan'])->name('berkas.batalkan');
    Route::post('/berkas/{id}/lanjutkan', [SloController::class, 'resume'])->name('berkas.lanjutkan');

    Route::get('/laporan/cair', [SloController::class, 'laporanCair'])->name('laporan.cair');
    Route::get('/laporan/batal', [SloController::class, 'laporanBatal'])->name('laporan.batal');
    Route::get('/laporan/tolak', [SloController::class, 'laporanTolak'])->name('laporan.tolak');
    Route::get('/laporan', [SloController::class, 'laporanIndex'])->name('laporan.index');
    Route::post('/laporan/export', [SloController::class, 'laporanExport'])->name('laporan.export');
    
});

// =====================
// ADMIN LEGAL
// =====================
Route::middleware(['auth', 'role:admin_legal'])
    ->prefix('legal')->name('legal.')->group(function () {
    Route::get('/dashboard', [AdminLegalController::class, 'dashboard'])->name('dashboard');
    Route::get('/berkas', [AdminLegalController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [AdminLegalController::class, 'berkasShow'])->name('berkas.show');
});



    // DIREKSI (read-only, semua kantor)
    Route::middleware(['auth', 'role:direksi'])
        ->prefix('direksi')->name('direksi.')->group(function () {
        Route::get('/dashboard', [DireksiController::class, 'dashboard'])->name('dashboard');
        Route::get('/berkas', [DireksiController::class, 'berkasIndex'])->name('berkas.index');
        Route::get('/berkas/{id}', [DireksiController::class, 'berkasShow'])->name('berkas.show');
        Route::get('/laporan', [DireksiController::class, 'laporanIndex'])->name('laporan.index');
        Route::post('/laporan/export', [DireksiController::class, 'laporanExport'])->name('laporan.export');
    });

    // AREA MANAGER (read-only, dibatasi wilayah)
    Route::middleware(['auth', 'role:area_manager'])
        ->prefix('area-manager')->name('am.')->group(function () {
        Route::get('/dashboard', [AreaManagerController::class, 'dashboard'])->name('dashboard');
        Route::get('/berkas', [AreaManagerController::class, 'berkasIndex'])->name('berkas.index');
        Route::get('/berkas/{id}', [AreaManagerController::class, 'berkasShow'])->name('berkas.show');
        Route::get('/laporan', [AreaManagerController::class, 'laporanIndex'])->name('laporan.index');\Route::post('/laporan/export', [AreaManagerController::class, 'laporanExport'])->name('laporan.export');

    });

    // MANAGER BISNIS (read-only, semua kantor)
    Route::middleware(['auth', 'role:manager_bisnis'])
        ->prefix('manager-bisnis')->name('mb.')->group(function () {
        Route::get('/dashboard', [ManagerBisnisController::class, 'dashboard'])->name('dashboard');
        Route::get('/berkas', [ManagerBisnisController::class, 'berkasIndex'])->name('berkas.index');
        Route::get('/berkas/{id}', [ManagerBisnisController::class, 'berkasShow'])->name('berkas.show');
        Route::get('/laporan', [ManagerBisnisController::class, 'laporanIndex'])->name('laporan.index');
        Route::post('/laporan/export', [ManagerBisnisController::class, 'laporanExport'])->name('laporan.export');
    });

require __DIR__.'/auth.php';