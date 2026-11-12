<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Kasir - Feedmart')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        /* Neo-brutalist scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1eb;
            border-left: 2px solid #000;
        }
        ::-webkit-scrollbar-thumb {
            background: #000;
            border-radius: 0;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #333;
        }
    </style>
</head>
<body class="bg-[#F8F8F5] text-black font-sans antialiased h-screen overflow-hidden flex flex-col selection:bg-yellow-300 selection:text-black">
    <!-- Top Cashier Header (Neo-Brutalist Minimalism) -->
    <header class="bg-black text-white h-14 px-4 flex items-center justify-between border-b-2 border-black flex-shrink-0 z-20">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-400 text-black border-2 border-white flex items-center justify-center font-black text-base shadow-[2px_2px_0px_#fff]">
                🌾
            </div>
            <div class="flex items-center gap-2">
                <span class="font-black text-sm tracking-wider text-white">FEEDMART POS</span>
                <span class="text-[10px] text-black font-black px-2 py-0.5 rounded bg-yellow-300 border border-black shadow-[1.5px_1.5px_0px_#fff]">
                    Shift #{{ $activeRegister?->id ?? '-' }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-2.5 text-xs">
            <div class="hidden sm:flex items-center space-x-1.5 bg-zinc-900 px-3 py-1 rounded-md border border-zinc-700">
                <span class="text-zinc-400 font-bold">Kasir:</span>
                <span class="font-black text-emerald-300">{{ auth()->user()->name }}</span>
            </div>
            
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="neo-btn px-2.5 py-1 bg-white hover:bg-zinc-200 text-black text-xs font-black">
                    ← Admin Panel
                </a>
            @endif

            <a href="{{ route('cash-register.close-form') }}" class="neo-btn px-2.5 py-1 bg-amber-400 hover:bg-amber-300 text-black text-xs font-black">
                Tutup Shift
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="neo-btn px-2.5 py-1 bg-rose-400 hover:bg-rose-300 text-black text-xs font-black">
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
