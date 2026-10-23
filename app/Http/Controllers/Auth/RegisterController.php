<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim_nip' => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:mahasiswa,dosen'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nim_nip.required' => 'NIM atau NIP wajib diisi.',
            'nim_nip.unique' => 'NIM atau NIP sudah terdaftar dalam sistem.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar dalam sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'role.required' => 'Silakan tentukan role akun (Mahasiswa atau Dosen).',
            'role.in' => 'Pilihan role tidak valid.',
        ]);

        User::create([
            'name' => $validated['name'],
            'nim_nip' => $validated['nim_nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] === 'dosen' ? UserRole::DOSEN : UserRole::MAHASISWA,
            'is_active' => true,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk dengan akun yang baru saja Anda daftarkan.');
    }
}