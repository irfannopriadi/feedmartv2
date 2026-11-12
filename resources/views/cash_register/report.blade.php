@extends('layouts.app')

@section('title', 'Laporan Rekonsiliasi Shift - Feedmart')
@section('page_title', 'Laporan Shift Kasir #' . $register->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="neo-box-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b-2 border-black gap-3">
            <div>
                <span class="neo-badge bg-emerald-300 text-black">Rekonsiliasi Selesai</span>
                <h2 class="text-2xl font-black text-black mt-1">Ringkasan Laci Kasir #{{ $register->id }}</h2>
                <p class="text-xs font-bold text-zinc-600 mt-1">
                    Kasir: <strong class="text-black">{{ $register->user->name }}</strong> |
                    Sesi: <span class="font-mono">{{ $register->opened_at->format('d M Y, H:i') }} - {{ $register->closed_at?->format('H:i') ?? 'Aktif' }}</span>
                </p>
            </div>
            <button onclick="window.print()" class="neo-btn px-3.5 py-2 bg-yellow-200 hover:bg-yellow-300 text-black text-xs font-black shadow-[2px_2px_0px_#000] flex items-center gap-1.5 w-fit">
                🖨 Cetak Laporan Shift
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-6">
            <div class="p-3.5 bg-zinc-50 rounded-xl border-2 border-black shadow-[2px_2px_0px_#000]">
                <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest block">Modal Awal</span>
                <span class="text-base font-black font-mono text-black mt-1 block">Rp {{ number_format($register->opening_cash, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 bg-emerald-100 rounded-xl border-2 border-black shadow-[2px_2px_0px_#000]">
                <span class="text-[9px] font-black text-zinc-700 uppercase tracking-widest block">Total Tunai</span>
                <span class="text-base font-black font-mono text-black mt-1 block">Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 bg-sky-100 rounded-xl border-2 border-black shadow-[2px_2px_0px_#000]">
                <span class="text-[9px] font-black text-zinc-700 uppercase tracking-widest block">Non-Tunai (QRIS)</span>
                <span class="text-base font-black font-mono text-black mt-1 block">Rp {{ number_format($nonCashSales, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 bg-purple-100 rounded-xl border-2 border-black shadow-[2px_2px_0px_#000]">
                <span class="text-[9px] font-black text-zinc-700 uppercase tracking-widest block">Kas Fisik Akhir</span>
                <span class="text-base font-black font-mono text-black mt-1 block">Rp {{ number_format($register->closing_cash, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Selisih -->
        @php
            $diff = (float) $register->closing_cash - (float) $register->expected_cash;
        @endphp
        <div class="p-4 rounded-xl border-2 border-black text-sm mb-6 shadow-[3px_3px_0px_#000] {{ $diff == 0 ? 'bg-emerald-200 text-black' : ($diff > 0 ? 'bg-amber-200 text-black' : 'bg-rose-200 text-black') }}">
            <div class="flex items-center justify-between font-black">
                <span class="uppercase tracking-wider text-xs">Selisih Kas (Fisik vs Sistem):</span>
                <span class="text-base font-mono">
                    {{ $diff == 0 ? 'COCOK / PAS (Rp 0)' : ($diff > 0 ? '+ LEBIH Rp ' . number_format($diff, 0, ',', '.') : '- KURANG Rp ' . number_format(abs($diff), 0, ',', '.')) }}
                </span>
            </div>
            @if($register->notes)
                <p class="text-xs mt-1.5 font-bold italic text-zinc-800">Catatan Kasir: "{{ $register->notes }}"</p>
            @endif
        </div>

        <!-- Riwayat Transaksi Selama Shift -->
        <h3 class="text-xs font-black text-black uppercase tracking-wider mb-2.5">Daftar Transaksi Kasir ({{ $transactions->count() }} Transaksi)</h3>
        <div class="overflow-x-auto rounded-xl border-2 border-black shadow-[2px_2px_0px_#000]">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-zinc-100 text-black font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Invoice</th>
                        <th class="p-3">Metode</th>
                        <th class="p-3 text-right">Total Belanja</th>
                        <th class="p-3 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="p-3 font-mono text-zinc-600">{{ $trx->created_at->format('H:i:s') }}</td>
                            <td class="p-3 font-mono font-black text-black">{{ $trx->invoice_number }}</td>
                            <td class="p-3">
                                <span class="neo-badge {{ $trx->payment_method === 'cash' ? 'bg-emerald-300 text-black' : 'bg-sky-300 text-black' }}">
                                    {{ $trx->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-mono font-black text-black">
                                Rp {{ number_format($trx->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="neo-btn px-2 py-0.5 bg-white text-black text-[10px] font-black">
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-zinc-500 font-bold">Tidak ada transaksi yang tercatat pada shift ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('cash-register.open-form') }}" class="neo-btn px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                Buka Shift Kasir Baru →
            </a>
        </div>
    </div>
</div>
@endsection
