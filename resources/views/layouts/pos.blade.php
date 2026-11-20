<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Kasir - Feedmart')</title>
    <script>
        if (localStorage.getItem('feedmart_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="glass-theme font-sans antialiased h-screen overflow-hidden flex flex-col selection:bg-emerald-200/80 selection:text-emerald-950">
    <!-- Top Cashier Header (Full Light Glassmorphism) -->
    <header class="glass-header h-14 px-4 flex items-center justify-between flex-shrink-0 z-20">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white border border-white/80 flex items-center justify-center font-black text-base shadow-sm shadow-emerald-500/20">
                🌾
            </div>
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-sm tracking-tight text-slate-900">FEEDMART POS</span>
                <span class="glass-badge bg-amber-400/20 text-amber-800 border-amber-400/35">
                    Shift #{{ $activeRegister?->id ?? '-' }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-2 text-xs">
            <div class="hidden sm:flex items-center space-x-1.5 bg-white/60 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/80 shadow-xs">
                <span class="text-slate-500 font-semibold">Kasir:</span>
                <span class="font-bold text-emerald-700">{{ auth()->user()->name }}</span>
            </div>

            <button type="button" x-data="themeSwitcher" @click="toggle()"
                :title="dark ? 'Ganti ke Light Mode' : 'Ganti ke Dark Mode'"
                class="glass-btn px-2.5 py-1.5 bg-white/75 hover:bg-white/95 text-slate-700 text-xs font-bold gap-1">
                <svg x-show="!dark" class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-show="dark" x-cloak class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="hidden md:inline" x-text="dark ? 'Light' : 'Dark'"></span>
            </button>
            
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="glass-btn px-3 py-1.5 bg-white/75 hover:bg-white/95 text-slate-700 text-xs font-bold">
                    ← Admin Panel
                </a>
            @endif

            <a href="{{ route('cash-register.close-form') }}" class="glass-btn px-3 py-1.5 bg-amber-400/25 hover:bg-amber-400/40 text-amber-900 border-amber-400/40 text-xs font-bold">
                Tutup Shift
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="glass-btn px-3 py-1.5 bg-rose-500/15 hover:bg-rose-500/25 text-rose-700 border-rose-400/35 text-xs font-bold">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Main View Slot -->
    <div class="flex-1 overflow-hidden">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
