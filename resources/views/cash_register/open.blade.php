@extends('layouts.app')

@section('title', 'Buka Kasir / Modal Awal - Feedmart')
@section('page_title', 'Buka Laci Kasir (Shift Baru)')

@section('content')
<div class="max-w-xl mx-auto mt-6">
    <div class="neo-box-lg p-6 sm:p-8">
        <div class="flex items-center space-x-3.5 mb-6 pb-4 border-b-2 border-black">
            <div class="w-12 h-12 rounded-xl bg-emerald-400 border-2 border-black text-black flex items-center justify-center text-2xl font-black shadow-[2px_2px_0px_#000]">
                💵
            </div>
            <div>
                <h2 class="text-lg font-black text-black uppercase tracking-tight">Buka Sesi Laci Kasir</h2>
                <p class="text-xs font-bold text-zinc-600">Masukkan jumlah modal uang kecil/kembalian di laci sebelum mulai melayani transaksi.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('cash-register.open') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-2">
                    Modal Awal di Laci (Rp) <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-black text-black">Rp</span>
                    <input type="number" name="opening_cash" value="{{ old('opening_cash', '200000') }}" min="0" step="1000" required autofocus
                        class="neo-input pl-11 pr-4 py-3 text-lg font-mono font-black">
                </div>
                <div class="flex flex-wrap gap-2 mt-2.5">
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 100000" class="neo-btn px-2.5 py-1 bg-yellow-200 hover:bg-yellow-100 text-black text-xs font-bold font-mono">Rp 100.000</button>
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 200000" class="neo-btn px-2.5 py-1 bg-yellow-200 hover:bg-yellow-100 text-black text-xs font-bold font-mono">Rp 200.000</button>
                    <button type="button" onclick="document.querySelector('input[name=opening_cash]').value = 500000" class="neo-btn px-2.5 py-1 bg-yellow-200 hover:bg-yellow-100 text-black text-xs font-bold font-mono">Rp 500.000</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-2">
                    Catatan Kasir (Opsional)
                </label>
                <input type="text" name="notes" placeholder="Contoh: Shift Pagi Kasir Utama"
                    class="neo-input px-3.5 py-2.5 text-xs font-bold">
            </div>

            <div class="pt-2">
                <button type="submit" class="neo-btn w-full py-3 px-4 bg-emerald-400 hover:bg-emerald-300 text-black font-black text-sm uppercase tracking-wider shadow-[4px_4px_0px_#000] flex items-center justify-center gap-2">
                    <span>Mulai Shift & Buka Kasir</span>
                    <span>→</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
