@extends('layouts.app')

@section('title', 'Master Produk Pakan - Feedmart')
@section('page_title', 'Master Data Produk & Pakan Ternak')

@section('content')
<div class="space-y-5">
    <!-- Action Bar & Filters (Neo-Brutalist Minimalism) -->
    <div class="neo-box p-4 flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.products.index') }}" class="w-full md:flex-1 grid grid-cols-1 sm:grid-cols-4 gap-2.5">
            <!-- Search Keyword -->
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / SKU / barcode..."
                class="neo-input px-3 py-2 text-xs">

            <!-- Category Filter -->
            <select name="category_id" onchange="this.form.submit()" class="neo-input px-3 py-2 text-xs">
                <option value="">Semua Kategori Hewan</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Stock Status Filter -->
            <select name="stock_status" onchange="this.form.submit()" class="neo-input px-3 py-2 text-xs">
                <option value="">Semua Status Stok</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Menipis (<= Min)</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>

            <button type="submit" class="neo-btn px-4 py-2 bg-black hover:bg-zinc-800 text-white text-xs font-black">
                Filter
            </button>
        </form>

        <div class="w-full md:w-auto flex items-center gap-2">
            <a href="{{ route('admin.products.create') }}" class="neo-btn w-full md:w-auto px-4 py-2 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black shadow-[3px_3px_0px_#000]">
                <span>+ Tambah Produk Pakan</span>
            </a>
        </div>
    </div>

    <!-- Product Table -->
    <div class="neo-box overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-black text-white font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5">Produk Pakan</th>
                        <th class="p-3.5">Kategori / Supplier</th>
                        <th class="p-3.5">Satuan & Jenis</th>
                        <th class="p-3.5 text-right">Harga Beli (HPP)</th>
                        <th class="p-3.5 text-right">Harga Jual</th>
                        <th class="p-3.5 text-right">Stok Fisik</th>
                        <th class="p-3.5 text-center">Status</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($products as $p)
                        <tr class="hover:bg-yellow-50/50 transition {{ ! $p->is_active ? 'opacity-60 bg-zinc-100/60' : '' }}">
                            <td class="p-3.5">
                                <span class="font-black text-black block text-sm">{{ $p->name }}</span>
                                <div class="flex items-center gap-2 mt-0.5 font-mono text-[10px] text-zinc-600">
                                    <span>SKU: {{ $p->sku }}</span>
                                    @if($p->barcode)
                                        <span>• Barcode: {{ $p->barcode }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="neo-badge bg-zinc-100 text-black">
                                    {{ $p->animalCategory?->name ?? 'Umum' }}
                                </span>
                                <span class="text-[10px] font-bold text-zinc-500 block mt-1">
                                    {{ $p->supplier?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                @if($p->is_repack)
                                    <span class="neo-badge bg-purple-300 text-black">
                                        Eceran ({{ $p->unit }})
                                    </span>
                                    <span class="text-[10px] font-mono font-bold text-purple-900 block mt-1">
                                        1 {{ $p->parentProduct?->unit ?? 'Sak' }} = {{ number_format($p->conversion_ratio, 0) }} {{ $p->unit }}
                                    </span>
                                @else
                                    <span class="neo-badge bg-sky-300 text-black">
                                        {{ strtoupper($p->unit) }} (Karung)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-mono font-bold text-zinc-700">
                                Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-right font-mono font-black text-black text-sm">
                                Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-right">
                                <span class="font-black font-mono text-sm {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock_alert ? 'text-amber-600' : 'text-black') }}">
                                    {{ number_format($p->stock, 2, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-black text-zinc-600">{{ $p->unit }}</span>
                                <span class="block text-[9px] font-mono text-zinc-500 font-bold">Min: {{ number_format($p->min_stock_alert, 0) }}</span>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="neo-badge {{ $p->is_active ? 'bg-emerald-300 text-black' : 'bg-zinc-200 text-zinc-700' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="neo-btn p-1.5 bg-yellow-200 hover:bg-yellow-300 text-black text-xs" title="Edit Data">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="neo-btn p-1.5 {{ $p->is_active ? 'bg-rose-200 hover:bg-rose-300' : 'bg-emerald-300 hover:bg-emerald-200' }} text-black text-xs" title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            {{ $p->is_active ? '⏸' : '▶' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-xs font-bold text-zinc-500">
                                Tidak ada data pakan ternak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t-2 border-black bg-zinc-50">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
