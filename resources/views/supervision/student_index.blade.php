@extends('layouts.app', [
    'title' => 'Jadwal & Pengajuan Bimbingan',
    'headerTitle' => 'Jadwal & Pengajuan Bimbingan',
    'headerSubtitle' => 'Pilih Slot Ketersediaan Dosen Pembimbing'
])

@section('content')
<div x-data="{
    detailSlot: null,
    showDetailModal: false,
    selectedSlotId: null,
    showBookModal: false,
    openDetail(slot) {
        this.detailSlot = slot;
        this.showDetailModal = true;
    },
    openBook(slotId) {
        this.selectedSlotId = slotId;
        this.showBookModal = true;
    }
}" class="space-y-6">

    <!-- Card 1: Slot Ketersediaan Dosen Pembimbing Anda -->
    <div class="bg-white border border-[#e1e1e8] rounded-2xl p-6 shadow-sm space-y-4">
        <div>
            <h2 class="text-lg font-extrabold text-[#4a4a5a] tracking-tight">Slot Ketersediaan Dosen Pembimbing Anda</h2>
            <p class="text-xs text-[#8a8a9d] mt-1">Pilih slot jadwal yang dipublikasikan oleh Pembimbing</p>
        </div>

        @if($availableSlots->isEmpty())
            <div class="text-center py-16 text-[#8a8a9d] text-xs">
                Belum ada slot ketersediaan bimbingan baru yang dibuka oleh dosen pembimbing Anda saat ini
            </div>
        @else
            <div class="space-y-3 pt-1">
                @foreach($availableSlots as $slot)
                    @php
                        $isBooked = in_array($slot->id, $myBookedSlotIds ?? []);
                        $myReq = $myRequests->firstWhere('supervision_slot_id', $slot->id);
                        $statusState = $myReq ? $myReq->status->value : null;

                        $slotJson = json_encode([
                            'id' => $slot->id,
                            'supervisor' => $slot->supervisor->name,
                            'date' => \Carbon\Carbon::parse($slot->date)->isoFormat('D MMMM Y'),
                            'time' => substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5),
                            'quota' => $slot->quota,
                            'remaining' => $slot->quota - $slot->booked_count,
                            'location_type' => $slot->location_type === 'online' ? 'Online (Zoom / Meet)' : 'Tatap muka (Offline)',
                            'room' => $slot->room_or_link ?: '-'
                        ]);
                    @endphp

                    <div class="border border-[#e5e5ea] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white hover:border-[#9D62CC] transition">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-[#e2d9f7] border-2 border-[#9D62CC] flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#7B5EA7]" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-[#4a4a5a] truncate">{{ $slot->supervisor->name }}</h4>
                                <p class="text-xs text-[#8a8a9d] mt-0.5">
                                    {{ \Carbon\Carbon::parse($slot->date)->isoFormat('dddd, D MMMM Y') }} &bull; {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }} WIB
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0 w-full sm:w-auto justify-end">
                            <button type="button" @click='openDetail({!! $slotJson !!})'
                                class="px-4 py-2 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7B5EA7] rounded-xl text-xs font-bold transition shadow-sm">
                                Info Detail
                            </button>

                            @if($isBooked && $statusState === 'approved')
                                <span class="px-4 py-2 bg-[#D9FBEA] text-[#009E70] rounded-xl text-xs font-bold shadow-sm">
                                    Disetujui
                                </span>
                            @elseif($isBooked && $statusState === 'pending')
                                <span class="px-4 py-2 bg-[#F3A142] text-white rounded-xl text-xs font-bold shadow-sm">
                                    Menunggu Persetujuan Pembimbing
                                </span>
                            @elseif($isBooked)
                                <span class="px-4 py-2 bg-[#EEE8FA] text-[#7B5EA7] rounded-xl text-xs font-bold">
                                    Sudah Diajukan
                                </span>
                            @elseif($slot->hasAvailableQuota())
                                <button type="button" @click="openBook({{ $slot->id }})"
                                    class="px-4 py-2 bg-[#DCEAFF] hover:bg-[#BFDBFE] text-[#1D4ED8] rounded-xl text-xs font-bold transition shadow-sm">
                                    Tambah
                                </button>
                            @else
                                <span class="px-4 py-2 bg-[#E9E9ED] text-[#7a7a8c] rounded-xl text-xs font-bold">
                                    Penuh
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Card 2: Jadwal Bimbingan Mendatang (Disetujui) -->
    @if(($approvedSessions ?? collect())->isNotEmpty())
        <div class="bg-white border border-[#e1e1e8] rounded-2xl p-6 shadow-sm space-y-4">
            <div>
                <h2 class="text-base font-extrabold text-[#4a4a5a] tracking-tight">Jadwal Bimbingan Mendatang (Disetujui)</h2>
                <p class="text-xs text-[#8a8a9d] mt-1">Daftar sesi konsultasi dan bimbingan aktif bersama dosen pembimbing</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($approvedSessions as $session)
                    <div class="border border-[#e1e1e8] rounded-2xl p-4 bg-white space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-[#DCEAFF] text-[#1D4ED8]">
                                {{ $session->status->label() }}
                            </span>
                            <span class="text-xs font-bold text-[#7B5EA7]">
                                {{ $session->scheduled_at ? $session->scheduled_at->isoFormat('dddd, D MMM Y - HH:mm') . ' WIB' : '-' }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-[#4a4a5a]">{{ $session->topic }}</h4>
                        <p class="text-xs text-[#8a8a9d]">Dosen: <strong class="text-[#4a4a5a]">{{ $session->supervisor?->name ?? '-' }}</strong></p>
                        @if($session->slot?->room_or_link)
                            <p class="text-xs text-[#6b6b7b]">📍 {{ $session->slot->room_or_link }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Card 3: Riwayat Pengajuan & Sesi Bimbingan Anda -->
    <div class="bg-white border border-[#e1e1e8] rounded-2xl p-6 shadow-sm space-y-4">
        <div>
            <h2 class="text-lg font-extrabold text-[#4a4a5a] tracking-tight">Riwayat Pengajuan &amp; Sesi Bimbingan Anda</h2>
            <p class="text-xs text-[#8a8a9d] mt-1">Status persetujuan bimbingan dan catatan berita acara dosen pembimbing</p>
        </div>

        @if($myRequests->isEmpty())
            <div class="text-center py-16 text-[#8a8a9d] text-xs">
                Ajukan Riwayat Pengajuan &amp; Sesi Bimbingan Anda
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-[#F4F4F6] text-[#6b6b7b] uppercase font-bold text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">JADWAL SESI</th>
                            <th class="py-3 px-4">DOSEN</th>
                            <th class="py-3 px-4">TOPIK BAHASAN</th>
                            <th class="py-3 px-4">STATUS</th>
                            <th class="py-3 px-4">CATATAN/BERITA ACARA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($myRequests as $req)
                            @php
                                $badgeStyle = match($req->status) {
                                    \App\Enums\SupervisionStatus::ACCEPTED => 'bg-[#DCEAFF] text-[#1D4ED8]',
                                    \App\Enums\SupervisionStatus::COMPLETED => 'bg-[#D9FBEA] text-[#009E70]',
                                    \App\Enums\SupervisionStatus::REQUESTED => 'bg-[#FDF3D8] text-[#B58E3A]',
                                    \App\Enums\SupervisionStatus::REJECTED => 'bg-[#FCE8E8] text-[#D93838]',
                                    default => 'bg-slate-100 text-slate-600'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-[#4a4a5a] font-medium">
                                    {{ $req->scheduled_at ? $req->scheduled_at->isoFormat('D MMM Y, HH:mm') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-[#4a4a5a]">{{ $req->supervisor->name }}</td>
                                <td class="py-3 px-4 text-[#4a4a5a]">{{ $req->topic }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-bold {{ $badgeStyle }}">
                                        {{ $req->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-[#6b6b7b]">
                                    @if($req->meeting_summary)
                                        <p class="font-bold text-[#4a4a5a]">{{ $req->meeting_summary }}</p>
                                        @if($req->action_items)
                                            <p class="text-[11px] text-[#7B5EA7] mt-0.5">Tindak lanjut: {{ $req->action_items }}</p>
                                        @endif
                                    @elseif($req->supervisor_notes)
                                        <p class="italic text-[#6b6b7b]">{{ $req->supervisor_notes }}</p>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $myRequests->links() }}
            </div>
        @endif
    </div>

    <!-- Modal 1: Detail Information Dosen (Sesuai Screenshot 1) -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div @click="showDetailModal = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-[#FDF7FE] rounded-[30px] max-w-md w-full p-8 shadow-2xl border-2 border-[#E2D9F7] z-10 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-extrabold text-[#4a4a5a] tracking-tight">Detail Information</h3>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-2xl leading-none">
                        &times;
                    </button>
                </div>

                <div class="space-y-5 text-[#4a4a5a]">
                    <!-- Tanggal -->
                    <div>
                        <h4 class="text-sm font-extrabold text-[#6b6b7b] mb-1.5">Tanggal</h4>
                        <div class="flex items-center gap-3 text-base font-medium">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#4a4a5a]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span x-text="detailSlot ? detailSlot.date : '-'"></span>
                        </div>
                    </div>

                    <!-- Jam -->
                    <div>
                        <h4 class="text-sm font-extrabold text-[#6b6b7b] mb-1.5">Jam</h4>
                        <div class="flex items-center gap-3 text-base font-medium">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 text-[#4a4a5a]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span x-text="detailSlot ? detailSlot.time : '-'"></span>
                        </div>
                    </div>

                    <!-- Kuota & Tipe Lokasi -->
                    <div class="grid grid-cols-2 gap-4 pt-1">
                        <div>
                            <h4 class="text-sm font-extrabold text-[#6b6b7b] mb-1">Kuota Mahasiswa</h4>
                            <p class="text-base font-medium text-[#4a4a5a]" x-text="detailSlot ? detailSlot.quota : '-'"></p>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-[#6b6b7b] mb-1">Tipe Lokasi</h4>
                            <p class="text-base font-medium text-[#4a4a5a]" x-text="detailSlot ? detailSlot.location_type : '-'"></p>
                        </div>
                    </div>

                    <!-- Ruangan / Link -->
                    <div class="pt-1">
                        <h4 class="text-sm font-extrabold text-[#6b6b7b] mb-1">Ruangan / LinkZoom / Google Meet</h4>
                        <p class="text-base font-medium text-[#4a4a5a]" x-text="detailSlot ? detailSlot.room : '-'"></p>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" @click="showDetailModal = false"
                        class="px-6 py-2.5 bg-[#DDD7FA] hover:bg-[#DCD7FE] text-[#7B5EA7] font-bold text-xs rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2: Form Pengajuan Bimbingan (Booking) -->
    <div x-show="showBookModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div @click="showBookModal = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-white rounded-[24px] max-w-lg w-full p-6 shadow-2xl border border-slate-200 z-10 space-y-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-extrabold text-[#4a4a5a]">Form Pengajuan Bimbingan</h3>
                    <button type="button" @click="showBookModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none">
                        &times;
                    </button>
                </div>

                <form :action="'{{ url('/supervision/slots') }}/' + selectedSlotId + '/book'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1.5">Topik / Bahasan Bimbingan</label>
                        <input type="text" name="topic" required
                            placeholder="Contoh: Konsultasi perbaikan metodologi BAB III & Diagram Arsitektur"
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#6b6b7b] mb-1.5">Catatan Tambahan untuk Dosen (Opsional)</label>
                        <textarea name="student_notes" rows="3"
                            placeholder="Tuliskan kendala atau pertanyaan yang ingin dibahas saat sesi bimbingan..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-[#9D62CC] focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="showBookModal = false" class="px-5 py-2 bg-[#FDE8E8] text-[#4a4a5a] rounded-xl text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#009E70] hover:bg-[#00875e] text-white rounded-xl text-xs font-bold transition">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
