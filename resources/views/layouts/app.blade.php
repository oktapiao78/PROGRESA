<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'Progresa') }}</title>
    
    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50">

    <!-- Mobile Top Navigation Bar (Shown only on Mobile: ☰  Progresa  🔔) -->
    <header class="md:hidden sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-slate-900 border-b border-slate-800 text-white shadow-md">
        <!-- Hamburger Button -->
        <button type="button" id="mobile-menu-open-btn" aria-label="Buka Menu" class="p-2 -ml-1 text-slate-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Brand Center -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-950/40">
                P
            </div>
            <span class="text-base font-bold text-white tracking-tight">PROGRESA</span>
        </a>

        <!-- Right Side: Notification & User Avatar -->
        <div class="flex items-center space-x-2">
            <!-- Notification Bell -->
            <div class="relative">
                <button type="button" aria-label="Notifikasi" class="p-2 text-slate-300 hover:text-white rounded-lg focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-slate-900"></span>
            </div>

            <!-- Mini Avatar -->
            <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center font-bold text-xs">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </header>

    <!-- Mobile Off-Canvas Drawer Backdrop -->
    <div id="mobile-drawer-backdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 hidden opacity-0 transition-opacity duration-300 md:hidden"></div>

    <!-- Mobile Off-Canvas Drawer Navigation -->
    <aside id="mobile-drawer" class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-slate-900 text-slate-300 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col shadow-2xl md:hidden">
        <!-- Drawer Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 bg-slate-950/50">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-black text-sm shadow-md">
                    P
                </div>
                <div>
                    <span class="text-base font-bold text-white tracking-tight">PROGRESA</span>
                    <span class="block text-[9px] uppercase tracking-wider text-emerald-400 font-semibold">Sistem Bimbingan TA</span>
                </div>
            </div>
            <button type="button" id="mobile-menu-close-btn" aria-label="Tutup Menu" class="p-2 text-slate-400 hover:text-white rounded-lg focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Drawer User Profile -->
        <div class="p-4 border-b border-slate-800/80 bg-slate-950/30 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[10px] font-semibold {{ auth()->user()->role->badgeColor() }}">
                    {{ auth()->user()->role->label() }}
                </span>
            </div>
        </div>

        <!-- Drawer Nav Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
            @include('layouts.partials.nav-links')
        </nav>

        <!-- Drawer Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2.5 rounded-xl bg-slate-800 hover:bg-rose-950/60 text-slate-300 hover:text-rose-300 border border-slate-700/80 transition text-xs font-semibold cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Desktop Sidebar Navigation (Hidden on mobile, fixed on desktop md+) -->
    <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-slate-900 text-slate-300 border-r border-slate-800 z-30">
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800/80 bg-slate-950/40">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-emerald-900/30 group-hover:scale-105 transition">
                    P
                </div>
                <div>
                    <span class="text-lg font-bold text-white tracking-tight">PROGRESA</span>
                    <span class="block text-[10px] uppercase tracking-wider text-emerald-400 font-semibold">Sistem Bimbingan TA</span>
                </div>
            </a>
        </div>

        <!-- User Mini Profile -->
        <div class="p-4 border-b border-slate-800/60 bg-slate-950/20 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <div class="flex items-center space-x-1.5 mt-0.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ auth()->user()->role->badgeColor() }}">
                        {{ auth()->user()->role->label() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
            @include('layouts.partials.nav-links')
        </nav>

        <!-- Logout Section -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-lg bg-slate-800/80 hover:bg-rose-950/60 text-slate-300 hover:text-rose-300 border border-slate-700/60 hover:border-rose-900 transition text-xs font-semibold cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area (Offset by md:pl-64 on desktop) -->
    <div class="md:pl-64 flex flex-col flex-1 min-h-screen bg-slate-50">
        
        <!-- Desktop Top Header Bar (hidden on mobile, visible on desktop) -->
        <header class="hidden md:flex h-16 bg-white border-b border-slate-200 px-6 items-center justify-between sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $headerTitle ?? 'Progresa' }}</h1>
                @if(isset($headerSubtitle))
                    <span class="text-xs text-slate-400">|</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $headerSubtitle }}</span>
                @endif
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <span class="text-xs font-semibold text-slate-800 block">{{ auth()->user()->name }}</span>
                    <span class="text-[11px] text-slate-500 block">{{ auth()->user()->nim_nip ?? auth()->user()->email }}</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs" role="alert">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-xs" role="alert">
                    <div class="flex items-center mb-1">
                        <svg class="w-5 h-5 mr-2 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span class="text-sm font-semibold">Terdapat kendala pada permintaan Anda:</span>
                    </div>
                    <ul class="list-disc pl-8 text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="flex-1 px-4 sm:px-6 py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-4 px-4 sm:px-6 border-t border-slate-200 bg-white text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Progresa Academic Portal. Sistem Manajemen Bimbingan Skripsi Berbasis Progres dan Approval Bertingkat.
        </footer>
    </div>

    <!-- Script for mobile navigation drawer toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('mobile-menu-open-btn');
            const closeBtn = document.getElementById('mobile-menu-close-btn');
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('mobile-drawer-backdrop');

            function openDrawer() {
                if (backdrop && drawer) {
                    backdrop.classList.remove('hidden');
                    // Force repaint
                    void backdrop.offsetWidth;
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    drawer.classList.remove('-translate-x-full');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeDrawer() {
                if (backdrop && drawer) {
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0');
                    drawer.classList.add('-translate-x-full');
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        backdrop.classList.add('hidden');
                    }, 300);
                }
            }

            if (openBtn) openBtn.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (backdrop) backdrop.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeDrawer();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
