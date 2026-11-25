@extends('layouts.app', [
    'title' => 'BAB ' . $chapter->order . ': ' . $chapter->title,
    'headerTitle' => 'BAB ' . $chapter->order . ': ' . $chapter->title,
    'headerSubtitle' => $thesis->student->name . ' (' . $thesis->student->nim_nip . ')'
])

@section('content')
@php
    $statusClass = match($chapter->status) {
        \App\Enums\ChapterStatus::APPROVED => 'status-disetujui',
        \App\Enums\ChapterStatus::REVISION_REQUIRED => 'status-revisi',
        \App\Enums\ChapterStatus::SUBMITTED, \App\Enums\ChapterStatus::IN_REVIEW => 'status-review',
        default => 'status-belum',
    };
@endphp

@push('styles')
<style>
    .card-chapter { background: #ffffff; border: 1px solid #e1e1e8; border-radius: 16px; padding: 25px; margin-bottom: 25px; }
    .chapter-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; }
    .breadcrumb { font-size: 0.85rem; color: #8a8a9d; margin-bottom: 8px; display: block; }
    .breadcrumb a { color: #7b5ea7; text-decoration: none; font-weight: 600; }
    .chapter-title { font-size: 1.5rem; font-weight: 800; color: #4a4a5a; margin: 0 0 10px 0; }
    .chapter-meta { display: flex; align-items: center; gap: 15px; font-size: 0.85rem; color: #8a8a9d; flex-wrap: wrap; }
    .status-badge { padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; }
    .status-disetujui { background-color: #e3f9e9; color: #28a745; }
    .status-revisi { background-color: #fce8e8; color: #d93838; }
    .status-review { background-color: #fdf3d8; color: #b58e3a; }
    .status-belum { background-color: #e9e9ed; color: #7a7a8c; }

    .btn-upload { background-color: #059669; color: white; border: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s; }
    .btn-upload:hover { background-color: #047857; }
    .btn-diff { background-color: #eee8fa; color: #7b5ea7; border: none; padding: 10px 16px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-diff:hover { background-color: #e2d9f7; }

    .tabs-container { border-bottom: 1px solid #e1e1e8; display: flex; gap: 28px; margin-bottom: 20px; overflow-x: auto; }
    .tab-btn { background: none; border: none; padding: 10px 0; font-size: 0.95rem; font-weight: 600; color: #8a8a9d; cursor: pointer; position: relative; white-space: nowrap; }
    .tab-btn:hover { color: #555568; }
    .tab-btn.active { color: #7b5ea7; }
    .tab-btn.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background-color: #9d62cc; border-radius: 3px 3px 0 0; }

    .version-card { border: 1px solid #e1e1e8; border-radius: 12px; padding: 18px; margin-bottom: 15px; }
    .version-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; }
    .version-badge { background-color: #059669; color: white; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; }
    .version-active { color: #059669; font-weight: 700; font-size: 0.85rem; }
    .version-date { color: #8a8a9d; font-size: 0.85rem; }
    .file-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 12px; flex-wrap: wrap; }
    .file-name { font-weight: 700; color: #4a4a5a; font-size: 0.95rem; }
    .file-size { color: #8a8a9d; font-size: 0.85rem; }
    .btn-download { background-color: #f0eff4; color: #4a4a5a; border: none; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
    .btn-download:hover { background-color: #e4e2ec; }
    .note-box { background-color: #faf5ff; border: 1px solid #e9d8fd; border-radius: 8px; padding: 12px 15px; font-size: 0.88rem; color: #553c9a; }

    .approval-flow { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
    .approval-card { border: 1px solid #d8c8f0; border-radius: 12px; padding: 18px; display: flex; flex-direction: column; gap: 10px; }
    .approval-card.active { border: 2px solid #9d62cc; }
    .approval-header { display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; font-weight: 700; color: #7b5ea7; gap: 8px; }
    .approval-card h4 { font-size: 0.98rem; font-weight: 700; color: #4a4a5a; margin: 0; }
    .approval-note { background-color: #f8f7fc; border: 1px solid #e1e1e8; border-radius: 8px; padding: 12px; font-size: 0.85rem; color: #6b6b7b; font-style: italic; flex-grow: 1; }

    @media (max-width: 768px) {
        .approval-flow { grid-template-columns: 1fr; }
    }
</style>
@endpush

<div x-data="{ tab: 'dokumen', showUploadModal: false }">
    <div class="card-chapter">
        <div class="chapter-top">
            <div>
                <span class="breadcrumb">
                    <a href="{{ route('theses.show', $thesis) }}">Skripsi</a> &gt; BAB {{ $chapter->order }}
                </span>
                <h2 class="chapter-title">{{ $chapter->title }}</h2>
                <div class="chapter-meta">
                    <span class="status-badge {{ $statusClass }}">{{ $chapter->status->label() }}</span>
                    <span>Total {{ $chapter->versions->count() }} Versi Terdaftar &bull; {{ $chapter->openRevisions->count() }} Revisi Terbuka</span>
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                @if($chapter->versions->count() >= 2)
                    <a href="{{ route('chapters.diff', $chapter) }}" class="btn-diff">
                        Bandingkan Versi
                    </a>
                @endif
                @if($isStudent)
                    <button type="button" class="btn-upload" @click="showUploadModal = true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        Unggah Draft Baru
                    </button>
                @endif
            </div>
        </div>

        <div class="tabs-container">
            <button type="button" class="tab-btn" :class="{'active': tab === 'dokumen'}" @click="tab = 'dokumen'">Dokumen dan Riwayat Versi</button>
            <button type="button" class="tab-btn" :class="{'active': tab === 'revisi'}" @click="tab = 'revisi'">
                Daftar Butir Revisi ({{ $chapter->revisionItems->count() }})
            </button>
            <button type="button" class="tab-btn" :class="{'active': tab === 'approval'}" @click="tab = 'approval'">Persetujuan Bertingkat</button>
        </div>

        <!-- TAB 1: Dokumen & Riwayat Versi -->
        <div x-show="tab === 'dokumen'" x-transition>
            @forelse($chapter->versions as $version)
                <div class="version-card">
                    <div class="version-header">
                        <span class="version-badge">Versi {{ $version->version_number }}</span>
                        @if($chapter->current_version_id === $version->id)
                            <span class="version-active">&bull; Versi Aktif</span>
                        @endif
                        <span class="version-date">
                            &bull; Diunggah oleh {{ $version->uploader?->name ?? 'Mahasiswa' }} ({{ $version->created_at->diffForHumans() }})
                        </span>
                    </div>
                    <div class="file-info">
                        <div>
                            <span class="file-name">{{ $version->original_filename }}</span>
                            <span class="file-size">({{ $version->formattedSize() }})</span>
                        </div>
                        <a href="{{ route('versions.download', $version) }}" class="btn-download">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Unduh File
                        </a>
                    </div>
                    @if($version->notes)
                        <div class="note-box">
                            <strong>Catatan Mahasiswa :</strong> {{ $version->notes }}
                        </div>
                    @endif
                </div>
            @empty
                <div style="text-align:center; padding:40px; color:#8a8a9d; border:1px dashed #d8d8e0; border-radius:12px;">
                    <p style="margin:0 0 12px 0;">Belum ada dokumen yang diunggah untuk bab ini.</p>
                    @if($isStudent)
                        <button type="button" class="btn-upload" @click="showUploadModal = true">Unggah Draft Pertama</button>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- TAB 2: Daftar Butir Revisi -->
        <div x-show="tab === 'revisi'" x-transition x-cloak>
            @if($isSupervisor)
                <form action="{{ route('revisions.store', $chapter) }}" method="POST" style="margin-bottom:20px; background:#faf5ff; padding:16px; border-radius:12px; border:1px solid #e9d8fd;">
                    @csrf
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:#553c9a; margin-bottom:8px;">Tambah Catatan Revisi Baru</label>
                    <textarea name="note" rows="2" required placeholder="Tuliskan poin revisi yang harus diperbaiki mahasiswa..." style="width:100%; padding:10px; border:1px solid #d8c8f0; border-radius:8px; font-size:0.85rem; margin-bottom:10px;"></textarea>
                    <button type="submit" style="background:#7b5ea7; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-size:0.8rem; font-weight:700; cursor:pointer;">Simpan Catatan Revisi</button>
                </form>
            @endif

            @forelse($chapter->revisionItems as $item)
                <div class="version-card" style="border-color: {{ $item->is_resolved ? '#c6f6d5' : '#fed7d7' }};">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; gap:10px; flex-wrap:wrap;">
                        <div>
                            <strong style="color:#4a4a5a; font-size:0.9rem;">{{ $item->supervisor?->name ?? 'Dosen Pembimbing' }}</strong>
                            <span style="color:#8a8a9d; font-size:0.8rem;">&bull; {{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        <span class="status-badge {{ $item->is_resolved ? 'status-disetujui' : 'status-revisi' }}">
                            {{ $item->is_resolved ? 'Selesai / Terverifikasi' : 'Perlu Perbaikan' }}
                        </span>
                    </div>

                    <p style="margin:0 0 12px 0; color:#4a4a5a; font-size:0.9rem;">{{ $item->note }}</p>

                    @if($item->student_response)
                        <div class="note-box" style="margin-bottom:10px;">
                            <strong>Tanggapan Mahasiswa:</strong> {{ $item->student_response }}
                        </div>
                    @endif

                    @if($isStudent && !$item->is_resolved)
                        <form action="{{ route('revisions.respond', $item) }}" method="POST" style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap;">
                            @csrf
                            <input type="text" name="student_response" required value="{{ $item->student_response }}" placeholder="Tulis tanggapan / penjelasan perbaikan Anda..." style="flex:1; min-width:220px; padding:8px 12px; border:1px solid #d8d8e0; border-radius:8px; font-size:0.85rem;">
                            <button type="submit" style="background:#7b5ea7; color:#fff; border:none; padding:8px 14px; border-radius:8px; font-size:0.8rem; font-weight:700; cursor:pointer;">Kirim Tanggapan</button>
                        </form>
                    @endif

                    @if($isSupervisor)
                        <form action="{{ route('revisions.toggle', $item) }}" method="POST" style="margin-top:8px;">
                            @csrf
                            <button type="submit" style="background:#eee8fa; color:#7b5ea7; border:none; padding:6px 12px; border-radius:8px; font-size:0.78rem; font-weight:700; cursor:pointer;">
                                {{ $item->is_resolved ? 'Buka Kembali Revisi' : 'Tandai Selesai' }}
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div style="text-align:center; padding:40px; color:#8a8a9d;">
                    Belum ada butir catatan revisi untuk bab ini.
                </div>
            @endforelse
        </div>

        <!-- TAB 3: Persetujuan Bertingkat -->
        <div x-show="tab === 'approval'" x-transition x-cloak>
            <p style="color: #8a8a9d; font-size:0.88rem; margin-bottom: 18px;">Setiap bab membutuhkan verifikasi bertahap sebelum dinyatakan selesai dan sah untuk diajukan seminar/sidang.</p>

            @php
                $p1Approval = $chapter->approvalForStage('pembimbing_1');
                $p2Approval = $chapter->approvalForStage('pembimbing_2');
                $koorApproval = $chapter->approvalForStage('koordinator');

                $badgeFor = function ($approval) {
                    if (!$approval) return ['status-belum', 'Menunggu Review'];
                    return match($approval->status) {
                        \App\Enums\ApprovalStatus::APPROVED => ['status-disetujui', $approval->status->label()],
                        \App\Enums\ApprovalStatus::REVISION_REQUESTED, \App\Enums\ApprovalStatus::REJECTED => ['status-revisi', $approval->status->label()],
                        default => ['status-belum', $approval->status->label()],
                    };
                };
            @endphp

            <div class="approval-flow">
                @php [$p1Class, $p1Label] = $badgeFor($p1Approval); @endphp
                <div class="approval-card {{ $p1Approval?->status === \App\Enums\ApprovalStatus::APPROVED ? 'active' : '' }}">
                    <div class="approval-header">
                        <span>Tahap 1: PEMBIMBING 1</span>
                        <span class="status-badge {{ $p1Class }}">{{ $p1Label }}</span>
                    </div>
                    <h4>{{ $thesis->supervisor1?->name ?? 'Belum Ditugaskan' }}</h4>
                    <div class="approval-note">
                        {{ $p1Approval?->feedback ? '"' . $p1Approval->feedback . '"' : 'Belum ada catatan review.' }}
                    </div>
                </div>

                @php [$p2Class, $p2Label] = $badgeFor($p2Approval); @endphp
                <div class="approval-card {{ $p2Approval?->status === \App\Enums\ApprovalStatus::APPROVED ? 'active' : '' }}">
                    <div class="approval-header">
                        <span>Tahap 2: PEMBIMBING 2</span>
                        <span class="status-badge {{ $p2Class }}">{{ $p2Label }}</span>
                    </div>
                    <h4>{{ $thesis->supervisor2?->name ?? 'Tidak Ada Pembimbing 2' }}</h4>
                    <div class="approval-note">
                        {{ $p2Approval?->feedback ? '"' . $p2Approval->feedback . '"' : 'Belum ada catatan review.' }}
                    </div>
                </div>

                @php [$koorClass, $koorLabel] = $badgeFor($koorApproval); @endphp
                <div class="approval-card {{ $koorApproval?->status === \App\Enums\ApprovalStatus::APPROVED ? 'active' : '' }}">
                    <div class="approval-header">
                        <span>Tahap 3: KOORDINATOR TA</span>
                        <span class="status-badge {{ $koorClass }}">{{ $koorLabel }}</span>
                    </div>
                    <h4>Validasi &amp; Verifikasi Akhir</h4>
                    <div class="approval-note">
                        {{ $koorApproval?->feedback ? '"' . $koorApproval->feedback . '"' : 'Menunggu validasi koordinator.' }}
                    </div>
                </div>
            </div>

            @php
                $userStage = null;
                if ($thesis->supervisor_1_id === auth()->id()) {
                    $userStage = 'pembimbing_1';
                } elseif ($thesis->supervisor_2_id === auth()->id()) {
                    $userStage = 'pembimbing_2';
                } elseif (auth()->user()->isKoordinator()) {
                    $userStage = 'koordinator';
                }
            @endphp

            @if($userStage)
                <form action="{{ route('approvals.stage', $chapter) }}" method="POST" style="margin-top:20px; background:#faf5ff; padding:18px; border-radius:12px; border:1px solid #e9d8fd;">
                    @csrf
                    <input type="hidden" name="stage" value="{{ $userStage }}">
                    <h4 style="margin:0 0 10px 0; font-size:0.95rem; font-weight:800; color:#553c9a;">
                        Berikan Hasil Review ({{ strtoupper(str_replace('_', ' ', $userStage)) }})
                    </h4>
                    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:12px; margin-bottom:12px;">
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:700; color:#4a4a5a; margin-bottom:5px;">Keputusan Review</label>
                            <select name="status" required style="width:100%; padding:9px 12px; border:1px solid #d8c8f0; border-radius:8px; font-size:0.85rem;">
                                <option value="approved">Disetujui</option>
                                <option value="revision_requested">Perlu Revisi</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:700; color:#4a4a5a; margin-bottom:5px;">Catatan / Feedback Review</label>
                            <input type="text" name="feedback" placeholder="Tuliskan catatan hasil review bab ini..." style="width:100%; padding:9px 12px; border:1px solid #d8c8f0; border-radius:8px; font-size:0.85rem;">
                        </div>
                    </div>
                    <button type="submit" style="background:#7b5ea7; color:#fff; border:none; padding:9px 18px; border-radius:8px; font-size:0.82rem; font-weight:700; cursor:pointer;">
                        Simpan Hasil Review
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Modal Unggah Draft Baru / Revisi -->
    @if($isStudent)
        <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="showUploadModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-800">Unggah Draf BAB {{ $chapter->order }} Versi Baru</h3>
                        <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                    </div>

                    <form action="{{ route('chapters.upload', $chapter) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Berkas Draf</label>
                            <input type="file" name="draft_file" required accept=".pdf,.doc,.docx,.odt,.txt"
                                class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 border border-slate-300 rounded-full p-1">
                            <p class="text-[11px] text-slate-400 mt-1">Format didukung: PDF, DOCX, DOC, ODT, TXT (Maks. 20MB)</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Perubahan / Rangkuman Versi</label>
                            <textarea name="notes" rows="3" placeholder="Tuliskan ringkasan pembaruan atau perbaikan revisi yang Anda buat pada versi draf ini..."
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showUploadModal = false" class="px-4 py-2 bg-rose-50 text-slate-600 rounded-xl text-xs font-bold hover:bg-rose-100">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">
                                Unggah &amp; Simpan Versi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
