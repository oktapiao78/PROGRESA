@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi - PROGRESA')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #e8dff5 0%, #f4d7f4 100%);
        min-height: 100vh;
        display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;
    }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 {
        font-size: 3.5rem; font-weight: 800; letter-spacing: 2px;
        background: linear-gradient(90deg, #7e7deb 0%, #b56dba 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px;
    }
    .header p { color: #6b6b7b; font-size: 1.1rem; font-weight: 600; max-width: 600px; line-height: 1.5; margin: 0 auto; }
    
    .login-card {
        background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
        border-radius: 30px; padding: 40px 50px; width: 100%; max-width: 650px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05), inset 0 0 0 1px rgba(255, 255, 255, 0.8);
    }
    .form-group { margin-bottom: 20px; }
    label { font-size: 0.85rem; font-weight: 800; color: #7a7a8c; letter-spacing: 0.5px; text-transform: uppercase; }
    .input-wrapper { position: relative; display: flex; align-items: center; margin-top: 8px; }
    .input-wrapper .icon-left { position: absolute; left: 20px; color: #a0a0b0; width: 20px; height: 20px; }
    input[type="email"] {
        width: 100%; padding: 16px 20px 16px 50px; border: 1px solid #e1e1e8; border-radius: 12px;
        background-color: #ffffff; font-size: 1rem; color: #333; outline: none; transition: all 0.3s ease;
    }
    input:focus { border-color: #b56dba; box-shadow: 0 0 0 3px rgba(181, 109, 186, 0.1); }
    
    .btn-submit {
        width: 100%; padding: 16px; border: none; border-radius: 12px;
        background: linear-gradient(90deg, #d3c4f3 0%, #f3c3e9 100%); color: #555; font-size: 1rem; font-weight: 800;
        cursor: pointer; transition: all 0.3s ease; margin-top: 10px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(181, 109, 186, 0.25); }
    .error-text { color: #dc2626; font-size: 0.85rem; margin-top: 5px; display: block; }
    .success-text { color: #059669; font-size: 0.9rem; margin-bottom: 15px; display: block; background: #ecfdf5; padding: 10px; border-radius: 8px; }
    
    .back-link { text-align: center; margin-top: 20px; display: block; color: #7e7deb; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
    .back-link:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
    <div class="header">
        <h1>PROGRESA</h1>
        <p>Masukkan alamat email atau NIM Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
    </div>

    <div class="login-card">
        <!-- Session Status -->
        @if (session('status'))
            <span class="success-text">{{ session('status') }}</span>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">EMAIL / NIM TERDAFTAR</label>
                <div class="input-wrapper">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Kirim Tautan Reset</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">&larr; Kembali ke Halaman Login</a>
    </div>
@endsection