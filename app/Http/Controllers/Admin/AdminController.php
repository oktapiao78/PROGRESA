<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ChapterTemplate;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function users(Request $request): View
    {
        $query = User::with('studyProgram');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $studyPrograms = StudyProgram::all();
        $roles = UserRole::cases();

        return view('admin.users.index', compact('users', 'studyPrograms', 'roles'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nim_nip' => ['required', 'string', 'unique:users,nim_nip'],
            'role' => ['required', 'string'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim_nip' => $validated['nim_nip'],
            'role' => UserRole::from($validated['role']),
            'study_program_id' => $validated['study_program_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        AuditLog::log('USER_CREATED', $user, "Admin membuat akun baru {$user->name} ({$user->role->label()}).");

        return back()->with('success', "Pengguna {$user->name} berhasil didaftarkan.");
    }

    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $user->update(['is_active' => !$user->is_active]);
        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::log('USER_STATUS_TOGGLED', $user, "Akun {$user->name} {$statusText} oleh Admin.");

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    public function chapterTemplates(): View
    {
        $templates = ChapterTemplate::orderBy('order')->get();
        return view('admin.chapters.index', compact('templates'));
    }

    public function storeChapterTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['required', 'integer', 'min:1'],
            'is_required' => ['boolean'],
        ]);

        $template = ChapterTemplate::create([
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
            'is_required' => $request->boolean('is_required', true),
        ]);

        AuditLog::log('CHAPTER_TEMPLATE_CREATED', $template, "Struktur bab {$template->title} berhasil ditambahkan.");

        return back()->with('success', 'Struktur bab baru berhasil ditambahkan.');
    }

    public function auditLogs(): View
    {
        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('admin.audit_logs', compact('logs'));
    }
}
