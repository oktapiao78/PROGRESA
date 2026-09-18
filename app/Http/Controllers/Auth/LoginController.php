<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required_without:identifier', 'nullable', 'string'],
            'identifier' => ['required_without:login', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required_without' => 'NIM / NIP / Email wajib diisi.',
            'identifier.required_without' => 'NIM / NIP / Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $identifier = trim((string) ($request->input('login') ?? $request->input('identifier') ?? ''));
        $password = (string) $request->input('password');

        // Multi-identifier lookup: NIM, NIP, or Email (case-insensitive)
        $user = User::findByIdentifier($identifier)->first();

        // Verify user existence and password hash securely
        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withInput($request->only('login', 'identifier', 'remember'))
                ->withErrors(['login' => 'NIM / NIP / Email atau kata sandi tidak cocok.']);
        }

        // Check if account is inactive
        if (! $user->is_active) {
            return back()->withInput($request->only('login', 'identifier', 'remember'))
                ->withErrors(['login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin program studi.']);
        }

        // Authenticate user & regenerate session
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $identifierType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';
        AuditLog::log('USER_LOGIN', $user, "Login berhasil menggunakan {$identifierType}: {$identifier}");

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::log('USER_LOGOUT', Auth::user(), 'Pengguna logout dari sistem.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar dari sistem Progresa.');
    }
}
