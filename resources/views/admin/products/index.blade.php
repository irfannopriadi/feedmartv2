@extends('layouts.app')

@section('title', 'Master Produk Pakan - Feedmart')
@section('page_title', 'Master Data Produk & Pakan Ternak')

@section('content')
<div class="space-y-5">
    <!-- Action Bar & Filters (Full Light Glassmorphism) -->
    <div class="glass-box p-4 flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.products.index') }}" class="w-full md:flex-1 grid grid-cols-1 sm:grid-cols-4 gap-2.5">
            <!-- Search Keyword -->
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / SKU / barcode..."
                class="glass-input px-3 py-2 text-xs">

            <!-- Category Filter -->
            <select name="category_id" onchange="this.form.submit()" class="glass-input px-3 py-2 text-xs">
                <option value="">Semua Kategori Hewan</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Stock Status Filter -->
            <select name="stock_status" onchange="this.form.submit()" class="glass-input px-3 py-2 text-xs">
                <option value="">Semua Status Stok</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Menipis (&lt;= Min)</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>

            <button type="submit" class="glass-btn px-4 py-2 bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 text-xs font-bold">
                Filter
            </button>
        </form>

        <div class="w-full md:w-auto flex items-center gap-2">
            <a href="{{ route('admin.barcode.index') }}" class="glass-btn w-full md:w-auto px-4 py-2 bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 text-xs font-bold">
                <span>🏷️ Cetak Barcode</span>
            </a>
            <a href="{{ route('admin.products.create') }}" class="glass-btn w-full md:w-auto px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold shadow-md shadow-emerald-500/20">
                <span>+ Tambah Produk Pakan</span>
            </a>
        </div>
    </div>

    <!-- Product Table -->
    <div class="glass-box overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5">Produk Pakan</th>
                        <th class="p-3.5">Kategori / Supplier</th>
                        <th class="p-3.5">Satuan &amp; Jenis</th>
                        <th class="p-3.5 text-right">Harga Beli (HPP)</th>
                        <th class="p-3.5 text-right">Harga Jual</th>
                        <th class="p-3.5 text-right">Stok Fisik</th>
                        <th class="p-3.5 text-center">Status</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($products as $p)
                        <tr class="hover:bg-white/60 transition {{ ! $p->is_active ? 'opacity-60 bg-slate-100/40' : '' }}">
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 block text-sm">{{ $p->name }}</span>
                                <div class="flex items-center gap-2 mt-0.5 font-mono text-[10px] text-slate-500">
                                    <span>SKU: {{ $p->sku }}</span>
                                    @if($p->barcode)
                                        <span>• Barcode: {{ $p->barcode }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="glass-badge bg-white/75 text-slate-700">
                                    {{ $p->animalCategory?->name ?? 'Umum' }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-500 block mt-1">
                                    {{ $p->supplier?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                @if($p->is_repack)
                                    <span class="glass-badge bg-violet-500/15 text-violet-800 border-violet-400/35">
                                        Eceran ({{ $p->unit }})
                                    </span>
                                    <span class="text-[10px] font-mono font-semibold text-violet-800 block mt-1">
                                        1 {{ $p->parentProduct?->unit ?? 'Sak' }} = {{ number_format($p->conversion_ratio, 0) }} {{ $p->unit }}
                                    </span>
                                @else
                                    <span class="glass-badge bg-sky-500/15 text-sky-800 border-sky-400/35">
                                        {{ strtoupper($p->unit) }} (Karung)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-mono font-semibold text-slate-600">
                                Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-right font-mono font-extrabold text-slate-900 text-sm">
                                Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-right">
                                <span class="font-extrabold font-mono text-sm {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock_alert ? 'text-amber-600' : 'text-slate-900') }}">
                                    {{ number_format($p->stock, 2, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-500">{{ $p->unit }}</span>
                                <span class="block text-[9px] font-mono text-slate-400 font-semibold">Min: {{ number_format($p->min_stock_alert, 0) }}</span>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="glass-badge {{ $p->is_active ? 'bg-emerald-500/15 text-emerald-800 border-emerald-400/35' : 'bg-slate-200/70 text-slate-600 border-slate-300/60' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.barcode.index', ['product_id' => $p->id]) }}" class="glass-btn p-1.5 bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-900 border-cyan-400/35 text-xs" title="Cetak Barcode">
                                        🏷️
                                    </a>
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="glass-btn p-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-900 border-amber-400/35 text-xs" title="Edit Data">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="glass-btn p-1.5 {{ $p->is_active ? 'bg-rose-500/15 hover:bg-rose-500/25 text-rose-700 border-rose-400/35' : 'bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-700 border-emerald-400/35' }} text-xs" title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            {{ $p->is_active ? '⏸' : '▶' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-xs font-semibold text-slate-500">
                                Tidak ada data pakan ternak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
