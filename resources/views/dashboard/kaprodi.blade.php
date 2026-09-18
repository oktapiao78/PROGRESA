@extends('layouts.app', [
    'title' => 'Dashboard Ketua Program Studi',
    'headerTitle' => 'Portal Eksekutif Kaprodi',
    'headerSubtitle' => 'Pemantauan Kinerja Bimbingan & Proyeksi Kelulusan Tepat Waktu'
])

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Skripsi Aktif</span>
            <p class="text-3xl font-black text-slate-900 mt-2">{{ $metrics['total'] }}</p>
            <span class="text-xs text-slate-400 mt-1 block">Tahun Akademik Berjalan</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Lulus / Selesai</span>
            <p class="text-3xl font-black text-emerald-600 mt-2">{{ $metrics['completed'] }}</p>
            <span class="text-xs text-slate-400 mt-1 block">Tingkat kelulusan</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider block">Beresiko Stagnan</span>
            <p class="text-3xl font-black text-amber-600 mt-2">{{ $metrics['stagnant_theses']->count() }}</p>
            <span class="text-xs text-slate-400 mt-1 block">Inaktif &gt; 14 hari</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider block">Siap Ujian Akhir</span>
            <p class="text-3xl font-black text-indigo-600 mt-2">{{ $metrics['ready'] }}</p>
            <span class="text-xs text-slate-400 mt-1 block">Memenuhi syarat sidang</span>
        </div>
    </div>

    <!-- Risk Distribution & Delay Projections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Risk Breakdown -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Distribusi Tingkat Risiko Mahasiswa</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between font-semibold text-slate-700 mb-1">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span>Lancar (On Track)</span>
                        <span>{{ $metrics['risk_counts']['on_track'] }} mahasiswa</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $metrics['total'] > 0 ? ($metrics['risk_counts']['on_track'] / $metrics['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-semibold text-slate-700 mb-1">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span>Risiko Rendah (7-14 hr)</span>
                        <span>{{ $metrics['risk_counts']['low'] }} mahasiswa</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $metrics['total'] > 0 ? ($metrics['risk_counts']['low'] / $metrics['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-semibold text-slate-700 mb-1">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>Risiko Sedang (14-21 hr)</span>
                        <span>{{ $metrics['risk_counts']['medium'] }} mahasiswa</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $metrics['total'] > 0 ? ($metrics['risk_counts']['medium'] / $metrics['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-semibold text-slate-700 mb-1">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span>Stagnan Tinggi (&gt; 21 hr)</span>
                        <span>{{ $metrics['risk_counts']['high'] }} mahasiswa</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $metrics['total'] > 0 ? ($metrics['risk_counts']['high'] / $metrics['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-semibold text-slate-700 mb-1">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-red-700 mr-2"></span>Kritis (&gt; 30 hr / Lewat Target)</span>
                        <span>{{ $metrics['risk_counts']['critical'] }} mahasiswa</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-red-700 h-2 rounded-full" style="width: {{ $metrics['total'] > 0 ? ($metrics['risk_counts']['critical'] / $metrics['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('monitoring.delay_risk') }}" class="block text-center py-2 px-3 bg-slate-900 hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold transition">
                    Lihat Proyeksi Keterlambatan Lengkap &rarr;
                </a>
            </div>
        </div>

        <!-- Supervisor Workload Table -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Beban Bimbingan & Respon Dosen</h3>
                    <p class="text-xs text-slate-500">Jumlah kuota mahasiswa per dosen dan estimasi kecepatan respons draf.</p>
                </div>
                <a href="{{ route('monitoring.supervisors') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Detail &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Nama Dosen</th>
                            <th class="py-2.5 px-3 text-center">Sebagai P1</th>
                            <th class="py-2.5 px-3 text-center">Sebagai P2</th>
                            <th class="py-2.5 px-3 text-center">Total Beban</th>
                            <th class="py-2.5 px-3 text-right">Rata-rata Respon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($metrics['supervisors']->take(6) as $sp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $sp['name'] }}</td>
                                <td class="py-2.5 px-3 text-center font-medium">{{ $sp['p1_count'] }}</td>
                                <td class="py-2.5 px-3 text-center font-medium">{{ $sp['p2_count'] }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-indigo-600">{{ $sp['total'] }} mhs</td>
                                <td class="py-2.5 px-3 text-right text-emerald-600 font-semibold">{{ $sp['avg_response_days'] }} hari</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
