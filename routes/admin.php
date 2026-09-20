<?php

use App\Http\Controllers\Admin\AktivitasController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\HasilTestController;
use App\Http\Controllers\Admin\MateriController;
use App\Http\Controllers\Admin\PerkembanganController;
use App\Http\Controllers\Admin\RespondenController;
use App\Http\Controllers\Admin\SoalController;
use Illuminate\Support\Facades\Route;

// Didaftarkan di bootstrap/app.php dengan prefix /admin, nama admin.*, middleware web.

Route::middleware('guest:admin')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // View-only (PRD §3.2): hanya index & show, tanpa create/edit/destroy.
    Route::get('responden', [RespondenController::class, 'index'])->name('responden.index');
    Route::get('responden/{responden}', [RespondenController::class, 'show'])->name('responden.show');
    Route::get('hasil-test', [HasilTestController::class, 'index'])->name('hasil-test.index');
    Route::get('hasil-test/{responden}', [HasilTestController::class, 'show'])->name('hasil-test.show');
    Route::get('aktivitas', [AktivitasController::class, 'index'])->name('aktivitas.index');
    Route::get('aktivitas/{responden}', [AktivitasController::class, 'show'])->name('aktivitas.show');
    Route::get('perkembangan', [PerkembanganController::class, 'index'])->name('perkembangan.index');
    Route::get('perkembangan/{responden}', [PerkembanganController::class, 'show'])->name('perkembangan.show');

    // View-only, kecuali satu field: video_youtube_id boleh diganti admin (Sesi 34).
    Route::get('materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('materi/{materi:slug}', [MateriController::class, 'show'])->name('materi.show');
    Route::patch('materi/{materi:slug}', [MateriController::class, 'update'])->name('materi.update');

    // Export (PRD §3.4): Excel 5 sheet & CSV ringkas, keduanya unduhan langsung tanpa halaman sendiri.
    Route::get('export/excel', [ExportController::class, 'excel'])->name('export.excel');
    Route::get('export/csv', [ExportController::class, 'csv'])->name('export.csv');

    // Satu-satunya CRUD penuh admin (PRD §3.2); `show` tak dipakai — daftarnya sudah memuat isi soal.
    Route::resource('soal', SoalController::class)->except('show');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
