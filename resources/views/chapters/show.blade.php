@extends('layouts.app', [
    'title' => $chapter->title . ' — Versi & Persetujuan',
    'headerTitle' => 'BAB ' . $chapter->order . ': ' . $chapter->title,
    'headerSubtitle' => $thesis->student->name . ' (' . $thesis->student->nim_nip . ')'
])

@section('content')
<div x-data="{ activeTab: 'versions', showUploadModal: false }" class="space-y-6">

    <!-- Top Chapter Breadcrumb & Action Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                    <a href="{{ route('theses.show', $thesis) }}" class="hover:text-slate-700 transition">Skripsi Mahasiswa</a>
                    <span>&gt;</span>
                    <span class="text-slate-600 font-semibold">BAB {{ $chapter->order }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">{{ $chapter->title }}</h2>
                <div class="flex items-center space-x-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $chapter->status->badgeColor() }}">
                        {{ $chapter->status->label() }}
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        Total {{ $chapter->versions->count() }} Versi Terdaftar • {{ $chapter->openRevisions()->count() }} Revisi Terbuka
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($isStudent || auth()->user()->isAdmin())
                    <button type="button" @click="showUploadModal = true"
                        class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Unggah Draf Baru</span>
                    </button>
                @endif

                @if($chapter->versions->count() >= 2)
                    <a href="{{ route('chapters.diff', $chapter) }}"
                        class="py-2.5 px-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Bandingkan Versi (Diff)</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-200 mt-6 -mb-6">
            <button @click="activeTab = 'versions'"
                :class="activeTab === 'versions' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="py-3 px-5 border-b-2 text-xs transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Dokumen & Riwayat Versi ({{ $chapter->versions->count() }})</span>
            </button>

            <button @click="activeTab = 'revisions'"
                :class="activeTab === 'revisions' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="py-3 px-5 border-b-2 text-xs transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Daftar Butir Revisi ({{ $chapter->revisionItems->count() }})</span>
                @if($chapter->openRevisions()->count() > 0)
                    <span class="px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">
                        {{ $chapter->openRevisions()->count() }}
                    </span>
                @endif
            </button>

            <button @click="activeTab = 'approvals'"
                :class="activeTab === 'approvals' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="py-3 px-5 border-b-2 text-xs transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Persetujuan Bertingkat (Approval)</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: Dokumen & Versi -->
    <div x-show="activeTab === 'versions'" class="space-y-4">
        @if($chapter->versions->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm">
                <div class="w-12 h-12 mx-auto bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Belum Ada Draf Dokumen</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Mahasiswa belum mengunggah draf untuk bab ini. Gunakan tombol 'Unggah Draf Baru' untuk memulai.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($chapter->versions as $version)
                    <div class="bg-white border {{ $chapter->current_version_id === $version->id ? 'border-emerald-300 ring-1 ring-emerald-300/50' : 'border-slate-200' }} rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-black {{ $chapter->current_version_id === $version->id ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                                        Versi {{ $version->version_number }}
                                    </span>
                                    @if($chapter->current_version_id === $version->id)
                                        <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">● Versi Aktif</span>
                                    @endif
                                    <span class="text-xs text-slate-400">• Diunggah oleh {{ $version->uploader->name }} ({{ $version->created_at->diffForHumans() }})</span>
                                </div>

                                <div class="text-xs text-slate-800 font-semibold flex items-center space-x-2">
                                    <span>{{ $version->original_filename }}</span>
                                    <span class="text-slate-400 font-normal">({{ $version->formattedSize() }})</span>
                                </div>

                                @if($version->notes)
                                    <p class="text-xs text-slate-600 bg-slate-50 border border-slate-200/60 rounded-xl p-2.5 mt-2">
                                        <span class="font-bold text-slate-700">Catatan Mahasiswa:</span> {{ $version->notes }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <a href="{{ route('versions.download', $version) }}"
                                    class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold transition flex items-center space-x-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh File</span>
                                </a>

                                @if($chapter->versions->count() >= 2)
                                    @php
                                        $prevVersion = $chapter->versions->where('version_number', '<', $version->version_number)->first();
                                    @endphp
                                    @if($prevVersion)
                                        <a href="{{ route('chapters.diff', ['chapter' => $chapter, 'v1' => $prevVersion->id, 'v2' => $version->id]) }}"
                                            class="py-2 px-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-semibold transition flex items-center space-x-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            <span>Diff vs v{{ $prevVersion->version_number }}</span>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 2: Butir Revisi (Checklist) -->
    <div x-show="activeTab === 'revisions'" class="space-y-6">
        @if($isSupervisor || auth()->user()->isAdmin())
            <!-- Add Revision Form for Supervisor -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h4 class="text-sm font-bold text-slate-900 mb-2">Tambah Butir Catatan / Revisi Dosen</h4>
                <form action="{{ route('revisions.store', $chapter) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <textarea name="note" rows="3" required
                            placeholder="Tuliskan catatan perbaikan spesifik pada bab ini (misal: 'Perbaiki format sitasi APA pada sub-bab 1.3 dan lengkapi rumusan masalah ketiga')..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                            + Tambah Butir Revisi
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
            <h4 class="text-sm font-bold text-slate-900">Daftar Butir Revisi & Checklist Perbaikan</h4>

            @if($chapter->revisionItems->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">Belum ada butir catatan revisi untuk bab ini.</p>
            @else
                <div class="space-y-4">
                    @foreach($chapter->revisionItems as $item)
                        <div class="border {{ $item->is_resolved ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }} rounded-2xl p-4 transition space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $item->is_resolved ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $item->is_resolved ? 'TERATASI / SELESAI' : 'PERLU PERBAIKAN' }}
                                        </span>
                                        <span class="text-xs text-slate-500">Oleh: {{ $item->supervisor->name }}</span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-900 mt-1 leading-relaxed">{{ $item->note }}</p>
                                </div>

                                @if($isSupervisor || auth()->user()->isAdmin())
                                    <form action="{{ route('revisions.toggle', $item) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="py-1.5 px-3 rounded-lg text-xs font-semibold {{ $item->is_resolved ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }} transition">
                                            {{ $item->is_resolved ? 'Buka Kembali' : 'Tandai Selesai' }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Student Response Section -->
                            @if($item->student_response)
                                <div class="bg-white border border-slate-200 rounded-xl p-3 text-xs">
                                    <span class="font-bold text-slate-700 block text-[11px] mb-0.5">Tanggapan Mahasiswa:</span>
                                    <p class="text-slate-800">{{ $item->student_response }}</p>
                                </div>
                            @elseif($isStudent)
                                <form action="{{ route('revisions.respond', $item) }}" method="POST" class="pt-2 border-t border-slate-200/60 flex items-center space-x-2">
                                    @csrf
                                    <input type="text" name="student_response" required
                                        placeholder="Jelaskan perbaikan yang telah Anda lakukan pada versi terbaru..."
                                        class="flex-1 px-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    <button type="submit" class="py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition">
                                        Kirim Respon
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: Persetujuan Bertingkat (Approval Stages) -->
    <div x-show="activeTab === 'approvals'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
            <div>
                <h4 class="text-base font-bold text-slate-900">Alur Persetujuan Bertingkat (Hierarchical Approval)</h4>
                <p class="text-xs text-slate-500">Setiap bab membutuhkan verifikasi bertahap sebelum dinyatakan selesai dan sah untuk diajukan ke seminar/sidang.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tahap 1: Pembimbing 1 -->
                @php $appP1 = $chapter->approvalForStage('pembimbing_1'); @endphp
                <div class="border {{ $appP1 && $appP1->status->value === 'approved' ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200' }} rounded-2xl p-5 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Tahap 1: Pembimbing 1</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $appP1 ? $appP1->status->badgeColor() : 'bg-slate-100 text-slate-600' }}">
                                {{ $appP1 ? $appP1->status->label() : 'Belum Ditinjau' }}
                            </span>
                        </div>
                        <h5 class="text-sm font-bold text-slate-900">{{ $thesis->supervisor1?->name ?? 'Dosen Pembimbing 1' }}</h5>
                        
                        @if($appP1 && $appP1->feedback)
                            <p class="text-xs text-slate-600 mt-2 bg-white border border-slate-200 rounded-xl p-3">
                                "{{ $appP1->feedback }}"
                            </p>
                        @endif
                    </div>

                    @if((auth()->user()->id === $thesis->supervisor_1_id || auth()->user()->isAdmin()) && (!$appP1 || $appP1->status->value !== 'approved'))
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <form action="{{ route('approvals.stage', $chapter) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="stage" value="pembimbing_1">
                                <input type="text" name="feedback" placeholder="Catatan persetujuan / revisi..."
                                    class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs">
                                
                                <div class="flex space-x-2">
                                    <button type="submit" name="status" value="approved"
                                        class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                        Setujui Bab (P1)
                                    </button>
                                    <button type="submit" name="status" value="revision_requested"
                                        class="py-1.5 px-3 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-xs font-semibold transition">
                                        Revisi
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Tahap 2: Pembimbing 2 -->
                @php $appP2 = $chapter->approvalForStage('pembimbing_2'); @endphp
                <div class="border {{ $appP2 && $appP2->status->value === 'approved' ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200' }} rounded-2xl p-5 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-600">Tahap 2: Pembimbing 2</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $appP2 ? $appP2->status->badgeColor() : 'bg-slate-100 text-slate-600' }}">
                                {{ $appP2 ? $appP2->status->label() : ($thesis->supervisor_2_id ? 'Belum Ditinjau' : 'Tidak Ditetapkan') }}
                            </span>
                        </div>
                        <h5 class="text-sm font-bold text-slate-900">{{ $thesis->supervisor2?->name ?? 'Dosen Pembimbing 2' }}</h5>

                        @if($appP2 && $appP2->feedback)
                            <p class="text-xs text-slate-600 mt-2 bg-white border border-slate-200 rounded-xl p-3">
                                "{{ $appP2->feedback }}"
                            </p>
                        @endif
                    </div>

                    @if($thesis->supervisor_2_id && (auth()->user()->id === $thesis->supervisor_2_id || auth()->user()->isAdmin()) && (!$appP2 || $appP2->status->value !== 'approved'))
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <form action="{{ route('approvals.stage', $chapter) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="stage" value="pembimbing_2">
                                <input type="text" name="feedback" placeholder="Catatan persetujuan / revisi..."
                                    class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs">
                                
                                <div class="flex space-x-2">
                                    <button type="submit" name="status" value="approved"
                                        class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                        Setujui Bab (P2)
                                    </button>
                                    <button type="submit" name="status" value="revision_requested"
                                        class="py-1.5 px-3 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-xs font-semibold transition">
                                        Revisi
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Tahap 3: Koordinator TA -->
                @php $appCoord = $chapter->approvalForStage('koordinator'); @endphp
                <div class="border {{ $appCoord && $appCoord->status->value === 'approved' ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200' }} rounded-2xl p-5 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Tahap 3: Koordinator TA</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $appCoord ? $appCoord->status->badgeColor() : 'bg-slate-100 text-slate-600' }}">
                                {{ $appCoord ? $appCoord->status->label() : 'Menunggu Validasi' }}
                            </span>
                        </div>
                        <h5 class="text-sm font-bold text-slate-900">Validasi & Verifikasi Akhir</h5>

                        @if($appCoord && $appCoord->feedback)
                            <p class="text-xs text-slate-600 mt-2 bg-white border border-slate-200 rounded-xl p-3">
                                "{{ $appCoord->feedback }}"
                            </p>
                        @endif
                    </div>

                    @if((auth()->user()->isKoordinator() || auth()->user()->isAdmin()) && (!$appCoord || $appCoord->status->value !== 'approved'))
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <form action="{{ route('approvals.stage', $chapter) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="stage" value="koordinator">
                                <input type="text" name="feedback" placeholder="Catatan koordinator..."
                                    class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs">
                                
                                <button type="submit" name="status" value="approved"
                                    class="w-full py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition">
                                    Sahkan Bab (Koordinator)
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Draft Modal -->
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showUploadModal" @click="showUploadModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showUploadModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900" id="modal-title">Unggah Draf BAB {{ $chapter->order }} Versi Baru</h3>
                    <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('chapters.upload', $chapter) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Berkas Draf</label>
                        <input type="file" name="draft_file" required
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl cursor-pointer">
                        <span class="text-[11px] text-slate-400 mt-1 block">Format didukung: PDF, DOCX, DOC, ODT, TXT (Maks. 20 MB).</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Perubahan / Rangkuman Versi</label>
                        <textarea name="notes" rows="3"
                            placeholder="Tuliskan ringkasan pembaruan yang Anda buat pada versi draf ini..."
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="showUploadModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                            Unggah & Simpan Versi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
