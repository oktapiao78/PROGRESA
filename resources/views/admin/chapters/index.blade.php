@extends('layouts.app', [
    'title' => 'Konfigurasi Struktur Bab',
    'headerTitle' => 'Konfigurasi Struktur Bab Skripsi',
    'headerSubtitle' => 'Atur Struktur Bab Standar untuk Tiap Program Studi'
])

@section('content')
<div x-data="{ showTemplateModal: false }" class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Master Template Bab</h3>
            <p class="text-xs text-slate-500">Struktur bab default yang akan otomatis dibuatkan saat mahasiswa ditugaskan skripsi baru.</p>
        </div>

        <button type="button" @click="showTemplateModal = true"
            class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-1.5 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Struktur Bab Baru</span>
        </button>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3 text-center">Urutan</th>
                        <th class="py-2.5 px-3">Kode Bab</th>
                        <th class="py-2.5 px-3">Judul Bab</th>
                        <th class="py-2.5 px-3">Deskripsi / Panduan</th>
                        <th class="py-2.5 px-3 text-center">Wajib Ada?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $tpl)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 text-center font-bold text-slate-900">{{ $tpl->order }}</td>
                            <td class="py-3 px-3 font-mono font-semibold text-indigo-600">{{ $tpl->code }}</td>
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $tpl->title }}</td>
                            <td class="py-3 px-3 text-slate-500 max-w-sm">{{ $tpl->description ?? '-' }}</td>
                            <td class="py-3 px-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $tpl->is_required ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $tpl->is_required ? 'Wajib' : 'Opsional' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada template bab terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Template Modal -->
    <div x-show="showTemplateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTemplateModal" @click="showTemplateModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showTemplateModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Tambah Template Bab</h3>
                    <button @click="showTemplateModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.chapters.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Bab</label>
                            <input type="text" name="code" required placeholder="BAB_1" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Urutan Nomor</label>
                            <input type="number" name="order" required min="1" value="1" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Bab</label>
                        <input type="text" name="title" required placeholder="Pendahuluan" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Panduan / Deskripsi Bab</label>
                        <textarea name="description" rows="3" placeholder="Pedoman isi bab..." class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="showTemplateModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                            Simpan Struktur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
