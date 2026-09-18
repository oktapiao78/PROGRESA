@extends('layouts.app', [
    'title' => 'Audit Log Sistem',
    'headerTitle' => 'Audit Log & Rekam Jejak Sistem',
    'headerSubtitle' => 'Aktivitas Persetujuan, Pengunggahan, dan Perubahan Status'
])

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
    <div>
        <h3 class="text-base font-bold text-slate-900">Catatan Aktivitas Sistem Progresa</h3>
        <p class="text-xs text-slate-500">Seluruh kejadian penting yang tercatat secara kronologis untuk kebutuhan audit akademik.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                <tr>
                    <th class="py-2.5 px-3">Waktu Kejadian</th>
                    <th class="py-2.5 px-3">Pengguna</th>
                    <th class="py-2.5 px-3">Kode Aksi</th>
                    <th class="py-2.5 px-3">Rincian Informasi</th>
                    <th class="py-2.5 px-3">Alamat IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $log->created_at->isoFormat('D MMM Y, HH:mm:ss') }}</td>
                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $log->user?->name ?? 'Sistem / Anonim' }}</td>
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-700 font-bold">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-slate-700 max-w-md">{{ $log->details ?? '-' }}</td>
                        <td class="py-3 px-3 font-mono text-[11px] text-slate-400">{{ $log->ip_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $logs->links() }}
    </div>
</div>
@endsection
