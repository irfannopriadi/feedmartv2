@extends('layouts.app')

@section('title', 'Dashboard - Feedmart POS & Inventaris')
@section('page_title', 'Ringkasan Eksekutif & Inventaris')

@section('content')
<div class="space-y-6">
    <!-- Top Financial Metrics (Neo-Brutalist Minimalism) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Hari Ini -->
        <div class="neo-box p-5 relative overflow-hidden flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-black text-zinc-500 uppercase tracking-widest block">Omzet Hari Ini</span>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-black text-black font-mono">
                        Rp {{ number_format($todaySales, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs text-zinc-600 mt-1 font-bold">Dari {{ $todayCount }} transaksi kasir</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-lg bg-emerald-300 border-2 border-black flex items-center justify-center text-lg font-black shadow-[2px_2px_0px_#000]">
                    💰
                </div>
            </div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="neo-box p-5 relative overflow-hidden flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-black text-zinc-500 uppercase tracking-widest block">Omzet Bulan Ini</span>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-black text-black font-mono">
                        Rp {{ number_format($monthSales, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs text-zinc-600 mt-1 font-bold">Periode {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-lg bg-sky-300 border-2 border-black flex items-center justify-center text-lg font-black shadow-[2px_2px_0px_#000]">
                    📈
                </div>
            </div>
        </div>

        <!-- Laba Kotor Bulan Ini -->
        <div class="neo-box p-5 relative overflow-hidden flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-black text-zinc-500 uppercase tracking-widest block">Laba Kotor (Gross Profit)</span>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-black text-black font-mono">
                        Rp {{ number_format($monthGrossProfit, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs text-zinc-600 mt-1 font-bold">Margin riil setelah HPP pakan</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-lg bg-yellow-300 border-2 border-black flex items-center justify-center text-lg font-black shadow-[2px_2px_0px_#000]">
                    🏷
                </div>
            </div>
        </div>

        <!-- Status Peringatan -->
        <div class="neo-box p-5 relative overflow-hidden flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-black text-zinc-500 uppercase tracking-widest block">Peringatan Inventaris</span>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="neo-badge bg-amber-200 text-black">
                        {{ $lowStockProducts->count() }} Stok Tipis
                    </span>
                    <span class="neo-badge bg-rose-300 text-black">
                        {{ $nearExpiryBatches->count() }} Kedaluwarsa
                    </span>
                </div>
                <p class="text-xs text-zinc-600 mt-2 font-bold">Perlu tindakan restock segera</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-lg bg-rose-200 border-2 border-black flex items-center justify-center text-lg font-black shadow-[2px_2px_0px_#000]">
                    ⚠️
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Sections: Low Stock & Expiring Batches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Alert -->
        <div class="neo-box overflow-hidden">
            <div class="p-4 bg-amber-200 border-b-2 border-black flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📦</span>
                    <h3 class="font-black text-sm text-black uppercase tracking-wide">Peringatan Stok Pakan Menipis</h3>
                </div>
                <a href="{{ route('admin.stock-in.index') }}" class="neo-btn px-2.5 py-1 bg-black text-white hover:bg-zinc-800 text-xs font-black">
                    + Restock
                </a>
            </div>
            <div class="divide-y-2 divide-black/10">
                @forelse($lowStockProducts as $lp)
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-zinc-50 transition">
                        <div>
                            <span class="font-black text-black block">{{ $lp->name }}</span>
                            <span class="text-[10px] font-mono text-zinc-600">{{ $lp->animalCategory?->name ?? 'Umum' }} | SKU: {{ $lp->sku }}</span>
                        </div>
                        <div class="text-right">
                            <span class="neo-badge bg-rose-300 text-black font-mono">
                                Sisa: {{ number_format($lp->stock, 2, ',', '.') }} {{ $lp->unit }}
                            </span>
                            <span class="block text-[10px] text-zinc-500 font-mono mt-1 font-bold">Min: {{ number_format($lp->min_stock_alert, 0) }} {{ $lp->unit }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs font-bold text-zinc-500">
                        ✓ Semua stok barang dalam kondisi aman.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Near Expiry Batches Alert -->
        <div class="neo-box overflow-hidden">
            <div class="p-4 bg-rose-200 border-b-2 border-black flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⏳</span>
                    <h3 class="font-black text-sm text-black uppercase tracking-wide">Peringatan Kedaluwarsa (Batch FIFO)</h3>
                </div>
                <span class="neo-badge bg-white text-black font-mono">Prioritas Jual</span>
            </div>
            <div class="divide-y-2 divide-black/10">
                @forelse($nearExpiryBatches as $batch)
                    @php
                        $daysLeft = $batch->days_left;
                    @endphp
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-zinc-50 transition">
                        <div>
                            <span class="font-black text-black block">{{ $batch->product->name }}</span>
                            <span class="text-[10px] font-mono text-zinc-600">Batch: {{ $batch->batch_number ?: '-' }} | Sisa: {{ number_format($batch->stock_in_batch, 2) }} {{ $batch->product->unit }}</span>
                        </div>
                        <div class="text-right">
                            <span class="neo-badge {{ $daysLeft < 0 ? 'bg-black text-white' : ($daysLeft <= 14 ? 'bg-rose-400 text-white' : 'bg-yellow-300 text-black') }}">
                                @if($daysLeft < 0)
                                    KEDALUWARSA
                                @elseif($daysLeft === 0)
                                    HARI INI
                                @else
                                    {{ $daysLeft }} HARI LAGI
                                @endif
                            </span>
                            <span class="block text-[10px] font-mono text-zinc-600 mt-1 font-bold">Exp: {{ $batch->expired_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs font-bold text-zinc-500">
                        ✓ Tidak ada pakan yang mendekati masa kedaluwarsa.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Transactions & Animal Category Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions (2 cols) -->
        <div class="lg:col-span-2 neo-box overflow-hidden">
            <div class="p-4 bg-black text-white border-b-2 border-black flex items-center justify-between">
                <h3 class="font-black text-sm tracking-wide uppercase">Transaksi Penjualan Terkini</h3>
                <a href="{{ route('admin.reports.sales') }}" class="neo-btn px-2.5 py-1 bg-yellow-300 text-black hover:bg-yellow-200 text-xs font-black">
                    Lihat Semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-black">
                    <thead class="bg-zinc-100 text-black font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3">Waktu</th>
                            <th class="p-3">No. Invoice</th>
                            <th class="p-3">Kasir</th>
                            <th class="p-3">Metode</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-center">Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-black/10">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-yellow-50/60 transition">
                                <td class="p-3 font-mono text-zinc-600">{{ $tx->created_at->format('H:i') }}</td>
                                <td class="p-3 font-black text-black font-mono">{{ $tx->invoice_number }}</td>
                                <td class="p-3 font-bold">{{ $tx->user->name }}</td>
                                <td class="p-3">
                                    <span class="neo-badge {{ $tx->payment_method === 'cash' ? 'bg-emerald-300 text-black' : 'bg-sky-300 text-black' }}">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-black font-mono text-black">
                                    Rp {{ number_format($tx->final_amount, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank" class="neo-btn px-2 py-0.5 bg-white text-black text-[10px] font-black">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-xs font-bold text-zinc-500">Belum ada transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales by Animal Category (1 col) -->
        <div class="neo-box p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-4 border-b-2 border-black">
                    <h3 class="font-black text-sm text-black uppercase tracking-wide">Pakan per Kategori</h3>
                    <span class="neo-badge bg-zinc-100 text-black">Bulan Ini</span>
                </div>
                <div class="space-y-3.5">
                    @foreach($categorySales as $c)
                        <div>
                            <div class="flex items-center justify-between text-xs font-black mb-1">
                                <span class="text-black">{{ $c->name }}</span>
                                <span class="text-black font-mono">Rp {{ number_format($c->total_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-zinc-100 border-2 border-black h-3 overflow-hidden shadow-[1px_1px_0px_#000]">
                                @php
                                    $percent = $monthSales > 0 ? min(100, ($c->total_sales / $monthSales) * 100) : 0;
                                @endphp
                                <div class="bg-emerald-400 border-r-2 border-black h-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t-2 border-black text-center">
                <a href="{{ route('pos.index') }}" class="neo-btn w-full py-2.5 px-4 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                    ⚡ Buka Kasir POS Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
