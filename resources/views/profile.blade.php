@extends('layouts.app')

@section('title', 'Profil Mahasiswa')

@push('styles')
<style>
    .card { background: #ffffff; border: 1px solid #e1e1e8; border-radius: 16px; padding: 25px; margin-bottom: 20px; }
    .section-title { font-size: 1.2rem; font-weight: 800; color: #333; margin-bottom: 5px; }
    .section-desc { font-size: 0.85rem; color: #8a8a9d; margin-bottom: 20px; }
    
    .profile-header { position: relative; margin-bottom: 20px; }
    .cover-photo { width: 100%; height: 200px; background: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop') center/cover; border-radius: 16px; }
    .avatar-wrapper { position: absolute; bottom: -50px; right: 40px; width: 120px; height: 120px; border-radius: 50%; border: 4px solid #fff; overflow: hidden; background: #e2d9f7; display: flex; justify-content: center; align-items: center; }
    .avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.75rem; font-weight: 700; color: #7a7a8c; text-transform: uppercase; margin-bottom: 5px; }
    .input-box { position: relative; display: flex; align-items: center; }
    .input-box input, .input-box select { width: 100%; padding: 12px 15px; border: 1px solid #e1e1e8; border-radius: 10px; font-size: 0.9rem; color: #333; outline: none; appearance: none; }
    .input-box input:focus { border-color: #b56dba; }
    .icon-edit { position: absolute; right: 15px; color: #a0a0b0; width: 18px; height: 18px; }
    
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .avatar-wrapper { width: 90px; height: 90px; right: 20px; }
    }
</style>
@endpush

@section('content')
    <div class="profile-header">
        <div class="cover-photo"></div>
        <div class="avatar-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="#9d62cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 60%; height: 60%;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
    </div>

    <div class="card">
        <h3 class="section-title">Data Diri</h3>
        <p class="section-desc">Informasi Pribadi Pengguna</p>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Lengkap :</label>
                <div class="input-box">
                    <input type="text" value="{{ $user->name }}" readonly>
                    <svg class="icon-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin :</label>
                <div class="input-box">
                    <select><option>{{ $user->gender }}</option></select>
                    <svg class="icon-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </div>

            <div class="form-group">
                <label>NIM :</label>
                <div class="input-box">
                    <input type="text" value="{{ $user->nim_nip }}" readonly>
                    <svg class="icon-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
            </div>
            <div class="form-group">
                <label>Domisili :</label>
                <div class="input-box">
                    <input type="text" value="{{ $user->domicile }}" readonly>
                    <svg class="icon-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
            </div>

            <div class="form-group">
                <label>Email :</label>
                <div class="input-box">
                    <input type="email" value="{{ $user->email }}" readonly>
                    <svg class="icon-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
            </div>
            <div class="form-group">
                <label>No HP :</label>
                <div class="input-box">
                    <input type="text" value="{{ $user->phone }}" readonly>
                    <svg class="icon-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="section-title">Data Akademik</h3>
        <p class="section-desc">Informasi Pendidikan Mahasiswa</p>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Perguruan Tinggi</label>
                <div class="input-box"><input type="text" value="{{ $user->university }}" readonly></div>
            </div>
            <div class="form-group">
                <label>Fakultas</label>
                <div class="input-box"><input type="text" value="{{ $user->faculty }}" readonly></div>
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <div class="input-box"><input type="text" value="{{ $user->major }}" readonly></div>
            </div>
            <div class="form-group">
                <label>Angkatan</label>
                <div class="input-box"><input type="text" value="{{ $user->year }}" readonly></div>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <div class="input-box"><input type="text" value="{{ $user->semester }}" readonly></div>
            </div>
            <div class="form-group">
                <label>IPK</label>
                <div class="input-box"><input type="text" value="{{ $user->gpa }}" readonly></div>
            </div>
        </div>
    </div>
@endsection