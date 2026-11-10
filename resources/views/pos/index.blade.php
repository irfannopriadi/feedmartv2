@extends('layouts.pos')

@section('title', 'Layar Kasir POS - Feedmart')

@section('content')
<div class="h-full flex flex-col md:flex-row overflow-hidden" x-data="posApp()">
    <!-- Left Column: Product Catalog & Fast Search (60%) -->
    <div class="flex-1 flex flex-col bg-slate-50 border-r border-slate-200 overflow-hidden">
        <!-- Search and Barcode Input Bar -->
        <div class="p-3 bg-white border-b border-slate-200 flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchProducts()"
                    @keydown.enter.prevent="handleScanEnter()"
                    x-ref="barcodeInput"
                    placeholder="Scan Barcode / Cari nama pakan ternak... [Tekan Enter untuk scan]"
                    class="w-full pl-9 pr-4 py-2 bg-slate-100 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>

            <!-- Recall Hold Bill Button -->
            <button type="button" @click="showHoldModal = true" class="relative px-3 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 border border-amber-300 rounded-xl text-xs font-bold transition flex items-center gap-1.5 flex-shrink-0">
                <span>📋 Bill Tersimpan</span>
                <span class="w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] flex items-center justify-center font-bold" x-text="heldBills.length"></span>
            </button>
        </div>

        <!-- Category Pills (Kategori Hewan) -->
        <div class="px-3 py-2 bg-white border-b border-slate-200 flex items-center gap-1.5 overflow-x-auto text-xs font-semibold whitespace-nowrap">
            <button type="button" @click="selectCategory('')"
                :class="selectedCategory === '' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                class="px-3 py-1.5 rounded-lg transition">
                Semua Hewan
            </button>
            @foreach($categories as $cat)
                <button type="button" @click="selectCategory('{{ $cat->id }}')"
                    :class="selectedCategory === '{{ $cat->id }}' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                    class="px-3 py-1.5 rounded-lg transition">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="flex-1 p-3 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                <template x-for="product in products" :key="product.id">
                    <button type="button"
                        @click="addToCart(product)"
                        :disabled="product.stock <= 0"
                        :class="product.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-slate-100' : 'hover:border-emerald-500 hover:shadow-md bg-white active:scale-95'"
                        class="p-2.5 rounded-xl border border-slate-200 text-left transition flex flex-col justify-between relative group">
                        
                        <!-- Badges -->
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded"
                                :class="product.is_repack ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-100 text-blue-700 border border-blue-200'"
                                x-text="product.is_repack ? 'Eceran' : product.unit">
                            </span>
                            <span class="text-[10px] font-bold"
                                :class="product.stock <= 0 ? 'text-rose-600' : (product.stock <= product.min_stock_alert ? 'text-amber-600' : 'text-slate-500')">
                                Stok: <span x-text="Number(product.stock).toLocaleString('id-ID', {maximumFractionDigits: 2})"></span> <span x-text="product.unit"></span>
                            </span>
                        </div>

                        <!-- Product Name -->
                        <div class="my-1">
                            <h4 class="text-xs font-bold text-slate-800 line-clamp-2" x-text="product.name"></h4>
                            <span class="text-[10px] text-slate-400 block font-mono" x-text="product.barcode || product.sku"></span>
                        </div>

                        <!-- Price -->
                        <div class="mt-2 pt-1 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-emerald-700" x-text="'Rp ' + Number(product.selling_price).toLocaleString('id-ID')"></span>
                            <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm group-hover:bg-emerald-600 group-hover:text-white transition">+</span>
                        </div>
                    </button>
                </template>
            </div>

            <div x-show="products.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400">
                <span class="text-3xl mb-2">🔍</span>
                <p class="text-xs font-medium">Tidak ada produk yang cocok dengan pencarian.</p>
            </div>
        </div>
    </div>

    <!-- Right Column: Active Cart & Fast Checkout (40%) -->
    <div class="w-full md:w-96 lg:w-[420px] bg-white flex flex-col h-full flex-shrink-0 shadow-lg">
        <!-- Cart Header -->
        <div class="p-3 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-base">🛒</span>
                <h3 class="font-bold text-sm">Keranjang Kasir</h3>
                <span class="text-xs text-slate-400 font-mono" x-text="'(' + cart.length + ' item)'"></span>
            </div>
            <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-400 hover:text-rose-300 font-semibold transition">
                Kosongkan
            </button>
        </div>

        <!-- Cart Item List -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2 divide-y divide-slate-100">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="pt-2 first:pt-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-2">
                            <h4 class="text-xs font-bold text-slate-800 line-clamp-1" x-text="item.name"></h4>
                            <span class="text-[10px] text-slate-400 font-mono" x-text="'@ Rp ' + Number(item.selling_price).toLocaleString('id-ID') + ' / ' + item.unit"></span>
                        </div>
                        <span class="text-xs font-bold text-emerald-700" x-text="'Rp ' + (item.qty * item.selling_price).toLocaleString('id-ID')"></span>
                    </div>

                    <!-- Quantity Control with Decimal Timbangan Support -->
                    <div class="flex items-center justify-between mt-1.5">
                        <div class="flex items-center space-x-1">
                            <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs transition">-</button>
                            
                            <!-- Input Decimal Kiloan (0.5, 1.25 dll) -->
                            <input type="number" step="0.01" min="0.01" x-model.number="item.qty" @change="validateQty(index)"
                                class="w-16 px-1 py-0.5 text-center bg-slate-50 border border-slate-300 rounded text-xs font-bold text-slate-800 focus:bg-white focus:border-emerald-500">
                            
                            <span class="text-[10px] font-semibold text-slate-500" x-text="item.unit"></span>

                            <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs transition">+</button>
                        </div>

                        <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-rose-500 text-xs transition p-1" title="Hapus">
                            🗑
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="cart.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-300">
                <span class="text-4xl mb-2">🌾</span>
                <p class="text-xs font-medium text-slate-400">Keranjang kasir masih kosong</p>
                <p class="text-[10px] text-slate-400 mt-1">Scan barcode atau klik produk di katalog</p>
            </div>
        </div>

        <!-- Cart Summary & Actions -->
        <div class="p-3 border-t border-slate-200 bg-slate-50 space-y-2 text-xs">
            <!-- Subtotal -->
            <div class="flex items-center justify-between text-slate-600 font-semibold">
                <span>Subtotal:</span>
                <span class="text-sm font-bold text-slate-800" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
            </div>

            <!-- Diskon -->
            <div class="flex items-center justify-between text-slate-600 font-semibold">
                <span>Diskon (Rp):</span>
                <input type="number" min="0" step="1000" x-model.number="discount"
                    class="w-24 px-2 py-1 text-right bg-white border border-slate-300 rounded text-xs font-bold text-slate-800">
            </div>

            <!-- Grand Total -->
            <div class="p-2.5 rounded-xl bg-emerald-600 text-white flex items-center justify-between shadow-xs">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider block opacity-90">Total Tagihan</span>
                    <span class="text-lg font-black tracking-tight" x-text="'Rp ' + finalAmount.toLocaleString('id-ID')"></span>
                </div>
                <span class="text-xs font-medium bg-emerald-700 px-2 py-0.5 rounded" x-text="paymentMethod.toUpperCase()"></span>
            </div>

            <!-- Buttons: Hold & Checkout -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                <button type="button" @click="holdBill()" :disabled="cart.length === 0 || isProcessing"
                    class="py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1 shadow-xs">
                    <span>⏸ Hold</span>
                </button>
                <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0 || isProcessing"
                    class="col-span-2 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1">
                    <span>💳 Bayar (F8)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- FAST PAYMENT MODAL -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl relative" @click.outside="showPaymentModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Checkout Transaksi</span>
                    <h3 class="text-lg font-black text-slate-800">Pembayaran Kasir</h3>
                </div>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
            </div>

            <!-- Total Amount Big -->
            <div class="my-4 p-4 rounded-xl bg-slate-900 text-white flex items-center justify-between">
                <span class="text-xs text-slate-300 font-medium">TOTAL PEMBAYARAN:</span>
                <span class="text-2xl font-black text-emerald-400" x-text="'Rp ' + finalAmount.toLocaleString('id-ID')"></span>
            </div>

            <!-- Payment Method Tabs -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" @click="paymentMethod = 'cash'; paidAmount = finalAmount"
                    :class="paymentMethod === 'cash' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-100 text-slate-700 border-slate-200'"
                    class="py-2 px-3 border rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                    💵 Tunai (Cash)
                </button>
                <button type="button" @click="paymentMethod = 'qris'; paidAmount = finalAmount"
                    :class="paymentMethod === 'qris' ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-100 text-slate-700 border-slate-200'"
                    class="py-2 px-3 border rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                    📱 QRIS
                </button>
                <button type="button" @click="paymentMethod = 'transfer'; paidAmount = finalAmount"
                    :class="paymentMethod === 'transfer' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-100 text-slate-700 border-slate-200'"
                    class="py-2 px-3 border rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                    🏦 Transfer
                </button>
            </div>

            <!-- Cash Input & Quick Money Buttons -->
            <div x-show="paymentMethod === 'cash'" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uang Diterima dari Pembeli (Rp)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm font-bold text-slate-400">Rp</span>
                        <input type="number" x-model.number="paidAmount" min="0" step="500" x-ref="cashInput"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xl font-black text-slate-800 focus:bg-white focus:border-emerald-500">
                    </div>
                </div>

                <!-- Quick Money Pills -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="paidAmount = finalAmount" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">Uang Pas</button>
                    <button type="button" @click="paidAmount = 20000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">20.000</button>
                    <button type="button" @click="paidAmount = 50000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">50.000</button>
                    <button type="button" @click="paidAmount = 100000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">100.000</button>
                    <button type="button" @click="paidAmount = 200000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">200.000</button>
                    <button type="button" @click="paidAmount = 500000" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-bold text-slate-700">500.000</button>
                </div>

                <!-- Change Calculation -->
                <div class="p-3.5 rounded-xl border"
                    :class="changeAmount >= 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase" x-text="changeAmount >= 0 ? 'Uang Kembalian:' : 'Uang Kurang:'"></span>
                        <span class="text-xl font-black" x-text="'Rp ' + Math.abs(changeAmount).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Final Checkout -->
            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" @click="showPaymentModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="button" @click="submitCheckout()" :disabled="isProcessing || (paymentMethod === 'cash' && changeAmount < 0)"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                    <span x-show="!isProcessing">Selesaikan & Cetak Struk 🖨</span>
                    <span x-show="isProcessing">Memproses Transaksi...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- RECALL BILL MODAL -->
    <div x-show="showHoldModal" x-cloak class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl relative" @click.outside="showHoldModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Antrean Bill Tersimpan (Hold)</h3>
                <button @click="showHoldModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
            </div>

            <div class="divide-y divide-slate-100 my-3 max-h-72 overflow-y-auto">
                <template x-for="bill in heldBills" :key="bill.id">
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800" x-text="bill.invoice_number"></span>
                            <p class="text-[10px] text-slate-400" x-text="bill.created_at + ' (' + bill.details.length + ' item)'"></p>
                            <span class="text-xs font-bold text-emerald-600" x-text="'Rp ' + Number(bill.final_amount).toLocaleString('id-ID')"></span>
                        </div>
                        <button type="button" @click="recallBill(bill.id)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition">
                            Ambil Kembali
                        </button>
                    </div>
                </template>

                <div x-show="heldBills.length === 0" class="py-8 text-center text-slate-400 text-xs">
                    Tidak ada antrean belanja yang disimpan saat ini.
                </div>
            </div>

            <div class="pt-2 text-right">
                <button @click="showHoldModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('posApp', () => ({
        products: @json($products),
        cart: [],
        searchQuery: '',
        selectedCategory: '',
        discount: 0,
        paymentMethod: 'cash',
        paidAmount: 0,
        isProcessing: false,
        showPaymentModal: false,
        showHoldModal: false,
        heldBills: @json($heldTransactions),
        activeRegisterId: {{ $activeRegister?->id ?? 'null' }},

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.qty * item.selling_price), 0);
        },

        get finalAmount() {
            return Math.max(0, this.subtotal - (this.discount || 0));
        },

        get changeAmount() {
            return this.paidAmount - this.finalAmount;
        },

        init() {
            this.$nextTick(() => {
                if (this.$refs.barcodeInput) {
                    this.$refs.barcodeInput.focus();
                }
            });

            // Keyboard shortcut F8 untuk checkout cepat
            window.addEventListener('keydown', (e) => {
                if (e.key === 'F8') {
                    e.preventDefault();
                    if (this.cart.length > 0) {
                        this.openPaymentModal();
                    }
                }
            });
        },

        selectCategory(catId) {
            this.selectedCategory = catId;
            this.searchProducts();
        },

        async searchProducts() {
            const params = new URLSearchParams({
                q: this.searchQuery,
                category_id: this.selectedCategory
            });

            try {
                const res = await fetch(`{{ route('pos.search') }}?${params.toString()}`);
                const data = await res.json();
                if (data.success) {
                    this.products = data.data;
                }
            } catch (err) {
                console.error(err);
            }
        },

        handleScanEnter() {
            if (!this.searchQuery) return;

            // Cari produk yang barcode / SKU-nya sama persis
            const matched = this.products.find(p => p.barcode === this.searchQuery || p.sku.toLowerCase() === this.searchQuery.toLowerCase());
            if (matched) {
                this.addToCart(matched);
                this.searchQuery = '';
                this.searchProducts();
            } else if (this.products.length === 1) {
                this.addToCart(this.products[0]);
                this.searchQuery = '';
                this.searchProducts();
            }
        },

        addToCart(product) {
            const existingIndex = this.cart.findIndex(i => i.id === product.id);
            if (existingIndex > -1) {
                // Periksa stok
                const newQty = this.cart[existingIndex].qty + 1;
                if (newQty > product.stock) {
                    alert(`Stok '${product.name}' tidak mencukupi! Maksimal: ${product.stock} ${product.unit}`);
                    return;
                }
                this.cart[existingIndex].qty = newQty;
            } else {
                if (product.stock < 1) {
                    alert(`Stok '${product.name}' habis.`);
                    return;
                }
                this.cart.push({
                    id: product.id,
                    sku: product.sku,
                    name: product.name,
                    unit: product.unit,
                    selling_price: Number(product.selling_price),
                    stock: Number(product.stock),
                    qty: 1
                });
            }
        },

        updateQty(index, change) {
            const item = this.cart[index];
            const newQty = Math.round((item.qty + change) * 100) / 100;
            if (newQty <= 0) {
                this.removeItem(index);
                return;
            }
            if (newQty > item.stock) {
                alert(`Stok tidak mencukupi (Tersedia: ${item.stock} ${item.unit})`);
                return;
            }
            item.qty = newQty;
        },

        validateQty(index) {
            const item = this.cart[index];
            if (item.qty <= 0) {
                this.removeItem(index);
                return;
            }
            if (item.qty > item.stock) {
                alert(`Stok tidak mencukupi (Tersedia: ${item.stock} ${item.unit})`);
                item.qty = item.stock;
            }
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Yakin ingin mengosongkan keranjang kasir?')) {
                this.cart = [];
                this.discount = 0;
            }
        },

        openPaymentModal() {
            this.paidAmount = this.finalAmount;
            this.showPaymentModal = true;
            this.$nextTick(() => {
                if (this.$refs.cashInput) {
                    this.$refs.cashInput.select();
                }
            });
        },

        async submitCheckout() {
            if (this.isProcessing) return;
            this.isProcessing = true;

            const payload = {
                items: this.cart.map(i => ({
                    product_id: i.id,
                    qty: i.qty,
                    selling_price: i.selling_price
                })),
                payment_method: this.paymentMethod,
                paid_amount: this.paidAmount,
                discount: this.discount || 0,
                cash_register_id: this.activeRegisterId
            };

            try {
                const res = await fetch(`{{ route('pos.checkout') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    this.showPaymentModal = false;
                    this.cart = [];
                    this.discount = 0;
                    
                    // Buka struk thermal di popup print
                    window.open(data.receipt_url, '_blank', 'width=450,height=600');
                    
                    // Refresh data produk agar stok terbaru terlihat
                    await this.searchProducts();
                } else {
                    alert('Gagal Checkout: ' + (data.message || 'Terjadi kesalahan sistem'));
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan atau server.');
            } finally {
                this.isProcessing = false;
            }
        },

        async holdBill() {
            if (this.cart.length === 0 || this.isProcessing) return;
            this.isProcessing = true;

            const payload = {
                items: this.cart.map(i => ({
                    product_id: i.id,
                    qty: i.qty
                })),
                discount: this.discount || 0
            };

            try {
                const res = await fetch(`{{ route('pos.hold') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    this.heldBills.unshift(data.transaction);
                    this.cart = [];
                    this.discount = 0;
                } else {
                    alert('Gagal Hold: ' + data.message);
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.isProcessing = false;
            }
        },

        async recallBill(id) {
            if (this.cart.length > 0 && !confirm('Keranjang saat ini akan digantikan dengan bill tersimpan. Lanjutkan?')) {
                return;
            }

            try {
                const res = await fetch(`{{ url('pos/recall') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    this.cart = data.items;
                    this.discount = data.discount;
                    this.heldBills = this.heldBills.filter(b => b.id !== id);
                    this.showHoldModal = false;
                }
            } catch (err) {
                console.error(err);
            }
        }
    }));
});
</script>
@endpush
