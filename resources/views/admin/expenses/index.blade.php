@extends('layouts.app')

@section('title', 'PENGELUARAN KAS TOKO')
@section('page_title', 'Pengeluaran Kas & Operasional Toko')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">💸</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">PENGELUARAN KAS &amp; OPERASIONAL TOKO</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Catat biaya harian (kuli panggul, plastik kresek, listrik, lakban) untuk hitung laba bersih</p>
        </div>

        <div>
            <button @click="showModal = true" class="glass-btn bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white border-rose-400/50 px-4 py-2.5 text-xs font-bold shadow-md shadow-rose-500/20 flex items-center gap-2">
                <span>➕</span> CATAT PENGELUARAN KAS
            </button>
        </div>
    </div>

    <!-- KPI Ringkasan Beban -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="glass-box bg-amber-400/15 border-amber-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-900">Total Pengeluaran Bulan Ini ({{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F Y') }})</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                Rp {{ number_format($totalMonthlyExpense, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Akumulasi seluruh pos biaya operasional</span>
        </div>

        <div class="glass-box bg-rose-500/12 border-rose-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-800">Pengeluaran Hari Ini</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                Rp {{ number_format($todayExpense, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Kas keluar hari ini ({{ date('d/m/Y') }})</span>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-box bg-emerald-500/15 border-emerald-400/35 p-4 font-bold text-emerald-950 flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Bulan & Kategori -->
    <div class="glass-box p-4">
        <form action="{{ route('admin.expenses.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="w-full md:w-56">
                <input type="month" name="month" value="{{ $currentMonth }}"
                    class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
            </div>

            <div class="w-full md:w-64">
                <select name="category" class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
                    <option value="">SEMUA KATEGORI BIAYA</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 px-5 py-2 text-xs font-bold uppercase">
                    FILTER
                </button>
                @if(request()->hasAny(['month', 'category']))
                    <a href="{{ route('admin.expenses.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengeluaran -->
    <div class="glass-box overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/70 bg-white/55 text-slate-600 text-xs font-bold uppercase">
                        <th class="p-4 border-r border-slate-200/50 w-12 text-center">NO</th>
                        <th class="p-4 border-r border-slate-200/50">TANGGAL</th>
                        <th class="p-4 border-r border-slate-200/50">KATEGORI BIAYA</th>
                        <th class="p-4 border-r border-slate-200/50">KETERANGAN / KEPERLUAN</th>
                        <th class="p-4 border-r border-slate-200/50">DICATAT OLEH</th>
                        <th class="p-4 border-r border-slate-200/50 text-right">JUMLAH (RP)</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60 text-xs font-semibold text-slate-800">
                    @forelse($expenses as $index => $exp)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-4 border-r border-slate-200/50 text-center text-slate-400">
                                {{ $expenses->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-mono text-slate-700">
                                {{ $exp->date->format('d/m/Y') }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50">
                                <span class="glass-badge bg-amber-400/20 text-amber-900 border-amber-400/35 uppercase">{{ $exp->category }}</span>
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-slate-700">
                                {{ $exp->description ?? '-' }}
                                @if($exp->cash_register_id)
                                    <span class="block text-[10px] text-slate-400 font-mono">Shift Kas #{{ $exp->cash_register_id }}</span>
                                @endif
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-slate-700">
                                {{ $exp->user->name }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-right font-mono font-extrabold text-rose-600 text-sm">
                                Rp {{ number_format($exp->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="glass-btn bg-white/75 hover:bg-rose-500/15 text-rose-700 border-rose-300/40 px-3 py-1.5 text-xs">
                                        🗑️ HAPUS
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500 uppercase font-bold">
                                Belum ada catatan pengeluaran pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Pengeluaran -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-lg p-6 relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase">Catat Pengeluaran Kas Baru</h3>
                <button @click="showModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST" class="space-y-4 my-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="glass-input w-full px-3.5 py-2 font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Pengeluaran *</label>
                    <select name="category" required class="glass-input w-full px-3.5 py-2 font-semibold uppercase text-xs">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal Biaya (Rp) *</label>
                    <input type="number" name="amount" min="1" required class="glass-input w-full px-3.5 py-2.5 font-extrabold text-base" placeholder="50000">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan / Rincian Pengeluaran</label>
                    <textarea name="description" rows="2" class="glass-input w-full px-3.5 py-2 font-semibold text-xs" placeholder="Contoh: Beli plastik 1kg 5 pack & lakban bening untuk repack pakan"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-200/70 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="glass-btn bg-white/75 text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                        BATAL
                    </button>
                    <button type="submit" class="glass-btn bg-gradient-to-r from-rose-500 to-pink-500 text-white border-rose-400/50 px-6 py-2 text-xs font-bold uppercase shadow-md shadow-rose-500/20">
                        SIMPAN PENGELUARAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
