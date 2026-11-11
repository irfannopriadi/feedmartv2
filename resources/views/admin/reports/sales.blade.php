@extends('layouts.app')

@section('title', 'Laporan Penjualan & Laba Rugi - Feedmart')
@section('page_title', 'Laporan Penjualan & Laba Kotor')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Metode Bayar</label>
                <select name="payment_method" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ $paymentMethod === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Kasir</label>
                <select name="user_id" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $userId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition shadow-xs">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.reports.sales.excel', request()->all()) }}" class="py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-xs flex items-center gap-1" title="Unduh File Excel">
                    <span>📊</span>
                    <span class="hidden sm:inline">Excel</span>
                </a>
                <a href="{{ route('admin.reports.sales.pdf', request()->all()) }}" target="_blank" class="py-2 px-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition shadow-xs flex items-center gap-1" title="Cetak / Simpan PDF">
                    <span>📄</span>
                    <span class="hidden sm:inline">PDF</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Omzet Penjualan</span>
            <span class="text-xl font-black text-slate-900 mt-1 block">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-500 mt-0.5 block">{{ $totalTransactionsCount }} Transaksi</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total HPP Barang Terjual</span>
            <span class="text-xl font-black text-slate-700 mt-1 block">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-500 mt-0.5 block">Modal pokok pakan</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Laba Kotor (Gross Profit)</span>
            <span class="text-xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
            <span class="text-[10px] font-bold text-emerald-700 mt-0.5 block">Margin: {{ number_format($profitMargin, 1) }}%</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Total Diskon Diberikan</span>
            <span class="text-xl font-black text-amber-600 mt-1 block">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-500 mt-0.5 block">Potongan penjualan</span>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">Rincian Transaksi Penjualan</h3>
            <span class="text-xs text-slate-400">Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">No. Invoice</th>
                        <th class="p-3">Kasir</th>
                        <th class="p-3">Item Belanja</th>
                        <th class="p-3">Metode</th>
                        <th class="p-3 text-right">Subtotal</th>
                        <th class="p-3 text-right">Diskon</th>
                        <th class="p-3 text-right">Total Akhir</th>
                        <th class="p-3 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 text-slate-400 whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $t->invoice_number }}</td>
                            <td class="p-3">{{ $t->user->name }}</td>
                            <td class="p-3 max-w-xs">
                                <div class="space-y-0.5">
                                    @foreach($t->details as $d)
                                        <div class="text-[11px] truncate">
                                            • {{ $d->product->name }} ({{ number_format($d->qty, 2) }} {{ $d->product->unit }})
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3 uppercase font-bold text-[10px]">
                                <span class="px-2 py-0.5 rounded {{ $t->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $t->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-medium">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                            <td class="p-3 text-right text-amber-600">
                                {{ $t->discount > 0 ? '- Rp ' . number_format($t->discount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-3 text-right font-black text-slate-900">
                                Rp {{ number_format($t->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold underline">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                Tidak ada transaksi pada rentang filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
