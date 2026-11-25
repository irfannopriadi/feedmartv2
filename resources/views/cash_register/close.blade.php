@extends('layouts.app')

@section('title', 'Tutup Kasir / Pergantian Shift - Feedmart')
@section('page_title', 'Rekonsiliasi & Tutup Laci Kasir')

@section('content')
<div class="max-w-2xl mx-auto" x-data="{
    expected: {{ $expectedCash }},
    actual: {{ $expectedCash }},
    get diff() {
        return this.actual - this.expected;
    }
}">
    <div class="glass-box-lg overflow-hidden">
        <!-- Header -->
        <div class="p-6 bg-white/45 border-b border-white/75 flex items-center justify-between">
            <div>
                <span class="glass-badge bg-emerald-500/15 text-emerald-800 border-emerald-400/35">Shift Aktif #{{ $register->id }}</span>
                <h2 class="text-xl font-extrabold mt-1.5 text-slate-900">{{ $register->user->name }}</h2>
                <p class="text-xs font-mono text-slate-500 mt-1 font-semibold">Dibuka: {{ $register->opened_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Total Transaksi</span>
                <span class="text-3xl font-extrabold font-mono text-slate-900">{{ $totalTransactions }}</span>
            </div>
        </div>

        <!-- Metrics Comparison -->
        <div class="p-6 border-b border-white/75 bg-white/30">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="p-3.5 glass-box-sm">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Modal Awal</span>
                    <span class="text-sm font-extrabold font-mono text-slate-900 mt-1 block">
                        Rp {{ number_format($register->opening_cash, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-500/12 border border-emerald-400/35 backdrop-blur-md">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-widest block">+ Penjualan Tunai</span>
                    <span class="text-sm font-extrabold font-mono text-emerald-950 mt-1 block">
                        Rp {{ number_format($cashSales, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-rose-500/12 border border-rose-400/35 backdrop-blur-md">
                    <span class="text-[10px] font-bold text-rose-700 uppercase tracking-widest block">- Kas Keluar Shift</span>
                    <span class="text-sm font-extrabold font-mono text-rose-700 mt-1 block">
                        Rp {{ number_format($cashExpenses ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-sky-500/12 border border-sky-400/35 backdrop-blur-md">
                    <span class="text-[10px] font-bold text-sky-800 uppercase tracking-widest block">Non-Tunai (QRIS/TF)</span>
                    <span class="text-sm font-extrabold font-mono text-sky-950 mt-1 block">
                        Rp {{ number_format($nonCashSales, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Target Expected Cash -->
            <div class="mt-4 p-4 rounded-2xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-between backdrop-blur-md">
                <div>
                    <span class="text-xs font-bold text-amber-950 uppercase tracking-wider block">Target Uang Kas Sistem di Laci:</span>
                    <p class="text-[11px] font-medium text-amber-900/80">Modal Awal + Penjualan Tunai - Kas Keluar</p>
                </div>
                <div class="text-right">
                    <span class="text-xl font-extrabold font-mono text-slate-900">
                        Rp {{ number_format($expectedCash, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Closing Form -->
        <form method="POST" action="{{ route('cash-register.close') }}" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Hitungan Uang Kas Fisik di Laci (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">Rp</span>
                    <input type="number" name="closing_cash" x-model.number="actual" min="0" step="500" required
                        class="glass-input pl-11 pr-4 py-3 text-lg font-mono font-extrabold">
                </div>
            </div>

            <!-- Live Difference Calculation -->
            <div class="p-4 rounded-2xl border backdrop-blur-md text-sm"
                :class="{
                    'bg-emerald-500/15 border-emerald-400/40 text-emerald-950': diff === 0,
                    'bg-amber-400/20 border-amber-400/45 text-amber-950': diff > 0,
                    'bg-rose-500/15 border-rose-400/40 text-rose-950': diff < 0
                }">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs uppercase tracking-wider">Status Selisih Kas:</span>
                    <span class="text-base font-extrabold font-mono"
                        x-text="diff === 0 ? 'COCOK (PAS)' : (diff > 0 ? '+ LEBIH Rp ' + Math.abs(diff).toLocaleString('id-ID') : '- KURANG Rp ' + Math.abs(diff).toLocaleString('id-ID'))">
                    </span>
                </div>
                <p class="text-xs mt-1 font-medium" x-text="diff === 0 ? 'Jumlah uang fisik laci sama persis dengan kalkulasi sistem.' : (diff > 0 ? 'Terdapat kelebihan kas fisik di laci.' : 'Terdapat kekurangan kas fisik di laci.')"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Rekonsiliasi / Keterangan Selisih
                </label>
                <textarea name="notes" rows="2" placeholder="Tuliskan keterangan bila ada kelebihan/kekurangan uang..."
                    class="glass-input px-3.5 py-2.5 text-xs font-semibold"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('pos.index') }}" class="glass-btn px-4 py-2.5 bg-white/75 hover:bg-white text-slate-700 font-bold text-xs">
                    Kembali ke Kasir
                </a>
                <button type="submit" class="glass-btn px-6 py-2.5 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white border-rose-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-rose-500/20">
                    Konfirmasi &amp; Tutup Shift
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
