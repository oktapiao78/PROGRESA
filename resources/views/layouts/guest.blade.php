<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>{{ $title ?? 'Masuk' }} — {{ config('app.name', 'Progresa') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen w-full font-sans antialiased text-slate-100 bg-slate-900 selection:bg-emerald-500 selection:text-white relative overflow-x-hidden">

    <!-- Decorative background glow -->
    <div class="fixed -top-32 -left-32 w-80 h-80 sm:w-96 sm:h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-32 -right-32 w-80 h-80 sm:w-96 sm:h-96 bg-teal-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main guest layout container -->
    <div class="min-h-screen w-full flex flex-col justify-center items-center py-8 px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Brand Header (Compact & Mobile-optimized) -->
        <div class="w-full max-w-md mx-auto text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white font-black text-2xl sm:text-3xl shadow-xl shadow-emerald-950/80 mb-3">
                P
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">PROGRESA</h1>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-400 px-2 leading-relaxed">
                Sistem Manajemen Bimbingan Skripsi & Tugas Akhir Berbasis Progres dan Approval Bertingkat
            </p>
        </div>

        <!-- Authentication Content Slot -->
        <div class="w-full max-w-md mx-auto">
            @yield('content')
        </div>

        <!-- Guest Footer -->
        <div class="w-full max-w-md mx-auto mt-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Progresa Academic Portal. Hak Cipta Dilindungi.
        </div>
    </div>

</body>
</html>
