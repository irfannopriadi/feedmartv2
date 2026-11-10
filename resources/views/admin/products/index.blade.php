@extends('layouts.app')

@section('title', 'Master Produk Pakan - Feedmart')
@section('page_title', 'Master Data Produk & Pakan Ternak')

@section('content')
<div class="space-y-5">
    <!-- Action Bar & Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.products.index') }}" class="w-full md:flex-1 grid grid-cols-1 sm:grid-cols-4 gap-2">
            <!-- Search Keyword -->
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / SKU / barcode..."
                class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500">

            <!-- Category Filter -->
            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                <option value="">Semua Kategori Hewan</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Stock Status Filter -->
            <select name="stock_status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800">
                <option value="">Semua Status Stok</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Menipis (<= Min)</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>

            <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition">
                Filter
            </button>
        </form>

        <div class="w-full md:w-auto flex items-center gap-2">
            <a href="{{ route('admin.products.create') }}" class="w-full md:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                <span>+ Tambah Produk Pakan</span>
            </a>
        </div>
    </div>

    <!-- Product Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition {{ ! $p->is_active ? 'opacity-60 bg-slate-50/50' : '' }}">
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 block text-sm">{{ $p->name }}</span>
                                <div class="flex items-center gap-2 mt-0.5 font-mono text-[10px] text-slate-400">
                                    <span>SKU: {{ $p->sku }}</span>
                                    @if($p->barcode)
                                        <span>• Barcode: {{ $p->barcode }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px] block w-fit">
                                    {{ $p->animalCategory?->name ?? 'Umum' }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-1">
                                    {{ $p->supplier?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                @if($p->is_repack)
                                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-bold text-[10px] block w-fit border border-purple-200">
                                        Eceran ({{ $p->unit }})
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-1">
                                        Dari 1 {{ $p->parentProduct?->unit ?? 'Sak' }} = {{ number_format($p->conversion_ratio, 0) }} {{ $p->unit }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold text-[10px] block w-fit border border-blue-200">
                                        {{ strtoupper($p->unit) }} (Karung/Grosir)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-medium text-slate-700">
                                Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-right font-bold text-emerald-700">
                                Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 text-right">
                                <span class="font-black text-sm {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock_alert ? 'text-amber-600' : 'text-slate-900') }}">
                                    {{ number_format($p->stock, 2, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-semibold">{{ $p->unit }}</span>
                                <span class="block text-[9px] text-slate-400">Min: {{ number_format($p->min_stock_alert, 0) }}</span>
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Edit Data">
                                        ✏️
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 {{ $p->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} rounded-lg transition" title="{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            {{ $p->is_active ? '⏸' : '▶' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                Tidak ada data pakan ternak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
