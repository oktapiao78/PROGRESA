<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Tugas Akhir')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body style="margin:0; padding:0; font-family: 'Inter', sans-serif; background-color: #f4f5f7;">

    @php
        if (!auth()->check()) {
            $dummyUser = new \App\Models\User();
            $dummyUser->role = \App\Enums\UserRole::MAHASISWA;
            auth()->login($dummyUser);
        }
        $currentUser = auth()->user();
    @endphp

    <header style="background-color: #e2d9f7; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <h1 style="color: #4a4a5a; font-size: 1.5rem; font-weight: 800; margin: 0;">Portal Tugas Akhir</h1>
            <span style="color: #9a9aae; font-size: 1.2rem;">|</span>
            <span style="color: #6b6b7b; font-weight: 600;">Proses & Bimbingan Skripsi</span>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="position: relative; cursor: pointer; color: #c29e43;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background-color: #9d62cc; border-radius: 50%; border: 2px solid #e2d9f7;"></span>
            </div>
            <a href="{{ route('profile') }}" style="width: 40px; height: 40px; background-color: #f3f0fb; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: #9d62cc; border: 2px solid #9d62cc; text-decoration: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </a>
        </div>
    </header>

    <main style="max-width: 1200px; margin: 30px auto; padding: 0 20px; padding-bottom: 100px; display: flex; flex-direction: column; gap: 25px;">
        @yield('content')
    </main>

    <div style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 100;">
        
        <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background-color: #d8d8e0; padding: 6px 30px; border-radius: 30px; display: flex; gap: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <a href="{{ $currentUser->role === \App\Enums\UserRole::DOSEN ? route('dashboard.dosen') : route('dashboard.mahasiswa') }}" style="cursor: pointer; color: #8a8a9d; display: flex; justify-content: center; align-items: center; width: 36px; height: 36px; border-radius: 50%; text-decoration: none; {{ request()->routeIs('dashboard*') ? 'background-color: #ffffff; color: #7b5ea7; box-shadow: 0 2px 5px rgba(0,0,0,0.1);' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </a>
            <a href="#" style="cursor: pointer; color: #8a8a9d; display: flex; justify-content: center; align-items: center; width: 36px; height: 36px; border-radius: 50%; text-decoration: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
            </a>
            <a href="#" style="cursor: pointer; color: #8a8a9d; display: flex; justify-content: center; align-items: center; width: 36px; height: 36px; border-radius: 50%; text-decoration: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </a>
        </div>

        <div style="background-color: #f7d6f3; width: 100%; display: flex; justify-content: center; box-shadow: 0 -2px 10px rgba(0,0,0,0.05);">
            <div style="max-width: 1200px; width: 100%; display: flex; justify-content: flex-end; align-items: center; padding: 15px 20px; box-sizing: border-box;">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #4a4a5a; font-weight: 700; display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.8rem; white-space: nowrap;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        LOG OUT
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>