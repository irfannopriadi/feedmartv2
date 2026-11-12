@extends('layouts.app')

@section('title', 'Laporan Penjualan & Laba Rugi - Feedmart')
@section('page_title', 'Laporan Penjualan & Laba Kotor')

@section('content')
<div class="space-y-6">
    <!-- Filter Card (Neo-Brutalist Minimalism) -->
    <div class="neo-box p-4">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-black text-black uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="neo-input px-3 py-1.5 text-xs font-bold">
            </div>

            <div>
                <label class="block text-[10px] font-black text-black uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="neo-input px-3 py-1.5 text-xs font-bold">
            </div>

            <div>
                <label class="block text-[10px] font-black text-black uppercase tracking-wider mb-1">Metode Bayar</label>
                <select name="payment_method" class="neo-input px-3 py-1.5 text-xs font-bold">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ $paymentMethod === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-black uppercase tracking-wider mb-1">Kasir</label>
                <select name="user_id" class="neo-input px-3 py-1.5 text-xs font-bold">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $userId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="neo-btn flex-1 py-2 px-3 bg-black hover:bg-zinc-800 text-white text-xs font-black shadow-[2px_2px_0px_#000]">
                    Filter
                </button>
                <a href="{{ route('admin.reports.sales.excel', request()->all()) }}" class="neo-btn py-2 px-3 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black shadow-[2px_2px_0px_#000]" title="Unduh File Excel">
                    <span>📊 Excel</span>
                </a>
                <a href="{{ route('admin.reports.sales.pdf', request()->all()) }}" target="_blank" class="neo-btn py-2 px-3 bg-rose-300 hover:bg-rose-200 text-black text-xs font-black shadow-[2px_2px_0px_#000]" title="Cetak / Simpan PDF">
                    <span>📄 PDF</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="neo-box p-4 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Total Omzet Penjualan</span>
                <span class="text-xl font-black font-mono text-black mt-1 block">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
            </div>
            <span class="neo-badge bg-zinc-100 text-black mt-2 w-fit">{{ $totalTransactionsCount }} Transaksi</span>
        </div>

        <div class="neo-box p-4 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Total HPP Terjual</span>
                <span class="text-xl font-black font-mono text-zinc-700 mt-1 block">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
            </div>
            <span class="neo-badge bg-zinc-100 text-black mt-2 w-fit">Modal Pokok Pakan</span>
        </div>

        <div class="neo-box p-4 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Laba Kotor (Gross Profit)</span>
                <span class="text-xl font-black font-mono text-emerald-800 mt-1 block">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
            </div>
            <span class="neo-badge bg-emerald-300 text-black mt-2 w-fit">Margin: {{ number_format($profitMargin, 1) }}%</span>
        </div>

        <div class="neo-box p-4 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Total Diskon</span>
                <span class="text-xl font-black font-mono text-amber-700 mt-1 block">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
            </div>
            <span class="neo-badge bg-yellow-300 text-black mt-2 w-fit">Potongan Kasir</span>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="neo-box overflow-hidden">
        <div class="p-4 bg-black text-white border-b-2 border-black flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wide">Rincian Transaksi Penjualan</h3>
            <span class="neo-badge bg-yellow-300 text-black font-mono">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-zinc-100 text-black font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="p-3 font-mono text-zinc-600 whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-mono font-black text-black">{{ $t->invoice_number }}</td>
                            <td class="p-3 font-bold">{{ $t->user->name }}</td>
                            <td class="p-3 max-w-xs">
                                <div class="space-y-0.5">
                                    @foreach($t->details as $d)
                                        <div class="text-[11px] font-bold text-zinc-800 truncate">
                                            • {{ $d->product->name }} ({{ number_format($d->qty, 2) }} {{ $d->product->unit }})
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3">
                                <span class="neo-badge {{ $t->payment_method === 'cash' ? 'bg-emerald-300 text-black' : 'bg-sky-300 text-black' }}">
                                    {{ $t->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-mono font-bold">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono font-bold text-amber-700">
                                {{ $t->discount > 0 ? '- Rp ' . number_format($t->discount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-3 text-right font-mono font-black text-black text-sm">
                                Rp {{ number_format($t->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="neo-btn px-2.5 py-0.5 bg-white text-black font-black text-[10px]">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-zinc-500 font-bold">
                                Tidak ada transaksi pada rentang filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t-2 border-black bg-zinc-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
