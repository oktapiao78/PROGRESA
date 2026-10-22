@extends('layouts.guest')

@section('title', 'Reset Kata Sandi - PROGRESA')

@push('styles')
<style>
    body { background: linear-gradient(135deg, #e8dff5 0%, #f4d7f4 100%); min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { font-size: 3.5rem; font-weight: 800; letter-spacing: 2px; background: linear-gradient(90deg, #7e7deb 0%, #b56dba 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px; }
    .login-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(15px); border-radius: 30px; padding: 40px 50px; width: 100%; max-width: 650px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); }
    .form-group { margin-bottom: 20px; }
    label { font-size: 0.85rem; font-weight: 800; color: #7a7a8c; text-transform: uppercase; }
    input[type="email"], input[type="password"] { width: 100%; padding: 16px 20px; border: 1px solid #e1e1e8; border-radius: 12px; margin-top: 8px; outline: none; }
    input:focus { border-color: #b56dba; }
    .btn-submit { width: 100%; padding: 16px; border: none; border-radius: 12px; background: linear-gradient(90deg, #d3c4f3 0%, #f3c3e9 100%); color: #555; font-size: 1rem; font-weight: 800; cursor: pointer; margin-top: 10px; }
    .error-text { color: #dc2626; font-size: 0.85rem; margin-top: 5px; display: block; }
</style>
@endpush

@section('content')
    <div class="header">
        <h1>PROGRESA</h1>
        <p>Silakan buat kata sandi baru untuk akun Anda.</p>
    </div>

    <div class="login-card">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email">EMAIL</label>
                <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required readonly>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password">KATA SANDI BARU</label>
                <input type="password" id="password" name="password" required>
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">KONFIRMASI KATA SANDI</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn-submit">Simpan Kata Sandi Baru</button>
        </form>
    </div>
@endsection