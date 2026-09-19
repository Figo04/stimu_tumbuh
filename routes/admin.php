<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Didaftarkan di bootstrap/app.php dengan prefix /admin, nama admin.*, middleware web.

Route::middleware('guest:admin')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    // ponytail: placeholder, diganti DashboardController + layout admin di Sesi 27–28.
    Route::view('dashboard', 'admin.dashboard')->name('dashboard');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
