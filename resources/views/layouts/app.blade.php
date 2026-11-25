<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? null ? $title : 'Portal Tugas Akhir' }} - PROGRESA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body style="margin:0; padding:0; font-family: 'Inter', sans-serif; background-color: #f4f5f7;">

    @php
        $currentUser = auth()->user();
        $unreadNotificationsCount = $currentUser
            ? \App\Models\Notification::where('user_id', $currentUser->id)->where('is_read', false)->count()
            : 0;
    @endphp

    <header style="background-color: #DCD7FE; padding: 14px 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 14px;">
            @if(isset($hasBack) ? $hasBack : (request()->routeIs('notifications.*', 'chat.*', 'profile.*', 'chapters.show') || isset($backUrl)))
                <a href="{{ $backUrl ?? url()->previous() }}" title="Kembali" style="color: #4a4a5a; display: flex; align-items: center; text-decoration: none; padding: 4px; border-radius: 8px; transition: opacity 0.2s;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </a>
            @endif
            <h1 style="color: #4a4a5a; font-size: 1.4rem; font-weight: 800; margin: 0; letter-spacing: -0.01em;">{{ $headerTitle ?? 'Portal Tugas Akhir' }}</h1>
            @if(!empty($headerSubtitle))
                <span style="color: #9a9aae; font-size: 1.2rem;">|</span>
                <span style="color: #6b6b7b; font-weight: 600; font-size: 0.95rem;">{{ $headerSubtitle }}</span>
            @endif
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="{{ route('notifications.index') }}" title="Pemberitahuan" style="position: relative; cursor: pointer; color: #e5a83b; text-decoration: none; display: flex; align-items: center;">
                <svg viewBox="0 0 24 24" fill="#e5a83b" stroke="#e5a83b" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="width: 26px; height: 26px;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                @if($unreadNotificationsCount > 0)
                    <span style="position: absolute; top: -5px; right: -6px; min-width: 18px; height: 18px; padding: 0 4px; background-color: #9d62cc; color: #fff; font-size: 10px; font-weight: 800; border-radius: 9999px; border: 2px solid #DCD7FE; display: flex; align-items: center; justify-content: center;">
                        {{ $unreadNotificationsCount }}
                    </span>
                @endif
            </a>
            
            {{-- TAUTAN AVATAR --}}
            <a href="{{ $currentUser && $currentUser->isDosen() ? route('profile.dosen') : route('profile.mahasiswa') }}" title="Profil" style="width: 38px; height: 38px; background-color: #f3f0fb; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: #9d62cc; border: 2px solid #9d62cc; text-decoration: none; overflow: hidden;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </a>
        </div>
    </header>

    <div style="display: flex; max-width: 1100px; margin: 25px auto; gap: 20px; width: 100%;">
        @if(auth()->check() && !auth()->user()->isMahasiswa() && !auth()->user()->isDosen())
            @include('layouts.partials.sidebar')
        @endif
        <main style="flex:1; padding: 0 20px; padding-bottom: 120px; display: flex; flex-direction: column; gap: 20px; min-width: 0; width: 100%;">
            @if(session('success'))
                <div style="background-color: #e3f9e9; border: 1px solid #a3e4b7; color: #1e7e34; padding: 12px 18px; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: #faeaea; border: 1px solid #f5c6c6; color: #b02525; padding: 12px 18px; border-radius: 12px; font-size: 0.875rem;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 100; pointer-events: none;">
        
        <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background-color: #d8d8e0; padding: 6px 26px; border-radius: 9999px; display: flex; gap: 24px; box-shadow: 0 4px 14px rgba(0,0,0,0.12); pointer-events: auto; align-items: center;">
            <a href="{{ route('theses.index') }}" title="Skripsi Saya" style="cursor: pointer; color: #8a8a9d; display: flex; justify-content: center; align-items: center; width: 38px; height: 38px; border-radius: 50%; text-decoration: none; transition: all 0.2s; {{ request()->routeIs('theses.*', 'chapters.*') ? 'background-color: #ffffff; color: #7b5ea7; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transform: scale(1.05);' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
            </a>
            <a href="{{ route('dashboard') }}" title="Dashboard" style="cursor: pointer; color: #8a8a9d; display: flex; justify-content: center; align-items: center; width: 38px; height: 38px; border-radius: 50%; text-decoration: none; transition: all 0.2s; {{ request()->routeIs('dashboard*') ? 'background-color: #ffffff; color: #7b5ea7; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transform: scale(1.05);' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </a>
            <a href="{{ route('supervision.index') }}" title="Jadwal & Pengajuan Bimbingan" style="cursor: pointer; color: #8a8a9d; display: flex; justify-content: center; align-items: center; width: 38px; height: 38px; border-radius: 50%; text-decoration: none; transition: all 0.2s; {{ request()->routeIs('supervision.*') ? 'background-color: #ffffff; color: #7b5ea7; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transform: scale(1.05);' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </a>
        </div>

        <div style="background-color: #F1C8E7; width: 100%; display: flex; justify-content: center; box-shadow: 0 -2px 10px rgba(0,0,0,0.04); pointer-events: auto;">
            <div style="max-width: 1100px; width: 100%; display: flex; justify-content: flex-end; align-items: center; padding: 12px 20px; box-sizing: border-box;">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #4a4a5a; font-weight: 700; display: flex; align-items: center; gap: 7px; cursor: pointer; font-size: 0.8rem; letter-spacing: 0.04em; white-space: nowrap;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        LOG OUT
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
