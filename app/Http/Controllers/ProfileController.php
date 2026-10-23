<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function mahasiswaProfile()
    {
        $user = Auth::user();

        if ($user->isDosen()) {
            return redirect()->route('profile.dosen');
        }

        return view('profile', compact('user'));
    }

    public function dosenProfile()
    {
        $user = Auth::user();

        if ($user->isMahasiswa()) {
            return redirect()->route('profile.mahasiswa');
        }

        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nim_nip' => ['required', 'string', 'max:50', Rule::unique('users', 'nim_nip')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'max:30'],
            'domicile' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'string', 'max:50'],
            'university' => ['nullable', 'string', 'max:100'],
            'faculty' => ['nullable', 'string', 'max:100'],
        ];

        if ($user->isMahasiswa()) {
            $rules['major'] = ['nullable', 'string', 'max:100'];
            $rules['year'] = ['nullable', 'string', 'max:10'];
            $rules['semester'] = ['nullable', 'string', 'max:10'];
            $rules['gpa'] = ['nullable', 'string', 'max:10'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nim_nip.required' => 'NIM/NIP wajib diisi.',
            'nim_nip.unique' => 'NIM/NIP ini sudah terdaftar pada akun lain.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nim_nip = $validated['nim_nip'];
        $user->phone = $validated['phone'] ?? null;
        $user->gender = $validated['gender'] ?? null;
        $user->domicile = $validated['domicile'] ?? null;
        $user->birth_date = $validated['birth_date'] ?? null;
        $user->university = $validated['university'] ?? null;
        $user->faculty = $validated['faculty'] ?? null;

        if ($user->isMahasiswa()) {
            $user->major = $validated['major'] ?? null;
            $user->year = $validated['year'] ?? null;
            $user->semester = $validated['semester'] ?? null;
            $user->gpa = $validated['gpa'] ?? null;
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}