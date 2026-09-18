@extends('layouts.app', [
    'title' => 'Dashboard Mahasiswa',
    'headerTitle' => 'Portal Tugas Akhir',
    'headerSubtitle' => 'Progres & Bimbingan Skripsi'
])

@section('content')
<div class="space-y-6">

    @if(!$thesis)
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum Ada Skripsi Terdaftar</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Akun Anda belum memiliki data penugasan tugas akhir atau skripsi aktif. Silakan hubungi Koordinator TA program studi Anda.</p>
        </div>
    @else
        <!-- Thesis Overview Card -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <div class="flex items-center space-x-2.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $thesis->status->badgeColor() }}">
                            {{ $thesis->status->label() }}
                        </span>
                        @if($thesis->latestStagnation)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $thesis->latestStagnation->risk_level->badgeColor() }}">
                                {{ $thesis->latestStagnation->risk_level->label() }}
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 leading-snug">{{ $thesis->title }}</h2>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $thesis->abstract ?? 'Belum ada abstrak ringkas.' }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs text-slate-600">
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-400 font-medium">Pembimbing 1:</span>
                            <span class="font-semibold text-slate-800">{{ $thesis->supervisor1?->name ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-400 font-medium">Pembimbing 2:</span>
                            <span class="font-semibold text-slate-800">{{ $thesis->supervisor2?->name ?? 'Belum ditentukan' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Meter -->
                <div class="w-full lg:w-72 bg-slate-50 border border-slate-200/80 rounded-2xl p-5 flex flex-col justify-center">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-semibold text-slate-600">Total Progres Bab</span>
                        <span class="text-lg font-black text-emerald-600">{{ $thesis->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-500" style="width: {{ $thesis->progress_percentage }}%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 text-center">
                        {{ $thesis->chapters->where('status', \App\Enums\ChapterStatus::APPROVED)->count() }} dari {{ $thesis->chapters->count() }} Bab telah disetujui
                    </p>
                </div>
            </div>
        </div>

        <!-- Open Revisions Alert Banner -->
        @if(count($openRevisions) > 0)
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-rose-900">Perhatian: Anda memiliki {{ count($openRevisions) }} butir revisi yang perlu diperbaiki</h4>
                        <p class="text-xs text-rose-700 mt-0.5">Periksa catatan dari dosen pembimbing dan berikan respons atau unggah draf bab versi terbaru.</p>
                        <div class="mt-3 space-y-2">
                            @foreach($openRevisions->take(3) as $rev)
                                <div class="bg-white/80 border border-rose-200 rounded-xl p-3 flex items-center justify-between">
                                    <div class="min-w-0 flex-1 pr-3">
                                        <span class="inline-block text-[10px] font-bold uppercase text-rose-600 tracking-wider">{{ $rev->chapter->title }}</span>
                                        <p class="text-xs text-slate-800 truncate font-medium">{{ $rev->note }}</p>
                                    </div>
                                    <a href="{{ route('chapters.show', $rev->chapter) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold transition flex-shrink-0">
                                        Lihat Bab
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Chapters Roadmap -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Struktur & Perkembangan Bab</h3>
                    <p class="text-xs text-slate-500">Unggah draf berkas per bab, pantau riwayat versi, dan bandingkan perubahan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($thesis->chapters as $chapter)
                    <div class="bg-white border border-slate-200 hover:border-emerald-300 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">BAB {{ $chapter->order }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $chapter->status->badgeColor() }}">
                                    {{ $chapter->status->label() }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 leading-snug">{{ $chapter->title }}</h4>
                            
                            <div class="mt-3 text-xs space-y-1 text-slate-500">
                                <div class="flex justify-between">
                                    <span>Versi Terkini:</span>
                                    <span class="font-semibold text-slate-800">
                                        {{ $chapter->currentVersion ? 'v' . $chapter->currentVersion->version_number : 'Belum ada draf' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Butir Revisi:</span>
                                    <span class="font-semibold {{ $chapter->openRevisions()->count() > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                        {{ $chapter->openRevisions()->count() }} Terbuka / {{ $chapter->revisionItems()->count() }} Total
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ route('chapters.show', $chapter) }}" class="flex-1 text-center py-2 px-3 bg-slate-900 hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold transition">
                                Buka Bab & Versi
                            </a>
                            @if($chapter->versions->count() >= 2)
                                <a href="{{ route('chapters.diff', $chapter) }}" title="Bandingkan Versi" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Upcoming Guidance Section -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Agenda Bimbingan Terjadwal</h3>
                    <p class="text-xs text-slate-500">Daftar sesi konsultasi dan bimbingan aktif bersama dosen pembimbing.</p>
                </div>
                <a href="{{ route('supervision.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition">
                    + Ajukan Bimbingan Baru
                </a>
            </div>

            @if(count($upcomingSupervisions) === 0)
                <p class="text-xs text-slate-400 text-center py-6">Tidak ada agenda bimbingan mendatang yang sedang aktif.</p>
            @else
                <div class="space-y-3">
                    @foreach($upcomingSupervisions as $session)
                        <div class="border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                            <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $session->status->badgeColor() }}">
                                        {{ $session->status->label() }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-700">{{ $session->supervisor->name }}</span>
                                </div>
                                <h5 class="text-sm font-bold text-slate-900">{{ $session->topic }}</h5>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Waktu: {{ \Carbon\Carbon::parse($session->scheduled_at)->isoFormat('dddd, D MMMM Y - HH:mm') }} WIB 
                                    @if($session->slot && $session->slot->room_or_link)
                                        | Tempat: <span class="font-medium text-slate-700">{{ $session->slot->room_or_link }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
