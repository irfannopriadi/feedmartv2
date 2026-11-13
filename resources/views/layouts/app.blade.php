<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Feedmart POS & Inventaris')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8F8F5] text-black font-sans antialiased selection:bg-yellow-300 selection:text-black">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar Navigation (Neo-Brutalist Minimalism) -->
        <aside class="w-full md:w-64 bg-[#0F0F0F] text-zinc-100 flex-shrink-0 flex flex-col justify-between border-r-2 border-black">
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center px-5 border-b-2 border-black bg-black">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-400 border-2 border-black flex items-center justify-center text-black font-black text-xl shadow-[2px_2px_0px_#fff]">
                            🌾
                        </div>
                        <div>
                            <span class="font-black text-white text-base tracking-wider block">FEEDMART</span>
                            <span class="text-[9px] text-emerald-400 font-black tracking-widest uppercase bg-emerald-950/80 px-1.5 py-0.2 border border-emerald-500/40 rounded inline-block">POS & INVENTORY</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3.5 space-y-1 text-sm">
                    @if(auth()->user()->isAdmin())
                        <div class="px-3 pt-3 pb-1 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                            Menu Utama
                        </div>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-400 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>

                        <div class="px-3 pt-4 pb-1 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                            Inventaris Pakan
                        </div>
                        <a href="{{ route('admin.products.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.products.*') ? 'bg-emerald-400 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Master Produk
                        </a>
                        <a href="{{ route('admin.suppliers.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.suppliers.*') ? 'bg-emerald-400 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Master Supplier
                        </a>
                        <a href="{{ route('admin.repack.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.repack.*') ? 'bg-purple-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Konversi Repack
                            <span class="ml-auto text-[9px] bg-yellow-300 text-black px-1.5 py-0.5 rounded font-black border border-black shadow-[1px_1px_0px_#000]">Bongkar</span>
                        </a>
                        <a href="{{ route('admin.stock-in.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.stock-in.*') ? 'bg-sky-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Penerimaan Stok
                        </a>
                        <a href="{{ route('admin.returns.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.returns.*') ? 'bg-rose-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Retur Barang
                        </a>
                        <a href="{{ route('admin.stock-opname.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.stock-opname.*') ? 'bg-amber-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Audit & Opname
                        </a>
                        <a href="{{ route('admin.barcode.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.barcode.*') ? 'bg-yellow-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Cetak Label Barcode
                        </a>

                        <div class="px-3 pt-4 pb-1 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                            Finansial & Mitra
                        </div>
                        <a href="{{ route('admin.reports.sales') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-400 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Laporan Laba / Rugi
                        </a>
                        <a href="{{ route('admin.expenses.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.expenses.*') ? 'bg-rose-400 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Pengeluaran Kas Toko
                        </a>
                        <a href="{{ route('admin.customers.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.customers.*') ? 'bg-cyan-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Pelanggan & Kasbon
                        </a>

                        <div class="px-3 pt-4 pb-1 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                            Pengaturan & Staf
                        </div>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.users.*') ? 'bg-yellow-300 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Master Staf / Kasir
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-white text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan & Backup
                        </a>
                    @endif

                    <div class="px-3 pt-4 pb-1 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                        Area Transaksi
                    </div>
                    <a href="{{ route('pos.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black transition-all {{ request()->routeIs('pos.*') ? 'bg-emerald-400 text-black border-2 border-black shadow-[3px_3px_0px_#fff] translate-x-[-1px] translate-y-[-1px]' : 'text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Kasir POS
                        <span class="ml-auto text-[9px] bg-emerald-400 text-black px-1.5 py-0.5 rounded font-black border border-black shadow-[1px_1px_0px_#000]">Aktif</span>
                    </a>
                    <a href="{{ route('cash-register.close-form') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-black text-zinc-300 hover:text-white hover:bg-zinc-800 border-2 border-transparent hover:border-black transition-all">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tutup Kasir / Shift
                    </a>
                </nav>
            </div>

            <!-- User Info & Logout -->
            <div class="p-3.5 border-t-2 border-black bg-black">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-md bg-emerald-400 text-black font-black border-2 border-black flex items-center justify-center text-xs shadow-[1.5px_1.5px_0px_#fff]">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-black text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-block px-1.5 py-0.2 text-[9px] font-black rounded border border-black {{ auth()->user()->isAdmin() ? 'bg-sky-300 text-black' : 'bg-yellow-300 text-black' }} uppercase">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar Akun" class="p-1.5 text-zinc-400 hover:text-rose-400 hover:bg-zinc-800 border-2 border-transparent hover:border-black rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Header Top Bar -->
            <header class="bg-white border-b-2 border-black h-16 flex items-center justify-between px-6 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg sm:text-xl font-black text-black tracking-tight">@yield('page_title', 'Feedmart')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-200 border-2 border-black rounded-md text-xs font-bold text-black shadow-[2px_2px_0px_#000]">
                        <span>📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <a href="{{ route('pos.index') }}" class="neo-btn px-3.5 py-1.5 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black">
                        <span>⚡ Layar Kasir</span>
                    </a>
                </div>
            </header>

            <!-- Alerts Banner -->
            <div class="p-6 pb-0 space-y-3">
                @if(session('success'))
                    <div class="p-3.5 bg-emerald-100 border-2 border-black text-black rounded-xl text-xs sm:text-sm font-bold flex items-start gap-3 shadow-[3px_3px_0px_#000]">
                        <span class="w-6 h-6 rounded-md bg-emerald-400 border-2 border-black flex items-center justify-center font-black text-xs flex-shrink-0">✓</span>
                        <div class="flex-1 pt-0.5">{{ session('success') }}</div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-3.5 bg-rose-100 border-2 border-black text-black rounded-xl text-xs sm:text-sm font-bold flex items-start gap-3 shadow-[3px_3px_0px_#000]">
                        <span class="w-6 h-6 rounded-md bg-rose-400 border-2 border-black flex items-center justify-center font-black text-xs flex-shrink-0 text-white">✕</span>
                        <div class="flex-1 pt-0.5">{{ session('error') }}</div>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="p-3.5 bg-amber-100 border-2 border-black text-black rounded-xl text-xs sm:text-sm font-bold flex items-start gap-3 shadow-[3px_3px_0px_#000]">
                        <span class="w-6 h-6 rounded-md bg-amber-400 border-2 border-black flex items-center justify-center font-black text-xs flex-shrink-0">⚠</span>
                        <div class="flex-1 pt-0.5">{{ session('warning') }}</div>
                    </div>
                @endif
                @if(session('info'))
                    <div class="p-3.5 bg-sky-100 border-2 border-black text-black rounded-xl text-xs sm:text-sm font-bold flex items-start gap-3 shadow-[3px_3px_0px_#000]">
                        <span class="w-6 h-6 rounded-md bg-sky-400 border-2 border-black flex items-center justify-center font-black text-xs flex-shrink-0">ℹ</span>
                        <div class="flex-1 pt-0.5">{{ session('info') }}</div>
                    </div>
                @endif
            </div>

            <!-- Main Page Slot -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
