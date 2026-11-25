@extends('layouts.app', [
    'title' => 'Profile - PROGRESA',
    'headerTitle' => 'Profile',
    'headerSubtitle' => 'Proses & Bimbingan Skripsi',
    'hasBack' => true,
    'backUrl' => route('dashboard')
])

@push('styles')
<style>
    .profile-card { background: #ffffff; border: 1px solid #e1e1e8; border-radius: 24px; padding: 28px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .section-title { font-size: 1.35rem; font-weight: 800; color: #4a4a5a; margin-bottom: 4px; }
    .section-desc { font-size: 0.85rem; color: #8a8a9d; margin-bottom: 22px; }

    .profile-header-banner { position: relative; margin-bottom: 30px; }
    .cover-photo {
        width: 100%;
        height: 190px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.85), rgba(15, 23, 42, 0.95)),
                    url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;
        border-radius: 22px;
    }
    .avatar-wrapper {
        position: absolute;
        bottom: -35px;
        right: 45px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid #ffffff;
        overflow: hidden;
        background: #DCD7FE;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.8rem; font-weight: 700; color: #555568; margin-bottom: 6px; }
    .input-box { position: relative; display: flex; align-items: center; }
    .input-box input, .input-box select {
        width: 100%;
        padding: 12px 16px;
        padding-right: 42px;
        border: 1px solid #d8d8e0;
        border-radius: 14px;
        font-size: 0.9rem;
        color: #333340;
        outline: none;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .editable-field.editing {
        background-color: #ffffff !important;
        border-color: #9D62CC !important;
        box-shadow: 0 0 0 3px rgba(157, 98, 204, 0.15) !important;
    }
    .editable-field:not(.editing) {
        background-color: #fafafc;
        color: #4a4a5a;
    }

    .input-icon {
        position: absolute;
        right: 14px;
        color: #9a9aae;
        pointer-events: none;
        width: 16px;
        height: 16px;
    }

    .btn-profile-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-edit-mode {
        background: #DDD7FA;
        color: #7B5EA7;
    }
    .btn-edit-mode:hover {
        background: #DCD7FE;
    }
    .btn-save-mode {
        background: #009E70;
        color: #ffffff;
    }
    .btn-save-mode:hover {
        background: #00875e;
    }
    .btn-cancel-mode {
        background: #FDE8E8;
        color: #4a4a5a;
    }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .avatar-wrapper { width: 85px; height: 85px; right: 20px; bottom: -25px; }
    }
</style>
@endpush

@php
    $isDosen = $user->isDosen();
    $isMahasiswa = $user->isMahasiswa();
    $roleLabel = $isDosen ? 'Dosen' : 'Mahasiswa';
@endphp

@section('content')
<div class="space-y-6">

    <!-- Profile Header Banner & Avatar (Sesuai Screenshot 10) -->
    <div class="profile-header-banner">
        <div class="cover-photo"></div>
        <div class="avatar-wrapper">
            <svg viewBox="0 0 24 24" class="w-16 h-16 text-[#7B5EA7]" fill="currentColor">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-[#E8F5E9] border border-[#C8E6C9] text-[#2E7D32] rounded-2xl text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" id="profile-form" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1: Data Diri -->
        <div class="profile-card">
            <div class="flex items-center justify-between gap-4 mb-2 flex-wrap">
                <div>
                    <h3 class="section-title">Data Diri</h3>
                    <p class="section-desc">Informasi Pribadi Pengguna</p>
                </div>
                <div id="action-buttons" class="flex items-center gap-2">
                    <button type="button" id="btn-edit" onclick="toggleEdit(true)" class="btn-profile-action btn-edit-mode">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Edit Profil
                    </button>
                    <button type="button" id="btn-cancel" onclick="toggleEdit(false)" class="btn-profile-action btn-cancel-mode" style="display: none;">
                        Batal
                    </button>
                    <button type="submit" id="btn-save" class="btn-profile-action btn-save-mode" style="display: none;">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <div class="form-grid">
                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="input-name">Nama Lengkap :</label>
                    <div class="input-box">
                        <input type="text" id="input-name" name="name" class="editable-field" value="{{ old('name', $user->name) }}" readonly required>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="form-group">
                    <label for="select-gender">Jenis Kelamin :</label>
                    <div class="input-box">
                        <select id="select-gender" name="gender" class="editable-field" disabled>
                            <option value="Perempuan" {{ old('gender', $user->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            <option value="Laki-laki" {{ old('gender', $user->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>

                <!-- NIM / NIP -->
                <div class="form-group">
                    <label for="input-nim-nip">{{ $isDosen ? 'NIP :' : 'NIM :' }}</label>
                    <div class="input-box">
                        <input type="text" id="input-nim-nip" name="nim_nip" class="editable-field" value="{{ old('nim_nip', $user->nim_nip) }}" readonly required>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </div>
                </div>

                <!-- Domisili -->
                <div class="form-group">
                    <label for="input-domicile">Domisili :</label>
                    <div class="input-box">
                        <input type="text" id="input-domicile" name="domicile" class="editable-field" value="{{ old('domicile', $user->domicile ?? 'Malang, Jawa Timur') }}" readonly>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="input-email">Email :</label>
                    <div class="input-box">
                        <input type="email" id="input-email" name="email" class="editable-field" value="{{ old('email', $user->email) }}" readonly required>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </div>
                </div>

                <!-- No HP -->
                <div class="form-group">
                    <label for="input-phone">No HP :</label>
                    <div class="input-box">
                        <input type="text" id="input-phone" name="phone" class="editable-field" value="{{ old('phone', $user->phone ?? '+ 62 8123451234567') }}" readonly>
                    </div>
                </div>

                <!-- Tanggal Lahir -->
                <div class="form-group sm:col-start-2">
                    <label for="input-birth-date">Tanggal Lahir :</label>
                    <div class="input-box">
                        <input type="text" id="input-birth-date" name="birth_date" class="editable-field" value="{{ old('birth_date', $user->birth_date ?? '12 Maret 2005') }}" readonly>
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Data Akademik -->
        <div class="profile-card">
            <h3 class="section-title">Data Akademik</h3>
            <p class="section-desc">Informasi Pendidikan {{ $roleLabel }}</p>

            <div class="form-grid">
                <div class="form-group">
                    <label>Perguruan Tinggi</label>
                    <div class="input-box">
                        <input type="text" name="university" class="editable-field" value="{{ old('university', $user->university ?? 'Universitas Brawijaya') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>Fakultas</label>
                    <div class="input-box">
                        <input type="text" name="faculty" class="editable-field" value="{{ old('faculty', $user->faculty ?? 'Fakultas Ilmu Komputer') }}" readonly>
                    </div>
                </div>

                @if($isMahasiswa)
                    <div class="form-group">
                        <label>Program Studi</label>
                        <div class="input-box">
                            <input type="text" name="major" class="editable-field" value="{{ old('major', $user->studyProgram->name ?? 'Pendidikan Teknologi Informasi') }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Angkatan</label>
                        <div class="input-box">
                            <input type="text" name="year" class="editable-field" value="{{ old('year', $user->year ?? '2023') }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <div class="input-box">
                            <input type="text" name="semester" class="editable-field" value="{{ old('semester', $user->semester ?? '8') }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>IPK</label>
                        <div class="input-box">
                            <input type="text" name="gpa" class="editable-field" value="{{ old('gpa', $user->gpa ?? '3.99') }}" readonly>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
    function toggleEdit(isEditing) {
        const editableInputs = document.querySelectorAll('.editable-field');
        const btnEdit = document.getElementById('btn-edit');
        const btnCancel = document.getElementById('btn-cancel');
        const btnSave = document.getElementById('btn-save');
        const selectGender = document.getElementById('select-gender');

        if (isEditing) {
            editableInputs.forEach(input => {
                if (input.id !== 'input-nim-nip') {
                    input.removeAttribute('readonly');
                    input.classList.add('editing');
                }
            });
            if (selectGender) {
                selectGender.removeAttribute('disabled');
                selectGender.classList.add('editing');
            }
            btnEdit.style.display = 'none';
            btnCancel.style.display = 'inline-flex';
            btnSave.style.display = 'inline-flex';
        } else {
            editableInputs.forEach(input => {
                input.setAttribute('readonly', 'readonly');
                input.classList.remove('editing');
            });
            if (selectGender) {
                selectGender.setAttribute('disabled', 'disabled');
                selectGender.classList.remove('editing');
            }
            btnEdit.style.display = 'inline-flex';
            btnCancel.style.display = 'none';
            btnSave.style.display = 'none';
        }
    }
</script>
@endsection
