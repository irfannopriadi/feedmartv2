<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Feedmart POS & Inventaris</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-950 border border-slate-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden" x-data="{ email: '{{ old('email', 'admin@feedmart.test') }}', password: 'password' }">
        <!-- Background Accent -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center mb-8 relative">
            <div class="w-14 h-14 mx-auto bg-gradient-to-tr from-emerald-600 to-teal-500 rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/20 mb-3">
                🌾
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">FEEDMART</h1>
            <p class="text-xs text-slate-400 mt-1">Sistem POS Kasir & Manajemen Inventaris Pakan Ternak</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3.5 bg-rose-950/70 border border-rose-800 text-rose-300 rounded-xl text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 p-3.5 bg-emerald-950/70 border border-emerald-800 text-emerald-300 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
                <input type="email" name="email" x-model="email" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi</label>
                <input type="password" name="password" x-model="password" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 cursor-pointer text-slate-400">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-800 text-emerald-600 focus:ring-0">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm shadow-md shadow-emerald-600/30 transition transform active:scale-[0.98]">
                Login
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-8 pt-6 border-t border-slate-800/80">
            <p class="text-[11px] font-semibold text-slate-400 text-center uppercase tracking-wider mb-2.5">
                Pilih Akun Demo Cepat
            </p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="email = 'admin@feedmart.test'; password = 'password'"
                    class="p-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-xl text-left transition group">
                    <span class="block text-xs font-bold text-emerald-400 group-hover:text-emerald-300">Admin Feedmart</span>
                    <span class="block text-[10px] text-slate-500 truncate">admin@feedmart.test</span>
                </button>
                <button type="button" @click="email = 'kasir@feedmart.test'; password = 'password'"
                    class="p-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-xl text-left transition group">
                    <span class="block text-xs font-bold text-amber-400 group-hover:text-amber-300">Kasir Feedmart</span>
                    <span class="block text-[10px] text-slate-500 truncate">kasir@feedmart.test</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
