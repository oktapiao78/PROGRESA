@extends('layouts.app', [
    'title' => 'Bandingkan Versi Dokumen — ' . $chapter->title,
    'headerTitle' => 'Document Diff & Komparasi Versi',
    'headerSubtitle' => 'BAB ' . $chapter->order . ': ' . $chapter->title
])

@section('content')
<div class="space-y-6">

    <!-- Header & Version Picker Bar -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <a href="{{ route('chapters.show', $chapter) }}" class="text-xs text-slate-500 hover:text-emerald-600 transition inline-flex items-center space-x-1 mb-1">
                    <span>&larr; Kembali ke Detail BAB {{ $chapter->order }}</span>
                </a>
                <h2 class="text-xl font-black text-slate-900">Perbandingan Riwayat Draf</h2>
                <p class="text-xs text-slate-500 mt-0.5">Mendeteksi dan menandai secara visual bagian teks yang ditambah, dihapus, atau dimodifikasi.</p>
            </div>

            @if(isset($allVersions) && $allVersions->count() >= 2)
                <!-- Version Comparison Selector Form -->
                <form method="GET" action="{{ route('chapters.diff', $chapter) }}" class="flex flex-wrap items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="flex items-center space-x-1.5 text-xs">
                        <span class="font-semibold text-slate-600">Basis (v Lama):</span>
                        <select name="v1" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800">
                            @foreach($allVersions as $ver)
                                <option value="{{ $ver->id }}" {{ $oldVersion->id === $ver->id ? 'selected' : '' }}>
                                    v{{ $ver->version_number }} ({{ $ver->created_at->format('d/m H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <span class="text-slate-400 font-bold">&rarr;</span>

                    <div class="flex items-center space-x-1.5 text-xs">
                        <span class="font-semibold text-slate-600">Target (v Baru):</span>
                        <select name="v2" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800">
                            @foreach($allVersions as $ver)
                                <option value="{{ $ver->id }}" {{ $newVersion->id === $ver->id ? 'selected' : '' }}>
                                    v{{ $ver->version_number }} ({{ $ver->created_at->format('d/m H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="py-1.5 px-3.5 bg-slate-900 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition">
                        Bandingkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(!$diffData)
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center text-slate-400 text-xs">
            {{ $message ?? 'Belum dapat melakukan perbandingan dokumen.' }}
        </div>
    @else
        <!-- Diff Metrics Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">+ Penambahan Teks</span>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $diffData['additions'] }}</p>
                <span class="text-[10px] text-slate-400">Baris / kalimat baru</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block">- Pengurangan Teks</span>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $diffData['deletions'] }}</p>
                <span class="text-[10px] text-slate-400">Baris / kalimat dihapus</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">= Kalimat Tetap</span>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $diffData['unchanged'] }}</p>
                <span class="text-[10px] text-slate-400">Tidak mengalami perubahan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block">Tingkat Kemiripan</span>
                <p class="text-2xl font-black text-indigo-600 mt-1">{{ $diffData['similarity'] }}%</p>
                <span class="text-[10px] text-slate-400">Konsistensi konten draf</span>
            </div>
        </div>

        <!-- Versions Metadata Comparison -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900">Versi Asal (v{{ $oldVersion->version_number }})</span>
                    <a href="{{ route('versions.download', $oldVersion) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Unduh &darr;</a>
                </div>
                <p class="text-slate-500">Berkas: <span class="font-mono text-slate-700">{{ $oldVersion->original_filename }}</span> ({{ $oldVersion->formattedSize() }})</p>
                <p class="text-slate-400">Diunggah: {{ $oldVersion->created_at->isoFormat('D MMMM Y, HH:mm') }}</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-emerald-700">Versi Pembanding (v{{ $newVersion->version_number }})</span>
                    <a href="{{ route('versions.download', $newVersion) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Unduh &darr;</a>
                </div>
                <p class="text-slate-500">Berkas: <span class="font-mono text-slate-700">{{ $newVersion->original_filename }}</span> ({{ $newVersion->formattedSize() }})</p>
                <p class="text-slate-400">Diunggah: {{ $newVersion->created_at->isoFormat('D MMMM Y, HH:mm') }}</p>
            </div>
        </div>

        <!-- Visual Highlighted Diff Viewer -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-slate-900 px-5 py-3 flex items-center justify-between text-xs text-slate-300 border-b border-slate-800">
                <div class="flex items-center space-x-4">
                    <span class="font-bold text-white tracking-wide">Hasil Ekstraksi & Perubahan Teks</span>
                    <span class="flex items-center text-emerald-400"><span class="w-2 h-2 rounded-full bg-emerald-400 mr-1.5"></span>+ Ditambahkan</span>
                    <span class="flex items-center text-rose-400"><span class="w-2 h-2 rounded-full bg-rose-400 mr-1.5"></span>- Dihapus</span>
                </div>
                <span class="text-[11px] text-slate-400 font-mono">Unified Diff Engine</span>
            </div>

            <div class="font-mono text-xs overflow-x-auto divide-y divide-slate-100 max-h-[600px] overflow-y-auto">
                @forelse($diffData['diff_rows'] as $index => $row)
                    <div class="flex items-start py-1.5 px-4 {{ $row['class'] }}">
                        <span class="w-8 text-slate-400 select-none flex-shrink-0 text-[11px] font-sans">{{ $index + 1 }}</span>
                        <span class="w-6 font-bold select-none flex-shrink-0 text-center">{{ $row['symbol'] }}</span>
                        <div class="flex-1 whitespace-pre-wrap break-words leading-relaxed">
                            {{ $row['content'] }}
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs font-sans">
                        Tidak ada perbedaan teks yang terdeteksi antara kedua versi ini.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
