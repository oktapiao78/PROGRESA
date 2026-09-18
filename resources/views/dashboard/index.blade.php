@extends('layouts.app', [
    'title' => 'Dashboard',
    'headerTitle' => 'Dashboard',
    'headerSubtitle' => 'Ringkasan Sistem Progresa'
])

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center font-bold text-xl shadow-md">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="text-xs sm:text-sm text-slate-500">Anda login sebagai <span class="font-semibold text-emerald-600">{{ auth()->user()->role->label() }}</span>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
