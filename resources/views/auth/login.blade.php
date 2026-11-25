<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Feedmart POS & Inventaris</title>
    <script>
        if (localStorage.getItem('feedmart_theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="glass-theme min-h-screen flex items-center justify-center p-4 selection:bg-emerald-200/80 selection:text-emerald-950 relative overflow-hidden">
    <!-- Theme Toggle -->
    <button type="button" x-data="themeSwitcher" @click="toggle()"
        :title="dark ? 'Ganti ke Light Mode' : 'Ganti ke Dark Mode'"
        class="fixed top-4 right-4 z-20 glass-btn px-3.5 py-2 bg-white/70 hover:bg-white/95 text-slate-700 text-xs font-bold gap-1.5">
        <svg x-show="!dark" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg x-show="dark" x-cloak class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span x-text="dark ? 'Light Mode' : 'Dark Mode'"></span>
    </button>
    <!-- Ambient Light Orbs -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-emerald-300/35 blur-3xl"></div>
        <div class="absolute top-1/3 -right-16 w-80 h-80 rounded-full bg-sky-300/35 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/4 w-80 h-80 rounded-full bg-amber-200/30 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md glass-box-lg p-6 sm:p-8 relative"
         x-data="{ email: '{{ old('email', 'admin@feedmart.test') }}', password: 'password' }">
        
        <!-- Header Branding -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-emerald-400 to-teal-500 border border-white/80 rounded-2xl flex items-center justify-center text-3xl text-white shadow-lg shadow-emerald-500/25 mb-3">
                🌾
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">FEEDMART</h1>
            <div class="inline-block mt-1.5 px-3 py-0.5 bg-amber-400/20 border border-amber-400/35 rounded-full text-[11px] font-bold text-amber-900 uppercase tracking-wider">
                POS Kasir &amp; Inventaris Pakan
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-500/12 border border-rose-400/35 text-rose-950 rounded-xl text-xs font-semibold flex items-center gap-2.5 backdrop-blur-sm">
                <span class="w-5 h-5 rounded-lg bg-rose-500 text-white font-bold flex items-center justify-center flex-shrink-0">✕</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-500/12 border border-emerald-400/35 text-emerald-950 rounded-xl text-xs font-semibold flex items-center gap-2.5 backdrop-blur-sm">
                <span class="w-5 h-5 rounded-lg bg-emerald-500 text-white font-bold flex items-center justify-center flex-shrink-0">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input type="email" name="email" x-model="email" required
                    placeholder="nama@feedmart.test"
                    class="glass-input px-3.5 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <input type="password" name="password" x-model="password" required
                    placeholder="••••••••"
                    class="glass-input px-3.5 py-2.5 text-sm">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 cursor-pointer font-semibold text-slate-700 select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 bg-white/70 text-emerald-600 focus:ring-emerald-500/30 cursor-pointer">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="glass-btn w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 font-bold text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/25">
                Masuk ke Aplikasi →
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-7 pt-5 border-t border-slate-200/70">
            <p class="text-[11px] font-bold text-slate-500 text-center uppercase tracking-widest mb-2.5">
                Pilih Akun Demo Cepat
            </p>
            <div class="grid grid-cols-2 gap-2.5">
                <button type="button" @click="email = 'admin@feedmart.test'; password = 'password'"
                    class="p-2.5 bg-white/55 hover:bg-emerald-500/15 border border-white/80 hover:border-emerald-400/40 rounded-xl text-left transition-all shadow-xs group backdrop-blur-sm cursor-pointer">
                    <span class="block text-xs font-bold text-slate-800 group-hover:text-emerald-900">👑 Admin</span>
                    <span class="block text-[10px] font-mono text-slate-500 truncate">admin@feedmart.test</span>
                </button>
                <button type="button" @click="email = 'kasir@feedmart.test'; password = 'password'"
                    class="p-2.5 bg-white/55 hover:bg-amber-400/20 border border-white/80 hover:border-amber-400/45 rounded-xl text-left transition-all shadow-xs group backdrop-blur-sm cursor-pointer">
                    <span class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">⚡ Kasir</span>
                    <span class="block text-[10px] font-mono text-slate-500 truncate">kasir@feedmart.test</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
