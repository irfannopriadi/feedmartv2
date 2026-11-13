@extends('layouts.app')

@section('title', 'RETUR BARANG')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="neo-box bg-white p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">🔄</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">RETUR BARANG (KONSUMEN & SUPPLIER)</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">Pencatatan barang kembali dari pembeli atau retur karung cacat ke distributor</p>
        </div>

        <div>
            <a href="{{ route('admin.returns.create') }}" class="neo-btn bg-yellow-300 text-black px-4 py-2 text-xs flex items-center gap-2">
                <span>➕</span> CATAT RETUR BARANG
            </a>
        </div>
    </div>

    <!-- KPI Ringkasan Retur -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="neo-box bg-amber-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-amber-900">Total Retur Penjualan (Konsumen)</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                {{ $saleReturnsCount }} Kasus
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Stok pakan masuk kembali ke gudang</span>
        </div>

        <div class="neo-box bg-rose-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-rose-900">Total Retur Pembelian (Ke Pabrik)</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                {{ $purchaseReturnsCount }} Kasus
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Karung cacat/bocor dikembalikan</span>
        </div>

        <div class="neo-box bg-cyan-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-cyan-900">Total Pengembalian Dana / Potong Kasbon</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                Rp {{ number_format($totalRefundGiven, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Refund dana kepada pembeli</span>
        </div>
    </div>

    @if(session('success'))
        <div class="neo-box bg-emerald-400 p-4 font-black text-black flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="neo-box bg-rose-400 p-4 font-black text-black flex items-center gap-3">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Jenis Retur -->
    <div class="neo-box bg-white p-4">
        <form action="{{ route('admin.returns.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="w-full md:w-64">
                <select name="type" class="neo-input w-full text-xs font-bold uppercase">
                    <option value="">SEMUA JENIS RETUR</option>
                    <option value="sale_return" {{ request('type') === 'sale_return' ? 'selected' : '' }}>RETUR PENJUALAN (DARI PEMBELI)</option>
                    <option value="purchase_return" {{ request('type') === 'purchase_return' ? 'selected' : '' }}>RETUR PEMBELIAN (KE SUPPLIER)</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="neo-btn bg-black text-white px-5 py-2 text-xs font-black uppercase">
                    FILTER
                </button>
                @if(request()->has('type'))
                    <a href="{{ route('admin.returns.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Retur -->
    <div class="neo-box bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-black bg-yellow-200 text-xs font-black uppercase">
                        <th class="p-4 border-r-2 border-black w-12 text-center">NO</th>
                        <th class="p-4 border-r-2 border-black">NO RETUR / TGL</th>
                        <th class="p-4 border-r-2 border-black text-center">JENIS</th>
                        <th class="p-4 border-r-2 border-black">PRODUK PAKAN</th>
                        <th class="p-4 border-r-2 border-black">PIHAK TERKAIT</th>
                        <th class="p-4 border-r-2 border-black text-right">JUMLAH</th>
                        <th class="p-4 border-r-2 border-black">ALASAN</th>
                        <th class="p-4 text-right">REFUND</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black text-xs font-bold">
                    @forelse($returns as $index => $r)
                        <tr class="hover:bg-amber-50 transition">
                            <td class="p-4 border-r-2 border-black text-center text-gray-500">
                                {{ $returns->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r-2 border-black font-mono">
                                <div class="font-black">{{ $r->return_number }}</div>
                                <div class="text-[10px] text-gray-500">{{ $r->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-4 border-r-2 border-black text-center">
                                @if($r->type === 'sale_return')
                                    <span class="neo-badge bg-emerald-200 text-emerald-900">RETUR PENJUALAN</span>
                                @else
                                    <span class="neo-badge bg-purple-200 text-purple-900">RETUR PEMBELIAN</span>
                                @endif
                            </td>
                            <td class="p-4 border-r-2 border-black font-black">
                                {{ $r->product->name }}
                                @if($r->batch)
                                    <span class="block text-[10px] font-mono text-gray-500 font-normal">
                                        Batch: {{ $r->batch->batch_number ?? '-' }} (Exp: {{ $r->batch->expired_date ? \Carbon\Carbon::parse($r->batch->expired_date)->format('d/m/y') : '-' }})
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 border-r-2 border-black">
                                @if($r->type === 'sale_return')
                                    <span>👤 {{ $r->customer->name ?? 'Pelanggan Umum' }}</span>
                                @else
                                    <span>🏭 {{ $r->supplier->name ?? 'Supplier' }}</span>
                                @endif
                            </td>
                            <td class="p-4 border-r-2 border-black text-right font-mono font-black">
                                {{ number_format($r->qty, 2, ',', '.') }} {{ $r->product->unit }}
                            </td>
                            <td class="p-4 border-r-2 border-black text-gray-700">
                                <div class="font-bold text-black">{{ $r->reason }}</div>
                                @if($r->notes)
                                    <div class="text-[10px] text-gray-500 italic">{{ $r->notes }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-right font-mono font-black text-rose-600">
                                @if($r->refund_amount > 0)
                                    Rp {{ number_format($r->refund_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500 uppercase font-black">
                                Belum ada riwayat retur barang yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4 border-t-2 border-black bg-gray-50">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
