@extends('layouts.guest')

@section('title', 'Daftar Akun - PROGRESA')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
    body { background: linear-gradient(135deg, #e8dff5 0%, #f4d7f4 100%); min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
    .header { text-align: center; margin-bottom: 25px; }
    .header h1 { font-size: 3rem; font-weight: 800; letter-spacing: 2px; background: linear-gradient(90deg, #7e7deb 0%, #b56dba 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 10px; }
    .header p { color: #6b6b7b; font-size: 1rem; font-weight: 600; max-width: 500px; line-height: 1.4; margin: 0 auto; }
    .register-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-radius: 30px; padding: 35px 45px; width: 100%; max-width: 550px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05), inset 0 0 0 1px rgba(255, 255, 255, 0.8); }
    .form-group { margin-bottom: 15px; }
    label { font-size: 0.75rem; font-weight: 800; color: #7a7a8c; letter-spacing: 0.5px; text-transform: uppercase; display: block; margin-bottom: 6px; }
    .input-wrapper { position: relative; display: flex; align-items: center; }
    .input-wrapper .icon-left { position: absolute; left: 15px; color: #a0a0b0; width: 18px; height: 18px; }
    input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 14px 15px 14px 45px; border: 1px solid #e1e1e8; border-radius: 12px; background-color: #ffffff; font-size: 0.95rem; color: #333; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02); outline: none; transition: all 0.3s ease; appearance: none; }
    select { padding-left: 15px; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23a0a0b0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 15px center; background-size: 16px; cursor: pointer; }
    input:focus, select:focus { border-color: #b56dba; box-shadow: 0 0 0 3px rgba(181, 109, 186, 0.1); }
    .btn-submit { width: 100%; padding: 16px; border: none; border-radius: 12px; background: linear-gradient(90deg, #d3c4f3 0%, #f3c3e9 100%); color: #555; font-size: 1rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 15px rgba(181, 109, 186, 0.15); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.5); margin-top: 10px; }
    .btn-submit:hover { background: linear-gradient(90deg, #c5b3ed 0%, #edb3e1 100%); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(181, 109, 186, 0.25); }
    .footer { margin-top: 30px; text-align: center; color: #8a8a9d; font-size: 0.75rem; font-weight: 600; line-height: 1.6; }
    .footer a { color: #7e7deb; text-decoration: none; font-weight: 700; }
    .footer a:hover { text-decoration: underline; }
    @media (max-width: 768px) { .header h1 { font-size: 2.5rem; } .register-card { padding: 25px 20px; } }
</style>
@endpush

@section('content')
    <div class="header">
        <h1>PROGRESA</h1>
        <p>Sistem Manajemen Bimbingan Skripsi & Tugas Akhir Berbasis Progres dan Approval Bertingkat</p>
    </div>

    <div class="register-card">
        @if ($errors->any())
            <div style="background-color: #fde8e8; border: 1px solid #f8b4b4; color: #9b1c1c; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem;">
                <div style="font-weight: 700; margin-bottom: 5px;">Pendaftaran Gagal:</div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">NAMA LENGKAP</label>
                <div class="input-wrapper">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label for="nim_nip">NIM / NIP</label>
                <div class="input-wrapper">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="7" y1="8" x2="17" y2="8"></line><line x1="7" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="13" y2="16"></line></svg>
                    <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">EMAIL</label>
                <div class="input-wrapper">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">KATA SANDI</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required style="padding-left: 15px;">
                </div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">KONFIRMASI KATA SANDI</label>
                <div class="input-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required style="padding-left: 15px;">
                </div>
            </div>
            <div class="form-group">
                <label for="role">PILIH ROLE</label>
                <select id="role" name="role" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                    <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Daftar Akun</button>
        </form>
    </div>

    <div class="footer">
        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        <p>&copy; {{ date('Y') }} Progresa Academic Portal. Hak Cipta Dilindungi.</p>
    </div>
@endsection