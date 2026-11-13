@extends('layouts.app')

@section('title', 'EDIT PENGGUNA')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="neo-box bg-white p-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">✏️</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">EDIT PENGGUNA</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">Perbarui data atau reset password pengguna</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs">
            ← KEMBALI
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="neo-box bg-white p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-black uppercase mb-1">Nama Lengkap *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="neo-input w-full font-bold uppercase">
            @error('name') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase mb-1">Email / Username Login *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="neo-input w-full font-bold">
            @error('email') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase mb-1">Role / Peran Akses *</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="neo-box p-3 flex items-center gap-3 cursor-pointer bg-emerald-50 hover:bg-emerald-100 transition">
                    <input type="radio" name="role" value="kasir" {{ old('role', $user->role) === 'kasir' ? 'checked' : '' }}
                        {{ $user->id === auth()->id() ? 'disabled' : '' }} class="w-4 h-4 text-black">
                    <div>
                        <div class="text-xs font-black uppercase">KASIR TOKO</div>
                        <div class="text-[10px] text-gray-500 font-bold">Akses POS kasir & shift kas</div>
                    </div>
                </label>
                <label class="neo-box p-3 flex items-center gap-3 cursor-pointer bg-purple-50 hover:bg-purple-100 transition">
                    <input type="radio" name="role" value="admin" {{ old('role', $user->role) === 'admin' ? 'checked' : '' }} class="w-4 h-4 text-black">
                    <div>
                        <div class="text-xs font-black uppercase">ADMINISTRATOR</div>
                        <div class="text-[10px] text-gray-500 font-bold">Akses penuh dashboard & master data</div>
                    </div>
                </label>
            </div>
            @if($user->id === auth()->id())
                <p class="text-[10px] font-bold text-amber-700 mt-1 uppercase">* Anda sedang mengedit akun Anda sendiri (role tidak dapat diubah ke kasir).</p>
            @endif
            @error('role') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password" minlength="6"
                class="neo-input w-full font-mono text-sm" placeholder="Masukkan password baru jika ingin mereset">
            @error('password') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        @if($user->id !== auth()->id())
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="w-5 h-5 accent-black">
                    <span class="text-xs font-black uppercase">Akun Aktif (Dapat Login)</span>
                </label>
            </div>
        @else
            <input type="hidden" name="is_active" value="1">
        @endif

        <div class="pt-4 border-t-2 border-black flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="neo-btn bg-gray-200 text-black px-6 py-2 text-xs font-black uppercase">
                BATAL
            </a>
            <button type="submit" class="neo-btn bg-black text-white px-6 py-2 text-xs font-black uppercase">
                SIMPAN PERUBAHAN
            </button>
        </div>
    </form>
</div>
@endsection
