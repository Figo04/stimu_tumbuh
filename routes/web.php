<?php

use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\MateriController;
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
});

Route::middleware('auth')->group(function () {
    Route::get('/pretest', [KuesionerController::class, 'pretest'])->name('pretest');
    Route::post('/pretest', [KuesionerController::class, 'storePretest'])->name('pretest.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
