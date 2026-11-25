<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FEEDMART - POS & Inventaris Pakan Ternak</title>
    <script>
        if (localStorage.getItem('feedmart_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="glass-theme min-h-screen flex flex-col justify-between p-6 selection:bg-emerald-200/80 selection:text-emerald-950 relative overflow-x-hidden">
    <!-- Ambient Light Orbs -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 -left-20 w-96 h-96 rounded-full bg-emerald-300/30 blur-3xl"></div>
        <div class="absolute top-1/4 -right-24 w-[28rem] h-[28rem] rounded-full bg-sky-300/30 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 w-96 h-96 rounded-full bg-violet-300/25 blur-3xl"></div>
    </div>

    <!-- Header -->
    <header class="max-w-5xl w-full mx-auto glass-box px-5 py-3.5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 border border-white/80 flex items-center justify-center text-xl text-white shadow-md shadow-emerald-500/20">
                🌾
            </div>
            <span class="font-extrabold text-xl tracking-tight text-slate-900">FEEDMART</span>
        </div>
        <div class="flex items-center gap-2.5">
            <button type="button" x-data="themeSwitcher" @click="toggle()"
                :title="dark ? 'Ganti ke Light Mode' : 'Ganti ke Dark Mode'"
                class="glass-btn px-3 py-2 bg-white/70 hover:bg-white/95 text-slate-700 text-xs font-bold gap-1.5">
                <svg x-show="!dark" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg x-show="dark" x-cloak class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="hidden sm:inline" x-text="dark ? 'Light' : 'Dark'"></span>
            </button>
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('pos.index') }}" class="glass-btn px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 text-xs font-bold shadow-md shadow-emerald-500/20">
                    Masuk ke Sistem →
                </a>
            @else
                <a href="{{ route('login') }}" class="glass-btn px-4 py-2 bg-slate-900/90 hover:bg-slate-800 text-white border-white/30 text-xs font-bold shadow-md shadow-slate-900/15">
                    Login Petugas →
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Hero -->
    <main class="max-w-4xl w-full mx-auto my-auto py-12 text-center">
        <div class="glass-box-lg p-8 sm:p-12">
            <div class="inline-block px-3.5 py-1.5 bg-amber-400/20 border border-amber-400/35 rounded-full text-xs font-bold text-amber-900 uppercase tracking-wider mb-5 backdrop-blur-md">
                ⚡ Light Glassmorphism Feed Store Engine
            </div>
            <h1 class="text-4xl sm:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                Sistem Kasir &amp; Inventaris <br class="hidden sm:inline"> Pakan Ternak Modern
            </h1>
            <p class="text-sm sm:text-base font-medium text-slate-600 max-w-xl mx-auto mb-8 leading-relaxed">
                Manajemen stok karung &amp; eceran repacking, rotasi batch FIFO kedaluwarsa, dan transaksi kasir POS secepat kilat.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="glass-btn px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-sm font-bold uppercase tracking-wider shadow-lg shadow-emerald-500/25">
                    Mulai Transaksi Kasir
                </a>
                <a href="{{ route('login') }}" class="glass-btn px-8 py-3.5 bg-white/75 hover:bg-white/95 text-slate-800 text-sm font-bold uppercase tracking-wider">
                    Dashboard Admin
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-5xl w-full mx-auto glass-box-sm px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between text-xs font-semibold text-slate-600 gap-2">
        <span>© {{ date('Y') }} Feedmart POS. All rights reserved.</span>
        <span class="font-mono text-[11px] font-bold text-slate-500">Full Light Glassmorphism UI</span>
    </footer>
</body>
</html>
