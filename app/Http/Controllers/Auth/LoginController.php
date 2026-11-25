<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $login = trim($request->input('login') ?? $request->input('identifier') ?? '');
        $password = $request->input('password') ?? '';

        $errors = [];
        if (empty($login)) {
            $errors['login'] = 'NIM, NIP, atau Email wajib diisi.';
        }
        if (empty($password)) {
            $errors['password'] = 'Kata sandi wajib diisi.';
        }

        if (!empty($errors)) {
            return back()->withInput($request->only('login', 'identifier', 'remember'))->withErrors($errors);
        }

        $user = User::findByIdentifier($login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withInput($request->only('login', 'identifier', 'remember'))->withErrors([
                'login' => 'NIM/NIP/Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        if (! $user->isMahasiswa() && ! $user->isDosen()) {
            return back()->withInput($request->only('login', 'identifier', 'remember'))->withErrors([
                'login' => 'Role akun tidak didukung. Gunakan akun Mahasiswa atau Dosen.',
            ]);
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('login', 'identifier'))->withErrors([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->isDosen()) {
            return redirect()->intended(route('dashboard.dosen'));
        }

        return redirect()->intended(route('dashboard.mahasiswa'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
