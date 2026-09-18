<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentDiffController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\SupervisionController;
use App\Http\Controllers\ThesisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Progresa Thesis Management System
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Thesis Management
    Route::get('/theses', [ThesisController::class, 'index'])->name('theses.index');
    Route::get('/theses/{thesis}', [ThesisController::class, 'show'])->name('theses.show');
    Route::get('/theses/{thesis}/edit', [ThesisController::class, 'edit'])->name('theses.edit');
    Route::put('/theses/{thesis}', [ThesisController::class, 'update'])->name('theses.update');

    // Chapters & Versions
    Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
    Route::post('/chapters/{chapter}/upload', [ChapterController::class, 'uploadDraft'])->name('chapters.upload');
    Route::get('/versions/{version}/download', [ChapterController::class, 'downloadVersion'])->name('versions.download');

    // Visual Document Diff
    Route::get('/chapters/{chapter}/diff', [DocumentDiffController::class, 'compare'])->name('chapters.diff');

    // Revisions Checklist
    Route::post('/chapters/{chapter}/revisions', [RevisionController::class, 'store'])->name('revisions.store');
    Route::post('/revisions/{item}/respond', [RevisionController::class, 'respond'])->name('revisions.respond');
    Route::post('/revisions/{item}/toggle', [RevisionController::class, 'toggleResolve'])->name('revisions.toggle');

    // Supervision & Guidance Scheduling
    Route::get('/supervision', [SupervisionController::class, 'index'])->name('supervision.index');
    Route::post('/supervision/slots', [SupervisionController::class, 'storeSlot'])->name('supervision.slots.store');
    Route::delete('/supervision/slots/{slot}', [SupervisionController::class, 'destroySlot'])->name('supervision.slots.destroy');
    Route::post('/supervision/slots/{slot}/book', [SupervisionController::class, 'book'])->name('supervision.slots.book');
    Route::post('/supervision/requests/{supervisionRequest}/respond', [SupervisionController::class, 'respond'])->name('supervision.requests.respond');
    Route::post('/supervision/requests/{supervisionRequest}/complete', [SupervisionController::class, 'complete'])->name('supervision.requests.complete');

    // Hierarchical Approvals & Escalation
    Route::post('/chapters/{chapter}/approve', [ApprovalController::class, 'approveStage'])->name('approvals.stage');
    Route::post('/theses/{thesis}/escalate', [ApprovalController::class, 'escalate'])->name('approvals.escalate');
    Route::post('/theses/{thesis}/resolve-escalation', [ApprovalController::class, 'resolveEscalation'])->name('approvals.resolve_escalation');

    // Monitoring & Risk Dashboards (Koordinator & Kaprodi)
    Route::middleware('role:koordinator,kaprodi,admin')->prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/stagnant', [MonitoringController::class, 'stagnantStudents'])->name('stagnant');
        Route::get('/supervisors', [MonitoringController::class, 'supervisorResponse'])->name('supervisors');
        Route::get('/delay-risk', [MonitoringController::class, 'delayRisk'])->name('delay_risk');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::get('/chapters', [AdminController::class, 'chapterTemplates'])->name('chapters.index');
        Route::post('/chapters', [AdminController::class, 'storeChapterTemplate'])->name('chapters.store');
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit_logs');
    });

    // Protected Role Route Aliases & Fallbacks
    Route::get('/mahasiswa/{any?}', [DashboardController::class, 'index'])->where('any', '.*')->name('mahasiswa.fallback');
    Route::get('/dosen/{any?}', [DashboardController::class, 'index'])->where('any', '.*')->name('dosen.fallback');
    Route::get('/koordinator/{any?}', [DashboardController::class, 'index'])->where('any', '.*')->name('koordinator.fallback');
    Route::get('/kaprodi/{any?}', [DashboardController::class, 'index'])->where('any', '.*')->name('kaprodi.fallback');
});
