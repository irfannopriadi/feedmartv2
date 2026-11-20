@extends('layouts.app')

@section('title', 'Laporan Penjualan & Laba Rugi - Feedmart')
@section('page_title', 'Laporan Penjualan & Laba Kotor')

@section('content')
<div class="space-y-6">
    <!-- Filter Card (Full Light Glassmorphism) -->
    <div class="glass-box p-4">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="glass-input px-3 py-1.5 text-xs font-semibold">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="glass-input px-3 py-1.5 text-xs font-semibold">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Metode Bayar</label>
                <select name="payment_method" class="glass-input px-3 py-1.5 text-xs font-semibold">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="transfer" {{ $paymentMethod === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kasir</label>
                <select name="user_id" class="glass-input px-3 py-1.5 text-xs font-semibold">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                        <option value="{{ $c->id }}" {{ $userId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="glass-btn flex-1 py-2 px-3 bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 text-xs font-bold">
                    Filter
                </button>
                <a href="{{ route('admin.reports.sales.excel', request()->all()) }}" class="glass-btn py-2 px-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold shadow-sm shadow-emerald-500/20" title="Unduh File Excel">
                    <span>📊 Excel</span>
                </a>
                <a href="{{ route('admin.reports.sales.pdf', request()->all()) }}" target="_blank" class="glass-btn py-2 px-3 bg-rose-500/15 hover:bg-rose-500/25 text-rose-800 border-rose-400/35 text-xs font-bold" title="Cetak / Simpan PDF">
                    <span>📄 PDF</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="glass-box p-4 flex flex-col justify-between">
            <div>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">Total Omzet Penjualan</span>
                <span class="text-base font-extrabold font-mono text-slate-900 mt-1 block">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
            </div>
            <span class="glass-badge bg-white/75 text-slate-700 mt-2 w-fit text-[10px]">{{ $totalTransactionsCount }} Transaksi</span>
        </div>

        <div class="glass-box p-4 flex flex-col justify-between">
            <div>
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest block">Total HPP Modal Pakan</span>
                <span class="text-base font-extrabold font-mono text-slate-700 mt-1 block">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
            </div>
            <span class="glass-badge bg-white/75 text-slate-700 mt-2 w-fit text-[10px]">HPP Pokok</span>
        </div>

        <div class="glass-box p-4 flex flex-col justify-between bg-emerald-500/10 border-emerald-400/35">
            <div>
                <span class="text-[9px] font-bold text-emerald-800 uppercase tracking-widest block">Laba Kotor Penjualan</span>
                <span class="text-base font-extrabold font-mono text-emerald-900 mt-1 block">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
            </div>
            <span class="glass-badge bg-emerald-500/20 text-emerald-900 border-emerald-400/40 mt-2 w-fit text-[10px]">Margin: {{ number_format($profitMargin, 1) }}%</span>
        </div>

        <div class="glass-box p-4 flex flex-col justify-between bg-rose-500/10 border-rose-400/35">
            <div>
                <span class="text-[9px] font-bold text-rose-800 uppercase tracking-widest block">Biaya Kas Operasional</span>
                <span class="text-base font-extrabold font-mono text-rose-700 mt-1 block">Rp {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</span>
            </div>
            <span class="glass-badge bg-rose-500/15 text-rose-900 border-rose-400/35 mt-2 w-fit text-[10px]">Beban Toko</span>
        </div>

        <div class="glass-box p-4 flex flex-col justify-between bg-amber-400/18 border-amber-400/40">
            <div>
                <span class="text-[9px] font-bold text-amber-900 uppercase tracking-widest block">Laba Bersih Nyata</span>
                <span class="text-base font-extrabold font-mono text-slate-900 mt-1 block">Rp {{ number_format($netProfit ?? $grossProfit, 0, ',', '.') }}</span>
            </div>
            <span class="glass-badge bg-slate-900/85 text-white border-slate-700 mt-2 w-fit text-[10px]">Laba Kotor - Beban</span>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="glass-box overflow-hidden">
        <div class="p-4 bg-white/45 border-b border-white/70 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Rincian Transaksi Penjualan</h3>
            <span class="glass-badge bg-amber-400/20 text-amber-950 border-amber-400/35 font-mono">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-3 font-mono text-slate-500 whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-mono font-bold text-slate-900">{{ $t->invoice_number }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $t->user->name }}</td>
                            <td class="p-3 max-w-xs">
                                <div class="space-y-0.5">
                                    @foreach($t->details as $d)
                                        <div class="text-[11px] font-medium text-slate-700 truncate">
                                            • {{ $d->product->name }} ({{ number_format($d->qty, 2) }} {{ $d->product->unit }})
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3">
                                <span class="glass-badge {{ $t->payment_method === 'cash' ? 'bg-emerald-500/15 text-emerald-800 border-emerald-400/35' : 'bg-sky-500/15 text-sky-800 border-sky-400/35' }}">
                                    {{ $t->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-mono font-semibold text-slate-700">Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono font-semibold text-amber-700">
                                {{ $t->discount > 0 ? '- Rp ' . number_format($t->discount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-3 text-right font-mono font-extrabold text-slate-900 text-sm">
                                Rp {{ number_format($t->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="glass-btn px-2.5 py-1 bg-white/80 hover:bg-white text-slate-700 font-bold text-[10px]">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500 font-semibold">
                                Tidak ada transaksi pada rentang filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
