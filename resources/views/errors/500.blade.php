<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kendala Server | Feedmart</title>
    <script>
        if (localStorage.getItem('feedmart_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="glass-theme min-h-screen flex items-center justify-center p-4">
    <div class="glass-box-lg max-w-lg w-full p-8 text-center space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-violet-500/15 border border-violet-400/35 text-5xl shadow-sm">
            ⚙️
        </div>

        <div>
            <span class="glass-badge bg-rose-500/15 text-rose-800 border-rose-400/35 text-xs font-bold">ERROR 500</span>
            <h1 class="text-3xl font-extrabold tracking-tight mt-2 text-slate-900">TERJADI KENDALA SISTEM</h1>
            <p class="text-xs font-medium text-slate-600 mt-2 leading-relaxed">
                Server sedang mengalami kendala sementara atau koneksi basis data terputus. Silakan muat ulang halaman atau hubungi penanggung jawab IT.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-200/70 flex flex-col sm:flex-row justify-center gap-3">
            <button onclick="window.location.reload()" class="glass-btn bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 px-6 py-2.5 text-xs font-bold uppercase">
                🔄 MUAT ULANG
            </button>
            <a href="{{ url('/') }}" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/30 px-6 py-2.5 text-xs font-bold uppercase">
                🏠 KEMBALI
            </a>
        </div>
    </div>
</body>
</html>
