@extends('layouts.app')

@section('title', 'TAMBAH PENGGUNA BARU')
@section('page_title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="glass-box p-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">➕</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">TAMBAH PENGGUNA BARU</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Buat akun untuk kasir toko atau admin baru</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold">
            ← KEMBALI
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="glass-box-lg p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="glass-input w-full px-3.5 py-2 font-semibold uppercase" placeholder="CONTOH: AHMAD RIFAI">
            @error('name') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email / Username Login *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="glass-input w-full px-3.5 py-2 font-semibold" placeholder="kasir2@feedmart.test">
            @error('email') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Role / Peran Akses *</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="glass-box-sm p-3 flex items-center gap-3 cursor-pointer bg-emerald-500/10 hover:bg-emerald-500/18 border-emerald-400/35 transition">
                    <input type="radio" name="role" value="kasir" {{ old('role', 'kasir') === 'kasir' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-900">KASIR TOKO</div>
                        <div class="text-[10px] text-slate-500 font-medium">Akses POS kasir &amp; buka/tutup laci kas</div>
                    </div>
                </label>
                <label class="glass-box-sm p-3 flex items-center gap-3 cursor-pointer bg-violet-500/10 hover:bg-violet-500/18 border-violet-400/35 transition">
                    <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} class="w-4 h-4 text-violet-600">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-900">ADMINISTRATOR</div>
                        <div class="text-[10px] text-slate-500 font-medium">Akses penuh dashboard &amp; master data</div>
                    </div>
                </label>
            </div>
            @error('role') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi (Password) *</label>
            <input type="password" name="password" required minlength="6"
                class="glass-input w-full px-3.5 py-2 font-mono text-sm" placeholder="Minimal 6 karakter">
            @error('password') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 bg-white/70 text-emerald-600">
                <span class="text-xs font-bold uppercase text-slate-800">Akun Langsung Aktif &amp; Dapat Digunakan</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-200/70 flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="glass-btn bg-white/75 text-slate-700 px-6 py-2 text-xs font-bold uppercase">
                BATAL
            </a>
            <button type="submit" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 px-6 py-2 text-xs font-bold uppercase shadow-md shadow-emerald-500/20">
                SIMPAN PENGGUNA
            </button>
        </div>
    </form>
</div>
@endsection
