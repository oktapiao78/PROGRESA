@extends('layouts.app', [
    'title' => 'Chat' . ($selectedContact ? ' - ' . $selectedContact->name : ''),
    'headerTitle' => 'Chat',
    'headerSubtitle' => $selectedContact ? $selectedContact->name : 'Bimbingan Skripsi',
    'hasBack' => true,
    'backUrl' => route('dashboard')
])

@section('content')
<div class="space-y-4">

    <!-- Contact Selector Pill Bar (If multiple supervisors / students) -->
    @if($contacts->count() > 1)
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            <span class="text-xs font-bold text-[#8a8a9d] uppercase mr-1">Kontak:</span>
            @foreach($contacts as $c)
                <a href="{{ route('chat.index', ['user_id' => $c->id]) }}"
                   class="px-4 py-2 rounded-full text-xs font-bold transition flex items-center gap-2 {{ $selectedContact && $selectedContact->id === $c->id ? 'bg-[#7B5EA7] text-white shadow-sm' : 'bg-white border border-slate-200 text-[#4a4a5a] hover:bg-slate-50' }}">
                    <span>{{ $c->name }}</span>
                    @if(($c->unread_count ?? 0) > 0)
                        <span class="px-1.5 py-0.5 bg-rose-500 text-white text-[10px] rounded-full">
                            {{ $c->unread_count }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    <!-- Chat Message Container -->
    <div class="min-h-[440px] flex flex-col justify-between">
        @if($selectedContact)
            <div class="space-y-6 py-4 px-2">
                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp

                    @if(!$isMe)
                        <!-- Message from Dosen / Other Person (Left aligned, Avatar on left) -->
                        <div class="flex items-start gap-3.5 max-w-2xl">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-200 border-2 border-white shadow-sm shrink-0">
                                <svg viewBox="0 0 24 24" class="w-full h-full text-slate-500 p-1" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div class="relative bg-white border border-[#e1e1e8] rounded-[22px] rounded-tl-sm px-5 py-3.5 shadow-sm text-xs text-[#4a4a5a] leading-relaxed">
                                <p class="whitespace-pre-line">{{ $msg->message }}</p>
                                <span class="block text-[10px] text-[#8a8a9d] mt-1 text-right">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Message from Me (Right aligned, Avatar on right) -->
                        <div class="flex items-start justify-end gap-3.5 max-w-2xl ml-auto">
                            <div class="relative bg-white border border-[#e1e1e8] rounded-[22px] rounded-tr-sm px-5 py-3.5 shadow-sm text-xs text-[#4a4a5a] leading-relaxed">
                                <p class="whitespace-pre-line">{{ $msg->message }}</p>
                                <span class="block text-[10px] text-[#8a8a9d] mt-1 text-right">
                                    {{ $msg->created_at->format('H:i') }}
                                    &bull; {{ $msg->is_read ? '✓✓ Dibaca' : '✓ Terkirim' }}
                                </span>
                            </div>
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-[#e2d9f7] border-2 border-[#9D62CC] shadow-sm shrink-0">
                                <svg viewBox="0 0 24 24" class="w-full h-full text-[#7B5EA7] p-1" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-20 text-xs text-[#8a8a9d]">
                        Belum ada pesan percakapan. Tuliskan pesan pertama Anda kepada pembimbing.
                    </div>
                @endforelse
            </div>

            <!-- Sticky Bottom Input Pill Bar (Sesuai Screenshot 7) -->
            <form action="{{ route('chat.store') }}" method="POST"
                  class="bg-white border border-slate-300 rounded-full px-4 py-2 flex items-center gap-3 shadow-md">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $selectedContact->id }}">

                <!-- Attachment / Add Icon -->
                <button type="button" class="text-slate-400 hover:text-slate-600 transition" title="Lampiran / Emoji">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </button>

                <!-- Input Text -->
                <input type="text" name="message" required placeholder="Ketik Pesan" autocomplete="off"
                       class="flex-1 bg-transparent border-0 text-xs text-slate-800 placeholder-[#8a8a9d] focus:outline-none focus:ring-0">

                <!-- Camera Icon -->
                <button type="button" class="text-slate-400 hover:text-slate-600 transition" title="Kamera">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </button>

                <!-- Mic Icon -->
                <button type="button" class="text-slate-400 hover:text-slate-600 transition" title="Audio">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                        <line x1="12" y1="19" x2="12" y2="23"></line>
                        <line x1="8" y1="23" x2="16" y2="23"></line>
                    </svg>
                </button>

                <!-- Send Button -->
                <button type="submit" class="p-1.5 bg-[#7B5EA7] hover:bg-[#6b4f96] text-white rounded-full transition shadow-sm" title="Kirim">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-xs text-[#8a8a9d] shadow-sm">
                Pilih kontak bimbingan untuk memulai chat.
            </div>
        @endif
    </div>

</div>
@endsection
