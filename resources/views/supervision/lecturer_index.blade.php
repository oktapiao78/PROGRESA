@extends('layouts.app', [
    'title' => 'Sesi Bimbingan & Konsultasi',
    'headerTitle' => 'Sesi Bimbingan & Konsultasi',
    'headerSubtitle' => 'Atur Ketersediaan Jadwal & Tanggapi Pengajuan Mahasiswa'
])

@section('content')
<div x-data="{ completeModal: false, activeRequestId: null }" class="space-y-6">

    <!-- Top Grid: Slot Creator & Active Slots -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Slot Creator Form -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-extrabold text-[#4a4a5a] mb-1">Buka Slot Ketersediaan Baru</h3>
            <p class="text-xs text-[#8a8a9d] mb-4">Publikasikan waktu luang Anda agar mahasiswa bimbingan dapat memilih jadwal.</p>

            <form action="{{ route('supervision.slots.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Tanggal</label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Mulai (WIB)</label>
                        <input type="time" name="start_time" required
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Selesai (WIB)</label>
                        <input type="time" name="end_time" required
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Kuota Mahasiswa</label>
                        <input type="number" name="quota" min="1" max="20" value="2" required
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Tipe Lokasi</label>
                        <select name="location_type" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white">
                            <option value="offline">Tatap Muka (Offline)</option>
                            <option value="online">Daring (Online)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Ruangan / Link Zoom / Meet</label>
                    <input type="text" name="room_or_link" placeholder="Misal: R. Dosen 302 atau https://meet.google.com/..."
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none bg-white">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-[#7B5EA7] hover:bg-[#6b4f96] text-white rounded-xl text-xs font-bold shadow-sm transition">
                    + Publikasikan Slot Ketersediaan
                </button>
            </form>
        </div>

        <!-- Active Published Slots -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
            <div>
                <h3 class="text-base font-extrabold text-[#4a4a5a] mb-1">Jadwal Slot Ketersediaan Anda</h3>
                <p class="text-xs text-[#8a8a9d]">Slot jadwal aktif yang dapat dilihat dan dipesan oleh mahasiswa bimbingan.</p>
            </div>

            @if($mySlots->isEmpty())
                <p class="text-xs text-[#8a8a9d] text-center py-12">Belum ada slot ketersediaan bimbingan yang aktif.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach($mySlots as $slot)
                        <div class="border border-[#e1e1e8] rounded-2xl p-4 bg-white flex flex-col justify-between space-y-3 hover:border-[#9D62CC] transition">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $slot->location_type === 'online' ? 'bg-[#DCEAFF] text-[#1D4ED8]' : 'bg-[#D9FBEA] text-[#009E70]' }}">
                                        {{ ucfirst($slot->location_type) }}
                                    </span>
                                    <span class="text-[11px] font-bold {{ $slot->booked_count >= $slot->quota ? 'text-rose-600' : 'text-[#6b6b7b]' }}">
                                        Terisi: {{ $slot->booked_count }}/{{ $slot->quota }}
                                    </span>
                                </div>
                                <h5 class="text-xs font-bold text-[#4a4a5a] mt-2.5">
                                    {{ \Carbon\Carbon::parse($slot->date)->isoFormat('dddd, D MMMM Y') }}
                                </h5>
                                <p class="text-xs text-[#8a8a9d] mt-0.5">
                                    {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }} WIB
                                </p>
                                @if($slot->room_or_link)
                                    <p class="text-[11px] text-[#6b6b7b] truncate mt-1">
                                        📍 {{ $slot->room_or_link }}
                                    </p>
                                @endif
                            </div>

                            @if($slot->booked_count === 0)
                                <form action="{{ route('supervision.slots.destroy', $slot) }}" method="POST" class="pt-2 border-t border-slate-100">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition">
                                        Hapus Slot
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Incoming Requests (Pending) -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div>
            <h3 class="text-base font-extrabold text-[#4a4a5a] mb-1">Permintaan Bimbingan Masuk (Menunggu Konfirmasi)</h3>
            <p class="text-xs text-[#8a8a9d]">Setujui atau tolak pengajuan konsultasi dari mahasiswa bimbingan.</p>
        </div>

        @if($pendingRequests->isEmpty())
            <p class="text-xs text-[#8a8a9d] text-center py-8">Tidak ada permintaan bimbingan baru yang menunggu konfirmasi.</p>
        @else
            <div class="space-y-3">
                @foreach($pendingRequests as $req)
                    <div class="border border-[#e1e1e8] rounded-2xl p-4.5 bg-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 hover:border-[#9D62CC] transition">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-11 h-11 rounded-full overflow-hidden bg-[#e2d9f7] border-2 border-[#9D62CC] flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#7B5EA7]" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-[#4a4a5a] truncate">{{ $req->student->name }}</h4>
                                <p class="text-xs text-[#8a8a9d]">
                                    NIM: {{ $req->student->nim_nip }} &bull; Topik: <span class="font-bold text-[#4a4a5a]">{{ $req->topic }}</span>
                                </p>
                                <p class="text-[11px] text-[#6b6b7b] mt-0.5">
                                    Jadwal: {{ $req->scheduled_at ? $req->scheduled_at->isoFormat('dddd, D MMM Y - HH:mm') . ' WIB' : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 w-full md:w-auto justify-end">
                            <form action="{{ route('supervision.respond', $req) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="px-4 py-2 bg-[#009E70] hover:bg-[#00875e] text-white rounded-xl text-xs font-bold transition shadow-sm">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('supervision.respond', $req) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="px-4 py-2 bg-[#FCE8E8] hover:bg-[#fad4d4] text-[#d93838] rounded-xl text-xs font-bold transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Approved Upcoming Sessions -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div>
            <h3 class="text-base font-extrabold text-[#4a4a5a] mb-1">Sesi Bimbingan Mendatang (Disetujui)</h3>
            <p class="text-xs text-[#8a8a9d]">Sesi konsultasi yang telah dijadwalkan bersama mahasiswa.</p>
        </div>

        @if(($upcomingSessions ?? collect())->isEmpty())
            <p class="text-xs text-[#8a8a9d] text-center py-8">Belum ada sesi bimbingan yang terjadwal dalam waktu dekat.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($upcomingSessions as $session)
                    <div class="border border-[#e1e1e8] rounded-2xl p-4.5 bg-white space-y-2 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-[#DCEAFF] text-[#1D4ED8]">
                                    {{ $session->status->label() }}
                                </span>
                                <span class="text-xs font-bold text-[#7B5EA7]">
                                    {{ $session->scheduled_at ? $session->scheduled_at->isoFormat('dddd, D MMM Y - HH:mm') . ' WIB' : '-' }}
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-[#4a4a5a]">{{ $session->topic }}</h4>
                            <p class="text-xs text-[#8a8a9d] mt-1">
                                Mahasiswa: <strong class="text-[#4a4a5a]">{{ $session->student->name }}</strong> ({{ $session->student->nim_nip }})
                            </p>
                            @if($session->slot?->room_or_link)
                                <p class="text-xs text-[#6b6b7b] mt-1">📍 {{ $session->slot->room_or_link }}</p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <button type="button" @click="activeRequestId = {{ $session->id }}; completeModal = true"
                                class="w-full py-2 px-3 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7B5EA7] rounded-xl text-xs font-bold transition">
                                Selesaikan Sesi &amp; Buat Berita Acara
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Riwayat Bimbingan Selesai / Berita Acara -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div>
            <h3 class="text-base font-extrabold text-[#4a4a5a] mb-1">Riwayat Bimbingan Selesai &amp; Berita Acara</h3>
            <p class="text-xs text-[#8a8a9d]">Logbook dan catatan bimbingan yang telah diselesaikan.</p>
        </div>

        @if(($completedSessions ?? collect())->isEmpty())
            <p class="text-xs text-[#8a8a9d] text-center py-8">Belum ada riwayat bimbingan yang diselesaikan.</p>
        @else
            <div class="space-y-3">
                @foreach($completedSessions as $session)
                    <div class="border border-[#e1e1e8] rounded-2xl p-4.5 bg-white space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-[#D9FBEA] text-[#009E70]">
                                    Selesai
                                </span>
                                <h4 class="text-sm font-bold text-[#4a4a5a] mt-1">{{ $session->topic }}</h4>
                                <p class="text-xs text-[#8a8a9d]">
                                    Mahasiswa: <strong class="text-[#4a4a5a]">{{ $session->student->name }}</strong> ({{ $session->student->nim_nip }})
                                </p>
                            </div>
                            <span class="text-xs font-bold text-[#7B5EA7] shrink-0">
                                {{ $session->completed_at ? $session->completed_at->isoFormat('dddd, D MMM Y - HH:mm') . ' WIB' : ($session->scheduled_at ? $session->scheduled_at->isoFormat('dddd, D MMM Y - HH:mm') . ' WIB' : '-') }}
                            </span>
                        </div>

                        @if($session->meeting_summary)
                            <div class="bg-[#F8F7FF] rounded-xl p-3 border border-[#E8E4F8] text-xs">
                                <p class="font-bold text-[#5c3e80] mb-0.5">Rangkuman / Catatan Pembimbing:</p>
                                <p class="text-[#4a4a5a] whitespace-pre-line">{{ $session->meeting_summary }}</p>
                            </div>
                        @endif

                        @if($session->action_items)
                            <div class="bg-[#FDF9EA] rounded-xl p-3 border border-[#F6E9BE] text-xs">
                                <p class="font-bold text-[#8F6A00] mb-0.5">Tindak Lanjut Mahasiswa:</p>
                                <p class="text-[#4a4a5a] whitespace-pre-line">{{ $session->action_items }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(method_exists($completedSessions, 'hasPages') && $completedSessions->hasPages())
                <div class="pt-2">
                    {{ $completedSessions->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Complete Session Modal -->
    <div x-show="completeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div @click="completeModal = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-white rounded-[24px] max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-[#4a4a5a]">Selesaikan Sesi &amp; Berita Acara</h3>
                    <button type="button" @click="completeModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none">
                        &times;
                    </button>
                </div>

                <form :action="'{{ url('/supervision/requests') }}/' + activeRequestId + '/complete'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Rangkuman Hasil Diskusi / Berita Acara</label>
                        <textarea name="meeting_summary" rows="3" required placeholder="Tuliskan poin-poin yang dibahas selama sesi bimbingan..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1">Tindak Lanjut / Action Items Mahasiswa</label>
                        <textarea name="action_items" rows="2" placeholder="Tugas atau target revisi untuk bimbingan berikutnya..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="completeModal = false" class="px-5 py-2 bg-[#FDE8E8] text-[#4a4a5a] rounded-xl text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#009E70] hover:bg-[#00875e] text-white rounded-xl text-xs font-bold transition">
                            Simpan Berita Acara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
