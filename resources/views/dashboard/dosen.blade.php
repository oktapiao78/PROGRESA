@extends('layouts.app', [
    'title' => 'Portal Tugas Akhir',
    'headerTitle' => 'Portal Tugas Akhir',
    'headerSubtitle' => 'Proses & Bimbingan Skripsi'
])

@push('styles')
<style>
    .dash-container { display: flex; flex-direction: column; gap: 24px; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .stat-card { background: #fff; border: 1px solid #e1e1e8; border-radius: 18px; padding: 22px; display: flex; justify-content: space-between; align-items: flex-start; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .stat-info h4 { color: #6b6b7b; font-size: 0.85rem; font-weight: 700; margin: 0 0 10px 0; }
    .stat-info .number { font-size: 1.7rem; font-weight: 800; color: #4a4a5a; }
    .stat-info .label { font-size: 0.8rem; color: #8a8a9d; font-weight: 600; margin-left: 4px; }
    
    .stat-badge { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; }
    .badge-p1 { background-color: #DDD7FA; color: #7B5EA7; }
    .badge-p2 { background-color: #D9FBEA; color: #009E70; }
    .badge-draft { background-color: #FDF3D8; color: #B58E3A; }
    .badge-slot { background-color: #DCEAFF; color: #1D4ED8; }

    .card { background: #fff; border: 1px solid #e1e1e8; border-radius: 20px; padding: 26px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .section-title { font-size: 1.25rem; font-weight: 800; color: #4a4a5a; margin: 0 0 4px 0; }
    .section-desc { font-size: 0.85rem; color: #8a8a9d; margin: 0 0 20px 0; }
    
    .empty-state { text-align: center; padding: 40px; color: #8a8a9d; font-size: 0.85rem; }

    .student-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .student-card { border: 1px solid #e1e1e8; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; background: #fff; hover:border-[#9D62CC]; transition: all 0.2s; }
    .student-card:hover { border-color: #9D62CC; box-shadow: 0 4px 12px rgba(157, 98, 204, 0.08); }
    .student-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .student-role { font-size: 0.75rem; font-weight: 800; color: #7B5EA7; text-transform: uppercase; background: #EEE8FA; padding: 3px 8px; border-radius: 6px; }
    .risk-badge { font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; }
    .risk-high { background-color: #FDF3D8; color: #B58E3A; }
    .risk-critical { background-color: #FCE8E8; color: #d93838; }
    
    .student-title { font-size: 0.95rem; font-weight: 700; color: #4a4a5a; margin: 0 0 6px 0; line-height: 1.4; flex-grow: 1; }
    .student-name { font-size: 0.8rem; color: #8a8a9d; margin-bottom: 16px; }
    
    .progress-label { display: flex; justify-content: space-between; font-size: 0.75rem; color: #8a8a9d; margin-bottom: 6px; font-weight: 600; }
    .progress-bar-bg { height: 8px; background-color: #e8e8ed; border-radius: 4px; margin-bottom: 16px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background-color: #9D62CC; border-radius: 4px; }
    
    .btn-detail { width: 100%; background-color: #DDD7FA; color: #7B5EA7; border: none; padding: 9px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s; text-align: center; text-decoration: none; display: block; margin-top: auto; }
    .btn-detail:hover { background-color: #DCD7FE; }

    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .student-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .student-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="dash-container">
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h4>Bimbingan Utama (P1)</h4>
                <span class="number">{{ $stats->p1_count }}</span> <span class="label">Mahasiswa</span>
            </div>
            <span class="stat-badge badge-p1">(P1)</span>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h4>Pendamping (P2)</h4>
                <span class="number">{{ $stats->p2_count }}</span> <span class="label">Mahasiswa</span>
            </div>
            <span class="stat-badge badge-p2">(P2)</span>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h4>Menunggu Review</h4>
                <span class="number">{{ $stats->draft_count }}</span> <span class="label">Bab</span>
            </div>
            <span class="stat-badge badge-draft">draft</span>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h4>Permintaan Bimbingan</h4>
                <span class="number">{{ $stats->request_count }}</span> <span class="label">Mahasiswa</span>
            </div>
            <span class="stat-badge badge-slot">slot</span>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">Draf Bab Perlu Ditinjau</h2>
        <p class="section-desc">Daftar unggahan draf bab terbaru dari mahasiswa bimbingan yang menunggu feedback atau approval Anda</p>

        @if(($pendingDrafts ?? collect())->isEmpty())
            <div class="empty-state">
                Tidak ada draft bab yang sedang menunggu review saat ini. Semua telah direspon.
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach($pendingDrafts as $draft)
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; border:1px solid #e1e1e8; border-radius:14px; padding:16px 20px; background:#fafafc;">
                        <div>
                            <div style="font-weight:700; color:#4a4a5a; font-size:0.95rem;">
                                {{ $draft->chapter_title }} <span style="color:#7b5ea7;">(v{{ $draft->version }})</span>
                            </div>
                            <div style="font-size:0.8rem; color:#6b6b7b; margin-top:3px;">
                                {{ $draft->student_name }} ({{ $draft->student_nim }}) &bull; {{ \Illuminate\Support\Str::limit($draft->thesis_title, 70) }}
                            </div>
                        </div>
                        <a href="{{ route('chapters.show', $draft->chapter_id) }}" style="background:#DDD7FA; color:#7B5EA7; padding:9px 18px; border-radius:10px; font-size:0.82rem; font-weight:700; text-decoration:none; white-space:nowrap; transition: background 0.2s;">
                            Review Bab &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2 class="section-title">Daftar Mahasiswa Bimbingan Aktif</h2>
        <p class="section-desc">Kemajuan bimbingan dan indikator risiko stagnansi mahasiswa</p>

        @if($mahasiswa_list->isEmpty())
            <div class="empty-state">Belum ada mahasiswa bimbingan yang terdaftar untuk Anda.</div>
        @else
            <div class="student-grid">
                @foreach($mahasiswa_list as $mhs)
                <div class="student-card">
                    <div>
                        <div class="student-header">
                            <span class="student-role">{{ $mhs->pembimbing }}</span>
                            @if($mhs->risk)
                                <span class="risk-badge {{ $mhs->risk == 'Risiko Tinggi (Stagnan)' ? 'risk-high' : 'risk-critical' }}">{{ $mhs->risk }}</span>
                            @endif
                        </div>
                        <h3 class="student-title">{{ $mhs->judul }}</h3>
                        <p class="student-name">{{ $mhs->nama }} ({{ $mhs->nim }})</p>

                        <div class="progress-label">
                            <span>Progress Skripsi:</span>
                            <span style="color: #7B5EA7; font-weight: 800;">{{ $mhs->progress }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $mhs->progress }}%;"></div>
                        </div>
                    </div>

                    <a href="{{ route('theses.show', $mhs->id) }}" class="btn-detail">
                        Detail Skripsi &rarr;
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
