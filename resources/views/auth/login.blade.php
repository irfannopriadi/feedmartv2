<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Feedmart POS & Inventaris</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F0] text-black min-h-screen flex items-center justify-center p-4 selection:bg-yellow-300 selection:text-black">
    <div class="w-full max-w-md bg-white border-3 border-black rounded-2xl p-6 sm:p-8 shadow-[8px_8px_0px_#000] relative"
         x-data="{ email: '{{ old('email', 'admin@feedmart.test') }}', password: 'password' }">
        
        <!-- Header Branding -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-emerald-400 border-2 border-black rounded-2xl flex items-center justify-center text-3xl shadow-[3px_3px_0px_#000] mb-3">
                🌾
            </div>
            <h1 class="text-3xl font-black tracking-tight text-black">FEEDMART</h1>
            <div class="inline-block mt-1 px-2.5 py-0.5 bg-yellow-300 border-2 border-black rounded-md text-[11px] font-black uppercase tracking-wider shadow-[2px_2px_0px_#000]">
                POS Kasir & Inventaris Pakan
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-100 border-2 border-black text-black rounded-xl text-xs font-bold shadow-[3px_3px_0px_#000] flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-rose-400 text-white font-black flex items-center justify-center flex-shrink-0">✕</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-100 border-2 border-black text-black rounded-xl text-xs font-bold shadow-[3px_3px_0px_#000] flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-emerald-400 text-black font-black flex items-center justify-center flex-shrink-0">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input type="email" name="email" x-model="email" required
                    placeholder="nama@feedmart.test"
                    class="neo-input px-3.5 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <input type="password" name="password" x-model="password" required
                    placeholder="••••••••"
                    class="neo-input px-3.5 py-2.5 text-sm">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 cursor-pointer font-bold text-black select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 border-2 border-black rounded bg-white text-black focus:ring-0 cursor-pointer">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="neo-btn w-full py-3 px-4 bg-emerald-400 hover:bg-emerald-300 text-black font-black text-sm uppercase tracking-wider shadow-[4px_4px_0px_#000]">
                Masuk ke Aplikasi →
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-7 pt-5 border-t-2 border-black">
            <p class="text-[11px] font-black text-zinc-600 text-center uppercase tracking-widest mb-2.5">
                Pilih Akun Demo Cepat
            </p>
            <div class="grid grid-cols-2 gap-2.5">
                <button type="button" @click="email = 'admin@feedmart.test'; password = 'password'"
                    class="p-2.5 bg-zinc-50 hover:bg-emerald-100 border-2 border-black rounded-xl text-left transition-all shadow-[2px_2px_0px_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_#000] group">
                    <span class="block text-xs font-black text-black group-hover:text-emerald-900">👑 Admin</span>
                    <span class="block text-[10px] font-mono text-zinc-600 truncate">admin@feedmart.test</span>
                </button>
                <button type="button" @click="email = 'kasir@feedmart.test'; password = 'password'"
                    class="p-2.5 bg-zinc-50 hover:bg-yellow-100 border-2 border-black rounded-xl text-left transition-all shadow-[2px_2px_0px_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_#000] group">
                    <span class="block text-xs font-black text-black group-hover:text-yellow-900">⚡ Kasir</span>
                    <span class="block text-[10px] font-mono text-zinc-600 truncate">kasir@feedmart.test</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
