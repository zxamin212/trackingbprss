<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CsController;
use App\Http\Controllers\SloController;
use App\Http\Controllers\AdminLegalController;
use App\Http\Controllers\DireksiController;
use Illuminate\Support\Facades\Route;

// =====================
// PUBLIC
// =====================
Route::prefix('/')->group(function () {
    Route::get('/', fn() => view('welcome'))->name('home');

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

    // Berkas (monitoring semua kantor)
    Route::get('/berkas', [AdminController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [AdminController::class, 'berkasShow'])->name('berkas.show');

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
});

// =====================
// SENIOR LOAN OFFICER
// =====================
Route::middleware(['auth', 'role:senior_loan_officer'])
    ->prefix('slo')->name('slo.')->group(function () {

    Route::get('/dashboard', [SloController::class, 'dashboard'])->name('dashboard');

    Route::get('/berkas', [SloController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [SloController::class, 'berkasShow'])->name('berkas.show');
    Route::post('/berkas/{id}/survey', [SloController::class, 'updateSurvey'])->name('berkas.survey');
    Route::post('/berkas/{id}/komite', [SloController::class, 'updateKomite'])->name('berkas.komite');
    Route::post('/berkas/{id}/batalkan', [SloController::class, 'batalkan'])->name('berkas.batalkan');
});

// =====================
// ADMIN LEGAL
// =====================
Route::middleware(['auth', 'role:admin_legal'])
    ->prefix('legal')->name('legal.')->group(function () {

    Route::get('/dashboard', [AdminLegalController::class, 'dashboard'])->name('dashboard');

    Route::get('/berkas', [AdminLegalController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [AdminLegalController::class, 'berkasShow'])->name('berkas.show');
    Route::post('/berkas/{id}/akad', [AdminLegalController::class, 'updateAkad'])->name('berkas.akad');
    Route::post('/berkas/{id}/belum-lengkap', [AdminLegalController::class, 'belumLengkap'])->name('berkas.belumlengkap');
    Route::post('/berkas/{id}/cair', [AdminLegalController::class, 'pencairan'])->name('berkas.cair');
    Route::post('/berkas/{id}/batalkan', [AdminLegalController::class, 'batalkan'])->name('berkas.batalkan');
});

// =====================
// DIREKSI (read-only)
// =====================
Route::middleware(['auth', 'role:direksi'])
    ->prefix('direksi')->name('direksi.')->group(function () {

    Route::get('/dashboard', [DireksiController::class, 'dashboard'])->name('dashboard');
    Route::get('/berkas', [DireksiController::class, 'berkasIndex'])->name('berkas.index');
    Route::get('/berkas/{id}', [DireksiController::class, 'berkasShow'])->name('berkas.show');
    Route::get('/laporan', [DireksiController::class, 'laporanIndex'])->name('laporan.index');
});

require __DIR__.'/auth.php';