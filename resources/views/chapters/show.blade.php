@extends('layouts.app', [
    'title' => 'BAB ' . $chapter->order . ': BAB ' . \App\Helpers\RomanHelper::toRoman($chapter->order) . ': ' . $chapter->title,
    'headerTitle' => 'BAB ' . $chapter->order . ': BAB ' . \App\Helpers\RomanHelper::toRoman($chapter->order) . ': ' . $chapter->title,
    'headerSubtitle' => 'Proses & Bimbingan Skripsi',
    'hasBack' => true,
    'backUrl' => route('theses.index')
])

@section('content')
@php
    $badgeClass = match($chapter->status) {
        \App\Enums\ChapterStatus::APPROVED => 'bg-[#D9FBEA] text-[#009E70]',
        \App\Enums\ChapterStatus::REVISION_REQUIRED => 'bg-[#FCE8E8] text-[#d93838]',
        \App\Enums\ChapterStatus::SUBMITTED, \App\Enums\ChapterStatus::IN_REVIEW => 'bg-[#FDF3D8] text-[#b58e3a]',
        default => 'bg-[#E9E9ED] text-[#7a7a8c]',
    };
@endphp

<div x-data="{ tab: 'revisi', showUploadModal: false }" class="space-y-6">

    <!-- Card 1: Header Bab & Tab Selector -->
    <div class="bg-white border border-[#e1e1e8] rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-semibold text-[#8a8a9d] mb-1.5">
                    Skripsi Mahasiswa &gt; BAB {{ $chapter->order }}
                </p>
                <h2 class="text-xl font-extrabold text-[#4a4a5a] tracking-tight">
                    BAB {{ \App\Helpers\RomanHelper::toRoman($chapter->order) }} : {{ $chapter->title }}
                </h2>
                <div class="flex items-center gap-3 mt-2 flex-wrap text-xs text-[#8a8a9d]">
                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold {{ $badgeClass }}">
                        {{ $chapter->status->label() }}
                    </span>
                    <span>Total {{ $chapter->versions->count() }} Versi Terdaftar &bull; {{ $chapter->openRevisions->count() }} Revisi Terbuka</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($chapter->versions->count() >= 2)
                    <a href="{{ route('chapters.diff', $chapter) }}"
                       class="px-4 py-2 bg-[#EEE8FA] hover:bg-[#E2D9F7] text-[#7B5EA7] rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
                        Bandingkan Versi
                    </a>
                @endif
                @if($isStudent)
                    <button type="button" @click="showUploadModal = true"
                        class="px-5 py-2.5 bg-[#009E70] hover:bg-[#00875e] text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        Unggah Draft Baru
                    </button>
                @endif
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-8 border-b border-slate-200 overflow-x-auto text-xs font-bold">
            <button type="button" @click="tab = 'dokumen'"
                class="pb-3 border-b-2 transition whitespace-nowrap"
                :class="tab === 'dokumen' ? 'border-[#9D62CC] text-[#7B5EA7]' : 'border-transparent text-[#8a8a9d] hover:text-[#4a4a5a]'">
                Dokumen dan Riwayat Versi
            </button>
            <button type="button" @click="tab = 'revisi'"
                class="pb-3 border-b-2 transition whitespace-nowrap"
                :class="tab === 'revisi' ? 'border-[#9D62CC] text-[#7B5EA7]' : 'border-transparent text-[#8a8a9d] hover:text-[#4a4a5a]'">
                Daftar Butir Revisi
            </button>
            <button type="button" @click="tab = 'approval'"
                class="pb-3 border-b-2 transition whitespace-nowrap"
                :class="tab === 'approval' ? 'border-[#9D62CC] text-[#7B5EA7]' : 'border-transparent text-[#8a8a9d] hover:text-[#4a4a5a]'">
                Persetujuan Bertingkat Approval
            </button>
        </div>
    </div>

    <!-- Card 2: Tab Contents -->
    <div class="bg-white border border-[#e1e1e8] rounded-2xl p-6 shadow-sm min-h-[300px]">

        <!-- TAB 1: Dokumen dan Riwayat Versi -->
        <div x-show="tab === 'dokumen'" x-transition class="space-y-4">
            @forelse($chapter->versions as $version)
                <div class="border border-[#e1e1e8] rounded-2xl p-5 hover:border-[#9D62CC] transition space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-[#009E70] text-white rounded-md text-[11px] font-bold">
                                Versi {{ $version->version_number }}
                            </span>
                            @if($chapter->current_version_id === $version->id)
                                <span class="text-[#009E70] text-xs font-bold">&bull; Versi Aktif</span>
                            @endif
                            <span class="text-xs text-[#8a8a9d]">
                                &bull; Diunggah oleh {{ $version->uploader?->name ?? 'Mahasiswa' }} ({{ $version->created_at->isoFormat('D MMM Y, HH:mm') }})
                            </span>
                        </div>
                        <a href="{{ route('versions.download', $version) }}"
                           class="px-4 py-1.5 bg-[#f0eff4] hover:bg-[#e4e2ec] text-[#4a4a5a] rounded-lg text-xs font-bold transition flex items-center gap-2">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Unduh File
                        </a>
                    </div>

                    <div class="text-xs font-semibold text-[#4a4a5a]">
                        <span>{{ $version->original_filename }}</span>
                        <span class="text-[#8a8a9d] font-normal ml-2">({{ $version->formattedSize() }})</span>
                    </div>

                    @if($version->notes)
                        <div class="bg-[#faf5ff] border border-[#e9d8fd] rounded-xl p-3.5 text-xs text-[#553c9a] leading-relaxed">
                            <strong class="font-bold">Catatan Mahasiswa:</strong> {{ $version->notes }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 text-[#8a8a9d] text-xs">
                    Belum ada dokumen yang diunggah untuk bab ini.
                </div>
            @endforelse
        </div>

        <!-- TAB 2: Daftar Butir Revisi -->
        <div x-show="tab === 'revisi'" x-transition class="space-y-4">
            <h3 class="text-base font-extrabold text-[#4a4a5a]">Daftar Butir Revisi &amp; Checklist Perbaikan</h3>

            @if($isSupervisor)
                <form action="{{ route('revisions.store', $chapter) }}" method="POST" class="bg-[#faf5ff] border border-[#e9d8fd] rounded-2xl p-4 space-y-3">
                    @csrf
                    <label class="block text-xs font-bold text-[#553c9a]">Tambah Catatan Revisi Baru</label>
                    <textarea name="note" rows="2" required placeholder="Tuliskan butir catatan revisi untuk mahasiswa..."
                              class="w-full px-3.5 py-2.5 border border-[#d8c8f0] rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white"></textarea>
                    <button type="submit" class="px-4 py-2 bg-[#7b5ea7] hover:bg-[#6b4f96] text-white rounded-xl text-xs font-bold transition">
                        Simpan Catatan Revisi
                    </button>
                </form>
            @endif

            @forelse($chapter->revisionItems as $item)
                <div class="border rounded-2xl p-4.5 space-y-3 {{ $item->is_resolved ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }}">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#4a4a5a]">{{ $item->supervisor?->name ?? 'Dosen Pembimbing' }}</span>
                            <span class="text-xs text-[#8a8a9d]">&bull; {{ $item->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold {{ $item->is_resolved ? 'bg-[#D9FBEA] text-[#009E70]' : 'bg-[#FCE8E8] text-[#d93838]' }}">
                            {{ $item->is_resolved ? 'Selesai / Terverifikasi' : 'Perlu Perbaikan' }}
                        </span>
                    </div>

                    <p class="text-xs text-[#4a4a5a] leading-relaxed">{{ $item->note }}</p>

                    @if($item->student_response)
                        <div class="bg-white border border-[#e1e1e8] rounded-xl p-3 text-xs text-[#553c9a]">
                            <strong class="font-bold">Tanggapan Mahasiswa:</strong> {{ $item->student_response }}
                        </div>
                    @endif

                    @if($isStudent && !$item->is_resolved)
                        <form action="{{ route('revisions.respond', $item) }}" method="POST" class="flex gap-2 pt-1 flex-wrap sm:flex-nowrap">
                            @csrf
                            <input type="text" name="student_response" required value="{{ $item->student_response }}"
                                   placeholder="Tulis tanggapan atau penjelasan perbaikan Anda..."
                                   class="flex-1 px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none">
                            <button type="submit" class="px-4 py-2 bg-[#7b5ea7] hover:bg-[#6b4f96] text-white rounded-xl text-xs font-bold transition whitespace-nowrap">
                                Kirim Tanggapan
                            </button>
                        </form>
                    @endif

                    @if($isSupervisor)
                        <form action="{{ route('revisions.toggle', $item) }}" method="POST" class="pt-1">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-[#eee8fa] hover:bg-[#e2d9f7] text-[#7b5ea7] rounded-lg text-xs font-bold transition">
                                {{ $item->is_resolved ? 'Buka Kembali Revisi' : 'Tandai Selesai' }}
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 text-[#8a8a9d] text-xs">
                    Belum ada butir catatan revisi untuk bab ini
                </div>
            @endforelse
        </div>

        <!-- TAB 3: Persetujuan Bertingkat Approval -->
        <div x-show="tab === 'approval'" x-transition class="space-y-6">
            <div>
                <h3 class="text-base font-extrabold text-[#4a4a5a]">Alur Persetujuan Bertingkat (Hierarchical Approval)</h3>
                <p class="text-xs text-[#8a8a9d] mt-1">Setiap bab membutuhkan verifikasi bertahap sebelum dinyatakan selesai dan sah untuk diajukan seminar/sidang.</p>
            </div>

            @php
                $p1Approval = $chapter->approvalForStage('pembimbing_1');
                $p2Approval = $chapter->approvalForStage('pembimbing_2');
                $koorApproval = $chapter->approvalForStage('koordinator');

                $badgeHelper = function ($app) {
                    if (!$app) return ['bg-[#FEE2E2]/60 text-[#991B1B]', 'Menunggu Validasi'];
                    return match($app->status) {
                        \App\Enums\ApprovalStatus::APPROVED => ['bg-[#D9FBEA] text-[#009E70]', 'Disetujui'],
                        \App\Enums\ApprovalStatus::REVISION_REQUESTED => ['bg-[#FCE8E8] text-[#d93838]', 'Perlu Revisi'],
                        \App\Enums\ApprovalStatus::REJECTED => ['bg-[#FCE8E8] text-[#d93838]', 'Ditolak'],
                        default => ['bg-[#FEE2E2]/60 text-[#991B1B]', 'Menunggu Validasi'],
                    };
                };
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Tahap 1: Pembimbing 1 -->
                @php [$p1BadgeBg, $p1BadgeText] = $badgeHelper($p1Approval); @endphp
                <div class="border rounded-2xl p-5 flex flex-col justify-between space-y-3 {{ $p1Approval?->status === \App\Enums\ApprovalStatus::APPROVED ? 'border-[#9D62CC] bg-[#faf5ff]/40' : 'border-slate-200 bg-white' }}">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-[#b83280]">Tahap 1: PEMBIMBING 1</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p1BadgeBg }}">
                                {{ $p1BadgeText }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-[#4a4a5a]">{{ $thesis->supervisor1?->name ?? 'Belum Ditugaskan' }}</h4>
                    </div>
                    <div class="border border-[#e1e1e8] bg-white rounded-xl p-3 text-xs text-[#6b6b7b] italic min-h-[60px]">
                        {{ $p1Approval?->feedback ? '"' . $p1Approval->feedback . '"' : ($p1Approval?->status === \App\Enums\ApprovalStatus::APPROVED ? '"Latar belakang dan rumusan masalah sudah kuat dan disetujui."' : 'Menunggu review dari pembimbing 1.') }}
                    </div>
                </div>

                <!-- Tahap 2: Pembimbing 2 -->
                @php [$p2BadgeBg, $p2BadgeText] = $badgeHelper($p2Approval); @endphp
                <div class="border rounded-2xl p-5 flex flex-col justify-between space-y-3 {{ $p2Approval?->status === \App\Enums\ApprovalStatus::APPROVED ? 'border-[#9D62CC] bg-[#faf5ff]/40' : 'border-slate-200 bg-white' }}">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-[#2563EB]">Tahap 2: PEMBIMBING 2</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p2BadgeBg }}">
                                {{ $p2BadgeText }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-[#4a4a5a]">{{ $thesis->supervisor2?->name ?? 'Tidak Ada Pembimbing 2' }}</h4>
                    </div>
                    <div class="border border-[#e1e1e8] bg-white rounded-xl p-3 text-xs text-[#6b6b7b] italic min-h-[60px]">
                        {{ $p2Approval?->feedback ? '"' . $p2Approval->feedback . '"' : ($p2Approval?->status === \App\Enums\ApprovalStatus::APPROVED ? '"Disetujui untuk melanjutkan pada bab selanjutnya."' : 'Menunggu review dari pembimbing 2.') }}
                    </div>
                </div>

                <!-- Tahap 3: Koordinator TA -->
                @php [$koorBadgeBg, $koorBadgeText] = $badgeHelper($koorApproval); @endphp
                <div class="border rounded-2xl p-5 flex flex-col justify-between space-y-3 {{ $koorApproval?->status === \App\Enums\ApprovalStatus::APPROVED ? 'border-[#9D62CC] bg-[#faf5ff]/40' : 'border-slate-200 bg-white' }}">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-[#D97706]">Tahap 3: KOORDINATOR TA</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $koorBadgeBg }}">
                                {{ $koorBadgeText }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-[#4a4a5a]">Validasi &amp; Verifikasi Akhir</h4>
                    </div>
                    <div class="border border-[#e1e1e8] bg-white rounded-xl p-3 text-xs text-[#6b6b7b] italic min-h-[60px]">
                        {{ $koorApproval?->feedback ? '"' . $koorApproval->feedback . '"' : 'Menunggu validasi akhir koordinator program studi.' }}
                    </div>
                </div>
            </div>

            <!-- Form Persetujuan jika pengguna adalah Dosen / Koordinator yang berwenang -->
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
                <form action="{{ route('approvals.stage', $chapter) }}" method="POST" class="bg-[#faf5ff] border border-[#e9d8fd] rounded-2xl p-5 space-y-3 mt-4">
                    @csrf
                    <input type="hidden" name="stage" value="{{ $userStage }}">
                    <h4 class="text-xs font-bold text-[#553c9a] uppercase tracking-wider">
                        Form Keputusan Review ({{ strtoupper(str_replace('_', ' ', $userStage)) }})
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#4a4a5a] mb-1">Keputusan</label>
                            <select name="status" required class="w-full px-3 py-2 border border-[#d8c8f0] rounded-xl text-xs bg-white focus:outline-none">
                                <option value="approved">Disetujui</option>
                                <option value="revision_requested">Perlu Revisi</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-[#4a4a5a] mb-1">Catatan / Feedback</label>
                            <input type="text" name="feedback" placeholder="Tuliskan catatan persetujuan atau poin revisi..."
                                   class="w-full px-3 py-2 border border-[#d8c8f0] rounded-xl text-xs bg-white focus:outline-none">
                        </div>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#7b5ea7] hover:bg-[#6b4f96] text-white rounded-xl text-xs font-bold transition">
                        Simpan Keputusan Review
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Modal Unggah Draft BAB Baru (Sesuai Screenshot 2) -->
    @if($isStudent)
        <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div @click="showUploadModal = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

                <div class="relative bg-white rounded-[24px] max-w-xl w-full p-8 shadow-2xl border border-slate-200 z-10 space-y-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-[#4a4a5a] tracking-tight">
                            Unggah Draf BAB {{ $chapter->order }} Versi Baru
                        </h3>
                        <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-2xl leading-none">
                            &times;
                        </button>
                    </div>

                    <!-- Modal Form -->
                    <form action="{{ route('chapters.upload', $chapter) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-[#6b6b7b] mb-2">Pilih Berkas Draf</label>
                            <div class="border border-[#a3a3b5] rounded-full p-1.5 flex items-center pr-4">
                                <input type="file" name="draft_file" required accept=".pdf,.doc,.docx,.odt,.txt"
                                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#E8F8D8] file:text-[#2d6a2e] hover:file:bg-[#dcfce7] cursor-pointer">
                            </div>
                            <p class="text-xs text-[#8a8a9d] mt-2">Format didukung PDF,DOCX, DOC, ODT, TXT (Maks.20MB)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-[#6b6b7b] mb-2">Catatan Perubahan/Rangkuman Versi</label>
                            <textarea name="notes" rows="4"
                                placeholder="Tuliskan ringkasan pembaruan yang anda buat pada versi Draf ini..."
                                class="w-full px-4 py-3 border border-[#a3a3b5] rounded-[18px] text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none placeholder-[#8a8a9d] leading-relaxed"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end items-center gap-3 pt-2">
                            <button type="button" @click="showUploadModal = false"
                                class="px-6 py-2.5 bg-[#FDE8E8] hover:bg-[#FCDADA] text-[#4a4a5a] rounded-xl text-xs font-bold transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-[#009E70] hover:bg-[#00875e] text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
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
