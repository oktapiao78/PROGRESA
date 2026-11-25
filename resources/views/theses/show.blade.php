@extends('layouts.app', [
    'title' => 'Detail Skripsi — ' . $thesis->student->name,
    'headerTitle' => 'Detail Tugas Akhir',
    'headerSubtitle' => $thesis->student->name . ' (' . $thesis->student->nim_nip . ')'
])

@section('content')
<div class="space-y-6">

    <!-- Thesis Main Header Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 relative">
        <div class="flex flex-col lg:flex-row justify-between gap-6">
            <div class="space-y-3 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $thesis->status->badgeColor() }}">
                        {{ $thesis->status->label() }}
                    </span>
                    @if($thesis->latestStagnation)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $thesis->latestStagnation->risk_level->badgeColor() }}">
                            {{ $thesis->latestStagnation->risk_level->label() }}
                        </span>
                    @endif
                    @if($thesis->latestStagnation?->is_escalated)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300 animate-pulse">
                            TERESKALASI
                        </span>
                    @endif
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">{{ $thesis->title }}</h2>

                @if($thesis->abstract)
                    <div class="bg-slate-50 border border-slate-200/70 rounded-xl p-4 text-xs text-slate-600 leading-relaxed">
                        <span class="font-bold text-slate-800 block mb-1">Abstrak / Rencana Penelitian:</span>
                        {{ $thesis->abstract }}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Mahasiswa:</span>
                        <span class="font-semibold text-slate-800">{{ $thesis->student->name }}</span>
                        <span class="text-[11px] text-slate-500 block">{{ $thesis->student->nim_nip }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Program Studi:</span>
                        <span class="font-semibold text-slate-800">{{ $thesis->studyProgram->name }}</span>
                        <span class="text-[11px] text-slate-500 block">T.A. {{ $thesis->academic_year }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Pembimbing 1:</span>
                        <span class="font-semibold text-slate-800">{{ $thesis->supervisor1?->name ?? 'Belum ada' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Pembimbing 2:</span>
                        <span class="font-semibold text-slate-800">{{ $thesis->supervisor2?->name ?? 'Belum ada' }}</span>
                    </div>
                </div>
            </div>

            <!-- Progress & Actions -->
            <div class="w-full lg:w-72 flex flex-col justify-between space-y-4">
                <div class="bg-[#f8f7fc] border border-slate-200/80 rounded-2xl p-4 text-center">
                    <span class="text-xs font-semibold text-slate-500 block">Kemajuan Kumulatif</span>
                    <span class="text-3xl font-black text-[#7B5EA7] my-1 block">{{ $thesis->progress_percentage }}%</span>
                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden my-2">
                        <div class="bg-[#9D62CC] h-2.5 rounded-full" style="width: {{ $thesis->progress_percentage }}%"></div>
                    </div>
                    <span class="text-[11px] text-slate-400">
                        {{ $thesis->chapters->where('status', \App\Enums\ChapterStatus::APPROVED)->count() }} dari {{ $thesis->chapters->count() }} Bab Disetujui
                    </span>
                </div>

                <div class="space-y-2">
                    @if(auth()->user()->isDosen())
                        <a href="{{ route('chat.index', ['user_id' => $thesis->student_id]) }}" class="block text-center py-2 px-3 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7B5EA7] rounded-xl text-xs font-bold transition">
                            💬 Chat Mahasiswa Ini
                        </a>
                    @endif
                    @if(auth()->user()->isKoordinator() || auth()->user()->isAdmin())
                        @if(!$thesis->latestStagnation?->is_escalated)
                            <form action="{{ route('approvals.escalate', $thesis) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 bg-purple-100 hover:bg-purple-200 text-purple-800 rounded-xl text-xs font-bold transition">
                                    Tandai Eskalasi Bimbingan
                                </button>
                            </form>
                        @else
                            <form action="{{ route('approvals.resolve_escalation', $thesis) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-semibold transition">
                                    Selesaikan Status Eskalasi
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('theses.edit', $thesis) }}" class="block text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Edit Pembimbing & Data Skripsi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chapter Breakdown Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perkembangan Dokumen Per Bab</h3>
                <p class="text-xs text-slate-500">Klik bab untuk melihat draf berkas, riwayat versi dokumen, dan persetujuan bertingkat.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($thesis->chapters as $ch)
                <div class="bg-white border border-slate-200 hover:border-emerald-400 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">BAB {{ $ch->order }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $ch->status->badgeColor() }}">
                                {{ $ch->status->label() }}
                            </span>
                        </div>
                        <h4 class="text-base font-bold text-slate-900 leading-snug">{{ $ch->title }}</h4>

                        <div class="mt-3 text-xs space-y-1.5 text-slate-500">
                            <div class="flex justify-between">
                                <span>Versi Aktif:</span>
                                <span class="font-bold text-slate-800">
                                    {{ $ch->currentVersion ? 'v' . $ch->currentVersion->version_number : 'Belum diunggah' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Approval P1:</span>
                                <span class="font-semibold {{ $ch->isStageApproved('pembimbing_1') ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $ch->isStageApproved('pembimbing_1') ? 'Disetujui' : 'Menunggu' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Approval P2:</span>
                                <span class="font-semibold {{ $ch->isStageApproved('pembimbing_2') ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $ch->isStageApproved('pembimbing_2') ? 'Disetujui' : 'Menunggu' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Revisi Terbuka:</span>
                                <span class="font-semibold {{ $ch->openRevisions()->count() > 0 ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                    {{ $ch->openRevisions()->count() }} butir
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('chapters.show', $ch) }}" class="flex-1 text-center py-2 px-3 bg-slate-900 hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold transition">
                            Buka Detail Bab
                        </a>
                        @if($ch->versions->count() >= 2)
                            <a href="{{ route('chapters.diff', $ch) }}" title="Bandingkan Versi" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Guidance Session History / Logbook -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Buku Log Bimbingan & Konsultasi</h3>
                <p class="text-xs text-slate-500">Rekam berita acara tatap muka / daring bersama dosen pembimbing.</p>
            </div>
        </div>

        @php
            $completedLogs = $thesis->supervisionRequests->where('status', \App\Enums\SupervisionStatus::COMPLETED);
        @endphp

        @if($completedLogs->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Belum ada sesi bimbingan yang telah diselesaikan dan diverifikasi.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Tanggal Sesi</th>
                            <th class="py-2.5 px-3">Dosen Pembimbing</th>
                            <th class="py-2.5 px-3">Topik / Bahasan</th>
                            <th class="py-2.5 px-3">Catatan / Berita Acara</th>
                            <th class="py-2.5 px-3">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($completedLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-3 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $log->completed_at ? $log->completed_at->isoFormat('D MMM Y') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $log->supervisor->name }}
                                </td>
                                <td class="py-2.5 px-3 font-medium text-slate-800">
                                    {{ $log->topic }}
                                </td>
                                <td class="py-2.5 px-3 max-w-xs text-slate-600">
                                    {{ $log->meeting_summary ?? '-' }}
                                </td>
                                <td class="py-2.5 px-3 max-w-xs text-emerald-700 font-medium">
                                    {{ $log->action_items ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
