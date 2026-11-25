@extends('layouts.app', [
    'title' => 'Jadwal & Pengajuan Bimbingan',
    'headerTitle' => 'Jadwal & Pengajuan Bimbingan',
    'headerSubtitle' => 'Pilih Slot Ketersediaan Dosen Pembimbing'
])

@section('content')
<div x-data="{ selectedSlot: null, showBookModal: false }" class="space-y-6">

    <!-- Available Slots From Supervisors -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Slot Ketersediaan Dosen Pembimbing Anda</h3>
                <p class="text-xs text-slate-500">Pilih slot jadwal yang dipublikasikan oleh Pembimbing 1 atau Pembimbing 2 Anda.</p>
            </div>
        </div>

        @if($availableSlots->isEmpty())
            <div class="text-center py-8 text-slate-400 text-xs">
                Belum ada slot ketersediaan bimbingan baru yang dibuka oleh dosen pembimbing Anda saat ini.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableSlots as $slot)
                    <div class="border border-slate-200 hover:border-emerald-400 rounded-2xl p-4 bg-slate-50/50 hover:bg-white transition flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $slot->location_type === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($slot->location_type) }}
                                </span>
                                <span class="text-xs font-bold text-slate-500">
                                    Sisa Kuota: {{ $slot->quota - $slot->booked_count }}
                                </span>
                            </div>

                            <h4 class="text-sm font-bold text-slate-900">{{ $slot->supervisor->name }}</h4>
                            <p class="text-xs text-slate-600 mt-1">
                                📅 {{ \Carbon\Carbon::parse($slot->date)->isoFormat('dddd, D MMMM Y') }}
                            </p>
                            <p class="text-xs text-slate-600">
                                ⏰ {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }} WIB
                            </p>
                            @if($slot->room_or_link)
                                <p class="text-xs text-slate-500 mt-1 font-mono truncate">
                                    📍 {{ $slot->room_or_link }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-slate-200/60">
                            @if(in_array($slot->id, $myBookedSlotIds ?? []))
                                <button disabled class="w-full py-2 px-3 bg-purple-100 text-purple-700 rounded-xl text-xs font-semibold cursor-not-allowed">
                                    Sudah Diajukan
                                </button>
                            @elseif($slot->hasAvailableQuota())
                                <button type="button" @click="selectedSlot = {{ $slot->id }}; showBookModal = true"
                                    class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                    Ajukan Bimbingan di Slot Ini
                                </button>
                            @else
                                <button disabled class="w-full py-2 px-3 bg-slate-200 text-slate-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                                    Kuota Penuh
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Approved Upcoming Schedule -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-base font-bold text-slate-900">Jadwal Bimbingan Mendatang (Disetujui)</h3>
            <p class="text-xs text-slate-500">Daftar jadwal bimbingan Anda yang telah disetujui oleh dosen pembimbing.</p>
        </div>

        @if(($approvedSessions ?? collect())->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Belum ada jadwal bimbingan yang disetujui saat ini.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($approvedSessions as $session)
                    <div class="border border-purple-200 bg-purple-50/40 rounded-2xl p-4 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                {{ $session->status->label() }}
                            </span>
                            <span class="text-[11px] font-semibold text-purple-800">
                                {{ $session->scheduled_at ? $session->scheduled_at->isoFormat('dddd, D MMM Y - HH:mm') . ' WIB' : '-' }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $session->topic }}</h4>
                        <p class="text-xs text-slate-600">Dosen: <strong>{{ $session->supervisor?->name ?? '-' }}</strong></p>
                        @if($session->slot?->room_or_link)
                            <p class="text-[11px] text-slate-500">📍 {{ $session->slot->room_or_link }}</p>
                        @endif
                        @if($session->supervisor_notes)
                            <p class="text-[11px] text-slate-600 italic">Catatan Dosen: "{{ $session->supervisor_notes }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- My Requests & Guidance History -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Pengajuan & Sesi Bimbingan Anda</h3>
                <p class="text-xs text-slate-500">Status persetujuan bimbingan dan catatan berita acara dari dosen pembimbing.</p>
            </div>
        </div>

        @if($myRequests->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Anda belum pernah mengajukan sesi bimbingan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Jadwal Sesi</th>
                            <th class="py-2.5 px-3">Dosen</th>
                            <th class="py-2.5 px-3">Topik Bahasan</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Catatan / Berita Acara</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($myRequests as $req)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-3 whitespace-nowrap text-slate-800 font-medium">
                                    {{ $req->scheduled_at ? $req->scheduled_at->isoFormat('D MMM Y, HH:mm') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $req->supervisor->name }}</td>
                                <td class="py-2.5 px-3 font-medium text-slate-800">{{ $req->topic }}</td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $req->status->badgeColor() }}">
                                        {{ $req->status->label() }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 max-w-xs text-slate-600">
                                    @if($req->meeting_summary)
                                        <span class="font-bold text-slate-700 block">Berita Acara:</span>
                                        <p>{{ $req->meeting_summary }}</p>
                                        @if($req->action_items)
                                            <span class="text-[11px] text-purple-700 block mt-1">Tindak Lanjut: {{ $req->action_items }}</span>
                                        @endif
                                    @elseif($req->status === \App\Enums\SupervisionStatus::REJECTED && $req->supervisor_notes)
                                        <span class="font-bold text-rose-700 block">Alasan Penolakan:</span>
                                        <span class="text-rose-600">{{ $req->supervisor_notes }}</span>
                                    @elseif($req->supervisor_notes)
                                        <span class="text-slate-500 italic">{{ $req->supervisor_notes }}</span>
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

    <!-- Booking Modal -->
    <div x-show="showBookModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showBookModal" @click="showBookModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showBookModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Form Pengajuan Bimbingan</h3>
                    <button @click="showBookModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/supervision/slots') }}/' + selectedSlot + '/book'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Topik / Bahasan Bimbingan</label>
                        <input type="text" name="topic" required
                            placeholder="Contoh: Diskusi metodologi penelitian dan perbaikan BAB III"
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Dosen (Opsional)</label>
                        <textarea name="student_notes" rows="3"
                            placeholder="Tuliskan kendala atau pertanyaan yang ingin dibahas saat sesi bimbingan..."
                            class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="showBookModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
