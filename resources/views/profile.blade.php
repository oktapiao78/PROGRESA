@extends('layouts.app')

@section('title', 'Profil Pengguna - PROGRESA')

@push('styles')
<style>
    .card { background: #ffffff; border: 1px solid #e1e1e8; border-radius: 16px; padding: 25px; margin-bottom: 20px; }
    .section-title { font-size: 1.2rem; font-weight: 800; color: #333; margin-bottom: 5px; }
    .section-desc { font-size: 0.85rem; color: #8a8a9d; margin-bottom: 20px; }
    
    .profile-header { position: relative; margin-bottom: 20px; }
    .cover-photo { width: 100%; height: 200px; background: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop') center/cover; border-radius: 16px; }
    .avatar-wrapper { position: absolute; bottom: -50px; right: 40px; width: 120px; height: 120px; border-radius: 50%; border: 4px solid #fff; overflow: hidden; background: #e2d9f7; display: flex; justify-content: center; align-items: center; }
    .avatar-wrapper svg { width: 60%; height: 60%; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.75rem; font-weight: 700; color: #7a7a8c; text-transform: uppercase; margin-bottom: 5px; }
    .input-box { position: relative; display: flex; align-items: center; }
    .input-box input, .input-box select { width: 100%; padding: 12px 15px; border: 1px solid #e1e1e8; border-radius: 10px; font-size: 0.9rem; color: #333; outline: none; appearance: none; transition: all 0.25s ease; }
    
    .editable-field.editing {
        background-color: #ffffff !important;
        border-color: #b56dba !important;
        box-shadow: 0 0 0 3px rgba(181, 109, 186, 0.15) !important;
        color: #1a1a24 !important;
    }
    .editable-field:not(.editing) {
        background-color: #fafafc;
        color: #4a4a5a;
        cursor: default;
    }

    .btn-profile-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-edit-mode {
        background: linear-gradient(90deg, #d3c4f3 0%, #f3c3e9 100%);
        color: #4a4a5a;
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 4px 12px rgba(181, 109, 186, 0.2);
    }
    .btn-edit-mode:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(181, 109, 186, 0.3);
    }
    .btn-save-mode {
        background: linear-gradient(90deg, #8ad19c 0%, #5cb874 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(92, 184, 116, 0.25);
    }
    .btn-save-mode:hover {
        transform: translateY(-1px);
        background: linear-gradient(90deg, #7ec991 0%, #4ea865 100%);
    }
    .btn-cancel-mode {
        background: #f0f0f4;
        color: #7a7a8c;
        border: 1px solid #e1e1e8;
    }
    .btn-cancel-mode:hover {
        background: #e5e5ea;
        color: #4a4a5a;
    }

    .alert-banner {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .alert-success {
        background-color: #e8f5e9;
        border: 1px solid #c8e6c9;
        color: #2e7d32;
        font-weight: 600;
    }
    .alert-danger {
        background-color: #fde8e8;
        border: 1px solid #f8b4b4;
        color: #9b1c1c;
    }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .avatar-wrapper { width: 90px; height: 90px; right: 20px; }
        .card-header-flex { flex-direction: column; gap: 15px; }
    }
</style>
@endpush

@php
    $isDosen = $user->isDosen();
    $isMahasiswa = $user->isMahasiswa();
    $roleLabel = $isDosen ? 'Dosen' : 'Mahasiswa';
@endphp

@section('content')
    <div class="profile-header">
        <div class="cover-photo"></div>
        <div class="avatar-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="#9d62cc" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-banner alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-banner alert-danger">
            <div style="font-weight: 700; margin-bottom: 6px;">Gagal memperbarui profil:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" id="profile-form">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header-flex" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <div>
                    <h3 class="section-title">Data Diri</h3>
                    <p class="section-desc" style="margin-bottom: 0;">Informasi Pribadi Pengguna</p>
                </div>
                <div id="action-buttons" style="display: flex; gap: 10px;">
                    <button type="button" id="btn-edit" onclick="toggleEdit(true)" class="btn-profile-action btn-edit-mode">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Edit Profil
                    </button>
                    <button type="button" id="btn-cancel" onclick="toggleEdit(false)" class="btn-profile-action btn-cancel-mode" style="display: none;">
                        Batal
                    </button>
                    <button type="submit" id="btn-save" class="btn-profile-action btn-save-mode" style="display: none;">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="input-name">Nama Lengkap :</label>
                    <div class="input-box">
                        <input type="text" id="input-name" name="name" class="editable-field" value="{{ old('name', $user->name) }}" readonly required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="select-gender">Jenis Kelamin :</label>
                    <div class="input-box">
                        <select id="select-gender" name="gender" class="editable-field" disabled>
                            <option value="" {{ empty(old('gender', $user->gender)) ? 'selected' : '' }}>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('gender', $user->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $user->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="input-nim-nip">{{ $isDosen ? 'NIP :' : 'NIM :' }}</label>
                    <div class="input-box">
                        <input type="text" id="input-nim-nip" name="nim_nip" class="editable-field" value="{{ old('nim_nip', $user->nim_nip) }}" readonly required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input-domicile">Domisili :</label>
                    <div class="input-box">
                        <input type="text" id="input-domicile" name="domicile" class="editable-field" value="{{ old('domicile', $user->domicile) }}" readonly placeholder="Contoh: Malang, Jawa Timur">
                    </div>
                </div>

                <div class="form-group">
                    <label for="input-email">Email :</label>
                    <div class="input-box">
                        <input type="email" id="input-email" name="email" class="editable-field" value="{{ old('email', $user->email) }}" readonly required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input-phone">No HP :</label>
                    <div class="input-box">
                        <input type="text" id="input-phone" name="phone" class="editable-field" value="{{ old('phone', $user->phone) }}" readonly placeholder="Contoh: +62 81234567890">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="section-title">Data Akademik</h3>
            <p class="section-desc">Informasi Pendidikan {{ $roleLabel }}</p>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="input-university">Perguruan Tinggi</label>
                    <div class="input-box">
                        <input type="text" id="input-university" name="university" class="editable-field" value="{{ old('university', $user->university ?? 'Universitas Brawijaya') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input-faculty">Fakultas</label>
                    <div class="input-box">
                        <input type="text" id="input-faculty" name="faculty" class="editable-field" value="{{ old('faculty', $user->faculty ?? 'Fakultas Ilmu Komputer') }}" readonly>
                    </div>
                </div>

                {{-- Tampilkan data khusus Mahasiswa --}}
                @if($isMahasiswa)
                <div class="form-group">
                    <label for="input-major">Program Studi</label>
                    <div class="input-box">
                        <input type="text" id="input-major" name="major" class="editable-field" value="{{ old('major', $user->major ?? 'Pendidikan Teknologi Informasi') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input-year">Angkatan</label>
                    <div class="input-box">
                        <input type="text" id="input-year" name="year" class="editable-field" value="{{ old('year', $user->year ?? '2023') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input-semester">Semester</label>
                    <div class="input-box">
                        <input type="text" id="input-semester" name="semester" class="editable-field" value="{{ old('semester', $user->semester ?? '8') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="input-gpa">IPK</label>
                    <div class="input-box">
                        <input type="text" id="input-gpa" name="gpa" class="editable-field" value="{{ old('gpa', $user->gpa ?? '3.99') }}" readonly>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>

    <script>
        function toggleEdit(isEditing) {
            const editableInputs = document.querySelectorAll('.editable-field');
            const btnEdit = document.getElementById('btn-edit');
            const btnCancel = document.getElementById('btn-cancel');
            const btnSave = document.getElementById('btn-save');
            const selectGender = document.getElementById('select-gender');

            if (isEditing) {
                editableInputs.forEach(input => {
                    input.readOnly = false;
                    input.classList.add('editing');
                });
                if (selectGender) {
                    selectGender.disabled = false;
                    selectGender.classList.add('editing');
                }
                btnEdit.style.display = 'none';
                btnCancel.style.display = 'inline-flex';
                btnSave.style.display = 'inline-flex';
            } else {
                document.getElementById('profile-form').reset();
                editableInputs.forEach(input => {
                    input.readOnly = true;
                    input.classList.remove('editing');
                });
                if (selectGender) {
                    selectGender.disabled = true;
                    selectGender.classList.remove('editing');
                }
                btnEdit.style.display = 'inline-flex';
                btnCancel.style.display = 'none';
                btnSave.style.display = 'none';
            }
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                toggleEdit(true);
            });
        @endif
    </script>
@endsection