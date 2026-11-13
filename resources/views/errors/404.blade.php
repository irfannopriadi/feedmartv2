<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Feedmart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F0] min-h-screen flex items-center justify-center p-4">
    <div class="neo-box max-w-lg w-full bg-white p-8 text-center space-y-6 shadow-[8px_8px_0px_#000]">
        <div class="inline-block p-4 neo-box bg-yellow-300 text-5xl">
            🔍
        </div>

        <div>
            <span class="neo-badge bg-rose-400 text-black text-xs font-black">ERROR 404</span>
            <h1 class="text-3xl font-black uppercase tracking-tight mt-2 text-black">HALAMAN TIDAK DITEMUKAN</h1>
            <p class="text-xs font-bold text-gray-600 mt-2">
                Halaman atau data pakan ternak yang Anda cari tidak tersedia, telah dihapus, atau tautan yang dimasukkan keliru.
            </p>
        </div>

        <div class="pt-4 border-t-2 border-black flex flex-col sm:flex-row justify-center gap-3">
            <a href="{{ url('/') }}" class="neo-btn bg-black text-white px-6 py-2.5 text-xs font-black uppercase tracking-wider">
                🏠 KEMBALI KE BERANDA
            </a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="neo-btn bg-emerald-400 text-black px-6 py-2.5 text-xs font-black uppercase">
                        📊 KE DASHBOARD
                    </a>
                @else
                    <a href="{{ route('pos.index') }}" class="neo-btn bg-emerald-400 text-black px-6 py-2.5 text-xs font-black uppercase">
                        🛒 KE LAYAR KASIR
                    </a>
                @endif
            @endauth
        </div>
    </div>
</body>
</html>
