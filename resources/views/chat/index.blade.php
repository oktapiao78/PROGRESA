@extends('layouts.app', [
    'title' => 'Chat Bimbingan',
    'headerTitle' => 'Chat',
    'headerSubtitle' => $selectedContact ? $selectedContact->name : 'Pilih Kontak Bimbingan'
])

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-3 min-h-[520px]">
    <!-- Daftar Kontak -->
    <div class="border-b md:border-b-0 md:border-r border-slate-200 bg-purple-50/30 p-4 space-y-2">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-purple-800 mb-3">
            {{ auth()->user()->isMahasiswa() ? 'Dosen Pembimbing' : 'Mahasiswa Bimbingan' }}
        </h3>

        @forelse($contacts as $contact)
            <a href="{{ route('chat.index', ['user_id' => $contact->id]) }}"
               class="flex items-center justify-between gap-3 p-3 rounded-xl transition {{ $selectedContact && $selectedContact->id === $contact->id ? 'bg-purple-200/70 text-purple-900 font-bold' : 'hover:bg-purple-100/60 text-slate-700' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold truncate">{{ $contact->name }}</p>
                        @if($contact->last_message)
                            <p class="text-[11px] text-slate-500 truncate font-normal">
                                {{ $contact->last_message->sender_id === auth()->id() ? 'Anda: ' : '' }}{{ $contact->last_message->message }}
                            </p>
                        @else
                            <p class="text-[11px] text-slate-400 truncate font-normal">{{ $contact->nim_nip }}</p>
                        @endif
                    </div>
                </div>
                @if(($contact->unread_count ?? 0) > 0)
                    <span class="px-2 py-0.5 bg-purple-600 text-white text-[10px] font-bold rounded-full shrink-0">
                        {{ $contact->unread_count }}
                    </span>
                @endif
            </a>
        @empty
            <p class="text-xs text-slate-400 py-4">Belum ada kontak pembimbing/mahasiswa yang terhubung.</p>
        @endforelse
    </div>

    <!-- Ruang Percakapan -->
    <div class="md:col-span-2 flex flex-col justify-between bg-slate-50/50">
        @if($selectedContact)
            <div class="p-4 border-b border-slate-200 bg-white flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr($selectedContact->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">{{ $selectedContact->name }}</h4>
                    <p class="text-[11px] text-slate-500">{{ $selectedContact->nim_nip }}</p>
                </div>
            </div>

            <div class="flex-1 p-5 space-y-3 overflow-y-auto max-h-[380px]">
                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-2.5 shadow-sm text-xs leading-relaxed {{ $isMe ? 'bg-purple-600 text-white rounded-br-none' : 'bg-white border border-slate-200 text-slate-700 rounded-bl-none' }}">
                            <p class="whitespace-pre-line">{{ $msg->message }}</p>
                            <span class="block text-[10px] mt-1 {{ $isMe ? 'text-purple-200 text-right' : 'text-slate-400' }}">
                                {{ $msg->created_at->format('d M H:i') }}
                                @if($isMe)
                                    &bull; {{ $msg->is_read ? '✓✓ Dibaca' : '✓ Terkirim' }}
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-xs text-slate-400">
                        Belum ada pesan. Mulai percakapan bimbingan Anda di bawah ini.
                    </div>
                @endforelse
            </div>

            <form action="{{ route('chat.store') }}" method="POST" class="p-4 bg-white border-t border-slate-200 flex items-center gap-2">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $selectedContact->id }}">
                <input type="text" name="message" required placeholder="Ketik pesan..."
                    class="flex-1 px-4 py-2.5 border border-slate-300 rounded-full text-xs focus:outline-none focus:ring-2 focus:ring-purple-500">
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-full text-xs font-bold transition">
                    Kirim
                </button>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center p-8 text-xs text-slate-400">
                Pilih kontak untuk memulai percakapan.
            </div>
        @endif
    </div>
</div>
@endsection

