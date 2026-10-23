@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    .dashboard-container { display: flex; flex-direction: column; gap: 20px; }
    
    .card { background: #ffffff; border: 1px solid #e5e5ea; border-radius: 16px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    
    .main-info-card { display: flex; justify-content: space-between; align-items: stretch; gap: 30px; }
    .info-left { flex: 1; display: flex; flex-direction: column; }
    .tags { display: flex; gap: 10px; margin-bottom: 12px; }
    .tag { padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
    .tag-yellow { background-color: #fdf3d8; color: #b58e3a; }
    .tag-purple { background-color: #eee8fa; color: #7b5ea7; }
    .info-left .title { font-size: 1.6rem; color: #4a4a5a; margin: 0 0 5px 0; font-weight: 800; }
    .info-left .subtitle { color: #8a8a9d; margin: 0 0 25px 0; font-size: 0.95rem; }
    .dosen-row { display: flex; gap: 40px; flex-wrap: wrap; margin-top: auto; }
    .dosen { color: #555568; font-size: 0.9rem; }
    .dosen span { color: #8a8a9d; font-weight: 500; }

    .info-right { width: 320px; display: flex; align-items: center; }
    .progress-box { background-color: #f4f4f6; padding: 20px; border-radius: 12px; width: 100%; }
    .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; color: #555568; font-weight: 600; font-size: 0.9rem; }
    .progress-header strong { color: #9d62cc; font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .progress-bar-bg { width: 100%; height: 10px; background-color: #d8d8e0; border-radius: 5px; margin-bottom: 10px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background-color: #9d62cc; border-radius: 5px; }
    .progress-text { text-align: center; font-size: 0.75rem; color: #8a8a9d; }

    .alert-card { background-color: #faeaea; border-color: #f5c6c6; padding: 20px; }
    .alert-header { display: flex; gap: 12px; margin-bottom: 15px; }
    .alert-icon { width: 24px; height: 24px; color: #d93838; flex-shrink: 0; }
    .alert-header h3 { color: #b02525; font-size: 0.9rem; margin: 0 0 3px 0; font-weight: 800; }
    .alert-header p { color: #8c5252; font-size: 0.8rem; margin: 0; }
    .alert-body { background-color: #ffffff; border-radius: 10px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #f0d0d0; }
    .alert-content h4 { color: #b02525; font-size: 0.9rem; margin: 0 0 5px 0; font-weight: 800; text-decoration: underline; }
    .alert-content p { color: #555568; font-size: 0.85rem; margin: 0; }
    .btn-red { background-color: #cc0000; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; transition: 0.2s; }
    .btn-red:hover { background-color: #a30000; }

    .section-header { margin-bottom: 15px; margin-top: 10px; }
    .section-title { color: #4a4a5a; font-size: 1.3rem; margin: 0 0 5px 0; font-weight: 800; }
    .section-desc { color: #8a8a9d; font-size: 0.9rem; margin: 0; }
    
    .grid-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    .bab-card { display: flex; flex-direction: column; padding: 20px; border-radius: 12px; border: 1px solid #e5e5ea; background: #fff; }
    .bab-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .bab-label { color: #8a8a9d; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .status { font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; }
    .status-green { background-color: #e3f9e9; color: #28a745; }
    .status-red { background-color: #fce8e8; color: #d93838; }
    .status-grey { background-color: #e9e9ed; color: #7a7a8c; }
    
    .bab-title { color: #4a4a5a; font-size: 0.95rem; margin: 0 0 15px 0; font-weight: 700; line-height: 1.4; }
    .bab-stats { margin-bottom: 20px; flex-grow: 1; }
    .stat { display: flex; justify-content: space-between; font-size: 0.75rem; color: #8a8a9d; margin-bottom: 5px; }
    
    .btn-outline { width: 100%; background-color: #eee8fa; color: #7b5ea7; border: none; padding: 10px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s; text-align: center; text-decoration: none; display: block; }
    .btn-outline:hover { background-color: #e2d9f7; }

    .agenda-card { padding: 25px; }
    .agenda-box { border: 1px solid #e5e5ea; border-radius: 12px; padding: 15px 20px; }
    .tag-purple-light { background-color: #eee8fa; color: #7b5ea7; display: inline-block; margin-bottom: 8px; font-size: 0.75rem; padding: 5px 10px; border-radius: 6px; font-weight: 700; }
    .agenda-box h4 { color: #4a4a5a; font-size: 0.95rem; margin: 0 0 5px 0; font-weight: 700; }
    .agenda-box p { color: #8a8a9d; font-size: 0.85rem; margin: 0; }

    @media (max-width: 1024px) {
        .grid-cards { grid-template-columns: repeat(2, 1fr); }
        .main-info-card { flex-direction: column; }
        .info-right { width: 100%; }
    }
    @media (max-width: 768px) {
        .grid-cards { grid-template-columns: 1fr; }
        .alert-body { flex-direction: column; align-items: flex-start; gap: 15px; }
        .dosen-row { flex-direction: column; gap: 10px; }
    }
</style>
@endpush

@section('content')
    <div class="dashboard-container">
        
        <section class="card main-info-card">
            <div class="info-left">
                <div class="tags">
                    <span class="tag tag-yellow">{{ $thesis->status ?? 'Bimbingan Berjalan' }}</span>
                    <span class="tag tag-purple">Risiko {{ $thesis->risk_level ?? 'Rendah' }}</span>
                </div>
                <h2 class="title">{{ $thesis->title ?? 'Judul Skripsi' }}</h2>
                <p class="subtitle">{{ $thesis->objective ?? 'Tujuan Penelitian' }}</p>
                
                <div class="dosen-row">
                    <div class="dosen">
                        <span>Pembimbing 1 |</span> <strong>{{ $thesis->supervisor1->name ?? 'Ir. Dr. Arka Mahendrata., S.T., S.kom.' }}</strong>
                    </div>
                    <div class="dosen">
                        <span>Pembimbing 2 |</span> <strong>{{ $thesis->supervisor2->name ?? 'Anindya Sekar Pranadya., S.pd., M. Cs.' }}</strong>
                    </div>
                </div>
            </div>

            <div class="info-right">
                <div class="progress-box">
                    <div class="progress-header">
                        <span>Total Progres Bab</span>
                        <strong>40%</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: 40%;"></div>
                    </div>
                    <p class="progress-text">2 dari 5 Bab telah diperbarui</p>
                </div>
            </div>
        </section>

        <section class="card alert-card">
            <div class="alert-header">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <div>
                    <h3>Perhatian : Anda memiliki 1 butir revisi yang perlu di perbaiki</h3>
                    <p>Periksa catatan dari dosen pembimbing dan berikan respons atau unggah draf bab versi terbaru</p>
                </div>
            </div>
            <div class="alert-body">
                <div class="alert-content">
                    <h4>BAB III: METODOLOGI PENELITIAN</h4>
                    <p>Tambahkan diagram arsitektur modular monolith pada sub-bab perancangan sistem</p>
                </div>
                <a href="#" class="btn-red">Lihat Bab</a>
            </div>
        </section>

        <div class="section-header">
            <h2 class="section-title">Struktur & Pengembangan Bab</h2>
            <p class="section-desc">Unggah draft berkas per bab, pantau riwayat versi dan bandingkan perubahan.</p>
        </div>
        
        <div class="grid-cards">
            <div class="bab-card">
                <div class="bab-header">
                    <span class="bab-label">BAB 1</span>
                    <span class="status status-green">Disetujui</span>
                </div>
                <h3 class="bab-title">BAB I: Pendahuluan</h3>
                <div class="bab-stats">
                    <div class="stat"><span>Versi Terkini:</span> <span>v1</span></div>
                    <div class="stat"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                </div>
                <a href="#" class="btn-outline">Buka Bab & Versi</a>
            </div>

            <div class="bab-card">
                <div class="bab-header">
                    <span class="bab-label">BAB 2</span>
                    <span class="status status-green">Disetujui</span>
                </div>
                <h3 class="bab-title">BAB II: Tinjauan Pustaka</h3>
                <div class="bab-stats">
                    <div class="stat"><span>Versi Terkini:</span> <span>v1</span></div>
                    <div class="stat"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                </div>
                <a href="#" class="btn-outline">Buka Bab & Versi</a>
            </div>

            <div class="bab-card">
                <div class="bab-header">
                    <span class="bab-label">BAB 3</span>
                    <span class="status status-red">Perlu Revisi</span>
                </div>
                <h3 class="bab-title">BAB III: Metodologi Penelitian</h3>
                <div class="bab-stats">
                    <div class="stat"><span>Versi Terkini:</span> <span>v1</span></div>
                    <div class="stat"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                </div>
                <a href="#" class="btn-outline">Buka Bab & Versi</a>
            </div>

            <div class="bab-card">
                <div class="bab-header">
                    <span class="bab-label">BAB 4</span>
                    <span class="status status-grey">Belum Dimulai</span>
                </div>
                <h3 class="bab-title">BAB IV: Hasil Penelitian</h3>
                <div class="bab-stats">
                    <div class="stat"><span>Versi Terkini:</span> <span>v1</span></div>
                    <div class="stat"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                </div>
                <a href="#" class="btn-outline">Buka Bab & Versi</a>
            </div>
        </div>

        <section class="card agenda-card">
            <h2 class="section-title">Agenda Bimbingan Terjadwal</h2>
            <p class="section-desc" style="margin-bottom: 15px;">Daftar sesi konsultasi dan bimbingan aktif bersama dosen pembimbing</p>
            
            <div class="agenda-box">
                <span class="tag-purple-light">Disetujui/Terjadwal</span>
                <h4>Konsultasi Perbaikan Metodologi BAB III & Diagram Arsitektur</h4>
                <p>Waktu : Sunday, 20 October 2026-0.9.00 WIB | Tempat: Ruang Dosen F7.3, Gedung Filkom</p>
            </div>
        </section>
        
    </div>
@endsection