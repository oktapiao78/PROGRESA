@extends('layouts.app', [
    'title' => 'Dashboard Koordinator Tugas Akhir',
    'headerTitle' => 'Monitoring Koordinator TA',
    'headerSubtitle' => 'Pemantauan Tahapan Bimbingan & Penanganan Eskalasi'
])

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Skripsi</span>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $metrics['total'] }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Mahasiswa terdaftar</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[11px] font-bold text-blue-500 uppercase tracking-wider block">Sedang Berjalan</span>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ $metrics['in_progress'] }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Tahap draf & revisi</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Siap Sidang/Semhas</span>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ $metrics['ready'] }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Bab tuntas disetujui</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Potensi Stagnan</span>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $metrics['stagnant_theses']->count() }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">&gt; 14 hari inaktif</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Perlu Eskalasi</span>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ $metrics['escalated_theses']->count() }}</p>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Dosen &gt; 7 hr blm respon</span>
        </div>
    </div>

    <!-- Escalated Alert Section -->
    @if($metrics['escalated_theses']->isNotEmpty())
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-start space-x-3">
                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                    !
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-purple-950">Peringatan Eskalasi Koordinator: {{ $metrics['escalated_theses']->count() }} Mahasiswa Menunggu Tindak Lanjut</h4>
                    <p class="text-xs text-purple-800 mt-0.5">Dosen pembimbing belum memberikan respons terhadap unggahan draf mahasiswa selama lebih dari 7 hari.</p>
                    
                    <div class="mt-3 space-y-2">
                        @foreach($metrics['escalated_theses'] as $esc)
                            <div class="bg-white border border-purple-200 rounded-xl p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="font-bold text-slate-900 text-xs">{{ $esc->student->name }} ({{ $esc->student->nim_nip }})</span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $esc->title }}</p>
                                    <span class="text-[10px] text-purple-700 font-semibold mt-1 block">
                                        {{ $esc->latestStagnation->notes }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('theses.show', $esc) }}" class="px-3 py-1.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-xs font-semibold transition">
                                        Periksa Skripsi
                                    </a>
                                    <form action="{{ route('approvals.resolve_escalation', $esc) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                            Selesaikan Eskalasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- All Theses Progress Pipeline -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pipeline Progres Tugas Akhir Mahasiswa</h3>
                <p class="text-xs text-slate-500">Status kelulusan tahapan approval bertingkat (Pembimbing 1 & 2 serta Koordinator).</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('monitoring.stagnant') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Lihat Mahasiswa Stagnan &rarr;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Mahasiswa</th>
                        <th class="py-3 px-4">Judul Skripsi</th>
                        <th class="py-3 px-4">Pembimbing</th>
                        <th class="py-3 px-4">Progres</th>
                        <th class="py-3 px-4">Risiko</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($theses as $thesis)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $thesis->student->name }}</span>
                                <span class="text-slate-400 text-[11px] block">{{ $thesis->student->nim_nip }}</span>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <span class="font-medium text-slate-800 line-clamp-2">{{ $thesis->title }}</span>
                            </td>
                            <td class="py-3 px-4 text-[11px]">
                                <span class="block text-slate-700">P1: {{ $thesis->supervisor1?->name ?? '-' }}</span>
                                <span class="block text-slate-500">P2: {{ $thesis->supervisor2?->name ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="w-24">
                                    <div class="flex justify-between text-[10px] font-bold text-slate-600 mb-0.5">
                                        <span>{{ $thesis->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $thesis->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($thesis->latestStagnation)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $thesis->latestStagnation->risk_level->badgeColor() }}">
                                        {{ $thesis->latestStagnation->risk_level->label() }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">{{ $thesis->latestStagnation->days_inactive }} hr inaktif</span>
                                @else
                                    <span class="text-slate-400 text-[10px]">On Track</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <a href="{{ route('theses.show', $thesis) }}" class="px-2.5 py-1.5 bg-slate-900 hover:bg-emerald-600 text-white rounded-lg font-semibold transition">
                                    Detail
                                </a>
                                @if(!$thesis->latestStagnation?->is_escalated)
                                    <form action="{{ route('approvals.escalate', $thesis) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Tandai Eskalasi" class="px-2.5 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-800 rounded-lg font-semibold transition">
                                            Eskalasi
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $theses->links() }}
        </div>
    </div>

</div>
@endsection
