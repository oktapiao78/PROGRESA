@extends('layouts.app', [
    'title' => 'Monitoring Respon & Beban Dosen',
    'headerTitle' => 'Kinerja & Beban Dosen Pembimbing',
    'headerSubtitle' => 'Distribusi Bimbingan & Rata-rata Kecepatan Respon Draf'
])

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Statistik Beban dan Kecepatan Respon Pembimbing</h3>
                <p class="text-xs text-slate-500">Membantu pemerataan kuota mahasiswa bimbingan dan mendeteksi potensi kelambatan feedback.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Nama Dosen</th>
                        <th class="py-2.5 px-3">NIP</th>
                        <th class="py-2.5 px-3 text-center">Bimbingan Utama (P1)</th>
                        <th class="py-2.5 px-3 text-center">Pendamping (P2)</th>
                        <th class="py-2.5 px-3 text-center">Total Beban</th>
                        <th class="py-2.5 px-3 text-right">Rata-rata Respon</th>
                        <th class="py-2.5 px-3 text-center">Status Beban</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($supervisors as $sp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $sp['name'] }}</td>
                            <td class="py-3 px-3 font-mono text-[11px] text-slate-400">{{ $sp['nim_nip'] }}</td>
                            <td class="py-3 px-3 text-center font-medium">{{ $sp['p1_count'] }} mhs</td>
                            <td class="py-3 px-3 text-center font-medium">{{ $sp['p2_count'] }} mhs</td>
                            <td class="py-3 px-3 text-center font-black text-indigo-600 text-sm">{{ $sp['total'] }} mhs</td>
                            <td class="py-3 px-3 text-right font-semibold text-emerald-600">{{ $sp['avg_response_days'] }} hari</td>
                            <td class="py-3 px-3 text-center">
                                @if($sp['total'] >= 8)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Kelebihan Beban</span>
                                @elseif($sp['total'] >= 4)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">Ideal</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Tersedia Kuota</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
