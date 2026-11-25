@extends('layouts.app', [
    'title' => 'Kelola Slot & Permintaan Bimbingan',
    'headerTitle' => 'Sesi Bimbingan & Konsultasi',
    'headerSubtitle' => 'Atur Ketersediaan Jadwal & Tanggapi Pengajuan Mahasiswa'
])

@section('content')
<div x-data="{ completeModal: false, activeRequestId: null }" class="space-y-6">

    <!-- Top Grid: Slot Creator & Active Slots -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Slot Creator Form -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-1">Buka Slot Ketersediaan Baru</h3>
            <p class="text-xs text-slate-500 mb-4">Publikasikan waktu luang Anda agar mahasiswa bimbingan dapat memilih jadwal.</p>

            <form action="{{ route('supervision.slots.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai (WIB)</label>
                        <input type="time" name="start_time" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai (WIB)</label>
                        <input type="time" name="end_time" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kuota Mahasiswa</label>
                        <input type="number" name="quota" min="1" max="20" value="2" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Lokasi</label>
                        <select name="location_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="offline">Tatap Muka (Offline)</option>
                            <option value="online">Daring (Online)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ruangan / Link Zoom / Google Meet</label>
                    <input type="text" name="room_or_link" placeholder="Misal: R. Dosen 302 atau https://meet.google.com/..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    + Publikasikan Slot Ketersediaan
                </button>
            </form>
        </div>

        <!-- Active Published Slots -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-1">Jadwal Slot Ketersediaan Anda</h3>
            <p class="text-xs text-slate-500 mb-4">Slot jadwal aktif yang dapat dilihat dan dipesan oleh mahasiswa bimbingan.</p>

            @if($mySlots->isEmpty())
                <p class="text-xs text-slate-400 text-center py-8">Belum ada slot ketersediaan bimbingan yang aktif.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($mySlots as $slot)
                        <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50/60 flex flex-col justify-between space-y-2">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $slot->location_type === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst($slot->location_type) }}
                                    </span>
                                    <span class="text-[11px] font-semibold {{ $slot->booked_count >= $slot->quota ? 'text-rose-600' : 'text-slate-600' }}">
                                        Terisi: {{ $slot->booked_count }}/{{ $slot->quota }}
                                    </span>
                                </div>
                                <h5 class="text-xs font-bold text-slate-900 mt-2">
                                    {{ \Carbon\Carbon::parse($slot->date)->isoFormat('dddd, D MMMM Y') }}
                                </h5>
                                <p class="text-[11px] text-slate-600">
                                    {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }} WIB
                                </p>
                                @if($slot->room_or_link)
                                    <p class="text-[10px] text-slate-500 truncate mt-1">
                                        📍 {{ $slot->room_or_link }}
                                    </p>
                                @endif
                            </div>

                            @if($slot->booked_count === 0)
                                <form action="{{ route('supervision.slots.destroy', $slot) }}" method="POST" class="pt-2 border-t border-slate-200">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 transition">
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
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 mb-1">Permintaan Bimbingan Masuk (Menunggu Konfirmasi)</h3>
        <p class="text-xs text-slate-500 mb-4">Setujui atau tolak pengajuan konsultasi dari mahasiswa.</p>

        @if($pendingRequests->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Tidak ada permintaan bimbingan baru yang menunggu konfirmasi.</p>
        @else
            <div class="space-y-3">
                @foreach($pendingRequests as $req)
                    <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-slate-900 text-xs">{{ $req->student->name }}</span>
                                <span class="text-slate-400 text-[11px]">({{ $req->student->nim_nip }})</span>
                            </div>
                            <h5 class="text-sm font-bold text-indigo-950">{{ $req->topic }}</h5>
                            @if($req->student_notes)
                                <p class="text-xs text-slate-600 italic">"{{ $req->student_notes }}"</p>
                            @endif
                            <p class="text-[11px] text-slate-500">
                                📅 Rencana Sesi: {{ $req->scheduled_at ? $req->scheduled_at->isoFormat('dddd, D MMMM Y, HH:mm') : '-' }} WIB
                            </p>
                        </div>

                        <form action="{{ route('supervision.respond', $req) }}" method="POST" class="flex items-center space-x-2 flex-shrink-0">
                            @csrf
                            <input type="text" name="supervisor_notes" placeholder="Catatan / alasan jika tolak..."
                                class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs w-52">
                            <button type="submit" name="status" value="accepted"
                                class="py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                Terima
                            </button>
                            <button type="submit" name="status" value="rejected"
                                class="py-1.5 px-3 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-xs font-semibold transition">
                                Tolak
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Upcoming Approved Sessions -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 mb-1">Sesi Bimbingan Mendatang (Disetujui)</h3>
        <p class="text-xs text-slate-500 mb-4">Klik 'Selesaikan Sesi & Buat Berita Acara' setelah pertemuan bimbingan terlaksana.</p>

        @if($upcomingSessions->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Belum ada agenda sesi bimbingan mendatang.</p>
        @else
            <div class="space-y-3">
                @foreach($upcomingSessions as $session)
                    <div class="border border-indigo-100 rounded-2xl p-4 bg-indigo-50/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="font-bold text-slate-900 text-xs">{{ $session->student->name }}</span>
                            <h5 class="text-sm font-bold text-slate-800 mt-0.5">{{ $session->topic }}</h5>
                            <p class="text-xs text-indigo-700 font-medium mt-1">
                                📅 {{ $session->scheduled_at ? $session->scheduled_at->isoFormat('dddd, D MMMM Y - HH:mm') : '-' }} WIB
                            </p>
                        </div>

                        <div>
                            <button type="button" @click="activeRequestId = {{ $session->id }}; completeModal = true"
                                class="py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                Selesaikan Sesi & Buat Berita Acara
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Completed Sessions & Berita Acara -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 mb-1">Riwayat Sesi Bimbingan &amp; Berita Acara</h3>
        <p class="text-xs text-slate-500 mb-4">Daftar sesi bimbingan yang telah selesai dilaksanakan beserta ringkasan berita acara.</p>

        @if($completedSessions->isEmpty())
            <p class="text-xs text-slate-400 text-center py-6">Belum ada sesi bimbingan yang selesai.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Tanggal Selesai</th>
                            <th class="py-2.5 px-3">Mahasiswa</th>
                            <th class="py-2.5 px-3">Topik</th>
                            <th class="py-2.5 px-3">Berita Acara</th>
                            <th class="py-2.5 px-3">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($completedSessions as $done)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-3 whitespace-nowrap text-slate-700 font-medium">
                                    {{ $done->completed_at ? $done->completed_at->isoFormat('D MMM Y, HH:mm') : '-' }}
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $done->student?->name ?? '-' }}</td>
                                <td class="py-2.5 px-3 font-medium text-slate-800">{{ $done->topic }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $done->meeting_summary ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $done->action_items ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $completedSessions->links() }}
            </div>
        @endif
    </div>

    <!-- Complete Session Modal -->
    <div x-show="completeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="completeModal" @click="completeModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="completeModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Catatan Berita Acara Sesi Bimbingan</h3>
                    <button @click="completeModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ url('/supervision/requests') }}/' + activeRequestId + '/complete'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Rangkuman Hasil Diskusi / Berita Acara</label>
                        <textarea name="meeting_summary" rows="4" required
                            placeholder="Tuliskan materi yang dibahas, masukan metodologi, revisi instrumen, dll..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tindak Lanjut / Action Items Sebelum Bimbingan Berikutnya</label>
                        <textarea name="action_items" rows="2"
                            placeholder="Misal: Perbaiki BAB III dan siapkan kuesioner sebelum pertemuan selanjutnya..."
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="completeModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                            Simpan & Selesaikan Sesi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
