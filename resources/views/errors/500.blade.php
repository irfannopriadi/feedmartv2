<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kendala Server | Feedmart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F0] min-h-screen flex items-center justify-center p-4">
    <div class="neo-box max-w-lg w-full bg-white p-8 text-center space-y-6 shadow-[8px_8px_0px_#000]">
        <div class="inline-block p-4 neo-box bg-purple-300 text-5xl">
            ⚙️
        </div>

        <div>
            <span class="neo-badge bg-rose-400 text-black text-xs font-black">ERROR 500</span>
            <h1 class="text-3xl font-black uppercase tracking-tight mt-2 text-black">TERJADI KENDALA SISTEM</h1>
            <p class="text-xs font-bold text-gray-600 mt-2">
                Server sedang mengalami kendala sementara atau koneksi basis data terputus. Silakan muat ulang halaman atau hubungi penanggung jawab IT.
            </p>
        </div>

        <div class="pt-4 border-t-2 border-black flex flex-col sm:flex-row justify-center gap-3">
            <button onclick="window.location.reload()" class="neo-btn bg-yellow-300 text-black px-6 py-2.5 text-xs font-black uppercase">
                🔄 MUAT ULANG
            </button>
            <a href="{{ url('/') }}" class="neo-btn bg-black text-white px-6 py-2.5 text-xs font-black uppercase">
                🏠 KEMBALI
            </a>
        </div>
    </div>
</body>
</html>
