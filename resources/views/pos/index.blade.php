@extends('layouts.pos')

@section('title', 'Layar Kasir POS - Feedmart')

@section('content')
<div class="h-full flex flex-col md:flex-row overflow-hidden" x-data="posApp()">
    <!-- Left Column: Product Catalog & Fast Search (Neo-Brutalist Minimalism) -->
    <div class="flex-1 flex flex-col bg-[#F8F8F5] border-r-2 border-black overflow-hidden">
        <!-- Search and Barcode Input Bar -->
        <div class="p-3 bg-white border-b-2 border-black flex items-center gap-2.5">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-black font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchProducts()"
                    @keydown.enter.prevent="handleScanEnter()"
                    x-ref="barcodeInput"
                    placeholder="Scan Barcode / Cari pakan ternak... [Enter scan]"
                    class="neo-input pl-9 pr-4 py-2 text-xs font-bold">
            </div>

            <!-- Recall Hold Bill Button -->
            <button type="button" @click="showHoldModal = true" class="neo-btn px-3 py-2 bg-yellow-300 hover:bg-yellow-200 text-black text-xs font-black shadow-[2px_2px_0px_#000] flex items-center gap-1.5 flex-shrink-0">
                <span>📋 Bill Tersimpan</span>
                <span class="px-1.5 py-0.2 rounded-full bg-black text-white text-[10px] font-mono font-black" x-text="heldBills.length"></span>
            </button>
        </div>

        <!-- Category Pills (Kategori Hewan) -->
        <div class="px-3 py-2 bg-white border-b-2 border-black flex items-center gap-2 overflow-x-auto text-xs whitespace-nowrap">
            <button type="button" @click="selectCategory('')"
                :class="selectedCategory === '' ? 'bg-emerald-400 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-black'"
                class="neo-btn px-3 py-1.5 text-xs font-black shadow-[2px_2px_0px_#000]">
                Semua Hewan
            </button>
            @foreach($categories as $cat)
                <button type="button" @click="selectCategory('{{ $cat->id }}')"
                    :class="selectedCategory === '{{ $cat->id }}' ? 'bg-emerald-400 text-black' : 'bg-zinc-100 hover:bg-zinc-200 text-black'"
                    class="neo-btn px-3 py-1.5 text-xs font-black shadow-[2px_2px_0px_#000]">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="flex-1 p-3 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <template x-for="product in products" :key="product.id">
                    <button type="button"
                        @click="addToCart(product)"
                        :disabled="product.stock <= 0"
                        :class="product.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-zinc-100 border-2 border-black/40 shadow-none' : 'neo-box-sm hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[5px_5px_0px_#000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_#000] bg-white cursor-pointer'"
                        class="p-3 text-left transition-all flex flex-col justify-between relative group">
                        
                        <!-- Badges -->
                        <div class="flex items-center justify-between mb-1">
                            <span class="neo-badge"
                                :class="product.is_repack ? 'bg-purple-300 text-black' : 'bg-sky-300 text-black'"
                                x-text="product.is_repack ? 'Eceran' : product.unit">
                            </span>
                            <span class="text-[10px] font-mono font-black"
                                :class="product.stock <= 0 ? 'text-rose-600' : (product.stock <= product.min_stock_alert ? 'text-amber-600' : 'text-zinc-600')">
                                Stok: <span x-text="Number(product.stock).toLocaleString('id-ID', {maximumFractionDigits: 2})"></span> <span x-text="product.unit"></span>
                            </span>
                        </div>

                        <!-- Product Name -->
                        <div class="my-1.5">
                            <h4 class="text-xs font-black text-black line-clamp-2" x-text="product.name"></h4>
                            <span class="text-[10px] text-zinc-500 block font-mono font-bold mt-0.5" x-text="product.barcode || product.sku"></span>
                        </div>

                        <!-- Price -->
                        <div class="mt-2 pt-2 border-t-2 border-black/10 flex items-center justify-between">
                            <span class="text-xs font-black font-mono text-black" x-text="'Rp ' + Number(product.selling_price).toLocaleString('id-ID')"></span>
                            <span class="w-6 h-6 rounded-md bg-emerald-400 text-black border-2 border-black flex items-center justify-center font-black text-xs shadow-[1.5px_1.5px_0px_#000] group-hover:bg-black group-hover:text-emerald-400 transition-colors">+</span>
                        </div>
                    </button>
                </template>
            </div>

            <div x-show="products.length === 0" class="h-64 flex flex-col items-center justify-center text-zinc-400">
                <span class="text-4xl mb-2">🔍</span>
                <p class="text-xs font-black text-zinc-600">Tidak ada produk yang cocok dengan pencarian.</p>
            </div>
        </div>
    </div>

    <!-- Right Column: Active Cart & Fast Checkout (Neo-Brutalist Minimalism) -->
    <div class="w-full md:w-96 lg:w-[420px] bg-white border-l-2 border-black flex flex-col h-full flex-shrink-0">
        <!-- Cart Header -->
        <div class="p-3 bg-black text-white border-b-2 border-black flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-base">🛒</span>
                <h3 class="font-black text-sm tracking-wide uppercase">Keranjang Kasir</h3>
                <span class="neo-badge bg-yellow-300 text-black font-mono" x-text="cart.length + ' item'"></span>
            </div>
            <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-400 hover:text-rose-300 font-black uppercase tracking-wider underline cursor-pointer">
                Kosongkan
            </button>
        </div>

        <!-- Cart Item List -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-[#FBFBFA]">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="p-2.5 bg-white border-2 border-black rounded-xl shadow-[2px_2px_0px_#000]">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-2">
                            <h4 class="text-xs font-black text-black line-clamp-1" x-text="item.name"></h4>
                            <span class="text-[10px] text-zinc-600 font-mono font-bold" x-text="'@ Rp ' + Number(item.selling_price).toLocaleString('id-ID') + ' / ' + item.unit"></span>
                        </div>
                        <span class="text-xs font-black font-mono text-black" x-text="'Rp ' + (item.qty * item.selling_price).toLocaleString('id-ID')"></span>
                    </div>

                    <!-- Quantity Control with Decimal Timbangan Support -->
                    <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black/10">
                        <div class="flex items-center space-x-1.5">
                            <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded border-2 border-black bg-white hover:bg-zinc-200 text-black font-black flex items-center justify-center text-xs shadow-[1px_1px_0px_#000] cursor-pointer">-</button>
                            
                            <!-- Input Decimal Kiloan (0.5, 1.25 dll) -->
                            <input type="number" step="0.01" min="0.01" x-model.number="item.qty" @change="validateQty(index)"
                                class="w-16 px-1 py-0.5 text-center bg-white border-2 border-black rounded text-xs font-black font-mono text-black focus:outline-none shadow-[1px_1px_0px_#000]">
                            
                            <span class="text-[10px] font-black text-zinc-600" x-text="item.unit"></span>

                            <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded border-2 border-black bg-white hover:bg-zinc-200 text-black font-black flex items-center justify-center text-xs shadow-[1px_1px_0px_#000] cursor-pointer">+</button>
                        </div>

                        <button type="button" @click="removeItem(index)" class="w-6 h-6 rounded border-2 border-black bg-rose-200 hover:bg-rose-300 text-black font-black flex items-center justify-center text-xs shadow-[1px_1px_0px_#000] cursor-pointer" title="Hapus">
                            🗑
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="cart.length === 0" class="h-64 flex flex-col items-center justify-center text-zinc-400">
                <span class="text-4xl mb-2">🌾</span>
                <p class="text-xs font-black text-black">Keranjang kasir masih kosong</p>
                <p class="text-[10px] text-zinc-500 font-bold mt-1">Scan barcode atau klik produk di katalog</p>
            </div>
        </div>

        <!-- Cart Summary & Actions -->
        <div class="p-3 border-t-2 border-black bg-zinc-100 space-y-2 text-xs">
            <!-- Subtotal -->
            <div class="flex items-center justify-between font-black text-black">
                <span class="uppercase tracking-wider text-[11px]">Subtotal:</span>
                <span class="text-sm font-mono" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
            </div>

            <!-- Diskon -->
            <div class="flex items-center justify-between font-black text-black">
                <span class="uppercase tracking-wider text-[11px]">Diskon (Rp):</span>
                <input type="number" min="0" step="1000" x-model.number="discount"
                    class="w-28 px-2 py-1 text-right neo-input text-xs font-mono font-black">
            </div>

            <!-- Grand Total Banner -->
            <div class="p-3 rounded-xl bg-emerald-400 border-2 border-black text-black flex items-center justify-between shadow-[3px_3px_0px_#000]">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider block text-black">Total Tagihan</span>
                    <span class="text-xl font-black font-mono tracking-tight" x-text="'Rp ' + finalAmount.toLocaleString('id-ID')"></span>
                </div>
                <span class="neo-badge bg-black text-white" x-text="paymentMethod.toUpperCase()"></span>
            </div>

            <!-- Buttons: Hold & Checkout -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                <button type="button" @click="holdBill()" :disabled="cart.length === 0 || isProcessing"
                    class="neo-btn py-2.5 bg-amber-300 hover:bg-amber-200 disabled:opacity-50 text-black font-black text-xs shadow-[2px_2px_0px_#000]">
                    <span>⏸ Hold</span>
                </button>
                <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0 || isProcessing"
                    class="neo-btn col-span-2 py-2.5 bg-black hover:bg-zinc-800 disabled:opacity-50 text-white font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_#000] flex items-center justify-center gap-1.5">
                    <span>💳 Bayar (F8)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- FAST PAYMENT MODAL (Neo-Brutalist Minimalism) -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-lg p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showPaymentModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <div>
                    <span class="neo-badge bg-yellow-300 text-black">Checkout Transaksi</span>
                    <h3 class="text-xl font-black text-black mt-1">Pembayaran Kasir</h3>
                </div>
                <button @click="showPaymentModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <!-- Total Amount Big -->
            <div class="my-4 p-4 rounded-xl bg-yellow-300 border-2 border-black text-black flex items-center justify-between shadow-[3px_3px_0px_#000]">
                <span class="text-xs font-black uppercase tracking-wider">TOTAL TAGIHAN:</span>
                <span class="text-2xl font-black font-mono" x-text="'Rp ' + finalAmount.toLocaleString('id-ID')"></span>
            </div>

            <!-- Payment Method Tabs -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" @click="paymentMethod = 'cash'; paidAmount = finalAmount"
                    :class="paymentMethod === 'cash' ? 'bg-emerald-400 text-black shadow-[3px_3px_0px_#000]' : 'bg-zinc-100 text-black shadow-[1.5px_1.5px_0px_#000]'"
                    class="neo-btn py-2 px-3 text-xs font-black">
                    💵 Tunai (Cash)
                </button>
                <button type="button" @click="paymentMethod = 'qris'; paidAmount = finalAmount"
                    :class="paymentMethod === 'qris' ? 'bg-sky-300 text-black shadow-[3px_3px_0px_#000]' : 'bg-zinc-100 text-black shadow-[1.5px_1.5px_0px_#000]'"
                    class="neo-btn py-2 px-3 text-xs font-black">
                    📱 QRIS
                </button>
                <button type="button" @click="paymentMethod = 'transfer'; paidAmount = finalAmount"
                    :class="paymentMethod === 'transfer' ? 'bg-purple-300 text-black shadow-[3px_3px_0px_#000]' : 'bg-zinc-100 text-black shadow-[1.5px_1.5px_0px_#000]'"
                    class="neo-btn py-2 px-3 text-xs font-black">
                    🏦 Transfer
                </button>
            </div>

            <!-- Cash Input & Quick Money Buttons -->
            <div x-show="paymentMethod === 'cash'" class="space-y-3">
                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Uang Diterima dari Pembeli (Rp)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm font-black text-black">Rp</span>
                        <input type="number" x-model.number="paidAmount" min="0" step="500" x-ref="cashInput"
                            class="neo-input pl-10 pr-4 py-2.5 text-xl font-black font-mono">
                    </div>
                </div>

                <!-- Quick Money Pills -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="paidAmount = finalAmount" class="neo-btn px-2.5 py-1 bg-yellow-200 text-black text-xs font-black font-mono">Uang Pas</button>
                    <button type="button" @click="paidAmount = 20000" class="neo-btn px-2.5 py-1 bg-zinc-100 text-black text-xs font-black font-mono">20.000</button>
                    <button type="button" @click="paidAmount = 50000" class="neo-btn px-2.5 py-1 bg-zinc-100 text-black text-xs font-black font-mono">50.000</button>
                    <button type="button" @click="paidAmount = 100000" class="neo-btn px-2.5 py-1 bg-zinc-100 text-black text-xs font-black font-mono">100.000</button>
                    <button type="button" @click="paidAmount = 200000" class="neo-btn px-2.5 py-1 bg-zinc-100 text-black text-xs font-black font-mono">200.000</button>
                    <button type="button" @click="paidAmount = 500000" class="neo-btn px-2.5 py-1 bg-zinc-100 text-black text-xs font-black font-mono">500.000</button>
                </div>

                <!-- Change Calculation -->
                <div class="p-3.5 rounded-xl border-2 border-black shadow-[3px_3px_0px_#000]"
                    :class="changeAmount >= 0 ? 'bg-emerald-200 text-black' : 'bg-rose-200 text-black'">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider" x-text="changeAmount >= 0 ? 'Uang Kembalian:' : 'Uang Kurang:'"></span>
                        <span class="text-xl font-black font-mono" x-text="'Rp ' + Math.abs(changeAmount).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Final Checkout -->
            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" @click="showPaymentModal = false" class="neo-btn px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-black font-bold text-xs">
                    Batal
                </button>
                <button type="button" @click="submitCheckout()" :disabled="isProcessing || (paymentMethod === 'cash' && changeAmount < 0)"
                    class="neo-btn px-6 py-2.5 bg-emerald-400 hover:bg-emerald-300 disabled:opacity-50 text-black font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_#000] flex items-center gap-1.5">
                    <span x-show="!isProcessing">Selesaikan & Cetak Struk 🖨</span>
                    <span x-show="isProcessing">Memproses Transaksi...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- RECALL BILL MODAL -->
    <div x-show="showHoldModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-md p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showHoldModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <h3 class="text-base font-black text-black uppercase">Antrean Bill Tersimpan (Hold)</h3>
                <button @click="showHoldModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <div class="divide-y-2 divide-black/10 my-3 max-h-72 overflow-y-auto">
                <template x-for="bill in heldBills" :key="bill.id">
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-black font-mono text-black" x-text="bill.invoice_number"></span>
                            <p class="text-[10px] text-zinc-500 font-bold" x-text="bill.created_at + ' (' + bill.details.length + ' item)'"></p>
                            <span class="text-xs font-black font-mono text-emerald-700" x-text="'Rp ' + Number(bill.final_amount).toLocaleString('id-ID')"></span>
                        </div>
                        <button type="button" @click="recallBill(bill.id)" class="neo-btn px-3 py-1.5 bg-emerald-400 hover:bg-emerald-300 text-black font-black text-xs shadow-[2px_2px_0px_#000]">
                            Ambil Kembali
                        </button>
                    </div>
                </template>

                <div x-show="heldBills.length === 0" class="py-8 text-center text-zinc-500 font-bold text-xs">
                    Tidak ada antrean belanja yang disimpan saat ini.
                </div>
            </div>

            <div class="pt-2 text-right">
                <button @click="showHoldModal = false" class="neo-btn px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-black font-bold text-xs">
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
