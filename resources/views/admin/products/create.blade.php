@extends('layouts.app')

@section('title', 'Tambah Produk Pakan - Feedmart')
@section('page_title', 'Tambah Produk Pakan Baru')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ isRepack: {{ old('is_repack', 0) ? 'true' : 'false' }} }">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
            @csrf

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
                            <option value="{{ $pc->id }}" {{ old('parent_product_id') == $pc->id ? 'selected' : '' }}>
                                {{ $pc->name }} (Stok: {{ $pc->stock }} {{ $pc->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Rasio Konversi (1 Sak = N Satuan Eceran) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.1" name="conversion_ratio" value="{{ old('conversion_ratio', '50.00') }}" placeholder="Contoh: 50 untuk 50 kg"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800">
                    <span class="text-[10px] text-slate-500 mt-1 block">Contoh: 1 Sak 50kg = 50 unit pakan 1 kg</span>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SKU Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Contoh: PK-AYM-511-50KG" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Barcode (Opsional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik barcode..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Produk Pakan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Pakan Ayam Bravo 511 (50 Kg)" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Hewan</label>
                    <select name="animal_category_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-500">
                        <option value="">-- Kategori Hewan --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('animal_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier Distributor</label>
                    <select name="supplier_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-500">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'sak') }}" placeholder="sak, kg, bungkus, pcs" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batas Minimum Alert Stok</label>
                    <input type="number" step="0.01" min="0" name="min_stock_alert" value="{{ old('min_stock_alert', '5.00') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>
            </div>

            <!-- Pricing & Initial Stock -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli / HPP (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" step="100" name="cost_price" value="{{ old('cost_price', '0') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Jual Kasir (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" step="100" name="selling_price" value="{{ old('selling_price', '0') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-emerald-700 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Stok Awal Fisik</label>
                    <input type="number" min="0" step="0.01" name="stock" value="{{ old('stock', '0') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Kedaluwarsa Stok Awal (Opsional)</label>
                    <input type="date" name="initial_expired_date" value="{{ old('initial_expired_date') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-emerald-500">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                    Simpan Produk Pakan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
