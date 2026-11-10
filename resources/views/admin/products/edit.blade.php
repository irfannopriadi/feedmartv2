@extends('layouts.app')

@section('title', 'Edit Produk Pakan - Feedmart')
@section('page_title', 'Edit Produk: ' . $product->name)

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ isRepack: {{ old('is_repack', $product->is_repack) ? 'true' : 'false' }} }">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Repack Switcher Alert -->
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-purple-900 uppercase tracking-wider">Jenis Produk Eceran / Repack Karung?</h4>
                    <p class="text-xs text-purple-700 mt-0.5">Aktifkan opsi ini jika produk ini merupakan hasil eceran/kiloan dari karung pakan induk.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_repack" value="1" x-model="isRepack" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>

            <!-- Repack Settings (Conditional) -->
            <div x-show="isRepack" x-cloak class="p-4 rounded-xl bg-slate-50 border border-purple-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Pilih Karung Pakan Induk <span class="text-rose-500">*</span>
                    </label>
                    <select name="parent_product_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800">
                        <option value="">-- Pilih Karung Sak Induk --</option>
                        @foreach($parentCandidates as $pc)
                            <option value="{{ $pc->id }}" {{ old('parent_product_id', $product->parent_product_id) == $pc->id ? 'selected' : '' }}>
                                {{ $pc->name }} (Stok: {{ $pc->stock }} {{ $pc->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Rasio Konversi (1 Sak = N Satuan Eceran) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.1" name="conversion_ratio" value="{{ old('conversion_ratio', $product->conversion_ratio) }}"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800">
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SKU Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Barcode (Opsional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Produk Pakan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Hewan</label>
                    <select name="animal_category_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-500">
                        <option value="">-- Kategori Hewan --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('animal_category_id', $product->animal_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier Distributor</label>
                    <select name="supplier_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-500">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batas Minimum Alert Stok</label>
                    <input type="number" step="0.01" min="0" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>
            </div>

            <!-- Pricing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli / HPP (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" step="100" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Jual Kasir (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" step="100" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-emerald-700 focus:bg-white focus:border-emerald-500">
                </div>
            </div>

            <!-- Active Status -->
            <div class="pt-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-0">
                    <span class="text-xs font-bold text-slate-700">Produk ini aktif dan dapat dijual di kasir POS</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                    Perbarui Data Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
