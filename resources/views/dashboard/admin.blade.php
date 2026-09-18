@extends('layouts.app', [
    'title' => 'Dashboard Administrator',
    'headerTitle' => 'Panel Administrator',
    'headerSubtitle' => 'Master Data & Pengaturan Sistem Progresa'
])

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pengguna</span>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['users_count'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold text-sky-500 uppercase tracking-wider block">Mahasiswa</span>
            <p class="text-2xl font-black text-sky-600 mt-1">{{ $stats['students_count'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Dosen</span>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['lecturers_count'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider block">Skripsi</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['theses_count'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold text-purple-500 uppercase tracking-wider block">Prodi</span>
            <p class="text-2xl font-black text-purple-600 mt-1">{{ $stats['study_programs_count'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider block">Template Bab</span>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['chapter_templates_count'] }}</p>
        </div>
    </div>

    <!-- Quick Shortcuts -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" class="bg-white border border-slate-200 hover:border-emerald-500 p-5 rounded-2xl shadow-sm hover:shadow-md transition group">
            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Manajemen Pengguna</h4>
            <p class="text-xs text-slate-500 mt-1">Tambah akun mahasiswa, dosen, koordinator, atau kaprodi serta atur hak akses.</p>
        </a>

        <a href="{{ route('admin.chapters.index') }}" class="bg-white border border-slate-200 hover:border-emerald-500 p-5 rounded-2xl shadow-sm hover:shadow-md transition group">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Konfigurasi Struktur Bab</h4>
            <p class="text-xs text-slate-500 mt-1">Atur format bab wajib/opsional dan urutan bab skripsi untuk setiap program studi.</p>
        </a>

        <a href="{{ route('admin.audit_logs') }}" class="bg-white border border-slate-200 hover:border-emerald-500 p-5 rounded-2xl shadow-sm hover:shadow-md transition group">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Rekam Jejak Audit</h4>
            <p class="text-xs text-slate-500 mt-1">Pantau seluruh aktivitas persetujuan, pengunggahan dokumen, dan tindakan eskalasi.</p>
        </a>
    </div>

    <!-- Recent Audit Logs -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Aktivitas Sistem Terbaru</h3>
            <a href="{{ route('admin.audit_logs') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Semua Log &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Pengguna</th>
                        <th class="py-2.5 px-3">Aksi</th>
                        <th class="py-2.5 px-3">Rincian</th>
                        <th class="py-2.5 px-3">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 font-semibold">{{ $log->action }}</span></td>
                            <td class="py-2.5 px-3 text-slate-600 max-w-sm truncate">{{ $log->details }}</td>
                            <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">{{ $log->ip_address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
