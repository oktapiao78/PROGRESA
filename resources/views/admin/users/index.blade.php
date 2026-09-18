@extends('layouts.app', [
    'title' => 'Manajemen Pengguna',
    'headerTitle' => 'Manajemen Pengguna & Hak Akses',
    'headerSubtitle' => 'Kelola Akun Mahasiswa, Dosen, Koordinator, dan Staf'
])

@section('content')
<div x-data="{ showUserModal: false }" class="space-y-6">

    <!-- Top Bar: Filter & Add User Button -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau NIM/NIP..."
                class="px-3 py-2 border border-slate-300 rounded-xl text-xs w-64 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            
            <select name="role" class="px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Peran</option>
                @foreach($roles as $r)
                    <option value="{{ $r->value }}" {{ request('role') === $r->value ? 'selected' : '' }}>
                        {{ $r->label() }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="py-2 px-3 bg-slate-900 hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs transition">
                    Reset
                </a>
            @endif
        </form>

        <button type="button" @click="showUserModal = true"
            class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-1.5 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Nama Pengguna</th>
                        <th class="py-2.5 px-3">NIM / NIP</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Peran (Role)</th>
                        <th class="py-2.5 px-3">Program Studi</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $u->name }}</td>
                            <td class="py-3 px-3 font-mono text-[11px] text-slate-500">{{ $u->nim_nip ?? '-' }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $u->email }}</td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $u->role->badgeColor() }}">
                                    {{ $u->role->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-500">{{ $u->studyProgram?->code ?? '-' }}</td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $u->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold {{ $u->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }} transition">
                                            {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create User Modal -->
    <div x-show="showUserModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showUserModal" @click="showUserModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showUserModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Daftarkan Pengguna Baru</h3>
                    <button @click="showUserModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">NIM / NIP</label>
                            <input type="text" name="nim_nip" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Peran (Role)</label>
                            <select name="role" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                @foreach($roles as $r)
                                    <option value="{{ $r->value }}">{{ $r->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Program Studi</label>
                        <select name="study_program_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($studyPrograms as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->name }} ({{ $sp->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Default</label>
                        <input type="password" name="password" required minlength="6" value="password123" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="showUserModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
