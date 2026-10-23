<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');

// Guest Routes (Khusus pengguna yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated Routes (Khusus pengguna yang sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Khusus Mahasiswa
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/dashboard/mahasiswa', [DashboardController::class, 'mahasiswaDashboard'])->name('dashboard.mahasiswa');
        Route::get('/profile/mahasiswa', [ProfileController::class, 'mahasiswaProfile'])->name('profile.mahasiswa');
    });

    // Khusus Dosen
    Route::middleware('role:dosen')->group(function () {
        Route::get('/dashboard/dosen', [DashboardController::class, 'dosenDashboard'])->name('dashboard.dosen');
        Route::get('/profile/dosen', [ProfileController::class, 'dosenProfile'])->name('profile.dosen');
    });

    // Update Profile
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'update']);
});