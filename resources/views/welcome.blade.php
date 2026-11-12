<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FEEDMART - POS & Inventaris Pakan Ternak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F8F5] text-black min-h-screen flex flex-col justify-between p-6 selection:bg-yellow-300 selection:text-black">
    <!-- Header -->
    <header class="max-w-5xl w-full mx-auto flex items-center justify-between py-4 border-b-2 border-black">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-400 border-2 border-black flex items-center justify-center text-xl font-black shadow-[2px_2px_0px_#000]">
                🌾
            </div>
            <span class="font-black text-xl tracking-tight text-black">FEEDMART</span>
        </div>
        <div>
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('pos.index') }}" class="neo-btn px-4 py-2 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black shadow-[3px_3px_0px_#000]">
                    Masuk ke Sistem →
                </a>
            @else
                <a href="{{ route('login') }}" class="neo-btn px-4 py-2 bg-black hover:bg-zinc-800 text-white text-xs font-black shadow-[3px_3px_0px_#000]">
                    Login Petugas →
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Hero -->
    <main class="max-w-4xl w-full mx-auto my-auto py-12 text-center">
        <div class="inline-block px-3 py-1 bg-yellow-300 border-2 border-black rounded-lg text-xs font-black uppercase tracking-wider mb-4 shadow-[2px_2px_0px_#000]">
            ⚡ Neo-Brutalist & Minimalist Feed Store Engine
        </div>
        <h1 class="text-4xl sm:text-6xl font-black text-black tracking-tight uppercase leading-none mb-6">
            Sistem Kasir & Inventaris <br class="hidden sm:inline"> Pakan Ternak Modern
        </h1>
        <p class="text-sm sm:text-base font-bold text-zinc-700 max-w-xl mx-auto mb-8">
            Manajemen stok karung & eceran repacking, rotasi batch FIFO kedaluwarsa, dan transaksi kasir POS secepat kilat.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('login') }}" class="neo-btn px-8 py-3.5 bg-emerald-400 hover:bg-emerald-300 text-black text-sm font-black uppercase tracking-wider shadow-[4px_4px_0px_#000]">
                Mulai Transaksi Kasir
            </a>
            <a href="{{ route('login') }}" class="neo-btn px-8 py-3.5 bg-white hover:bg-zinc-100 text-black text-sm font-black uppercase tracking-wider shadow-[4px_4px_0px_#000]">
                Dashboard Admin
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-5xl w-full mx-auto py-4 border-t-2 border-black flex flex-col sm:flex-row items-center justify-between text-xs font-bold text-zinc-600 gap-2">
        <span>© {{ date('Y') }} Feedmart POS. All rights reserved.</span>
        <span class="font-mono text-[11px] font-black">Neo-Brutalism + Minimalism UI</span>
    </footer>
</body>
</html>
