@extends('layouts.app')

@section('title', 'Dashboard - Feedmart POS & Inventaris')
@section('page_title', 'Ringkasan Eksekutif & Inventaris')

@section('content')
<div class="space-y-6">
    <!-- Top Financial Metrics (Full Light Glassmorphism) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Hari Ini -->
        <div class="glass-box p-5 relative overflow-hidden flex flex-col justify-between hover:-translate-y-0.5 transition-all">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block">Omzet Hari Ini</span>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono">
                        Rp {{ number_format($todaySales, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 font-medium">Dari {{ $todayCount }} transaksi kasir</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-400/35 flex items-center justify-center text-lg shadow-xs">
                    💰
                </div>
            </div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="glass-box p-5 relative overflow-hidden flex flex-col justify-between hover:-translate-y-0.5 transition-all">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block">Omzet Bulan Ini</span>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono">
                        Rp {{ number_format($monthSales, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 font-medium">Periode {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-400/35 flex items-center justify-center text-lg shadow-xs">
                    📈
                </div>
            </div>
        </div>

        <!-- Laba Kotor Bulan Ini -->
        <div class="glass-box p-5 relative overflow-hidden flex flex-col justify-between hover:-translate-y-0.5 transition-all">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block">Laba Kotor (Gross Profit)</span>
                <div class="flex items-baseline gap-2 mt-1.5">
                    <span class="text-2xl font-extrabold text-slate-900 font-mono">
                        Rp {{ number_format($monthGrossProfit, 0, ',', '.') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 font-medium">Margin riil setelah HPP pakan</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-lg shadow-xs">
                    🏷
                </div>
            </div>
        </div>

        <!-- Status Peringatan -->
        <div class="glass-box p-5 relative overflow-hidden flex flex-col justify-between hover:-translate-y-0.5 transition-all">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block">Peringatan Inventaris</span>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="glass-badge bg-amber-400/20 text-amber-900 border-amber-400/35">
                        {{ $lowStockProducts->count() }} Stok Tipis
                    </span>
                    <span class="glass-badge bg-rose-500/15 text-rose-800 border-rose-400/35">
                        {{ $nearExpiryBatches->count() }} Kedaluwarsa
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2 font-medium">Perlu tindakan restock segera</p>
            </div>
            <div class="mt-3 flex justify-end">
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-400/35 flex items-center justify-center text-lg shadow-xs">
                    ⚠️
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Launchpad -->
    <div class="glass-box p-4 flex flex-wrap items-center justify-between gap-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
            <span>⚡</span> AKSI CEPAT SISTEM:
        </span>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.barcode.index') }}" class="glass-btn px-3.5 py-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-950 border-amber-400/35 text-xs font-bold">
                🏷️ Cetak Barcode
            </a>
            <a href="{{ route('admin.customers.index') }}" class="glass-btn px-3.5 py-1.5 bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-950 border-cyan-400/35 text-xs font-bold">
                👥 Pelanggan &amp; Kasbon
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="glass-btn px-3.5 py-1.5 bg-rose-500/15 hover:bg-rose-500/25 text-rose-950 border-rose-400/35 text-xs font-bold">
                💸 Kas Keluar
            </a>
            <a href="{{ route('admin.returns.create') }}" class="glass-btn px-3.5 py-1.5 bg-violet-500/15 hover:bg-violet-500/25 text-violet-950 border-violet-400/35 text-xs font-bold">
                🔄 Catat Retur
            </a>
            <a href="{{ route('admin.backup.download') }}" class="glass-btn px-3.5 py-1.5 bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-950 border-emerald-400/35 text-xs font-bold">
                💾 Backup DB
            </a>
            <a href="{{ route('admin.settings.index') }}" class="glass-btn px-3.5 py-1.5 bg-white/75 hover:bg-white text-slate-700 text-xs font-bold">
                ⚙️ Pengaturan
            </a>
        </div>
    </div>

    <!-- Alert Sections: Low Stock & Expiring Batches -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Alert -->
        <div class="glass-box overflow-hidden">
            <div class="p-4 bg-amber-400/15 border-b border-white/70 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📦</span>
                    <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Peringatan Stok Pakan Menipis</h3>
                </div>
                <a href="{{ route('admin.stock-in.index') }}" class="glass-btn px-3 py-1 bg-white/80 hover:bg-white text-slate-800 text-xs font-bold">
                    + Restock
                </a>
            </div>
            <div class="divide-y divide-slate-200/60">
                @forelse($lowStockProducts as $lp)
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-white/50 transition">
                        <div>
                            <span class="font-bold text-slate-900 block">{{ $lp->name }}</span>
                            <span class="text-[10px] font-mono text-slate-500">{{ $lp->animalCategory?->name ?? 'Umum' }} | SKU: {{ $lp->sku }}</span>
                        </div>
                        <div class="text-right">
                            <span class="glass-badge bg-rose-500/15 text-rose-800 border-rose-400/35 font-mono">
                                Sisa: {{ number_format($lp->stock, 2, ',', '.') }} {{ $lp->unit }}
                            </span>
                            <span class="block text-[10px] text-slate-500 font-mono mt-1 font-semibold">Min: {{ number_format($lp->min_stock_alert, 0) }} {{ $lp->unit }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs font-semibold text-slate-500">
                        ✓ Semua stok barang dalam kondisi aman.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Near Expiry Batches Alert -->
        <div class="glass-box overflow-hidden">
            <div class="p-4 bg-rose-500/12 border-b border-white/70 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⏳</span>
                    <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Peringatan Kedaluwarsa (Batch FIFO)</h3>
                </div>
                <span class="glass-badge bg-white/80 text-slate-700 font-mono">Prioritas Jual</span>
            </div>
            <div class="divide-y divide-slate-200/60">
                @forelse($nearExpiryBatches as $batch)
                    @php
                        $daysLeft = $batch->days_left;
                    @endphp
                    <div class="p-3.5 flex items-center justify-between text-xs hover:bg-white/50 transition">
                        <div>
                            <span class="font-bold text-slate-900 block">{{ $batch->product->name }}</span>
                            <span class="text-[10px] font-mono text-slate-500">Batch: {{ $batch->batch_number ?: '-' }} | Sisa: {{ number_format($batch->stock_in_batch, 2) }} {{ $batch->product->unit }}</span>
                        </div>
                        <div class="text-right">
                            <span class="glass-badge {{ $daysLeft < 0 ? 'bg-slate-800 text-white border-slate-700' : ($daysLeft <= 14 ? 'bg-rose-500 text-white border-rose-400' : 'bg-amber-400/25 text-amber-950 border-amber-400/40') }}">
                                @if($daysLeft < 0)
                                    KEDALUWARSA
                                @elseif($daysLeft === 0)
                                    HARI INI
                                @else
                                    {{ $daysLeft }} HARI LAGI
                                @endif
                            </span>
                            <span class="block text-[10px] font-mono text-slate-500 mt-1 font-semibold">Exp: {{ $batch->expired_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs font-semibold text-slate-500">
                        ✓ Tidak ada pakan yang mendekati masa kedaluwarsa.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Transactions & Animal Category Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions (2 cols) -->
        <div class="lg:col-span-2 glass-box overflow-hidden">
            <div class="p-4 bg-white/45 border-b border-white/70 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900 tracking-wide uppercase">Transaksi Penjualan Terkini</h3>
                <a href="{{ route('admin.reports.sales') }}" class="glass-btn px-3 py-1 bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 text-xs font-bold">
                    Lihat Semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-800">
                    <thead class="bg-white/50 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3">Waktu</th>
                            <th class="p-3">No. Invoice</th>
                            <th class="p-3">Kasir</th>
                            <th class="p-3">Metode</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-center">Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-white/60 transition">
                                <td class="p-3 font-mono text-slate-500">{{ $tx->created_at->format('H:i') }}</td>
                                <td class="p-3 font-bold text-slate-900 font-mono">{{ $tx->invoice_number }}</td>
                                <td class="p-3 font-medium text-slate-700">{{ $tx->user->name }}</td>
                                <td class="p-3">
                                    <span class="glass-badge {{ $tx->payment_method === 'cash' ? 'bg-emerald-500/15 text-emerald-800 border-emerald-400/35' : 'bg-sky-500/15 text-sky-800 border-sky-400/35' }}">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-extrabold font-mono text-slate-900">
                                    Rp {{ number_format($tx->final_amount, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank" class="glass-btn px-2.5 py-1 bg-white/80 hover:bg-white text-slate-700 text-[10px] font-bold">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-xs font-semibold text-slate-500">Belum ada transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales by Animal Category (1 col) -->
        <div class="glass-box p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200/70">
                    <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Pakan per Kategori</h3>
                    <span class="glass-badge bg-white/75 text-slate-600">Bulan Ini</span>
                </div>
                <div class="space-y-3.5">
                    @foreach($categorySales as $c)
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                <span class="text-slate-800">{{ $c->name }}</span>
                                <span class="text-slate-900 font-mono">Rp {{ number_format($c->total_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-white/60 border border-white/80 rounded-full h-3 overflow-hidden shadow-inner">
                                @php
                                    $percent = $monthSales > 0 ? min(100, ($c->total_sales / $monthSales) * 100) : 0;
                                @endphp
                                <div class="bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full h-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-200/70 text-center">
                <a href="{{ route('pos.index') }}" class="glass-btn w-full py-2.5 px-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20">
                    ⚡ Buka Kasir POS Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
