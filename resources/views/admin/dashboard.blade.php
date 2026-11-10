@extends('layouts.app')

@section('title', 'Dashboard - Feedmart POS & Inventaris')
@section('page_title', 'Ringkasan Eksekutif & Inventaris')

@section('content')
<div class="space-y-6">
    <!-- Top Financial Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Omzet Hari Ini</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($todaySales, 0, ',', '.') }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Dari {{ $todayCount }} transaksi kasir</p>
            <div class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                💰
            </div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Omzet Bulan Ini</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($monthSales, 0, ',', '.') }}
                </span>
            </div>
            <p class="text-xs text-emerald-600 mt-1 font-semibold">Periode {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            <div class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                📈
            </div>
        </div>

        <!-- Laba Kotor Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Laba Kotor (Gross Profit)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-2xl font-black text-emerald-600">
                    Rp {{ number_format($monthGrossProfit, 0, ',', '.') }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Margin penjualan setelah HPP</p>
            <div class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                🏷
            </div>
        </div>

        <!-- Status Peringatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Peringatan Inventaris</span>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-sm font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">
                    {{ $lowStockProducts->count() }} Stok Tipis
                </span>
                <span class="text-sm font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">
                    {{ $nearExpiryBatches->count() }} Expired
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Memerlukan restock / tindakan segera</p>
            <div class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                ⚠️
            </div>
        </div>
    </div>

    <!-- Alert Sections: Low Stock & Expiring Batches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Alert -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-base">📦</span>
                    <h3 class="font-bold text-sm text-slate-800">Peringatan Stok Pakan Menipis</h3>
                </div>
                <a href="{{ route('admin.stock-in.index') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800 underline">
                    + Restock Barang
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($lowStockProducts as $lp)
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-slate-50 transition">
                        <div>
                            <span class="font-bold text-slate-800 block">{{ $lp->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $lp->animalCategory?->name ?? 'Umum' }} | SKU: {{ $lp->sku }}</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded font-black text-rose-700 bg-rose-50 border border-rose-200">
                                Sisa: {{ number_format($lp->stock, 2, ',', '.') }} {{ $lp->unit }}
                            </span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">Min: {{ number_format($lp->min_stock_alert, 0) }} {{ $lp->unit }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        ✓ Semua stok barang dalam kondisi aman.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Near Expiry Batches Alert -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-base">⏳</span>
                    <h3 class="font-bold text-sm text-slate-800">Peringatan Masa Kedaluwarsa (Batch FIFO)</h3>
                </div>
                <span class="text-[11px] font-semibold text-rose-700">Prioritas Jual Terdekat</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($nearExpiryBatches as $batch)
                    @php
                        $daysLeft = \Carbon\Carbon::now()->diffInDays($batch->expired_date, false);
                    @endphp
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-slate-50 transition">
                        <div>
                            <span class="font-bold text-slate-800 block">{{ $batch->product->name }}</span>
                            <span class="text-[10px] text-slate-400">Batch: {{ $batch->batch_number ?: '-' }} | Sisa di Batch: {{ number_format($batch->stock_in_batch, 2) }} {{ $batch->product->unit }}</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded font-black {{ $daysLeft <= 0 ? 'bg-rose-600 text-white' : ($daysLeft <= 14 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $daysLeft <= 0 ? 'KEDALUWARSA' : $daysLeft . ' Hari Lagi' }}
                            </span>
                            <span class="block text-[10px] text-slate-500 mt-0.5 font-mono">Exp: {{ $batch->expired_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        ✓ Tidak ada pakan yang mendekati masa kedaluwarsa.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Transactions & Animal Category Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-800">Transaksi Penjualan Terkini</h3>
                <a href="{{ route('admin.reports.sales') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">Waktu</th>
                            <th class="p-3">No. Invoice</th>
                            <th class="p-3">Kasir</th>
                            <th class="p-3">Metode</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-center">Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3 text-slate-400">{{ $tx->created_at->format('H:i') }}</td>
                                <td class="p-3 font-semibold text-slate-800">{{ $tx->invoice_number }}</td>
                                <td class="p-3">{{ $tx->user->name }}</td>
                                <td class="p-3 uppercase font-bold text-[10px]">
                                    <span class="px-2 py-0.5 rounded {{ $tx->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-black text-slate-800">
                                    Rp {{ number_format($tx->final_amount, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-semibold underline">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400">Belum ada transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales by Animal Category (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <h3 class="font-bold text-sm text-slate-800 mb-3">Distribusi Pakan per Kategori</h3>
            <div class="space-y-3">
                @foreach($categorySales as $c)
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-700">{{ $c->name }}</span>
                            <span class="text-emerald-700 font-bold">Rp {{ number_format($c->total_sales, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            @php
                                $percent = $monthSales > 0 ? min(100, ($c->total_sales / $monthSales) * 100) : 0;
                            @endphp
                            <div class="bg-emerald-600 h-full rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                    ⚡ Buka Kasir POS Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
