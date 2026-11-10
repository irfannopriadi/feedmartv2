@extends('layouts.app')

@section('title', 'Laporan Rekonsiliasi Shift - Feedmart')
@section('page_title', 'Laporan Shift Kasir #' . $register->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rekonsiliasi Selesai</span>
                <h2 class="text-2xl font-black text-slate-900 mt-0.5">Ringkasan Laci Kasir #{{ $register->id }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Kasir: <strong class="text-slate-800">{{ $register->user->name }}</strong> |
                    Sesi: {{ $register->opened_at->format('d M Y, H:i') }} - {{ $register->closed_at?->format('H:i') ?? 'Aktif' }}
                </p>
            </div>
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                🖨 Cetak Laporan Shift
            </button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 my-6">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Modal Awal</span>
                <span class="text-base font-extrabold text-slate-800 mt-1 block">Rp {{ number_format($register->opening_cash, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Total Tunai</span>
                <span class="text-base font-extrabold text-emerald-700 mt-1 block">Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Non-Tunai (QRIS/TF)</span>
                <span class="text-base font-extrabold text-blue-700 mt-1 block">Rp {{ number_format($nonCashSales, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 bg-purple-50 rounded-xl border border-purple-100">
                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider block">Kas Fisik Akhir</span>
                <span class="text-base font-extrabold text-purple-700 mt-1 block">Rp {{ number_format($register->closing_cash, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Selisih -->
        @php
            $diff = (float) $register->closing_cash - (float) $register->expected_cash;
        @endphp
        <div class="p-4 rounded-xl border text-sm mb-6 {{ $diff == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($diff > 0 ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-rose-50 border-rose-200 text-rose-800') }}">
            <div class="flex items-center justify-between font-bold">
                <span>Selisih Kas (Fisik vs Sistem):</span>
                <span class="text-base">
                    {{ $diff == 0 ? 'COCOK / PAS (Rp 0)' : ($diff > 0 ? '+ LEBIH Rp ' . number_format($diff, 0, ',', '.') : '- KURANG Rp ' . number_format(abs($diff), 0, ',', '.')) }}
                </span>
            </div>
            @if($register->notes)
                <p class="text-xs mt-2 italic text-slate-600">Catatan Kasir: "{{ $register->notes }}"</p>
            @endif
        </div>

        <!-- Riwayat Transaksi Selama Shift -->
        <h3 class="text-sm font-bold text-slate-800 mb-3">Daftar Transaksi Kasir ({{ $transactions->count() }} Transaksi)</h3>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Invoice</th>
                        <th class="p-3">Metode</th>
                        <th class="p-3 text-right">Total Belanja</th>
                        <th class="p-3 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3">{{ $trx->created_at->format('H:i:s') }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $trx->invoice_number }}</td>
                            <td class="p-3 uppercase font-bold text-[10px]">
                                <span class="px-2 py-0.5 rounded {{ $trx->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $trx->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-bold text-slate-800">
                                Rp {{ number_format($trx->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-semibold underline">
                                    Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-400">Tidak ada transaksi yang tercatat pada shift ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('cash-register.open-form') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                Buka Shift Kasir Baru →
            </a>
        </div>
    </div>
</div>
@endsection
