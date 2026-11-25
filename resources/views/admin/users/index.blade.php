@extends('layouts.app')

@section('title', 'MANAJEMEN PENGGUNA')
@section('page_title', 'Manajemen Pengguna & Staf')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">👥</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">MANAJEMEN PENGGUNA &amp; STAF</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola akun admin dan kasir yang memiliki akses sistem</p>
        </div>

        <div>
            <a href="{{ route('admin.users.create') }}" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-500/20 flex items-center gap-2">
                <span>➕</span> TAMBAH PENGGUNA BARU
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-box bg-emerald-500/15 border-emerald-400/35 p-4 font-bold text-emerald-950 flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-box bg-rose-500/15 border-rose-400/35 p-4 font-bold text-rose-950 flex items-center gap-3">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="glass-box p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="CARI NAMA ATAU EMAIL..."
                    class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
            </div>

            <div class="w-full md:w-48">
                <select name="role" class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
                    <option value="">SEMUA ROLE</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>ADMINISTRATOR</option>
                    <option value="kasir" {{ request('role') === 'kasir' ? 'selected' : '' }}>KASIR TOKO</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 px-5 py-2 text-xs font-bold uppercase">
                    FILTER
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="glass-box overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/70 bg-white/55 text-slate-600 text-xs font-bold uppercase">
                        <th class="p-4 border-r border-slate-200/50 w-12 text-center">NO</th>
                        <th class="p-4 border-r border-slate-200/50">NAMA PENGGUNA</th>
                        <th class="p-4 border-r border-slate-200/50">EMAIL / USERNAME</th>
                        <th class="p-4 border-r border-slate-200/50 text-center">ROLE AKSES</th>
                        <th class="p-4 border-r border-slate-200/50 text-center">STATUS</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60 text-xs font-semibold text-slate-800">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-4 border-r border-slate-200/50 text-center text-slate-400">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-bold text-slate-900">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="ml-2 glass-badge bg-cyan-500/15 text-cyan-800 border-cyan-400/35 text-[10px]">ANDA</span>
                                @endif
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-mono text-slate-700">
                                {{ $u->email }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-center">
                                @if($u->isAdmin())
                                    <span class="glass-badge bg-violet-500/15 text-violet-800 border-violet-400/35">ADMIN</span>
                                @else
                                    <span class="glass-badge bg-emerald-500/15 text-emerald-800 border-emerald-400/35">KASIR</span>
                                @endif
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-center">
                                @if($u->is_active)
                                    <span class="glass-badge bg-emerald-500/15 text-emerald-800 border-emerald-400/35">AKTIF</span>
                                @else
                                    <span class="glass-badge bg-rose-500/15 text-rose-800 border-rose-400/35">NON-AKTIF</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="glass-btn bg-white/80 hover:bg-amber-400/20 text-slate-800 px-3 py-1.5 text-xs">
                                        ✏️ EDIT
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" onsubmit="return confirm('Ubah status pengguna ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="glass-btn {{ $u->is_active ? 'bg-rose-500/15 hover:bg-rose-500/25 text-rose-800 border-rose-400/35' : 'bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-800 border-emerald-400/35' }} px-3 py-1.5 text-xs">
                                                {{ $u->is_active ? '🚫 NONAKTIFKAN' : '✅ AKTIFKAN' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 uppercase font-bold">
                                Belum ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
