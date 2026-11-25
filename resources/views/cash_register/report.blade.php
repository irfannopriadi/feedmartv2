@extends('layouts.app')

@section('title', 'Laporan Rekonsiliasi Shift - Feedmart')
@section('page_title', 'Laporan Shift Kasir #' . $register->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="glass-box-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200/70 gap-3">
            <div>
                <span class="glass-badge bg-emerald-500/15 text-emerald-800 border-emerald-400/35">Rekonsiliasi Selesai</span>
                <h2 class="text-2xl font-extrabold text-slate-900 mt-1.5">Ringkasan Laci Kasir #{{ $register->id }}</h2>
                <p class="text-xs font-medium text-slate-600 mt-1">
                    Kasir: <strong class="text-slate-900">{{ $register->user->name }}</strong> |
                    Sesi: <span class="font-mono">{{ $register->opened_at->format('d M Y, H:i') }} - {{ $register->closed_at?->format('H:i') ?? 'Aktif' }}</span>
                </p>
            </div>
            <button onclick="window.print()" class="glass-btn px-4 py-2 bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 text-xs font-bold flex items-center gap-1.5 w-fit">
                🖨 Cetak Laporan Shift
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-6">
            <div class="p-3.5 glass-box-sm">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">Modal Awal</span>
                <span class="text-base font-extrabold font-mono text-slate-900 mt-1 block">Rp {{ number_format($register->opening_cash, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-500/12 border border-emerald-400/35 backdrop-blur-md">
                <span class="text-[9px] font-bold text-emerald-800 uppercase tracking-widest block">Total Tunai</span>
                <span class="text-base font-extrabold font-mono text-emerald-950 mt-1 block">Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-sky-500/12 border border-sky-400/35 backdrop-blur-md">
                <span class="text-[9px] font-bold text-sky-800 uppercase tracking-widest block">Non-Tunai (QRIS)</span>
                <span class="text-base font-extrabold font-mono text-sky-950 mt-1 block">Rp {{ number_format($nonCashSales, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-violet-500/12 border border-violet-400/35 backdrop-blur-md">
                <span class="text-[9px] font-bold text-violet-800 uppercase tracking-widest block">Kas Fisik Akhir</span>
                <span class="text-base font-extrabold font-mono text-violet-950 mt-1 block">Rp {{ number_format($register->closing_cash, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Selisih -->
        @php
            $diff = (float) $register->closing_cash - (float) $register->expected_cash;
        @endphp
        <div class="p-4 rounded-2xl border backdrop-blur-md text-sm mb-6 {{ $diff == 0 ? 'bg-emerald-500/15 border-emerald-400/40 text-emerald-950' : ($diff > 0 ? 'bg-amber-400/20 border-amber-400/45 text-amber-950' : 'bg-rose-500/15 border-rose-400/40 text-rose-950') }}">
            <div class="flex items-center justify-between font-bold">
                <span class="uppercase tracking-wider text-xs">Selisih Kas (Fisik vs Sistem):</span>
                <span class="text-base font-extrabold font-mono">
                    {{ $diff == 0 ? 'COCOK / PAS (Rp 0)' : ($diff > 0 ? '+ LEBIH Rp ' . number_format($diff, 0, ',', '.') : '- KURANG Rp ' . number_format(abs($diff), 0, ',', '.')) }}
                </span>
            </div>
            @if($register->notes)
                <p class="text-xs mt-1.5 font-medium italic opacity-85">Catatan Kasir: "{{ $register->notes }}"</p>
            @endif
        </div>

        <!-- Riwayat Transaksi Selama Shift -->
        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Daftar Transaksi Kasir ({{ $transactions->count() }} Transaksi)</h3>
        <div class="overflow-x-auto glass-box-sm overflow-hidden">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Invoice</th>
                        <th class="p-3">Metode</th>
                        <th class="p-3 text-right">Total Belanja</th>
                        <th class="p-3 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-3 font-mono text-slate-500">{{ $trx->created_at->format('H:i:s') }}</td>
                            <td class="p-3 font-mono font-bold text-slate-900">{{ $trx->invoice_number }}</td>
                            <td class="p-3">
                                <span class="glass-badge {{ $trx->payment_method === 'cash' ? 'bg-emerald-500/15 text-emerald-800 border-emerald-400/35' : 'bg-sky-500/15 text-sky-800 border-sky-400/35' }}">
                                    {{ $trx->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($trx->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="glass-btn px-2.5 py-1 bg-white/80 hover:bg-white text-slate-700 text-[10px] font-bold">
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-500 font-medium">Tidak ada transaksi yang tercatat pada shift ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('cash-register.open-form') }}" class="glass-btn px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-500/20">
                Buka Shift Kasir Baru →
            </a>
        </div>
    </div>
</div>
@endsection
