@extends('layouts.app')

@section('title', 'Tambah Produk Pakan - Feedmart')
@section('page_title', 'Tambah Produk Pakan Baru')

@section('content')
<div class="max-w-3xl mx-auto" x-data="productForm()">
    <div class="glass-box-lg p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
            @csrf

            <!-- Repack Switcher Alert -->
            <div class="p-4 rounded-2xl bg-violet-500/10 border border-violet-400/35 flex items-center justify-between backdrop-blur-sm">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Produk Hasil Eceran / Repack Karung?</h4>
                    <p class="text-xs font-medium text-slate-600 mt-0.5">Aktifkan opsi ini jika produk ini merupakan hasil eceran/kiloan dari karung pakan induk.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_repack" value="1" x-model="isRepack" class="sr-only peer">
                    <div class="w-12 h-6 bg-slate-200/80 border border-slate-300 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:shadow-sm after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-violet-500 peer-checked:border-violet-400"></div>
                </label>
            </div>

            <!-- Repack Settings (Conditional) -->
            <div x-show="isRepack" x-cloak class="p-4 rounded-2xl bg-violet-500/8 border border-violet-400/30 grid grid-cols-1 sm:grid-cols-2 gap-4 backdrop-blur-sm">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Pilih Karung Pakan Induk <span class="text-rose-500">*</span>
                    </label>
                    <select name="parent_product_id" class="glass-input px-3 py-2 text-xs">
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
                        Rasio Konversi (1 Sak = N Eceran) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.1" name="conversion_ratio" value="{{ old('conversion_ratio', '50.00') }}" placeholder="Contoh: 50 untuk 50 kg"
                        class="glass-input px-3 py-2 text-xs font-mono font-bold">
                    <span class="text-[10px] font-medium text-slate-500 mt-1 block">Contoh: 1 Sak 50kg = 50 unit pakan 1 kg</span>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SKU Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Contoh: PK-AYM-511-50KG" required
                        class="glass-input px-3.5 py-2.5 text-xs font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Barcode (Opsional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik barcode..."
                        class="glass-input px-3.5 py-2.5 text-xs font-mono font-bold">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Produk Pakan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Pakan Ayam Bravo 511 (50 Kg)" required
                        class="glass-input px-3.5 py-2.5 text-sm font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Hewan</label>
                    <select name="animal_category_id" class="glass-input px-3.5 py-2.5 text-xs font-semibold">
                        <option value="">-- Kategori Hewan --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('animal_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Distributor (Pilih atau Buat Baru) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Supplier Distributor</label>
                        <button type="button" @click="showSupplierModal = true" class="glass-btn px-2.5 py-0.5 bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 text-[10px] font-bold">
                            + Buat Baru
                        </button>
                    </div>

                    <div x-show="!showDirectInput">
                        <select name="supplier_id" x-ref="supplierSelect" class="glass-input px-3.5 py-2.5 text-xs font-semibold">
                            <option value="">-- Pilih Supplier Distributor --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="showDirectInput" x-cloak class="mt-1">
                        <input type="text" name="new_supplier_name" placeholder="Ketik nama distributor baru..."
                            class="glass-input px-3.5 py-2.5 text-xs font-semibold border-emerald-500/60">
                    </div>

                    <button type="button" @click="showDirectInput = !showDirectInput" class="text-[11px] font-semibold text-slate-500 hover:text-slate-900 underline mt-1.5 inline-block">
                        <span x-text="showDirectInput ? '← Kembali pilih dari daftar supplier' : '⚡ Atau ketik langsung nama distributor baru'"></span>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'sak') }}" placeholder="sak, kg, bungkus, pcs" required
                        class="glass-input px-3.5 py-2.5 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batas Minimum Alert Stok</label>
                    <input type="number" step="0.01" min="0" name="min_stock_alert" value="{{ old('min_stock_alert', '5.00') }}"
                        class="glass-input px-3.5 py-2.5 text-xs font-mono font-bold">
                </div>
            </div>

            <!-- Pricing & Initial Stock -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-200/70">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli / HPP (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" step="100" name="cost_price" value="{{ old('cost_price', '0') }}" required
                        class="glass-input px-3.5 py-2.5 text-sm font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Jual Kasir (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" step="100" name="selling_price" value="{{ old('selling_price', '0') }}" required
                        class="glass-input px-3.5 py-2.5 text-sm font-mono font-extrabold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Stok Awal Fisik</label>
                    <input type="number" min="0" step="0.01" name="stock" value="{{ old('stock', '0') }}"
                        class="glass-input px-3.5 py-2.5 text-sm font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Kedaluwarsa Stok Awal (Opsional)</label>
                    <input type="date" name="initial_expired_date" value="{{ old('initial_expired_date') }}"
                        class="glass-input px-3.5 py-2.5 text-xs font-semibold">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/70">
                <a href="{{ route('admin.products.index') }}" class="glass-btn px-4 py-2.5 bg-white/75 hover:bg-white text-slate-700 text-xs font-bold">
                    Batal
                </a>
                <button type="submit" class="glass-btn px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20">
                    Simpan Produk Pakan
                </button>
            </div>
        </form>
    </div>

    <!-- QUICK ADD SUPPLIER MODAL -->
    <div x-show="showSupplierModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-md p-6 relative" @click.outside="showSupplierModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase">Buat Supplier Baru</h3>
                <button @click="showSupplierModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <form @submit.prevent="createSupplierAjax()" class="space-y-4 my-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Supplier / PT / CV <span class="text-rose-500">*</span></label>
                    <input type="text" x-model="newSupplier.name" placeholder="Contoh: PT Japfa Comfeed Indonesia" required
                        class="glass-input px-3.5 py-2 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kontak Person / PIC</label>
                    <input type="text" x-model="newSupplier.contact_person" placeholder="Contoh: Bpk. Ahmad Sales"
                        class="glass-input px-3.5 py-2 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. HP / WhatsApp</label>
                    <input type="text" x-model="newSupplier.phone" placeholder="Contoh: 08123456789"
                        class="glass-input px-3.5 py-2 text-xs font-mono font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Gudang / Kantor</label>
                    <textarea x-model="newSupplier.address" rows="2" placeholder="Alamat lengkap distributor..."
                        class="glass-input px-3.5 py-2 text-xs font-semibold"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showSupplierModal = false" class="glass-btn px-4 py-2 bg-white/75 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingSupplier || !newSupplier.name"
                        class="glass-btn px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-500/20">
                        <span x-show="!isSubmittingSupplier">Simpan &amp; Pilih</span>
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
    Alpine.data('productForm', () => ({
        isRepack: {{ old('is_repack', 0) ? 'true' : 'false' }},
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
