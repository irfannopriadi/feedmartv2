<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | Feedmart</title>
    <script>
        if (localStorage.getItem('feedmart_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="glass-theme min-h-screen flex items-center justify-center p-4">
    <div class="glass-box-lg max-w-lg w-full p-8 text-center space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-rose-500/15 border border-rose-400/35 text-5xl shadow-sm">
            🚫
        </div>

        <div>
            <span class="glass-badge bg-rose-500/15 text-rose-800 border-rose-400/35 text-xs font-bold">ERROR 403</span>
            <h1 class="text-3xl font-extrabold tracking-tight mt-2 text-slate-900">AKSES DITOLAK</h1>
            <p class="text-xs font-medium text-slate-600 mt-2 leading-relaxed">
                Akun Anda tidak memiliki hak akses (permission) untuk membuka halaman administrator ini.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-200/70 flex flex-col sm:flex-row justify-center gap-3">
            <a href="{{ url('/') }}" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/30 px-6 py-2.5 text-xs font-bold uppercase tracking-wider">
                🏠 KEMBALI KE BERANDA
            </a>
            @auth
                <a href="{{ route('pos.index') }}" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 px-6 py-2.5 text-xs font-bold uppercase shadow-md shadow-emerald-500/20">
                    🛒 MENUJU KASIR POS
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
