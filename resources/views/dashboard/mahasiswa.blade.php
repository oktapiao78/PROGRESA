@extends('layouts.app', [
    'title' => 'Portal Tugas Akhir',
    'headerTitle' => 'Portal Tugas Akhir',
    'headerSubtitle' => 'Proses & Bimbingan Skripsi'
])

@push('styles')
<style>
    .dashboard-container { display: flex; flex-direction: column; gap: 24px; }
    .card { background: #ffffff; border: 1px solid #e1e1e8; border-radius: 20px; padding: 26px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .main-info-card { display: flex; justify-content: space-between; align-items: stretch; gap: 30px; }
    .info-left { flex: 1; display: flex; flex-direction: column; }
    .tags { display: flex; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; }
    .tag { padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; }
    .tag-yellow { background-color: #FDF3D8; color: #B58E3A; }
    .tag-purple { background-color: #EEE8FA; color: #7B5EA7; }
    .info-left .title { font-size: 1.55rem; color: #4a4a5a; margin: 0 0 6px 0; font-weight: 800; line-height: 1.25; letter-spacing: -0.01em; }
    .info-left .subtitle { color: #8a8a9d; margin: 0 0 22px 0; font-size: 0.9rem; line-height: 1.5; }
    .dosen-row { display: flex; gap: 30px; flex-wrap: wrap; margin-top: auto; }
    .dosen { color: #555568; font-size: 0.85rem; }
    .dosen span { color: #8a8a9d; font-weight: 500; }

    .info-right { width: 320px; display: flex; align-items: center; }
    .progress-box { background-color: #F4F4F6; padding: 22px; border-radius: 16px; width: 100%; }
    .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; color: #555568; font-weight: 600; font-size: 0.9rem; }
    .progress-header strong { color: #9D62CC; font-size: 1.85rem; font-weight: 800; line-height: 1; }
    .progress-bar-bg { width: 100%; height: 10px; background-color: #d8d8e0; border-radius: 5px; margin-bottom: 12px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background-color: #9D62CC; border-radius: 5px; transition: width 0.3s ease; }
    .progress-text { text-align: center; font-size: 0.75rem; color: #8a8a9d; margin: 0; }

    .alert-card { background-color: #FAEAEA; border: 1px solid #F5C6C6; border-radius: 18px; padding: 20px; }
    .alert-header { display: flex; gap: 12px; margin-bottom: 15px; }
    .alert-icon { width: 24px; height: 24px; color: #d93838; flex-shrink: 0; }
    .alert-header h3 { color: #b02525; font-size: 0.9rem; margin: 0 0 3px 0; font-weight: 800; }
    .alert-header p { color: #8c5252; font-size: 0.8rem; margin: 0; }
    .alert-body { background-color: #ffffff; border-radius: 12px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #f0d0d0; gap: 15px; margin-bottom: 10px; }
    .alert-body:last-child { margin-bottom: 0; }
    .alert-content h4 { color: #b02525; font-size: 0.9rem; margin: 0 0 4px 0; font-weight: 800; }
    .alert-content p { color: #555568; font-size: 0.85rem; margin: 0; }
    .btn-red { background-color: #cc0000; color: white; border: none; padding: 10px 22px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.85rem; text-decoration: none; transition: 0.2s; white-space: nowrap; }
    .btn-red:hover { background-color: #a30000; }

    .section-header { margin-bottom: 12px; margin-top: 4px; }
    .section-title { color: #4a4a5a; font-size: 1.25rem; margin: 0 0 4px 0; font-weight: 800; }
    .section-desc { color: #8a8a9d; font-size: 0.85rem; margin: 0; }

    .grid-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .bab-card { display: flex; flex-direction: column; padding: 20px; border-radius: 16px; border: 1px solid #e1e1e8; background: #fff; justify-content: space-between; }
    .bab-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px; }
    .bab-label { color: #8a8a9d; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .status { font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; }
    .status-green { background-color: #D9FBEA; color: #009E70; }
    .status-red { background-color: #FCE8E8; color: #d93838; }
    .status-yellow { background-color: #FDF3D8; color: #b58e3a; }
    .status-grey { background-color: #E9E9ED; color: #7a7a8c; }

    .bab-title { color: #4a4a5a; font-size: 0.95rem; margin: 0 0 15px 0; font-weight: 700; line-height: 1.4; }
    .bab-stats { margin-bottom: 20px; flex-grow: 1; }
    .stat { display: flex; justify-content: space-between; font-size: 0.75rem; color: #8a8a9d; margin-bottom: 5px; }

    .btn-outline { width: 100%; background-color: #DDD7FA; color: #7B5EA7; border: none; padding: 10px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s; text-align: center; text-decoration: none; display: block; }
    .btn-outline:hover { background-color: #DCD7FE; }

    .agenda-card { padding: 26px; }
    .agenda-box { border: 1px solid #e1e1e8; border-radius: 16px; padding: 18px 22px; margin-bottom: 12px; }
    .agenda-box:last-child { margin-bottom: 0; }
    .tag-purple-light { background-color: #EEE8FA; color: #7B5EA7; display: inline-block; margin-bottom: 8px; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 700; }
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
    @if(!$thesis)
        <section class="card" style="text-align: center; padding: 50px 20px;">
            <h2 class="section-title">Belum Ada Data Skripsi Terdaftar</h2>
            <p class="section-desc" style="margin-bottom: 24px;">
                Data tugas akhir / skripsi Anda belum didaftarkan oleh Koordinator TA atau Admin. Silakan hubungi koordinator program studi Anda.
            </p>
            <a href="{{ route('profile.mahasiswa') }}" class="btn-outline" style="max-width: 240px; margin: 0 auto;">Lihat Profil Mahasiswa</a>
        </section>
    @else
        <!-- Card 1: Informasi Utama Skripsi (Sesuai Screenshot 8) -->
        <section class="card main-info-card">
            <div class="info-left">
                <div class="tags">
                    <span class="tag tag-yellow">{{ $thesis->status }}</span>
                    <span class="tag tag-purple">{{ $thesis->risk_level }}</span>
                </div>
                <h2 class="title">{{ $thesis->title }}</h2>
                <p class="subtitle">{{ $thesis->objective ?: 'Tujuan Penelitian' }}</p>

                <div class="dosen-row">
                    <div class="dosen">
                        <span>Pembimbing 1 |</span> <strong>{{ $thesis->supervisor1?->name ?? 'Belum Ditugaskan' }}</strong>
                    </div>
                    <div class="dosen">
                        <span>Pembimbing 2 |</span> <strong>{{ $thesis->supervisor2?->name ?? 'Belum Ditugaskan' }}</strong>
                    </div>
                </div>
            </div>

            <div class="info-right">
                <div class="progress-box">
                    <div class="progress-header">
                        <span>Total Progres Bab</span>
                        <strong>{{ $thesis->progress_percentage }}%</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $thesis->progress_percentage }}%;"></div>
                    </div>
                    <p class="progress-text">{{ $thesis->approved_count }} dari {{ $thesis->total_chapters }} Bab telah disetujui ({{ $thesis->uploaded_count }} diunggah)</p>
                </div>
            </div>
        </section>

        <!-- Card 2: Peringatan Revisi (Jika Ada) -->
        @if($openRevisions->isNotEmpty())
            <section class="card alert-card">
                <div class="alert-header">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <div>
                        <h3>Perhatian : Anda memiliki {{ $openRevisions->count() }} butir revisi yang perlu di perbaiki</h3>
                        <p>Periksa catatan dari dosen pembimbing dan berikan respons atau unggah draf bab versi terbaru</p>
                    </div>
                </div>
                @foreach($openRevisions->take(3) as $rev)
                    <div class="alert-body">
                        <div class="alert-content">
                            <h4>{{ strtoupper($rev->chapter->title) }}</h4>
                            <p>{{ $rev->note }} <span style="color:#8a8a9d; font-size:0.75rem;">({{ $rev->supervisor?->name ?? 'Dosen' }})</span></p>
                        </div>
                        <a href="{{ route('chapters.show', $rev->thesis_chapter_id) }}" class="btn-red">Lihat Bab</a>
                    </div>
                @endforeach
            </section>
        @endif

        <!-- Card 3: Struktur & Pengembangan Bab -->
        <div>
            <div class="section-header">
                <h2 class="section-title">Struktur &amp; Pengembangan Bab</h2>
                <p class="section-desc">Unggah draft berkas per bab, pantau riwayat versi dan bandingkan perubahan.</p>
            </div>

            <div class="grid-cards">
                @foreach($thesis->chapters->take(4) as $bab)
                    @php
                        $statusClass = match($bab->status_enum) {
                            \App\Enums\ChapterStatus::APPROVED => 'status-green',
                            \App\Enums\ChapterStatus::REVISION_REQUIRED => 'status-red',
                            \App\Enums\ChapterStatus::SUBMITTED, \App\Enums\ChapterStatus::IN_REVIEW, \App\Enums\ChapterStatus::DRAFT => 'status-yellow',
                            default => 'status-grey',
                        };
                    @endphp
                    <div class="bab-card">
                        <div>
                            <div class="bab-header">
                                <span class="bab-label">BAB {{ $bab->order }}</span>
                                <span class="status {{ $statusClass }}">{{ $bab->status }}</span>
                            </div>
                            <h3 class="bab-title">
                                BAB {{ \App\Helpers\RomanHelper::toRoman($bab->order) }}: {{ $bab->title }}
                            </h3>
                            <div class="bab-stats">
                                <div class="stat">
                                    <span>Versi Terkini:</span>
                                    <span>{{ $bab->version > 0 ? 'v' . $bab->version : 'v1' }}</span>
                                </div>
                                <div class="stat">
                                    <span>Butir Revisi:</span>
                                    <span>{{ $bab->revisions_open }} Terbuka. {{ $bab->revisions_total }} Total</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('chapters.show', $bab->id) }}" class="btn-outline">Buka Bab &amp; Versi</a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card 4: Agenda Bimbingan Terjadwal -->
        <section class="card agenda-card">
            <div style="margin-bottom:16px;">
                <h2 class="section-title">Agenda Bimbingan Terjadwal</h2>
                <p class="section-desc">Daftar sesi konsultasi dan bimbingan aktif bersama dosen pembimbing</p>
            </div>

            @forelse($scheduledSessions as $session)
                <div class="agenda-box">
                    <span class="tag-purple-light">Disetujui/Terjadwal</span>
                    <h4>{{ $session->topic }}</h4>
                    <p>
                        Waktu : {{ $session->scheduled_at ? $session->scheduled_at->isoFormat('dddd, D MMMM Y - HH.mm') . ' WIB' : '-' }}
                        @if($session->slot?->room_or_link)
                            | Tempat: {{ $session->slot->room_or_link }}
                        @endif
                    </p>
                </div>
            @empty
                <div class="agenda-box" style="text-align:center; color:#8a8a9d; padding: 24px;">
                    Belum ada sesi bimbingan terjadwal saat ini.
                </div>
            @endforelse
        </section>
    @endif
</div>
@endsection
