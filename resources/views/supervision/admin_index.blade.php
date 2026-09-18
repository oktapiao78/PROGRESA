@extends('layouts.app', [
    'title' => 'Monitoring Bimbingan Mahasiswa',
    'headerTitle' => 'Monitoring Aktivitas Bimbingan',
    'headerSubtitle' => 'Audit Logbook & Sesi Konsultasi Pembimbing'
])

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
    <div>
        <h3 class="text-base font-bold text-slate-900">Rekap Seluruh Sesi Bimbingan</h3>
        <p class="text-xs text-slate-500">Seluruh permohonan bimbingan dan berita acara tatap muka antara mahasiswa dan dosen.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                <tr>
                    <th class="py-2.5 px-3">Tanggal Sesi</th>
                    <th class="py-2.5 px-3">Mahasiswa</th>
                    <th class="py-2.5 px-3">Dosen Pembimbing</th>
                    <th class="py-2.5 px-3">Topik Bahasan</th>
                    <th class="py-2.5 px-3">Status</th>
                    <th class="py-2.5 px-3">Catatan / Berita Acara</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($allRequests as $req)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-2.5 px-3 whitespace-nowrap text-slate-700 font-medium">
                            {{ $req->scheduled_at ? $req->scheduled_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $req->student->name }}</td>
                        <td class="py-2.5 px-3 font-medium text-slate-800">{{ $req->supervisor->name }}</td>
                        <td class="py-2.5 px-3 text-slate-800">{{ $req->topic }}</td>
                        <td class="py-2.5 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $req->status->badgeColor() }}">
                                {{ $req->status->label() }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 max-w-xs truncate text-slate-500">
                            {{ $req->meeting_summary ?? $req->supervisor_notes ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $allRequests->links() }}
    </div>
</div>
@endsection
