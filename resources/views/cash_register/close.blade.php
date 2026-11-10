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
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
            <div>
                <span class="text-xs text-emerald-400 font-bold uppercase tracking-wider">Shift Kasir Aktif #{{ $register->id }}</span>
                <h2 class="text-xl font-black mt-0.5">{{ $register->user->name }}</h2>
                <p class="text-xs text-slate-400 mt-1">Dibuka sejak: {{ $register->opened_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block">Total Transaksi</span>
                <span class="text-2xl font-black text-white">{{ $totalTransactions }}</span>
            </div>
        </div>

        <!-- Metrics Comparison -->
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-white rounded-xl border border-slate-200">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Modal Awal Laci</span>
                    <span class="text-base font-extrabold text-slate-800 mt-1 block">
                        Rp {{ number_format($register->opening_cash, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200">
                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">+ Penjualan Tunai</span>
                    <span class="text-base font-extrabold text-emerald-600 mt-1 block">
                        Rp {{ number_format($cashSales, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200">
                    <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Non-Tunai (QRIS/TF)</span>
                    <span class="text-base font-extrabold text-blue-600 mt-1 block">
                        Rp {{ number_format($nonCashSales, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Target Expected Cash -->
            <div class="mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Target Uang Kas Sistem di Laci:</span>
                    <p class="text-[11px] text-emerald-700">Modal Awal + Total Penjualan Tunai</p>
                </div>
                <div class="text-right">
                    <span class="text-xl font-black text-emerald-700">
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
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" name="closing_cash" x-model.number="actual" min="0" step="500" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-lg font-bold text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                </div>
            </div>

            <!-- Live Difference Calculation -->
            <div class="p-4 rounded-xl border text-sm"
                :class="{
                    'bg-emerald-50 border-emerald-200 text-emerald-800': diff === 0,
                    'bg-amber-50 border-amber-200 text-amber-800': diff > 0,
                    'bg-rose-50 border-rose-200 text-rose-800': diff < 0
                }">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs uppercase tracking-wider">Status Selisih Kas:</span>
                    <span class="text-base font-black"
                        x-text="diff === 0 ? 'COCOK (PAS)' : (diff > 0 ? '+ LEBIH Rp ' + Math.abs(diff).toLocaleString('id-ID') : '- KURANG Rp ' + Math.abs(diff).toLocaleString('id-ID'))">
                    </span>
                </div>
                <p class="text-xs mt-1" x-text="diff === 0 ? 'Jumlah uang fisik laci sama persis dengan kalkulasi sistem.' : (diff > 0 ? 'Terdapat kelebihan kas fisik di laci.' : 'Terdapat kekurangan kas fisik di laci.')"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Rekonsiliasi / Keterangan Selisih
                </label>
                <textarea name="notes" rows="2" placeholder="Tuliskan keterangan bila ada kelebihan/kekurangan uang..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('pos.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">
                    Kembali ke Kasir
                </a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-sm shadow-md shadow-rose-600/20 transition transform active:scale-[0.99]">
                    Konfirmasi & Tutup Shift
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
