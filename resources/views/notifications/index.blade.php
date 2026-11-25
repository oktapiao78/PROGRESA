@extends('layouts.app', [
    'title' => 'Pemberitahuan',
    'headerTitle' => 'Pemberitahuan',
    'headerSubtitle' => 'Proses & Bimbingan Skripsi',
    'hasBack' => true,
    'backUrl' => route('dashboard')
])

@section('content')
<div class="space-y-4">
    @if($notifications->where('is_read', false)->count() > 0)
        <div class="flex justify-end">
            <form action="{{ route('notifications.read_all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7B5EA7] rounded-xl text-xs font-bold transition shadow-sm">
                    Tandai Semua Dibaca
                </button>
            </form>
        </div>
    @endif

    @forelse($notifications as $notif)
        <div class="bg-white border border-[#e1e1e8] rounded-[24px] p-6 shadow-sm space-y-2 hover:border-[#9D62CC] transition">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-base font-extrabold text-[#4a4a5a]">{{ $notif->title }}</h3>
                <span class="text-xs text-[#8a8a9d] font-medium whitespace-nowrap">
                    {{ $notif->created_at->format('d-m-Y') }}
                </span>
            </div>

            @php
                $senderName = null;
                // If notification message contains actor or relation
                if ($notif->actor) {
                    $senderName = $notif->actor->name;
                }
            @endphp

            @if($senderName)
                <p class="text-xs text-[#4a4a5a]">
                    <span class="font-medium text-[#6b6b7b]">Nama Dosen / Pengirim :</span> <strong>{{ $senderName }}</strong>
                </p>
            @endif

            <p class="text-xs text-[#4a4a5a] leading-relaxed pt-1">
                <span class="font-medium text-[#6b6b7b]">Catatan :</span> {{ $notif->message }}
            </p>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $notif->is_read ? 'bg-[#f0eff4] text-[#8a8a9d]' : 'bg-[#DDD7FA] text-[#7B5EA7]' }}">
                    {{ $notif->is_read ? 'Sudah Dibaca' : 'Belum Dibaca' }}
                </span>

                <div class="flex items-center gap-2">
                    @if($notif->link)
                        <a href="{{ $notif->link }}" class="px-3.5 py-1.5 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7B5EA7] font-bold text-xs rounded-xl transition">
                            Lihat Rincian
                        </a>
                    @endif
                    @if(!$notif->is_read)
                        <form action="{{ route('notifications.read', $notif) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                Tandai Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-[#e1e1e8] rounded-[24px] p-12 text-center text-xs text-[#8a8a9d] shadow-sm">
            Belum ada pemberitahuan untuk Anda saat ini.
        </div>
    @endforelse

    <div class="pt-2">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
