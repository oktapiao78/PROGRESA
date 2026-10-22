@extends('layouts.app')

@section('title', 'Detail Bab - Portal Tugas Akhir')

@push('styles')
<style>
    /* --- Custom Header untuk Halaman Detail (Gambar 3, 4, 5) --- */
    .detail-header {
        background-color: #d8c8f0; /* Warna ungu muda header */
        padding: 15px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .detail-header h1 {
        color: #4a4a5a;
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0;
    }
    .btn-back {
        background: none;
        border: none;
        cursor: pointer;
        color: #6b6b7b;
        display: flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-back svg {
        width: 28px;
        height: 28px;
    }

    /* --- Card Utama --- */
    .card-chapter {
        background: #ffffff;
        border: 1px solid #e1e1e8;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .chapter-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .breadcrumb {
        font-size: 0.9rem;
        color: #8a8a9d;
        margin-bottom: 10px;
        display: block;
    }

    .chapter-title {
        font-size: 1.8rem;
        color: #555568;
        margin-bottom: 10px;
    }

    .chapter-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.9rem;
        color: #8a8a9d;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .status-disetujui { background-color: #e3f9e9; color: #28a745; }
    .status-revisi { background-color: #fce8e8; color: #d93838; }
    .status-belum { background-color: #e9e9ed; color: #7a7a8c; }

    .btn-upload {
        background-color: #059669; /* Hijau tombol upload */
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s;
    }
    .btn-upload:hover { background-color: #047857; }

    /* --- Tabs --- */
    .tabs-container {
        border-bottom: 1px solid #e1e1e8;
        display: flex;
        gap: 30px;
        margin-bottom: 20px;
    }
    .tab-btn {
        background: none;
        border: none;
        padding: 10px 0;
        font-size: 1rem;
        font-weight: 600;
        color: #8a8a9d;
        cursor: pointer;
        position: relative;
        transition: color 0.3s;
    }
    .tab-btn:hover { color: #555568; }
    .tab-btn.active {
        color: #7b5ea7;
    }
    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #b58e3a; /* Warna garis bawah tab aktif */
        border-radius: 3px 3px 0 0;
    }

    /* --- Tab Content: Dokumen & Versi --- */
    .version-card {
        border: 1px solid #e1e1e8;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
    }
    .version-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    .version-badge { background-color: #059669; color: white; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; }
    .version-active { color: #059669; font-weight: 600; font-size: 0.9rem; }
    .version-date { color: #8a8a9d; font-size: 0.9rem; }
    
    .file-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .file-name { font-weight: 600; color: #555568; }
    .file-size { color: #8a8a9d; font-size: 0.9rem; }
    .btn-download { background-color: #f0eff4; color: #555568; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px; text-decoration: none; }
    
    .note-box { background-color: #faeaea; border-radius: 8px; padding: 15px; font-size: 0.95rem; color: #8c5252; }

    /* --- Tab Content: Revisi --- */
    .revision-box {
        text-align: center;
        padding: 40px;
        color: #8a8a9d;
    }

    /* --- Tab Content: Approval --- */
    .approval-flow {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .approval-card {
        border: 1px solid #d8c8f0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    .approval-card.active { border: 2px solid #b58e3a; }
    .approval-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.9rem; font-weight: 700; color: #7b5ea7; }
    .approval-card h4 { font-size: 1.1rem; color: #555568; margin-bottom: 15px; }
    .approval-note {
        background-color: #f8f7fc;
        border: 1px solid #e1e1e8;
        border-radius: 8px;
        padding: 15px;
        font-size: 0.9rem;
        color: #6b6b7b;
        font-style: italic;
        flex-grow: 1;
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .chapter-top { flex-direction: column; gap: 15px; }
        .approval-flow { grid-template-columns: 1fr; }
        .tabs-container { gap: 15px; overflow-x: auto; white-space: nowrap; padding-bottom: 5px; }
    }
</style>
@endpush

@section('content')
    <!-- Menggunakan Alpine.js untuk Logika Tab -->
    <div x-data="{ tab: 'dokumen' }">
        
        <!-- Card Utama -->
        <div class="card-chapter">
            <div class="chapter-top">
                <div>
                    <span class="breadcrumb">Skripsi Mahasiswa > BAB {{ $bab->nomor_urut ?? '1' }}</span>
                    <h2 class="chapter-title">BAB {{ $bab->nomor_urut ?? 'I' }} : {{ $bab->judul ?? 'Pendahuluan' }}</h2>
                    <div class="chapter-meta">
                        <span class="status-badge {{ $bab->status == 'Disetujui' ? 'status-disetujui' : ($bab->status == 'Perlu Revisi' ? 'status-revisi' : 'status-belum') }}">
                            {{ $bab->status ?? 'Disetujui' }}
                        </span>
                        <span>Total {{ $bab->versi_terkini ?? 1 }} Versi Terdaftar &bull; {{ $bab->revisi_terbuka ?? 0 }} Revisi Terbuka</span>
                    </div>
                </div>
                <!-- Tombol Upload Memicu Modal (Akan kita buat nanti) -->
                <button class="btn-upload" onclick="alert('Fitur upload modal akan dibuka di sini')">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Unggah Draft Baru
                </button>
            </div>

            <!-- Navigasi Tab -->
            <div class="tabs-container">
                <button class="tab-btn" :class="{'active': tab === 'dokumen'}" @click="tab = 'dokumen'">Dokumen dan Riwayat Versi</button>
                <button class="tab-btn" :class="{'active': tab === 'revisi'}" @click="tab = 'revisi'">Daftar Butir Revisi</button>
                <button class="tab-btn" :class="{'active': tab === 'approval'}" @click="tab = 'approval'">Persetujuan Bertingkat Approval</button>
            </div>

            <!-- ISI TAB 1: Dokumen & Versi -->
            <div x-show="tab === 'dokumen'" x-transition>
                @forelse($versi_dokumen ?? [] as $index => $versi)
                    <!-- Kartu Versi -->
                    <div class="version-card">
                        <div class="version-header">
                            <span class="version-badge">Versi {{ $versi->nomor_versi }}</span>
                            @if($index == 0) <span class="version-active">&bull; Versi Aktif</span> @endif
                            <span class="version-date">&bull; Diunggah oleh {{ $versi->uploader->nama ?? 'Mahasiswa' }} ({{ $versi->created_at->diffForHumans() ?? '2 Days Ago' }})</span>
                        </div>
                        <div class="file-info">
                            <div>
                                <span class="file-name">{{ $versi->nama_file ?? 'Draft_Bab_1_Naura_v1.Docx' }}</span>
                                <span class="file-size">({{ $versi->ukuran_file ?? '100.0KB' }})</span>
                            </div>
                            <a href="{{ $versi->file_url ?? '#' }}" class="btn-download" download>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Unduh File
                            </a>
                        </div>
                        <div class="note-box">
                            <strong>Catatan Mahasiswa :</strong> {{ $versi->catatan_mahasiswa ?? 'Pengajuan awal pendahuluan dan rumusan masalah' }}
                        </div>
                    </div>
                @empty
                    <!-- Dummy Data jika database kosong -->
                    <div class="version-card">
                        <div class="version-header">
                            <span class="version-badge">Versi 1</span>
                            <span class="version-active">&bull; Versi Aktif</span>
                            <span class="version-date">&bull; Diunggah oleh Naura Ayunda (2 Days Ago)</span>
                        </div>
                        <div class="file-info">
                            <div>
                                <span class="file-name">Draft_Bab_1_Naura_v1.Docx</span>
                                <span class="file-size">(100.0KB)</span>
                            </div>
                            <a href="#" class="btn-download" download>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Unduh File
                            </a>
                        </div>
                        <div class="note-box">
                            <strong>Catatan Mahasiswa :</strong> Pengajuan awal pendahuluan dan rumusan masalah
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- ISI TAB 2: Daftar Butir Revisi -->
            <div x-show="tab === 'revisi'" x-transition x-cloak>
                <div class="revision-box">
                    @if(isset($revisi_list) && $revisi_list->count() > 0)
                        <!-- Looping data revisi asli di sini -->
                        <p>Ada {{ $revisi_list->count() }} butir revisi. (Desain tabel/list revisi menyusul)</p>
                    @else
                        <p>Belum ada butir catatan revisi untuk bab ini</p>
                    @endif
                </div>
            </div>

            <!-- ISI TAB 3: Persetujuan Bertingkat -->
            <div x-show="tab === 'approval'" x-transition x-cloak>
                <p style="color: #8a8a9d; margin-bottom: 20px;">Setiap bab membutuhkan verifikasi bertahap sebelum dinyatakan selesai dan sah untuk diajukan seminar/sidang.</p>
                
                <div class="approval-flow">
                    <!-- Tahap 1 -->
                    <div class="approval-card active">
                        <div class="approval-header">
                            <span>Tahap 1: PEMBIMBING 1</span>
                            <span class="status-badge status-disetujui">Disetujui</span>
                        </div>
                        <h4>{{ $bab->pembimbing1->nama ?? 'Ir. Dr. Arka Mahendrata, S.T., S.kom.' }}</h4>
                        <div class="approval-note">
                            "Latar belakang dan rumusan masalah sudah kuat dan disetujui."
                        </div>
                    </div>

                    <!-- Tahap 2 -->
                    <div class="approval-card active">
                        <div class="approval-header">
                            <span>Tahap 2: PEMBIMBING 2</span>
                            <span class="status-badge status-disetujui">Disetujui</span>
                        </div>
                        <h4>{{ $bab->pembimbing2->nama ?? 'Anindya Sekar Pranadya., S.pd., M. Cs.' }}</h4>
                        <div class="approval-note">
                            "Disetujui untuk melanjutkan pada Bab 2."
                        </div>
                    </div>

                    <!-- Tahap 3 -->
                    <div class="approval-card">
                        <div class="approval-header">
                            <span>Tahap 3: KOORDINATOR TA</span>
                            <span class="status-badge status-belum">Menunggu Validasi</span>
                        </div>
                        <h4>Validasi & Verifikasi Akhir</h4>
                        <div class="approval-note">
                            <!-- Kosong karena belum divalidasi -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<!-- Pastikan Alpine.js sudah dimuat di layout utama kamu. 
     Jika belum, tambahkan script ini di layouts/app.blade.php sebelum </body>:
     <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> 
-->
@endpush