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
        /* Custom scrollbar for fast cashier scanning */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased h-screen overflow-hidden flex flex-col">
    <!-- Top Cashier Header -->
    <header class="bg-slate-900 text-white h-14 px-4 flex items-center justify-between border-b border-slate-800 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-white shadow">
                🌾
            </div>
            <div>
                <span class="font-bold text-sm tracking-wide">FEEDMART POS</span>
                <span class="text-[10px] text-emerald-400 font-medium ml-1.5 px-1.5 py-0.5 rounded bg-emerald-950 border border-emerald-800">
                    Shift Aktif #{{ $activeRegister?->id ?? '-' }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <div class="hidden sm:flex items-center space-x-2 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                <span class="text-slate-400">Kasir:</span>
                <span class="font-bold text-emerald-300">{{ auth()->user()->name }}</span>
            </div>
            
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg transition font-medium border border-slate-700">
                    ← Admin Panel
                </a>
            @endif

            <a href="{{ route('cash-register.close-form') }}" class="px-2.5 py-1.5 bg-amber-600/90 hover:bg-amber-600 text-white rounded-lg transition font-medium shadow-xs">
                Tutup Shift
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-2.5 py-1.5 bg-rose-700/80 hover:bg-rose-700 text-white rounded-lg transition font-medium">
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
