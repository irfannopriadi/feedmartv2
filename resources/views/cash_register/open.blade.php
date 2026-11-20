@extends('layouts.app')

@section('title', 'Buka Kasir / Modal Awal - Feedmart')
@section('page_title', 'Buka Laci Kasir (Shift Baru)')

@section('content')
<div class="max-w-xl mx-auto mt-6">
    <div class="glass-box-lg p-6 sm:p-8">
        <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b border-slate-200/70">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 border border-white/80 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-500/20">
                💵
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Buka Sesi Laci Kasir</h2>
                <p class="text-xs font-medium text-slate-600">Masukkan jumlah modal uang kecil/kembalian di laci sebelum mulai melayani transaksi.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('cash-register.open') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Modal Awal di Laci (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">Rp</span>
                    <input type="number" name="opening_cash" value="{{ old('opening_cash', '200000') }}" min="0" step="1000" required autofocus
                        class="glass-input pl-11 pr-4 py-3 text-lg font-mono font-extrabold">
                </div>
                <div class="flex flex-wrap gap-2 mt-2.5">
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 100000" class="glass-btn px-3 py-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-900 border-amber-400/35 text-xs font-bold font-mono">Rp 100.000</button>
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 200000" class="glass-btn px-3 py-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-900 border-amber-400/35 text-xs font-bold font-mono">Rp 200.000</button>
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 500000" class="glass-btn px-3 py-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-900 border-amber-400/35 text-xs font-bold font-mono">Rp 500.000</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Kasir (Opsional)
                </label>
                <input type="text" name="notes" placeholder="Contoh: Shift Pagi Kasir Utama"
                    class="glass-input px-3.5 py-2.5 text-xs font-semibold">
            </div>

            <div class="pt-2">
                <button type="submit" class="glass-btn w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 font-bold text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2">
                    <span>Mulai Shift &amp; Buka Kasir</span>
                    <span>→</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
