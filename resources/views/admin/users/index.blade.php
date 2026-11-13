@extends('layouts.app')

@section('title', 'MANAJEMEN PENGGUNA')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="neo-box bg-white p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">👥</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">MANAJEMEN PENGGUNA & STAF</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">Kelola akun admin dan kasir yang memiliki akses sistem</p>
        </div>

        <div>
            <a href="{{ route('admin.users.create') }}" class="neo-btn bg-emerald-400 text-black px-4 py-2 text-xs flex items-center gap-2">
                <span>➕</span> TAMBAH PENGGUNA BARU
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="neo-box bg-emerald-400 p-4 font-black text-black flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="neo-box bg-rose-400 p-4 font-black text-black flex items-center gap-3">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="neo-box bg-white p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="CARI NAMA ATAU EMAIL..."
                    class="neo-input w-full text-xs font-bold uppercase">
            </div>

            <div class="w-full md:w-48">
                <select name="role" class="neo-input w-full text-xs font-bold uppercase">
                    <option value="">SEMUA ROLE</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>ADMINISTRATOR</option>
                    <option value="kasir" {{ request('role') === 'kasir' ? 'selected' : '' }}>KASIR TOKO</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="neo-btn bg-black text-white px-5 py-2 text-xs font-black uppercase">
                    FILTER
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="neo-box bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-black bg-yellow-200 text-xs font-black uppercase">
                        <th class="p-4 border-r-2 border-black w-12 text-center">NO</th>
                        <th class="p-4 border-r-2 border-black">NAMA PENGGUNA</th>
                        <th class="p-4 border-r-2 border-black">EMAIL / USERNAME</th>
                        <th class="p-4 border-r-2 border-black text-center">ROLE AKSES</th>
                        <th class="p-4 border-r-2 border-black text-center">STATUS</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black text-xs font-bold">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-amber-50 transition">
                            <td class="p-4 border-r-2 border-black text-center text-gray-500">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r-2 border-black font-black">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="ml-2 neo-badge bg-cyan-300 text-[10px]">ANDA</span>
                                @endif
                            </td>
                            <td class="p-4 border-r-2 border-black font-mono">
                                {{ $u->email }}
                            </td>
                            <td class="p-4 border-r-2 border-black text-center">
                                @if($u->isAdmin())
                                    <span class="neo-badge bg-purple-300">ADMIN</span>
                                @else
                                    <span class="neo-badge bg-emerald-300">KASIR</span>
                                @endif
                            </td>
                            <td class="p-4 border-r-2 border-black text-center">
                                @if($u->is_active)
                                    <span class="neo-badge bg-emerald-400">AKTIF</span>
                                @else
                                    <span class="neo-badge bg-rose-400">NON-AKTIF</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="neo-btn bg-white hover:bg-yellow-200 px-3 py-1 text-xs">
                                        ✏️ EDIT
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" onsubmit="return confirm('Ubah status pengguna ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="neo-btn {{ $u->is_active ? 'bg-rose-200 hover:bg-rose-300' : 'bg-emerald-200 hover:bg-emerald-300' }} px-3 py-1 text-xs">
                                                {{ $u->is_active ? '🚫 NONAKTIFKAN' : '✅ AKTIFKAN' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 uppercase font-black">
                                Belum ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t-2 border-black bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
