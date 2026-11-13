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
    <div class="neo-box-lg overflow-hidden">
        <!-- Header -->
        <div class="p-6 bg-black text-white border-b-2 border-black flex items-center justify-between">
            <div>
                <span class="neo-badge bg-emerald-400 text-black">Shift Aktif #{{ $register->id }}</span>
                <h2 class="text-xl font-black mt-1 text-white">{{ $register->user->name }}</h2>
                <p class="text-xs font-mono text-zinc-400 mt-1 font-bold">Dibuka: {{ $register->opened_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest block">Total Transaksi</span>
                <span class="text-3xl font-black font-mono text-white">{{ $totalTransactions }}</span>
            </div>
        </div>

        <!-- Metrics Comparison -->
        <div class="p-6 border-b-2 border-black bg-zinc-50">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="p-3 bg-white border-2 border-black rounded-xl shadow-[3px_3px_0px_#000]">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Modal Awal</span>
                    <span class="text-sm font-black font-mono text-black mt-1 block">
                        Rp {{ number_format($register->opening_cash, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3 bg-emerald-100 border-2 border-black rounded-xl shadow-[3px_3px_0px_#000]">
                    <span class="text-[10px] font-black text-zinc-700 uppercase tracking-widest block">+ Penjualan Tunai</span>
                    <span class="text-sm font-black font-mono text-black mt-1 block">
                        Rp {{ number_format($cashSales, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3 bg-rose-100 border-2 border-black rounded-xl shadow-[3px_3px_0px_#000]">
                    <span class="text-[10px] font-black text-rose-700 uppercase tracking-widest block">- Kas Keluar Shift</span>
                    <span class="text-sm font-black font-mono text-rose-700 mt-1 block">
                        Rp {{ number_format($cashExpenses ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3 bg-sky-100 border-2 border-black rounded-xl shadow-[3px_3px_0px_#000]">
                    <span class="text-[10px] font-black text-zinc-700 uppercase tracking-widest block">Non-Tunai (QRIS/TF)</span>
                    <span class="text-sm font-black font-mono text-black mt-1 block">
                        Rp {{ number_format($nonCashSales, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Target Expected Cash -->
            <div class="mt-4 p-4 rounded-xl bg-yellow-200 border-2 border-black flex items-center justify-between shadow-[3px_3px_0px_#000]">
                <div>
                    <span class="text-xs font-black text-black uppercase tracking-wider block">Target Uang Kas Sistem di Laci:</span>
                    <p class="text-[11px] font-bold text-zinc-700">Modal Awal + Penjualan Tunai - Kas Keluar</p>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black font-mono text-black">
                        Rp {{ number_format($expectedCash, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Closing Form -->
        <form method="POST" action="{{ route('cash-register.close') }}" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-2">
                    Hitungan Uang Kas Fisik di Laci (Rp) <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-black text-black">Rp</span>
                    <input type="number" name="closing_cash" x-model.number="actual" min="0" step="500" required
                        class="neo-input pl-11 pr-4 py-3 text-lg font-mono font-black">
                </div>
            </div>

            <!-- Live Difference Calculation -->
            <div class="p-4 rounded-xl border-2 border-black text-sm shadow-[3px_3px_0px_#000]"
                :class="{
                    'bg-emerald-200 text-black': diff === 0,
                    'bg-amber-200 text-black': diff > 0,
                    'bg-rose-200 text-black': diff < 0
                }">
                <div class="flex items-center justify-between">
                    <span class="font-black text-xs uppercase tracking-wider">Status Selisih Kas:</span>
                    <span class="text-base font-black font-mono"
                        x-text="diff === 0 ? 'COCOK (PAS)' : (diff > 0 ? '+ LEBIH Rp ' + Math.abs(diff).toLocaleString('id-ID') : '- KURANG Rp ' + Math.abs(diff).toLocaleString('id-ID'))">
                    </span>
                </div>
                <p class="text-xs mt-1 font-bold" x-text="diff === 0 ? 'Jumlah uang fisik laci sama persis dengan kalkulasi sistem.' : (diff > 0 ? 'Terdapat kelebihan kas fisik di laci.' : 'Terdapat kekurangan kas fisik di laci.')"></p>
            </div>

            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-2">
                    Catatan Rekonsiliasi / Keterangan Selisih
                </label>
                <textarea name="notes" rows="2" placeholder="Tuliskan keterangan bila ada kelebihan/kekurangan uang..."
                    class="neo-input px-3.5 py-2.5 text-xs font-bold"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('pos.index') }}" class="neo-btn px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-black font-bold text-xs">
                    Kembali ke Kasir
                </a>
                <button type="submit" class="neo-btn px-6 py-2.5 bg-rose-400 hover:bg-rose-300 text-black font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                    Konfirmasi & Tutup Shift
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
