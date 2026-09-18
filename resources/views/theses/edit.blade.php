@extends('layouts.app', [
    'title' => 'Edit Data Skripsi',
    'headerTitle' => 'Edit Data Skripsi',
    'headerSubtitle' => $thesis->student->name . ' (' . $thesis->student->nim_nip . ')'
])

@section('content')
<div class="max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
    <form action="{{ route('theses.update', $thesis) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Skripsi / Tugas Akhir</label>
            <input type="text" name="title" value="{{ old('title', $thesis->title) }}" required
                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Abstrak / Rencana Penelitian</label>
            <textarea name="abstract" rows="4"
                class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('abstract', $thesis->abstract) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Pembimbing 1</label>
                <select name="supervisor_1_id" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Pilih Pembimbing 1 --</option>
                    @foreach($supervisors as $sp)
                        <option value="{{ $sp->id }}" {{ old('supervisor_1_id', $thesis->supervisor_1_id) == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ $sp->nim_nip }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Pembimbing 2</label>
                <select name="supervisor_2_id" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Pilih Pembimbing 2 (Opsional) --</option>
                    @foreach($supervisors as $sp)
                        <option value="{{ $sp->id }}" {{ old('supervisor_2_id', $thesis->supervisor_2_id) == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }} ({{ $sp->nim_nip }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Tanggal Selesai</label>
                <input type="date" name="target_completion_date" value="{{ old('target_completion_date', $thesis->target_completion_date?->toDateString()) }}"
                    class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Skripsi</label>
                <select name="status" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @foreach(\App\Enums\ThesisStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ old('status', $thesis->status->value) === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3">
            <a href="{{ route('theses.show', $thesis) }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                Batal
            </a>
            <button type="submit" class="py-2.5 px-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
