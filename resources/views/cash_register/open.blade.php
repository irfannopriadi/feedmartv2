@extends('layouts.app')

@section('title', 'Buka Kasir / Modal Awal - Feedmart')
@section('page_title', 'Buka Laci Kasir (Shift Baru)')

@section('content')
<div class="max-w-xl mx-auto mt-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-bold">
                💵
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Buka Sesi Laci Kasir</h2>
                <p class="text-xs text-slate-500">Masukkan jumlah modal uang kecil/kembalian di laci sebelum memulai melayani pembeli.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('cash-register.open') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Modal Awal di Laci (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" name="opening_cash" value="{{ old('opening_cash', '200000') }}" min="0" step="1000" required autofocus
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-lg font-bold text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 100000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition">Rp 100.000</button>
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 200000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition">Rp 200.000</button>
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 500000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition">Rp 500.000</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Kasir (Opsional)
                </label>
                <input type="text" name="notes" placeholder="Contoh: Shift Pagi Kasir Utama"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md shadow-emerald-600/20 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Mulai Shift & Buka Kasir</span>
                    <span>→</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
