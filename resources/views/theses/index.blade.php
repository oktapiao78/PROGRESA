@extends('layouts.app', [
    'title' => auth()->user()->isMahasiswa() ? 'Portal Tugas Akhir' : 'Daftar Skripsi & Tugas Akhir',
    'headerTitle' => auth()->user()->isMahasiswa() ? 'Portal Tugas Akhir' : 'Daftar Skripsi Mahasiswa',
    'headerSubtitle' => auth()->user()->isMahasiswa() ? 'Proses & Bimbingan Skripsi' : 'Kelola Data & Pantau Progres Bimbingan Mahasiswa'
])

@section('content')
@if(auth()->user()->isMahasiswa())
    @php
        $myThesis = $theses->first() ?? auth()->user()->studentThesis;
    @endphp

    @if(!$myThesis)
        <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center shadow-sm">
            <h3 class="text-base font-bold text-slate-800 mb-2">Belum Ada Data Skripsi Terdaftar</h3>
            <p class="text-xs text-slate-500 mb-6">Data tugas akhir / skripsi Anda belum didaftarkan. Silakan hubungi dosen pembimbing Anda.</p>
            <a href="{{ route('profile.mahasiswa') }}" class="inline-block px-5 py-2.5 bg-[#DDD7FA] text-[#7b5ea7] font-bold rounded-xl text-xs hover:bg-[#DCD7FE] transition">
                Lihat Profil Mahasiswa
            </a>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            <div>
                <h2 class="text-xl font-extrabold text-[#4a4a5a] tracking-tight">Struktur & Perkembangan Bab</h2>
                <p class="text-xs text-[#8a8a9d] mt-1"><span class="font-bold text-[#4a4a5a]">{{ $myThesis->title }}</span> &bull; Unggah draft berkas per bab, pantau riwayat versi, dan bandingkan perubahan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($myThesis->chapters->sortBy('order') as $ch)
                    @php
                        $badgeClass = match($ch->status) {
                            \App\Enums\ChapterStatus::APPROVED => 'bg-[#D9FBEA] text-[#009E70]',
                            \App\Enums\ChapterStatus::REVISION_REQUIRED => 'bg-[#FCE8E8] text-[#d93838]',
                            \App\Enums\ChapterStatus::SUBMITTED, \App\Enums\ChapterStatus::IN_REVIEW => 'bg-[#FDF3D8] text-[#b58e3a]',
                            default => 'bg-[#E9E9ED] text-[#7a7a8c]'
                        };
                    @endphp
                    <div class="bg-white border border-[#e5e5ea] rounded-2xl p-5 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[11px] font-bold text-[#8a8a9d] uppercase tracking-wider">BAB {{ $ch->order }}</span>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold {{ $badgeClass }}">
                                    {{ $ch->status->label() }}
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-[#4a4a5a] mb-4 leading-snug">
                                BAB {{ \App\Helpers\RomanHelper::toRoman($ch->order) ?? $ch->order }} : {{ $ch->title }}
                            </h3>

                            <div class="space-y-1.5 text-xs text-[#8a8a9d] mb-6">
                                <div class="flex justify-between">
                                    <span>Versi Terkini:</span>
                                    <span class="font-bold text-[#4a4a5a]">v{{ $ch->latestVersion?->version_number ?? 1 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Butir Revisi:</span>
                                    <span class="font-bold text-[#4a4a5a]">{{ $ch->openRevisions->count() }} Terbuka. {{ $ch->revisionItems->count() }} Total</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('chapters.show', $ch) }}"
                           class="w-full py-2.5 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7b5ea7] text-center font-bold rounded-xl text-xs transition block shadow-sm">
                            Buka Bab & Versi
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-xs text-slate-400">
                        Belum ada struktur bab yang dibuat untuk skripsi Anda.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

@else
    <!-- Tampilan Dosen, Koordinator, Kaprodi, Admin -->
    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <form method="GET" action="{{ route('theses.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari judul atau nama mahasiswa..."
                        class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9d62cc] focus:outline-none">
                </div>

                <div>
                    <select name="status" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9d62cc] focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach(\App\Enums\ThesisStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-[#7b5ea7] hover:bg-[#6b4f96] text-white font-semibold rounded-xl text-xs transition">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('theses.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Theses Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($theses as $thesis)
                <div class="bg-white border border-slate-200 hover:border-[#9d62cc] rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $thesis->status->badgeColor() }}">
                                {{ $thesis->status->label() }}
                            </span>
                            @if($thesis->latestStagnation)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $thesis->latestStagnation->risk_level->badgeColor() }}">
                                    {{ $thesis->latestStagnation->risk_level->label() }}
                                </span>
                            @endif
                        </div>

                        <h4 class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">
                            {{ $thesis->title }}
                        </h4>

                        <div class="text-xs text-slate-500 pt-1">
                            <p class="font-semibold text-slate-800">{{ $thesis->student->name }}</p>
                            <p class="text-[11px] text-slate-400">NIM: {{ $thesis->student->nim_nip }} • {{ $thesis->studyProgram->code }}</p>
                        </div>

                        <div class="text-[11px] text-slate-600 pt-1 space-y-0.5 border-t border-slate-100">
                            <p><span class="text-slate-400">P1:</span> {{ $thesis->supervisor1?->name ?? 'Belum ditugaskan' }}</p>
                            <p><span class="text-slate-400">P2:</span> {{ $thesis->supervisor2?->name ?? 'Belum ditugaskan' }}</p>
                        </div>

                        <!-- Progress -->
                        <div class="pt-2">
                            <div class="flex justify-between text-[11px] mb-1 font-semibold text-slate-600">
                                <span>Progres Bab:</span>
                                <span class="text-[#7b5ea7] font-bold">{{ $thesis->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#9d62cc] h-2 rounded-full" style="width: {{ $thesis->progress_percentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">
                            {{ $thesis->chapters->count() }} Bab Terdaftar
                        </span>
                        <a href="{{ route('theses.show', $thesis) }}" class="px-3 py-1.5 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7b5ea7] rounded-lg text-xs font-bold transition">
                            Lihat Skripsi &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400 text-xs">
                    Tidak ada data skripsi yang sesuai dengan kriteria pencarian Anda.
                </div>
            @endforelse
        </div>

        <div>
            {{ $theses->links() }}
        </div>
    </div>
@endif
@endsection
