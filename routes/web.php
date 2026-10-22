<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/dashboard/mahasiswa', function () {
    $user = new \App\Models\User();
    $user->role = \App\Enums\UserRole::MAHASISWA;
    Auth::login($user);
    return app(DashboardController::class)->index();
})->name('dashboard.mahasiswa');

Route::get('/dashboard/dosen', function () {
    $user = new \App\Models\User();
    $user->role = \App\Enums\UserRole::DOSEN;
    Auth::login($user);
    return app(DashboardController::class)->index();
})->name('dashboard.dosen');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');