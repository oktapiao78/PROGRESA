@extends('layouts.guest', ['title' => 'Masuk ke Portal'])

@section('content')
<div class="w-full bg-slate-800/90 backdrop-blur-xl border border-slate-700/80 rounded-2xl shadow-2xl p-5 sm:p-8">
    <div class="mb-5 sm:mb-6 text-center sm:text-left">
        <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight">Selamat Datang Kembali</h2>
        <p class="text-xs text-slate-400 mt-1">Masuk menggunakan NIM, NIP, atau email terdaftar.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3.5 rounded-xl bg-rose-950/60 border border-rose-800/80 text-rose-300 text-xs">
            <div class="flex items-start space-x-2">
                <svg class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 p-3.5 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 text-xs">
            <div class="flex items-start space-x-2">
                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="login" class="block text-xs font-semibold text-slate-300 mb-1.5">NIM / NIP / Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <input id="login" name="login" type="text" required autofocus value="{{ old('login') }}"
                    placeholder="Contoh: 230101001 atau user@progresa.ac.id"
                    class="block w-full pl-10 pr-4 py-3 sm:py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-300">Kata Sandi</label>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input id="password" name="password" type="password" required
                    placeholder="••••••••"
                    class="block w-full pl-10 pr-4 py-3 sm:py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-slate-900">
                <span class="ml-2 text-xs text-slate-400">Ingat sesi saya</span>
            </label>
        </div>

        <button type="submit" class="w-full mt-2 py-3 sm:py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 active:scale-[0.99] text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-950/50 hover:shadow-emerald-900/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition cursor-pointer">
            Masuk ke Sistem
        </button>
    </form>

    <!-- Quick Demo Switcher -->
    <div class="mt-6 pt-5 border-t border-slate-700/60">
        <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">Akun Demo Cepat (1-Klik Isi)</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="fillCredentials('mahasiswa1@progresa.ac.id', 'password123')"
                class="p-2.5 rounded-lg bg-slate-900/70 border border-slate-700/70 hover:border-emerald-500/60 hover:bg-slate-700/50 text-left transition cursor-pointer">
                <span class="block font-semibold text-emerald-400 text-xs">Mahasiswa 1</span>
                <span class="block text-[10px] text-slate-400">NIM: 230101001</span>
            </button>
            <button type="button" onclick="fillCredentials('pembimbing1@progresa.ac.id', 'password123')"
                class="p-2.5 rounded-lg bg-slate-900/70 border border-slate-700/70 hover:border-indigo-500/60 hover:bg-slate-700/50 text-left transition cursor-pointer">
                <span class="block font-semibold text-indigo-400 text-xs">Pembimbing 1</span>
                <span class="block text-[10px] text-slate-400">NIP: 19800315...</span>
            </button>
            <button type="button" onclick="fillCredentials('pembimbing2@progresa.ac.id', 'password123')"
                class="p-2.5 rounded-lg bg-slate-900/70 border border-slate-700/70 hover:border-indigo-500/60 hover:bg-slate-700/50 text-left transition cursor-pointer">
                <span class="block font-semibold text-indigo-400 text-xs">Pembimbing 2</span>
                <span class="block text-[10px] text-slate-400">NIP: 19850720...</span>
            </button>
            <button type="button" onclick="fillCredentials('koordinator@progresa.ac.id', 'password123')"
                class="p-2.5 rounded-lg bg-slate-900/70 border border-slate-700/70 hover:border-amber-500/60 hover:bg-slate-700/50 text-left transition cursor-pointer">
                <span class="block font-semibold text-amber-400 text-xs">Koordinator TA</span>
                <span class="block text-[10px] text-slate-400">Monitoring & Approval</span>
            </button>
            <button type="button" onclick="fillCredentials('kaprodi@progresa.ac.id', 'password123')"
                class="p-2.5 rounded-lg bg-slate-900/70 border border-slate-700/70 hover:border-purple-500/60 hover:bg-slate-700/50 text-left transition cursor-pointer">
                <span class="block font-semibold text-purple-400 text-xs">Kaprodi</span>
                <span class="block text-[10px] text-slate-400">Eksekutif & Risiko</span>
            </button>
            <button type="button" onclick="fillCredentials('admin@progresa.ac.id', 'password123')"
                class="p-2.5 rounded-lg bg-slate-900/70 border border-slate-700/70 hover:border-rose-500/60 hover:bg-slate-700/50 text-left transition cursor-pointer">
                <span class="block font-semibold text-rose-400 text-xs">Administrator</span>
                <span class="block text-[10px] text-slate-400">Master Data & Audit</span>
            </button>
        </div>
    </div>
</div>

<script>
    function fillCredentials(login, password) {
        document.getElementById('login').value = login;
        document.getElementById('password').value = password;
    }
</script>
@endsection
