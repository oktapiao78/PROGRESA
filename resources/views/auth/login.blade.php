@extends('layouts.guest')

@section('title', 'Login - PROGRESA')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
    body { background: linear-gradient(135deg, #e8dff5 0%, #f4d7f4 100%); min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { font-size: 3.5rem; font-weight: 800; letter-spacing: 2px; background: linear-gradient(90deg, #7e7deb 0%, #b56dba 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px; }
    .header p { color: #6b6b7b; font-size: 1.1rem; font-weight: 600; max-width: 600px; line-height: 1.5; margin: 0 auto; }
    .login-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-radius: 30px; padding: 40px 50px; width: 100%; max-width: 650px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05), inset 0 0 0 1px rgba(255, 255, 255, 0.8); }
    .form-group { margin-bottom: 20px; }
    .label-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    label { font-size: 0.85rem; font-weight: 800; color: #7a7a8c; letter-spacing: 0.5px; text-transform: uppercase; }
    .input-wrapper { position: relative; display: flex; align-items: center; }
    .input-wrapper .icon-left { position: absolute; left: 20px; color: #a0a0b0; width: 20px; height: 20px; }
    input[type="text"], input[type="password"] { width: 100%; padding: 16px 20px 16px 50px; border: 1px solid #e1e1e8; border-radius: 12px; background-color: #ffffff; font-size: 1rem; color: #333; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02); outline: none; transition: all 0.3s ease; }
    input:focus { border-color: #b56dba; box-shadow: 0 0 0 3px rgba(181, 109, 186, 0.1); }
    .btn-toggle-password { position: absolute; right: 20px; background: none; border: none; cursor: pointer; color: #a0a0b0; display: flex; align-items: center; justify-content: center; }
    .btn-toggle-password:hover { color: #7a7a8c; }
    .btn-toggle-password svg { width: 20px; height: 20px; }
    .checkbox-group { display: flex; align-items: center; margin-bottom: 25px; margin-top: 10px; }
    .checkbox-group input[type="checkbox"] { appearance: none; -webkit-appearance: none; width: 20px; height: 20px; border: 2px solid #e1e1e8; border-radius: 6px; background-color: #fff; cursor: pointer; margin-right: 10px; position: relative; transition: all 0.2s; }
    .checkbox-group input[type="checkbox"]:checked { background-color: #b56dba; border-color: #b56dba; }
    .checkbox-group input[type="checkbox"]:checked::after { content: '✔'; position: absolute; color: white; font-size: 12px; top: 50%; left: 50%; transform: translate(-50%, -50%); }
    .checkbox-group label { color: #7a7a8c; font-size: 0.95rem; font-weight: 600; cursor: pointer; margin-bottom: 0; text-transform: none; }
    .btn-submit { width: 100%; padding: 16px; border: none; border-radius: 12px; background: linear-gradient(90deg, #d3c4f3 0%, #f3c3e9 100%); color: #555; font-size: 1rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 15px rgba(181, 109, 186, 0.15); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.5); }
    .btn-submit:hover { background: linear-gradient(90deg, #c5b3ed 0%, #edb3e1 100%); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(181, 109, 186, 0.25); }
    .footer { margin-top: 40px; text-align: center; color: #8a8a9d; font-size: 0.8rem; font-weight: 600; line-height: 1.8; }
    .footer a { color: #7e7deb; text-decoration: none; font-weight: 700; }
    .footer a:hover { text-decoration: underline; }
    @media (max-width: 768px) { .header h1 { font-size: 2.5rem; } .login-card { padding: 30px 20px; } }
</style>
@endpush

@section('content')
    <div class="header">
        <h1>PROGRESA</h1>
        <p>Sistem Manajemen Bimbingan Skripsi & Tugas Akhir Berbasis Progres dan Approval Bertingkat</p>
    </div>

    <div class="login-card" x-data="{ showPassword: false }">
        @if (session('success'))
            <div style="background-color: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background-color: #fde8e8; border: 1px solid #f8b4b4; color: #9b1c1c; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">NIM / NIP / EMAIL</label>
                <div class="input-wrapper">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <input type="text" id="username" name="login" value="{{ old('login') }}" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <div class="label-row"><label for="password">KATA SANDI</label></div>
                <div class="input-wrapper">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required>
                    <button type="button" class="btn-toggle-password" @click="showPassword = !showPassword">
                        <svg x-show="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat sesi saya</label>
            </div>
            <button type="submit" class="btn-submit">Masuk ke Sistem</button>
        </form>
    </div>

    <div class="footer">
        <p>Belum memiliki akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
        <p>&copy; {{ date('Y') }} Progresa Academic Portal. Hak Cipta Dilindungi.</p>
    </div>
@endsection