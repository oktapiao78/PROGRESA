@extends('layouts.app', [
    'title' => 'Monitoring Mahasiswa Stagnan',
    'headerTitle' => 'Monitoring Stagnansi & Eskalasi',
    'headerSubtitle' => 'Deteksi Mahasiswa Tanpa Kemajuan & Dosen Terlambat Merespon'
])

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Mahasiswa Berisiko Stagnan (&gt; 14 Hari Tanpa Aktivitas)</h3>
                <p class="text-xs text-slate-500">Sistem otomatis menghitung hari inaktif sejak draf atau respons terakhir.</p>
            </div>
        </div>

        @if($allTheses->isEmpty())
            <div class="text-center py-12 text-slate-400 text-xs">
                🎉 Luar biasa! Tidak ada mahasiswa yang terdeteksi stagnan saat ini. Semua proses bimbingan berjalan aktif.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Mahasiswa</th>
                            <th class="py-2.5 px-3">Judul Skripsi</th>
                            <th class="py-2.5 px-3">Pembimbing</th>
                            <th class="py-2.5 px-3">Hari Inaktif</th>
                            <th class="py-2.5 px-3">Tingkat Risiko</th>
                            <th class="py-2.5 px-3">Catatan Deteksi</th>
                            <th class="py-2.5 px-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($allTheses as $thesis)
                            @php $log = $thesis->latestStagnation; @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3">
                                    <span class="font-bold text-slate-900 block">{{ $thesis->student->name }}</span>
                                    <span class="text-slate-400 text-[11px] block">{{ $thesis->student->nim_nip }}</span>
                                </td>
                                <td class="py-3 px-3 max-w-xs">
                                    <span class="font-medium text-slate-800 line-clamp-2">{{ $thesis->title }}</span>
                                </td>
                                <td class="py-3 px-3 text-[11px]">
                                    <span class="block text-slate-700">P1: {{ $thesis->supervisor1?->name ?? '-' }}</span>
                                    <span class="block text-slate-500">P2: {{ $thesis->supervisor2?->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3 font-bold text-rose-600">
                                    {{ $log ? $log->days_inactive . ' hari' : '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    @if($log)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $log->risk_level->badgeColor() }}">
                                            {{ $log->risk_level->label() }}
                                        </span>
                                    @endif
                                    @if($log?->is_escalated)
                                        <span class="block text-[10px] text-purple-700 font-bold mt-0.5">● Eskalasi Aktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 max-w-xs text-slate-500">
                                    {{ $log?->notes ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('theses.show', $thesis) }}" class="px-2.5 py-1.5 bg-slate-900 hover:bg-emerald-600 text-white rounded-lg font-semibold transition">
                                        Periksa
                                    </a>
                                    @if(!$log?->is_escalated)
                                        <form action="{{ route('approvals.escalate', $thesis) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-800 rounded-lg font-semibold transition">
                                                Eskalasi
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('approvals.resolve_escalation', $thesis) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-semibold transition">
                                                Selesai
                                            </button>
                                        </form>
                                    @endif
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
