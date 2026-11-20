<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Feedmart POS & Inventaris')</title>
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
<body class="glass-theme font-sans antialiased selection:bg-emerald-200/80 selection:text-emerald-950">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar Navigation (Full Light Glassmorphism) -->
        <aside class="w-full md:w-64 glass-sidebar text-slate-800 flex-shrink-0 flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center px-5 border-b border-white/70 bg-white/30">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 border border-white/80 flex items-center justify-center text-white font-black text-xl shadow-md shadow-emerald-500/20">
                            🌾
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 text-base tracking-tight block">FEEDMART</span>
                            <span class="text-[9px] text-emerald-800 font-bold tracking-widest uppercase bg-emerald-400/20 px-2 py-0.5 border border-emerald-400/30 rounded-full inline-block">POS & INVENTORY</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3.5 space-y-1 text-sm">
                    @if(auth()->user()->isAdmin())
                        <div class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Menu Utama
                        </div>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-500/90 to-teal-500/90 text-white border border-white/60 shadow-md shadow-emerald-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>

                        <div class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Inventaris Pakan
                        </div>
                        <a href="{{ route('admin.products.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-emerald-500/90 to-teal-500/90 text-white border border-white/60 shadow-md shadow-emerald-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Master Produk
                        </a>
                        <a href="{{ route('admin.suppliers.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.suppliers.*') ? 'bg-gradient-to-r from-emerald-500/90 to-teal-500/90 text-white border border-white/60 shadow-md shadow-emerald-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Master Supplier
                        </a>
                        <a href="{{ route('admin.repack.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.repack.*') ? 'bg-gradient-to-r from-violet-500/90 to-purple-500/90 text-white border border-white/60 shadow-md shadow-purple-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Konversi Repack
                            <span class="ml-auto text-[9px] px-1.5 py-0.5 rounded-full font-bold border {{ request()->routeIs('admin.repack.*') ? 'bg-white/25 text-white border-white/40' : 'bg-amber-400/20 text-amber-800 border-amber-400/30' }}">Bongkar</span>
                        </a>
                        <a href="{{ route('admin.stock-in.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.stock-in.*') ? 'bg-gradient-to-r from-sky-500/90 to-cyan-500/90 text-white border border-white/60 shadow-md shadow-sky-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Penerimaan Stok
                        </a>
                        <a href="{{ route('admin.returns.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.returns.*') ? 'bg-gradient-to-r from-rose-500/90 to-pink-500/90 text-white border border-white/60 shadow-md shadow-rose-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Retur Barang
                        </a>
                        <a href="{{ route('admin.stock-opname.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.stock-opname.*') ? 'bg-gradient-to-r from-amber-500/90 to-orange-500/90 text-white border border-white/60 shadow-md shadow-amber-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Audit & Opname
                        </a>
                        <a href="{{ route('admin.barcode.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.barcode.*') ? 'bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 border border-white/70 shadow-md shadow-amber-400/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Cetak Label Barcode
                        </a>

                        <div class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Finansial & Mitra
                        </div>
                        <a href="{{ route('admin.reports.sales') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-gradient-to-r from-emerald-500/90 to-teal-500/90 text-white border border-white/60 shadow-md shadow-emerald-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Laporan Laba / Rugi
                        </a>
                        <a href="{{ route('admin.expenses.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.expenses.*') ? 'bg-gradient-to-r from-rose-500/90 to-pink-500/90 text-white border border-white/60 shadow-md shadow-rose-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Pengeluaran Kas Toko
                        </a>
                        <a href="{{ route('admin.customers.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.customers.*') ? 'bg-gradient-to-r from-cyan-500/90 to-sky-500/90 text-white border border-white/60 shadow-md shadow-cyan-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Pelanggan & Kasbon
                        </a>

                        <div class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Pengaturan & Staf
                        </div>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-indigo-500/90 to-blue-500/90 text-white border border-white/60 shadow-md shadow-indigo-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Master Staf / Kasir
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-white/85 text-slate-900 border border-white shadow-md shadow-slate-900/5' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan & Backup
                        </a>
                    @endif

                    <div class="px-3 pt-4 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Area Transaksi
                    </div>
                    <a href="{{ route('pos.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('pos.*') ? 'bg-gradient-to-r from-emerald-500/90 to-teal-500/90 text-white border border-white/60 shadow-md shadow-emerald-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Kasir POS
                        <span class="ml-auto text-[9px] bg-emerald-500/15 text-emerald-700 px-2 py-0.5 rounded-full font-bold border border-emerald-500/25">Aktif</span>
                    </a>
                    <a href="{{ route('cash-register.close-form') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white/60 border border-transparent hover:border-white/70 transition-all">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tutup Kasir / Shift
                    </a>
                </nav>
            </div>

            <!-- User Info & Logout -->
            <div class="p-3.5 border-t border-white/70 bg-white/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white font-extrabold border border-white/80 flex items-center justify-center text-xs shadow-sm shadow-emerald-500/20 flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-block px-2 py-0.5 text-[9px] font-extrabold rounded-full border {{ auth()->user()->isAdmin() ? 'bg-sky-500/15 text-sky-800 border-sky-400/30' : 'bg-amber-400/20 text-amber-800 border-amber-400/35' }} uppercase">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar Akun" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-500/10 border border-transparent hover:border-rose-400/30 rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Header Top Bar -->
            <header class="glass-header h-16 flex items-center justify-between px-6 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">@yield('page_title', 'Feedmart')</h1>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/65 backdrop-blur-md border border-white/80 rounded-full text-xs font-semibold text-slate-600 shadow-sm">
                        <span>📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <button type="button" x-data="themeSwitcher" @click="toggle()"
                        :title="dark ? 'Ganti ke Light Mode' : 'Ganti ke Dark Mode'"
                        class="glass-btn px-3 py-1.5 bg-white/70 hover:bg-white/95 text-slate-700 text-xs font-bold gap-1.5">
                        <svg x-show="!dark" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="dark" x-cloak class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="hidden sm:inline" x-text="dark ? 'Light' : 'Dark'"></span>
                    </button>
                    <a href="{{ route('pos.index') }}" class="glass-btn px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/40 text-xs font-bold shadow-md shadow-emerald-500/20">
                        <span>⚡ Layar Kasir</span>
                    </a>
                </div>
            </header>

            <!-- Alerts Banner -->
            <div class="p-6 pb-0 space-y-3">
                @if(session('success'))
                    <div class="glass-box p-3.5 bg-emerald-500/12 border-emerald-400/35 text-emerald-950 text-xs sm:text-sm font-semibold flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm">✓</span>
                        <div class="flex-1 pt-0.5">{{ session('success') }}</div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="glass-box p-3.5 bg-rose-500/12 border-rose-400/35 text-rose-950 text-xs sm:text-sm font-semibold flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm">✕</span>
                        <div class="flex-1 pt-0.5">{{ session('error') }}</div>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="glass-box p-3.5 bg-amber-400/15 border-amber-400/40 text-amber-950 text-xs sm:text-sm font-semibold flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-amber-400 text-amber-950 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm">⚠</span>
                        <div class="flex-1 pt-0.5">{{ session('warning') }}</div>
                    </div>
                @endif
                @if(session('info'))
                    <div class="glass-box p-3.5 bg-sky-500/12 border-sky-400/35 text-sky-950 text-xs sm:text-sm font-semibold flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-sky-500 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm">ℹ</span>
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
