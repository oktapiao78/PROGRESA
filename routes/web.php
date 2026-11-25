<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentDiffController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\SupervisionController;
use App\Http\Controllers\ThesisController;

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
        Route::post('/supervision/slots/{slot}/book', [SupervisionController::class, 'book'])->name('supervision.book');
        Route::post('/chapters/{chapter}/upload', [ChapterController::class, 'uploadDraft'])->name('chapters.upload');
        Route::post('/revisions/{item}/respond', [RevisionController::class, 'respond'])->name('revisions.respond');
    });

    // Khusus Dosen
    Route::middleware('role:dosen')->group(function () {
        Route::get('/dashboard/dosen', [DashboardController::class, 'dosenDashboard'])->name('dashboard.dosen');
        Route::get('/profile/dosen', [ProfileController::class, 'dosenProfile'])->name('profile.dosen');
        Route::post('/supervision/slots', [SupervisionController::class, 'storeSlot'])->name('supervision.slots.store');
        Route::delete('/supervision/slots/{slot}', [SupervisionController::class, 'destroySlot'])->name('supervision.slots.destroy');
        Route::post('/supervision/requests/{supervisionRequest}/respond', [SupervisionController::class, 'respond'])->name('supervision.respond');
        Route::post('/supervision/requests/{supervisionRequest}/complete', [SupervisionController::class, 'complete'])->name('supervision.complete');
        Route::post('/chapters/{chapter}/revisions', [RevisionController::class, 'store'])->name('revisions.store');
        Route::post('/revisions/{item}/toggle-resolve', [RevisionController::class, 'toggleResolve'])->name('revisions.toggle');
    });

    // Shared Authenticated Routes (dengan otorisasi di masing-masing controller)
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'update']);

    // Skripsi & Bab
    Route::get('/theses', [ThesisController::class, 'index'])->name('theses.index');
    Route::get('/theses/{thesis}', [ThesisController::class, 'show'])->name('theses.show');
    Route::get('/theses/{thesis}/edit', [ThesisController::class, 'edit'])->name('theses.edit');
    Route::put('/theses/{thesis}', [ThesisController::class, 'update'])->name('theses.update');

    Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
    Route::get('/chapters/{chapter}/diff', [DocumentDiffController::class, 'compare'])->name('chapters.diff');
    Route::get('/versions/{version}/download', [ChapterController::class, 'downloadVersion'])->name('versions.download');

    Route::post('/chapters/{chapter}/approve', [ApprovalController::class, 'approveStage'])->name('approvals.stage');

    // Jadwal & Bimbingan
    Route::get('/supervision', [SupervisionController::class, 'index'])->name('supervision.index');


    // Pemberitahuan & Chat
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

});
