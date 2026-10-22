<a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard*') ? 'bg-emerald-500/10 text-emerald-400 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-400 font-medium' }} transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <span>Dashboard</span>
</a>

@if(auth()->user()->role === \App\Enums\UserRole::MAHASISWA)
<a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-800/80 hover:text-white text-slate-400 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    <span>Skripsi Saya</span>
</a>
@endif

@if(auth()->user()->role === \App\Enums\UserRole::DOSEN)
<a href="#" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl hover:bg-slate-800/80 hover:text-white text-slate-400 font-medium transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    <span>Mahasiswa Bimbingan</span>
</a>
@endif

<a href="{{ route('profile') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('profile*') ? 'bg-emerald-500/10 text-emerald-400 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-400 font-medium' }} transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    <span>Profil Saya</span>
</a>