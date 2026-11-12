@extends('layouts.app')

@section('title', 'Edit Produk Pakan - Feedmart')
@section('page_title', 'Edit Produk: ' . $product->name)

@section('content')
<div class="max-w-3xl mx-auto" x-data="productEditForm()">
    <div class="neo-box-lg p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Repack Switcher Alert -->
            <div class="p-4 rounded-xl bg-purple-100 border-2 border-black flex items-center justify-between shadow-[3px_3px_0px_#000]">
                <div>
                    <h4 class="text-xs font-black text-black uppercase tracking-wider">Produk Hasil Eceran / Repack Karung?</h4>
                    <p class="text-xs font-bold text-zinc-700 mt-0.5">Aktifkan opsi ini jika produk ini merupakan hasil eceran/kiloan dari karung pakan induk.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_repack" value="1" x-model="isRepack" class="sr-only peer">
                    <div class="w-12 h-6 bg-zinc-200 border-2 border-black rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-black after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-black after:border-black after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-400"></div>
                </label>
            </div>

            <!-- Repack Settings (Conditional) -->
            <div x-show="isRepack" x-cloak class="p-4 rounded-xl bg-purple-50 border-2 border-black grid grid-cols-1 sm:grid-cols-2 gap-4 shadow-[3px_3px_0px_#000]">
                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">
                        Pilih Karung Pakan Induk <span class="text-rose-600">*</span>
                    </label>
                    <select name="parent_product_id" class="neo-input px-3 py-2 text-xs">
                        <option value="">-- Pilih Karung Sak Induk --</option>
                        @foreach($parentCandidates as $pc)
                            <option value="{{ $pc->id }}" {{ old('parent_product_id', $product->parent_product_id) == $pc->id ? 'selected' : '' }}>
                                {{ $pc->name }} (Stok: {{ $pc->stock }} {{ $pc->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">
                        Rasio Konversi (1 Sak = N Eceran) <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.1" name="conversion_ratio" value="{{ old('conversion_ratio', $product->conversion_ratio) }}"
                        class="neo-input px-3 py-2 text-xs font-mono font-bold">
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">SKU Produk <span class="text-rose-600">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                        class="neo-input px-3.5 py-2.5 text-xs font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Kode Barcode (Opsional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                        class="neo-input px-3.5 py-2.5 text-xs font-mono font-bold">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-black text-black uppercase mb-1">Nama Produk Pakan <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="neo-input px-3.5 py-2.5 text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Kategori Hewan</label>
                    <select name="animal_category_id" class="neo-input px-3.5 py-2.5 text-xs font-bold">
                        <option value="">-- Kategori Hewan --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('animal_category_id', $product->animal_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Distributor (Pilih atau Buat Baru) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-black text-black uppercase">Supplier Distributor</label>
                        <button type="button" @click="showSupplierModal = true" class="neo-btn px-2 py-0.5 bg-yellow-300 hover:bg-yellow-200 text-black text-[10px] font-black shadow-[1.5px_1.5px_0px_#000]">
                            + Buat Baru
                        </button>
                    </div>

                    <div x-show="!showDirectInput">
                        <select name="supplier_id" x-ref="supplierSelect" class="neo-input px-3.5 py-2.5 text-xs font-bold">
                            <option value="">-- Pilih Supplier Distributor --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="showDirectInput" x-cloak class="mt-1">
                        <input type="text" name="new_supplier_name" placeholder="Ketik nama distributor baru..."
                            class="neo-input px-3.5 py-2.5 text-xs font-bold border-emerald-600">
                    </div>

                    <button type="button" @click="showDirectInput = !showDirectInput" class="text-[11px] font-bold text-zinc-600 hover:text-black underline mt-1.5 inline-block">
                        <span x-text="showDirectInput ? '← Kembali pilih dari daftar supplier' : '⚡ Atau ketik langsung nama distributor baru'"></span>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Satuan <span class="text-rose-600">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                        class="neo-input px-3.5 py-2.5 text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Batas Minimum Alert Stok</label>
                    <input type="number" step="0.01" min="0" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}"
                        class="neo-input px-3.5 py-2.5 text-xs font-mono font-bold">
                </div>
            </div>

            <!-- Pricing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t-2 border-black">
                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Harga Beli / HPP (Rp) <span class="text-rose-600">*</span></label>
                    <input type="number" min="0" step="100" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required
                        class="neo-input px-3.5 py-2.5 text-sm font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Harga Jual Kasir (Rp) <span class="text-rose-600">*</span></label>
                    <input type="number" min="0" step="100" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required
                        class="neo-input px-3.5 py-2.5 text-sm font-mono font-black">
                </div>
            </div>

            <!-- Active Status -->
            <div class="pt-2">
                <label class="flex items-center space-x-2.5 cursor-pointer font-bold select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 border-2 border-black rounded bg-white text-black focus:ring-0 cursor-pointer">
                    <span class="text-xs font-black text-black">Produk ini aktif dan dapat dijual di kasir POS</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-black">
                <a href="{{ route('admin.products.index') }}" class="neo-btn px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-black text-xs font-bold">
                    Kembali
                </a>
                <button type="submit" class="neo-btn px-6 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                    Perbarui Data Produk
                </button>
            </div>
        </form>
    </div>

    <!-- QUICK ADD SUPPLIER MODAL -->
    <div x-show="showSupplierModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-md p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showSupplierModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <h3 class="text-base font-black text-black uppercase">Buat Supplier Baru</h3>
                <button @click="showSupplierModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <form @submit.prevent="createSupplierAjax()" class="space-y-4 my-4">
                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Nama Supplier / PT / CV <span class="text-rose-600">*</span></label>
                    <input type="text" x-model="newSupplier.name" placeholder="Contoh: PT Japfa Comfeed Indonesia" required
                        class="neo-input px-3.5 py-2 text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Kontak Person / PIC</label>
                    <input type="text" x-model="newSupplier.contact_person" placeholder="Contoh: Bpk. Ahmad Sales"
                        class="neo-input px-3.5 py-2 text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">No. HP / WhatsApp</label>
                    <input type="text" x-model="newSupplier.phone" placeholder="Contoh: 08123456789"
                        class="neo-input px-3.5 py-2 text-xs font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Alamat Gudang / Kantor</label>
                    <textarea x-model="newSupplier.address" rows="2" placeholder="Alamat lengkap distributor..."
                        class="neo-input px-3.5 py-2 text-xs font-bold"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showSupplierModal = false" class="neo-btn px-4 py-2 bg-zinc-100 text-black font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingSupplier || !newSupplier.name"
                        class="neo-btn px-5 py-2 bg-emerald-400 hover:bg-emerald-300 disabled:opacity-50 text-black font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                        <span x-show="!isSubmittingSupplier">Simpan & Pilih</span>
                        <span x-show="isSubmittingSupplier">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productEditForm', () => ({
        isRepack: {{ old('is_repack', $product->is_repack) ? 'true' : 'false' }},
        showDirectInput: false,
        showSupplierModal: false,
        isSubmittingSupplier: false,
        newSupplier: {
            name: '',
            contact_person: '',
            phone: '',
            address: ''
        },

        async createSupplierAjax() {
            if (!this.newSupplier.name.trim() || this.isSubmittingSupplier) return;
            this.isSubmittingSupplier = true;

            try {
                const res = await fetch(`{{ route('admin.suppliers.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newSupplier)
                });

                const data = await res.json();
                if (data.success && data.data) {
                    const select = this.$refs.supplierSelect;
                    const option = new Option(data.data.name, data.data.id, true, true);
                    select.add(option);
                    select.value = data.data.id;

                    this.showSupplierModal = false;
                    this.showDirectInput = false;
                    this.newSupplier = { name: '', contact_person: '', phone: '', address: '' };
                    alert(data.message);
                } else {
                    alert('Gagal membuat supplier: ' + (data.message || 'Periksa inputan Anda.'));
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan atau server.');
            } finally {
                this.isSubmittingSupplier = false;
            }
        }
    }));
});
</script>
@endpush
