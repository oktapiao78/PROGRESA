@extends('layouts.app', [
    'title' => 'Pemberitahuan',
    'headerTitle' => 'Pemberitahuan',
    'headerSubtitle' => 'Proses & Bimbingan Skripsi'
])

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap border-b border-slate-100 pb-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-800">Daftar Pemberitahuan</h2>
            <p class="text-xs text-slate-500">Pembaruan status bab, catatan revisi, pengajuan bimbingan, dan pesan terbaru.</p>
        </div>

        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.read_all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-800 rounded-xl text-xs font-bold transition">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notif)
        <div class="border rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $notif->is_read ? 'border-slate-200 bg-white' : 'border-purple-300 bg-purple-50/50' }}">
            <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $notif->is_read ? 'bg-slate-100 text-slate-500' : 'bg-purple-600 text-white' }}">
                        {{ $notif->is_read ? 'Sudah Dibaca' : 'Belum Dibaca' }}
                    </span>
                    <h3 class="text-sm font-bold text-slate-800">{{ $notif->title }}</h3>
                    <span class="text-[11px] text-slate-400">&bull; {{ $notif->created_at->format('d-m-Y H:i') }} ({{ $notif->created_at->diffForHumans() }})</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <form action="{{ route('notifications.read', $notif) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $notif->link ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        {{ $notif->link ? 'Buka' : 'Tandai Dibaca' }}
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center py-12 text-slate-400 text-xs">
            Belum ada pemberitahuan untuk Anda saat ini.
        </div>
    @endforelse

    <div class="pt-2">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

