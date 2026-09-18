@extends('layouts.app', [
    'title' => 'Daftar Skripsi & Tugas Akhir',
    'headerTitle' => 'Daftar Skripsi & Tugas Akhir',
    'headerSubtitle' => 'Kelola Data & Pantau Progres Bimbingan Mahasiswa'
])

@section('content')
<div class="space-y-6">

    <!-- Filter & Search Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('theses.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari judul atau nama mahasiswa..."
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\ThesisStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-emerald-600 text-white font-semibold rounded-xl text-xs transition">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('theses.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Theses Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($theses as $thesis)
            <div class="bg-white border border-slate-200 hover:border-emerald-400 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $thesis->status->badgeColor() }}">
                            {{ $thesis->status->label() }}
                        </span>
                        @if($thesis->latestStagnation)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $thesis->latestStagnation->risk_level->badgeColor() }}">
                                {{ $thesis->latestStagnation->risk_level->label() }}
                            </span>
                        @endif
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">
                        {{ $thesis->title }}
                    </h4>

                    <div class="text-xs text-slate-500 pt-1">
                        <p class="font-semibold text-slate-800">{{ $thesis->student->name }}</p>
                        <p class="text-[11px] text-slate-400">NIM: {{ $thesis->student->nim_nip }} • {{ $thesis->studyProgram->code }}</p>
                    </div>

                    <div class="text-[11px] text-slate-600 pt-1 space-y-0.5 border-t border-slate-100">
                        <p><span class="text-slate-400">P1:</span> {{ $thesis->supervisor1?->name ?? 'Belum ditugaskan' }}</p>
                        <p><span class="text-slate-400">P2:</span> {{ $thesis->supervisor2?->name ?? 'Belum ditugaskan' }}</p>
                    </div>

                    <!-- Progress -->
                    <div class="pt-2">
                        <div class="flex justify-between text-[11px] mb-1 font-semibold text-slate-600">
                            <span>Progres Bab:</span>
                            <span class="text-emerald-600">{{ $thesis->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $thesis->progress_percentage }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">
                        {{ $thesis->chapters->count() }} Bab Terdaftar
                    </span>
                    <a href="{{ route('theses.show', $thesis) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition">
                        Buka Skripsi &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400 text-xs">
                Tidak ada data skripsi yang sesuai dengan kriteria pencarian Anda.
            </div>
        @endforelse
    </div>

    <div>
        {{ $theses->links() }}
    </div>

</div>
@endsection
