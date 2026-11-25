@extends('layouts.app')

@section('title', 'RETUR BARANG')
@section('page_title', 'Retur Barang (Konsumen & Supplier)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">🔄</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">RETUR BARANG (KONSUMEN &amp; SUPPLIER)</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Pencatatan barang kembali dari pembeli atau retur karung cacat ke distributor</p>
        </div>

        <div>
            <a href="{{ route('admin.returns.create') }}" class="glass-btn bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 px-4 py-2.5 text-xs font-bold flex items-center gap-2">
                <span>➕</span> CATAT RETUR BARANG
            </a>
        </div>
    </div>

    <!-- KPI Ringkasan Retur -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-box bg-amber-400/15 border-amber-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-900">Total Retur Penjualan (Konsumen)</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                {{ $saleReturnsCount }} Kasus
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Stok pakan masuk kembali ke gudang</span>
        </div>

        <div class="glass-box bg-rose-500/12 border-rose-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-800">Total Retur Pembelian (Ke Pabrik)</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                {{ $purchaseReturnsCount }} Kasus
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Karung cacat/bocor dikembalikan</span>
        </div>

        <div class="glass-box bg-cyan-500/12 border-cyan-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-cyan-800">Total Pengembalian Dana / Potong Kasbon</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                Rp {{ number_format($totalRefundGiven, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Refund dana kepada pembeli</span>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-box bg-emerald-500/15 border-emerald-400/35 p-4 font-bold text-emerald-950 flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-box bg-rose-500/15 border-rose-400/35 p-4 font-bold text-rose-950 flex items-center gap-3">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Jenis Retur -->
    <div class="glass-box p-4">
        <form action="{{ route('admin.returns.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="w-full md:w-64">
                <select name="type" class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
                    <option value="">SEMUA JENIS RETUR</option>
                    <option value="sale_return" {{ request('type') === 'sale_return' ? 'selected' : '' }}>RETUR PENJUALAN (DARI PEMBELI)</option>
                    <option value="purchase_return" {{ request('type') === 'purchase_return' ? 'selected' : '' }}>RETUR PEMBELIAN (KE SUPPLIER)</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 px-5 py-2 text-xs font-bold uppercase">
                    FILTER
                </button>
                @if(request()->has('type'))
                    <a href="{{ route('admin.returns.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Retur -->
    <div class="glass-box overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/70 bg-white/55 text-slate-600 text-xs font-bold uppercase">
                        <th class="p-4 border-r border-slate-200/50 w-12 text-center">NO</th>
                        <th class="p-4 border-r border-slate-200/50">NO RETUR / TGL</th>
                        <th class="p-4 border-r border-slate-200/50 text-center">JENIS</th>
                        <th class="p-4 border-r border-slate-200/50">PRODUK PAKAN</th>
                        <th class="p-4 border-r border-slate-200/50">PIHAK TERKAIT</th>
                        <th class="p-4 border-r border-slate-200/50 text-right">JUMLAH</th>
                        <th class="p-4 border-r border-slate-200/50">ALASAN</th>
                        <th class="p-4 text-right">REFUND</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60 text-xs font-semibold text-slate-800">
                    @forelse($returns as $index => $r)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-4 border-r border-slate-200/50 text-center text-slate-400">
                                {{ $returns->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-mono">
                                <div class="font-bold text-slate-900">{{ $r->return_number }}</div>
                                <div class="text-[10px] text-slate-500">{{ $r->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-center">
                                @if($r->type === 'sale_return')
                                    <span class="glass-badge bg-emerald-500/15 text-emerald-800 border-emerald-400/35">RETUR PENJUALAN</span>
                                @else
                                    <span class="glass-badge bg-violet-500/15 text-violet-800 border-violet-400/35">RETUR PEMBELIAN</span>
                                @endif
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-bold text-slate-900">
                                {{ $r->product->name }}
                                @if($r->batch)
                                    <span class="block text-[10px] font-mono text-slate-500 font-normal">
                                        Batch: {{ $r->batch->batch_number ?? '-' }} (Exp: {{ $r->batch->expired_date ? \Carbon\Carbon::parse($r->batch->expired_date)->format('d/m/y') : '-' }})
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-slate-700">
                                @if($r->type === 'sale_return')
                                    <span>👤 {{ $r->customer->name ?? 'Pelanggan Umum' }}</span>
                                @else
                                    <span>🏭 {{ $r->supplier->name ?? 'Supplier' }}</span>
                                @endif
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-right font-mono font-extrabold text-slate-900">
                                {{ number_format($r->qty, 2, ',', '.') }} {{ $r->product->unit }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-slate-600">
                                <div class="font-semibold text-slate-900">{{ $r->reason }}</div>
                                @if($r->notes)
                                    <div class="text-[10px] text-slate-500 italic">{{ $r->notes }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-right font-mono font-extrabold text-rose-600">
                                @if($r->refund_amount > 0)
                                    Rp {{ number_format($r->refund_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 uppercase font-bold">
                                Belum ada riwayat retur barang yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
