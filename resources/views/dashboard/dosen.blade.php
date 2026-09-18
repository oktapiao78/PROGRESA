@extends('layouts.app', [
    'title' => 'Dashboard Dosen Pembimbing',
    'headerTitle' => 'Portal Dosen Pembimbing',
    'headerSubtitle' => 'Review Draf & Bimbingan Mahasiswa'
])

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bimbingan Utama (P1)</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">P1</span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $thesesAsP1->count() }} <span class="text-xs font-normal text-slate-400">Mahasiswa</span></p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pendamping (P2)</span>
                <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center font-bold text-xs">P2</span>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ $thesesAsP2->count() }} <span class="text-xs font-normal text-slate-400">Mahasiswa</span></p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Review</span>
                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xs">Draf</span>
            </div>
            <p class="text-2xl font-black text-amber-600 mt-2">{{ $pendingChapters->count() }} <span class="text-xs font-normal text-slate-400">Bab</span></p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Permintaan Bimbingan</span>
                <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">Slot</span>
            </div>
            <p class="text-2xl font-black text-blue-600 mt-2">{{ $pendingRequests->count() }} <span class="text-xs font-normal text-slate-400">Permintaan</span></p>
        </div>
    </div>

    <!-- Urgent Section: Pending Drafts to Review -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Draf Bab Perlu Ditinjau</h3>
                <p class="text-xs text-slate-500">Daftar unggahan draf bab terbaru dari mahasiswa bimbingan yang menunggu feedback atau approval Anda.</p>
            </div>
        </div>

        @if($pendingChapters->isEmpty())
            <div class="text-center py-8 text-slate-400 text-xs">
                Tidak ada draf bab yang sedang menunggu review saat ini. Semua draf telah direspon.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Mahasiswa</th>
                            <th class="py-3 px-4">Bab & Versi</th>
                            <th class="py-3 px-4">Status Bab</th>
                            <th class="py-3 px-4">Tanggal Unggah</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingChapters as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $item->thesis->student->name }}</span>
                                    <span class="text-slate-400 text-[11px] block">{{ $item->thesis->student->nim_nip }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $item->title }}</span>
                                    <span class="text-emerald-600 font-bold block text-[11px]">
                                        {{ $item->currentVersion ? 'Versi ' . $item->currentVersion->version_number : '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $item->status->badgeColor() }}">
                                        {{ $item->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500">
                                    {{ $item->currentVersion ? $item->currentVersion->created_at->diffForHumans() : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="{{ route('chapters.show', $item) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition">
                                        Review & Catatan
                                    </a>
                                    @if($item->versions->count() >= 2)
                                        <a href="{{ route('chapters.diff', $item) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition">
                                            Bandingkan Versi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Active Supervisees List -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Mahasiswa Bimbingan Aktif</h3>
                <p class="text-xs text-slate-500">Kemajuan bimbingan dan indikator risiko stagnansi.</p>
            </div>
        </div>

        @php
            $allSupervised = $thesesAsP1->concat($thesesAsP2);
        @endphp

        @if($allSupervised->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Belum ada mahasiswa yang ditugaskan ke Anda sebagai pembimbing.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($allSupervised as $t)
                    <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50 hover:border-indigo-300 transition flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">
                                    {{ $t->supervisor_1_id === auth()->id() ? 'Pembimbing 1' : 'Pembimbing 2' }}
                                </span>
                                @if($t->latestStagnation)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $t->latestStagnation->risk_level->badgeColor() }}">
                                        {{ $t->latestStagnation->risk_level->label() }}
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">{{ $t->title }}</h4>
                            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $t->student->name }} ({{ $t->student->nim_nip }})</p>

                            <!-- Mini progress -->
                            <div class="mt-3">
                                <div class="flex justify-between text-[11px] mb-1 font-medium text-slate-600">
                                    <span>Progres Skripsi:</span>
                                    <span class="font-bold text-emerald-600">{{ $t->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $t->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">
                                Inaktif: {{ $t->latestStagnation ? $t->latestStagnation->days_inactive . ' hari' : '-' }}
                            </span>
                            <a href="{{ route('theses.show', $t) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                Detail Skripsi &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
