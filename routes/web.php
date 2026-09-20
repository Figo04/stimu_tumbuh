<?php

use App\Http\Controllers\AktivitasStimulasiController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PerkembanganController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'pretest.selesai'])->name('dashboard');

Route::middleware(['auth', 'pretest.selesai'])->group(function () {
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/{materi:slug}', [MateriController::class, 'show'])->name('materi.show');
    Route::post('/materi/{materi:slug}/selesai', [MateriController::class, 'selesai'])->name('materi.selesai');
    Route::post('/materi/{materi:slug}/video', [MateriController::class, 'video'])->name('materi.video');
    Route::post('/materi/{materi:slug}/praktik', [MateriController::class, 'praktik'])->name('materi.praktik');

    Route::resource('kalender', AktivitasStimulasiController::class)
        ->except(['create', 'show'])
        ->names('aktivitas')
        ->parameters(['kalender' => 'aktivitas']);

    Route::get('/perkembangan', [PerkembanganController::class, 'index'])->name('perkembangan.index');
    Route::post('/perkembangan', [PerkembanganController::class, 'store'])->name('perkembangan.store');

    // Gating "semua materi selesai" ada di KuesionerController, bukan hanya di menu.
    Route::get('/posttest', [KuesionerController::class, 'show'])->defaults('tipe', 'post')->name('posttest');
    Route::post('/posttest', [KuesionerController::class, 'store'])->defaults('tipe', 'post')->name('posttest.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/pretest', [KuesionerController::class, 'show'])->defaults('tipe', 'pre')->name('pretest');
    Route::post('/pretest', [KuesionerController::class, 'store'])->defaults('tipe', 'pre')->name('pretest.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
