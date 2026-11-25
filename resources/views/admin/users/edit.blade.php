@extends('layouts.app')

@section('title', 'EDIT PENGGUNA')
@section('page_title', 'Edit Pengguna')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="glass-box p-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">✏️</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">EDIT PENGGUNA</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Perbarui data atau reset password pengguna</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold">
            ← KEMBALI
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="glass-box-lg p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="glass-input w-full px-3.5 py-2 font-semibold uppercase">
            @error('name') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email / Username Login *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="glass-input w-full px-3.5 py-2 font-semibold">
            @error('email') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Role / Peran Akses *</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="glass-box-sm p-3 flex items-center gap-3 cursor-pointer bg-emerald-500/10 hover:bg-emerald-500/18 border-emerald-400/35 transition">
                    <input type="radio" name="role" value="kasir" {{ old('role', $user->role) === 'kasir' ? 'checked' : '' }}
                        {{ $user->id === auth()->id() ? 'disabled' : '' }} class="w-4 h-4 text-emerald-600">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-900">KASIR TOKO</div>
                        <div class="text-[10px] text-slate-500 font-medium">Akses POS kasir &amp; shift kas</div>
                    </div>
                </label>
                <label class="glass-box-sm p-3 flex items-center gap-3 cursor-pointer bg-violet-500/10 hover:bg-violet-500/18 border-violet-400/35 transition">
                    <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} class="w-4 h-4 text-violet-600">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-900">ADMINISTRATOR</div>
                        <div class="text-[10px] text-slate-500 font-medium">Akses penuh dashboard &amp; master data</div>
                    </div>
                </label>
            </div>
            @if($user->id === auth()->id())
                <p class="text-[10px] font-semibold text-amber-700 mt-1 uppercase">* Anda sedang mengedit akun Anda sendiri (role tidak dapat diubah ke kasir).</p>
            @endif
            @error('role') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password" minlength="6"
                class="glass-input w-full px-3.5 py-2 font-mono text-sm" placeholder="Masukkan password baru jika ingin mereset">
            @error('password') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        @if($user->id !== auth()->id())
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 bg-white/70 text-emerald-600">
                    <span class="text-xs font-bold uppercase text-slate-800">Akun Aktif (Dapat Login)</span>
                </label>
            </div>
        @else
            <input type="hidden" name="is_active" value="1">
        @endif

        <div class="pt-4 border-t border-slate-200/70 flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="glass-btn bg-white/75 text-slate-700 px-6 py-2 text-xs font-bold uppercase">
                BATAL
            </a>
            <button type="submit" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 px-6 py-2 text-xs font-bold uppercase shadow-md shadow-emerald-500/20">
                SIMPAN PERUBAHAN
            </button>
        </div>
    </form>
</div>
@endsection
