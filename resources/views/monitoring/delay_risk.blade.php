@extends('layouts.app', [
    'title' => 'Proyeksi Risiko Keterlambatan Kelulusan',
    'headerTitle' => 'Proyeksi Risiko Kelulusan',
    'headerSubtitle' => 'Estimasi Kecepatan Pengerjaan Bab & Deteksi Risiko Keterlambatan'
])

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Matriks Estimasi Waktu Selesai Skripsi</h3>
                <p class="text-xs text-slate-500">Kalkulasi prediktif berdasarkan rasio persetujuan bab terhadap bulan yang telah berjalan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Mahasiswa</th>
                        <th class="py-2.5 px-3">Judul Skripsi</th>
                        <th class="py-2.5 px-3 text-center">Bulan Berjalan</th>
                        <th class="py-2.5 px-3 text-center">Bab Disetujui</th>
                        <th class="py-2.5 px-3 text-center">Estimasi Total Bulan</th>
                        <th class="py-2.5 px-3">Status Risiko Keterlambatan</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($theses as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900 block">{{ $item['thesis']->student->name }}</span>
                                <span class="text-slate-400 text-[11px] block">{{ $item['thesis']->student->nim_nip }}</span>
                            </td>
                            <td class="py-3 px-3 max-w-xs">
                                <span class="font-medium text-slate-800 line-clamp-2">{{ $item['thesis']->title }}</span>
                            </td>
                            <td class="py-3 px-3 text-center font-medium">{{ $item['months_elapsed'] }} bln</td>
                            <td class="py-3 px-3 text-center font-semibold text-emerald-700">
                                {{ $item['approved_chapters'] }}/{{ $item['total_chapters'] }}
                            </td>
                            <td class="py-3 px-3 text-center font-black text-slate-900">
                                ~ {{ $item['predicted_total_months'] }} bulan
                            </td>
                            <td class="py-3 px-3">
                                @if($item['predicted_total_months'] > 8)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">
                                        {{ $item['delay_status'] }}
                                    </span>
                                @elseif($item['predicted_total_months'] > 6)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        {{ $item['delay_status'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $item['delay_status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('theses.show', $item['thesis']) }}" class="px-2.5 py-1.5 bg-slate-900 hover:bg-emerald-600 text-white rounded-lg font-semibold transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
