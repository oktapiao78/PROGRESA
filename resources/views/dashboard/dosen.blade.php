@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@push('styles')
<style>
    .dash-container { display: flex; flex-direction: column; gap: 20px; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    .stat-card { background: #fff; border: 1px solid #e1e1e8; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: flex-start; }
    .stat-info h4 { color: #555568; font-size: 0.9rem; font-weight: 700; margin: 0 0 10px 0; }
    .stat-info .number { font-size: 1.5rem; font-weight: 800; color: #4a4a5a; }
    .stat-info .label { font-size: 0.85rem; color: #8a8a9d; font-weight: 600; }
    
    .stat-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
    .badge-p1 { background-color: #e2d9f7; color: #7b5ea7; }
    .badge-p2 { background-color: #d1f2eb; color: #28a745; }
    .badge-draft { background-color: #fdf3d8; color: #b58e3a; }
    .badge-slot { background-color: #dbeafe; color: #2563eb; }

    .card { background: #fff; border: 1px solid #e1e1e8; border-radius: 16px; padding: 25px; }
    .section-title { font-size: 1.3rem; font-weight: 800; color: #4a4a5a; margin: 0 0 5px 0; }
    .section-desc { font-size: 0.9rem; color: #8a8a9d; margin: 0 0 20px 0; }
    
    .empty-state { text-align: center; padding: 40px; color: #8a8a9d; font-size: 0.95rem; }

    .student-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    .student-card { border: 1px solid #e1e1e8; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; }
    .student-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .student-role { font-size: 0.75rem; font-weight: 700; color: #7b5ea7; text-transform: uppercase; }
    .risk-badge { font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 4px; }
    .risk-high { background-color: #fdf3d8; color: #b58e3a; }
    .risk-critical { background-color: #fce8e8; color: #d93838; }
    
    .student-title { font-size: 0.9rem; font-weight: 700; color: #333; margin: 0 0 5px 0; line-height: 1.4; flex-grow: 1; }
    .student-name { font-size: 0.8rem; color: #8a8a9d; margin-bottom: 15px; }
    
    .progress-label { display: flex; justify-content: space-between; font-size: 0.75rem; color: #8a8a9d; margin-bottom: 5px; }
    .progress-bar-bg { height: 6px; background-color: #e1e1e8; border-radius: 3px; margin-bottom: 15px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background-color: #10b981; border-radius: 3px; }
    
    .btn-detail { text-align: right; color: #7b5ea7; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: flex-end; gap: 5px; margin-top: auto; }
    .btn-detail:hover { color: #5a4585; }

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
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($pendingDrafts as $draft)
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; border:1px solid #e1e1e8; border-radius:12px; padding:14px 18px; background:#fafafc;">
                        <div>
                            <div style="font-weight:700; color:#4a4a5a; font-size:0.9rem;">
                                {{ $draft->chapter_title }} <span style="color:#7b5ea7;">(v{{ $draft->version }})</span>
                            </div>
                            <div style="font-size:0.8rem; color:#6b6b7b; margin-top:2px;">
                                {{ $draft->student_name }} ({{ $draft->student_nim }}) &bull; {{ \Illuminate\Support\Str::limit($draft->thesis_title, 70) }}
                            </div>
                        </div>
                        <a href="{{ route('chapters.show', $draft->chapter_id) }}" style="background:#7b5ea7; color:#fff; padding:8px 16px; border-radius:8px; font-size:0.8rem; font-weight:700; text-decoration:none; white-space:nowrap;">
                            Review Bab &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h2 class="section-title">Daftar Mahasiswa Bimbingan Aktif</h2>
        <p class="section-desc">Kemajuan Bimbingan dan indikator risiko stagnansi</p>

        @if($mahasiswa_list->isEmpty())
            <div class="empty-state">Belum ada mahasiswa bimbingan yang terdaftar untuk Anda.</div>
        @else
            <div class="student-grid">
                @foreach($mahasiswa_list as $mhs)
                <div class="student-card">
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
                        <span>{{ $mhs->progress }}%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $mhs->progress }}%;"></div>
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