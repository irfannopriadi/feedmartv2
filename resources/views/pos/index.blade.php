@extends('layouts.pos')

@section('title', 'Layar Kasir POS - Feedmart')

@section('content')
<div class="h-full flex flex-col md:flex-row overflow-hidden" x-data="posApp()">
    <!-- Left Column: Product Catalog & Fast Search (Full Light Glassmorphism) -->
    <div class="flex-1 flex flex-col border-r border-white/70 overflow-hidden">
        <!-- Search and Barcode Input Bar -->
        <div class="p-3 bg-white/55 backdrop-blur-xl border-b border-white/75 flex items-center gap-2.5">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchProducts()"
                    @keydown.enter.prevent="handleScanEnter()"
                    x-ref="barcodeInput"
                    placeholder="Scan Barcode / Cari pakan ternak... [Enter scan]"
                    class="glass-input pl-9 pr-4 py-2 text-xs font-semibold">
            </div>

            <!-- Sound Feedback Toggle -->
            <button type="button" @click="toggleSound()" class="glass-btn px-3 py-2 bg-white/70 hover:bg-white/95 text-slate-700 text-xs font-bold flex items-center gap-1.5 flex-shrink-0" :title="soundEnabled ? 'Matikan Suara Beep' : 'Aktifkan Suara Beep'">
                <span x-text="soundEnabled ? '🔊' : '🔇'"></span>
                <span class="hidden sm:inline text-[10px]" x-text="soundEnabled ? 'BEEP ON' : 'MUTE'"></span>
            </button>

            <!-- Quick Petty Cash / Expense -->
            <button type="button" @click="showExpenseModal = true" class="glass-btn px-3 py-2 bg-rose-500/15 hover:bg-rose-500/25 text-rose-800 border-rose-400/35 text-xs font-bold flex items-center gap-1 flex-shrink-0">
                <span>💸 Kas Keluar</span>
            </button>

            <!-- Recall Hold Bill Button -->
            <button type="button" @click="showHoldModal = true" class="glass-btn px-3 py-2 bg-amber-400/25 hover:bg-amber-400/40 text-amber-900 border-amber-400/40 text-xs font-bold flex items-center gap-1.5 flex-shrink-0">
                <span>📋 Bill</span>
                <span class="px-1.5 py-0.5 rounded-full bg-slate-800/85 text-white text-[10px] font-mono font-bold" x-text="heldBills.length"></span>
            </button>
        </div>

        <!-- Category Pills (Kategori Hewan) -->
        <div class="px-3 py-2 bg-white/40 backdrop-blur-md border-b border-white/70 flex items-center gap-2 overflow-x-auto text-xs whitespace-nowrap">
            <button type="button" @click="selectCategory('')"
                :class="selectedCategory === '' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 shadow-md shadow-emerald-500/20' : 'bg-white/65 hover:bg-white/90 text-slate-700'"
                class="glass-btn px-3.5 py-1.5 text-xs font-bold">
                Semua Hewan
            </button>
            @foreach($categories as $cat)
                <button type="button" @click="selectCategory('{{ $cat->id }}')"
                    :class="selectedCategory === '{{ $cat->id }}' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 shadow-md shadow-emerald-500/20' : 'bg-white/65 hover:bg-white/90 text-slate-700'"
                    class="glass-btn px-3.5 py-1.5 text-xs font-bold">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="flex-1 p-3.5 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <template x-for="product in products" :key="product.id">
                    <button type="button"
                        @click="addToCart(product)"
                        :disabled="product.stock <= 0"
                        :class="product.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-white/35 border border-white/50 rounded-2xl' : 'glass-box-sm hover:-translate-y-0.5 hover:bg-white/80 hover:shadow-lg hover:shadow-emerald-500/10 active:scale-[0.99] cursor-pointer'"
                        class="p-3.5 text-left transition-all flex flex-col justify-between relative group">
                        
                        <!-- Badges -->
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="glass-badge"
                                :class="product.is_repack ? 'bg-violet-500/15 text-violet-800 border-violet-400/30' : 'bg-sky-500/15 text-sky-800 border-sky-400/30'"
                                x-text="product.is_repack ? 'Eceran' : product.unit">
                            </span>
                            <span class="text-[10px] font-mono font-bold"
                                :class="product.stock <= 0 ? 'text-rose-600' : (product.stock <= product.min_stock_alert ? 'text-amber-600' : 'text-slate-500')">
                                Stok: <span x-text="Number(product.stock).toLocaleString('id-ID', {maximumFractionDigits: 2})"></span> <span x-text="product.unit"></span>
                            </span>
                        </div>

                        <!-- Product Name -->
                        <div class="my-1.5">
                            <h4 class="text-xs font-bold text-slate-900 line-clamp-2" x-text="product.name"></h4>
                            <span class="text-[10px] text-slate-500 block font-mono font-medium mt-0.5" x-text="product.barcode || product.sku"></span>
                        </div>

                        <!-- Price -->
                        <div class="mt-2 pt-2 border-t border-slate-200/70 flex items-center justify-between">
                            <span class="text-xs font-extrabold font-mono text-slate-900" x-text="'Rp ' + Number(product.selling_price).toLocaleString('id-ID')"></span>
                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-700 border border-emerald-400/35 flex items-center justify-center font-bold text-xs group-hover:bg-emerald-500 group-hover:text-white transition-colors">+</span>
                        </div>
                    </button>
                </template>
            </div>

            <div x-show="products.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400">
                <span class="text-4xl mb-2">🔍</span>
                <p class="text-xs font-semibold text-slate-500">Tidak ada produk yang cocok dengan pencarian.</p>
            </div>
        </div>
    </div>

    <!-- Right Column: Active Cart & Fast Checkout (Full Light Glassmorphism) -->
    <div class="w-full md:w-96 lg:w-[420px] bg-white/55 backdrop-blur-2xl border-l border-white/75 flex flex-col h-full flex-shrink-0 shadow-[-4px_0_24px_rgba(15,23,42,0.03)]">
        <!-- Cart Header -->
        <div class="p-3.5 bg-white/55 border-b border-white/75 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-base">🛒</span>
                <h3 class="font-extrabold text-sm tracking-tight text-slate-800 uppercase">Keranjang Kasir</h3>
                <span class="glass-badge bg-amber-400/20 text-amber-900 border-amber-400/35 font-mono" x-text="cart.length + ' item'"></span>
            </div>
            <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-600 hover:text-rose-700 font-bold uppercase tracking-wider hover:underline cursor-pointer">
                Kosongkan
            </button>
        </div>

        <!-- Cart Item List -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2.5">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="p-3 glass-box-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-2">
                            <h4 class="text-xs font-bold text-slate-900 line-clamp-1" x-text="item.name"></h4>
                            <span class="text-[10px] text-slate-500 font-mono font-semibold" x-text="'@ Rp ' + Number(item.selling_price).toLocaleString('id-ID') + ' / ' + item.unit"></span>
                        </div>
                        <span class="text-xs font-extrabold font-mono text-slate-900" x-text="'Rp ' + (item.qty * item.selling_price).toLocaleString('id-ID')"></span>
                    </div>

                    <!-- Quantity Control with Decimal Timbangan Support -->
                    <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-slate-200/70">
                        <div class="flex items-center space-x-1.5">
                            <button type="button" @click="updateQty(index, -1)" class="w-7 h-7 rounded-lg border border-white/80 bg-white/75 hover:bg-white text-slate-700 font-bold flex items-center justify-center text-xs shadow-xs cursor-pointer transition">-</button>
                            
                            <!-- Input Decimal Kiloan (0.5, 1.25 dll) -->
                            <input type="number" step="0.01" min="0.01" x-model.number="item.qty" @change="validateQty(index)"
                                class="w-16 px-1.5 py-1 text-center bg-white/75 border border-slate-300/60 rounded-lg text-xs font-bold font-mono text-slate-900 focus:outline-none focus:border-emerald-500">
                            
                            <span class="text-[10px] font-bold text-slate-500" x-text="item.unit"></span>

                            <button type="button" @click="updateQty(index, 1)" class="w-7 h-7 rounded-lg border border-white/80 bg-white/75 hover:bg-white text-slate-700 font-bold flex items-center justify-center text-xs shadow-xs cursor-pointer transition">+</button>
                        </div>

                        <button type="button" @click="removeItem(index)" class="w-7 h-7 rounded-lg border border-rose-300/40 bg-rose-500/12 hover:bg-rose-500/25 text-rose-700 font-bold flex items-center justify-center text-xs transition cursor-pointer" title="Hapus">
                            🗑
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="cart.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400">
                <span class="text-4xl mb-2">🌾</span>
                <p class="text-xs font-bold text-slate-700">Keranjang kasir masih kosong</p>
                <p class="text-[10px] text-slate-500 font-medium mt-1">Scan barcode atau klik produk di katalog</p>
            </div>
        </div>

        <!-- Cart Summary & Actions -->
        <div class="p-3.5 border-t border-white/75 bg-white/55 backdrop-blur-xl space-y-2.5 text-xs">
            <!-- Subtotal -->
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span class="uppercase tracking-wider text-[11px]">Subtotal:</span>
                <span class="text-sm font-mono text-slate-900" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
            </div>

            <!-- Diskon -->
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span class="uppercase tracking-wider text-[11px]">Diskon (Rp):</span>
                <input type="number" min="0" step="1000" x-model.number="discount"
                    class="w-28 px-2.5 py-1 text-right glass-input text-xs font-mono font-bold">
            </div>

            <!-- Grand Total Banner -->
            <div class="p-3.5 rounded-2xl bg-gradient-to-r from-emerald-500/20 to-teal-500/20 border border-emerald-400/40 text-slate-900 flex items-center justify-between shadow-sm backdrop-blur-md">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider block text-emerald-800">Total Tagihan</span>
                    <span class="text-xl font-extrabold font-mono tracking-tight text-slate-900" x-text="'Rp ' + finalAmount.toLocaleString('id-ID')"></span>
                </div>
                <span class="glass-badge bg-white/80 text-emerald-800 border-emerald-400/35" x-text="paymentMethod.toUpperCase()"></span>
            </div>

            <!-- Buttons: Hold & Checkout -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                <button type="button" @click="holdBill()" :disabled="cart.length === 0 || isProcessing"
                    class="glass-btn py-2.5 bg-amber-400/25 hover:bg-amber-400/40 text-amber-900 border-amber-400/40 font-bold text-xs">
                    <span>⏸ Hold</span>
                </button>
                <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0 || isProcessing"
                    class="glass-btn col-span-2 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5">
                    <span>💳 Bayar (F8)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- FAST PAYMENT MODAL (Full Light Glassmorphism) -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-lg p-6 relative" @click.outside="showPaymentModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div>
                    <span class="glass-badge bg-amber-400/20 text-amber-900 border-amber-400/35">Checkout Transaksi</span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-1">Pembayaran Kasir</h3>
                </div>
                <button @click="showPaymentModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <!-- Total Amount Big -->
            <div class="my-4 p-4 rounded-2xl bg-gradient-to-r from-amber-400/20 to-yellow-300/25 border border-amber-400/35 text-slate-900 flex items-center justify-between backdrop-blur-md">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-900">TOTAL TAGIHAN:</span>
                <span class="text-2xl font-extrabold font-mono text-slate-900" x-text="'Rp ' + finalAmount.toLocaleString('id-ID')"></span>
            </div>

            <!-- Customer Selector -->
            <div class="mb-4 p-3.5 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-700">Pelanggan / Peternak</label>
                    <button type="button" @click="quickAddCustomer()" class="text-[10px] font-bold text-violet-700 hover:underline uppercase">+ Pelanggan Baru</button>
                </div>
                <select x-model="customerId" class="glass-input w-full px-3 py-2 text-xs font-bold uppercase">
                    <option value="">-- PELANGGAN UMUM (NON-KASBON) --</option>
                    <template x-for="c in customers" :key="c.id">
                        <option :value="c.id" x-text="`${c.name} ${c.debt_balance > 0 ? '(Kasbon: Rp ' + Number(c.debt_balance).toLocaleString('id-ID') + ')' : ''}`"></option>
                    </template>
                </select>
            </div>

            <!-- Payment Method Tabs -->
            <div class="grid grid-cols-4 gap-2 mb-4">
                <button type="button" @click="paymentMethod = 'cash'; paidAmount = finalAmount"
                    :class="paymentMethod === 'cash' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 shadow-md shadow-emerald-500/20' : 'bg-white/65 text-slate-700'"
                    class="glass-btn py-2.5 px-1 text-xs font-bold text-center">
                    💵 Tunai
                </button>
                <button type="button" @click="paymentMethod = 'qris'; paidAmount = finalAmount"
                    :class="paymentMethod === 'qris' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white border-sky-400/50 shadow-md shadow-sky-500/20' : 'bg-white/65 text-slate-700'"
                    class="glass-btn py-2.5 px-1 text-xs font-bold text-center">
                    📱 QRIS
                </button>
                <button type="button" @click="paymentMethod = 'transfer'; paidAmount = finalAmount"
                    :class="paymentMethod === 'transfer' ? 'bg-gradient-to-r from-violet-500 to-purple-500 text-white border-violet-400/50 shadow-md shadow-violet-500/20' : 'bg-white/65 text-slate-700'"
                    class="glass-btn py-2.5 px-1 text-xs font-bold text-center">
                    🏦 Transfer
                </button>
                <button type="button" @click="paymentMethod = 'tempo'; paidAmount = 0"
                    :class="paymentMethod === 'tempo' ? 'bg-gradient-to-r from-rose-500 to-pink-500 text-white border-rose-400/50 shadow-md shadow-rose-500/20' : 'bg-white/65 text-slate-700'"
                    class="glass-btn py-2.5 px-1 text-xs font-bold text-center">
                    📋 Kasbon
                </button>
            </div>

            <!-- Tempo / Kasbon Panel -->
            <div x-show="paymentMethod === 'tempo'" class="space-y-3 p-3.5 bg-rose-500/10 border border-rose-400/35 rounded-2xl backdrop-blur-sm">
                <div class="text-xs font-semibold text-rose-900">
                    <span class="font-extrabold uppercase">⚠️ Transaksi Kasbon / Tempo:</span>
                    <p class="mt-0.5">Tagihan sebesar <strong>Rp <span x-text="finalAmount.toLocaleString('id-ID')"></span></strong> akan dicatat sebagai piutang pelanggan.</p>
                </div>
                <div x-show="!customerId" class="p-2.5 bg-rose-500/15 border border-rose-400/40 rounded-xl text-[11px] font-bold text-rose-900">
                    PILIH NAMA PELANGGAN DI ATAS UNTUK MENGAKTIFKAN KASBON!
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-700 mb-1">Tanggal Jatuh Tempo Pelunasan</label>
                    <input type="date" x-model="dueDate" class="glass-input w-full px-3 py-2 text-xs font-bold">
                </div>
            </div>

            <!-- Cash Input & Quick Money Buttons -->
            <div x-show="paymentMethod === 'cash'" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uang Diterima dari Pembeli (Rp)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-bold text-slate-500">Rp</span>
                        <input type="number" x-model.number="paidAmount" min="0" step="500" x-ref="cashInput"
                            class="glass-input pl-11 pr-4 py-2.5 text-xl font-extrabold font-mono">
                    </div>
                </div>

                <!-- Quick Money Pills -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="paidAmount = finalAmount" class="glass-btn px-3 py-1.5 bg-amber-400/25 text-amber-900 border-amber-400/40 text-xs font-bold font-mono">Uang Pas</button>
                    <button type="button" @click="paidAmount = 20000" class="glass-btn px-3 py-1.5 bg-white/70 text-slate-700 text-xs font-bold font-mono">20.000</button>
                    <button type="button" @click="paidAmount = 50000" class="glass-btn px-3 py-1.5 bg-white/70 text-slate-700 text-xs font-bold font-mono">50.000</button>
                    <button type="button" @click="paidAmount = 100000" class="glass-btn px-3 py-1.5 bg-white/70 text-slate-700 text-xs font-bold font-mono">100.000</button>
                    <button type="button" @click="paidAmount = 200000" class="glass-btn px-3 py-1.5 bg-white/70 text-slate-700 text-xs font-bold font-mono">200.000</button>
                    <button type="button" @click="paidAmount = 500000" class="glass-btn px-3 py-1.5 bg-white/70 text-slate-700 text-xs font-bold font-mono">500.000</button>
                </div>

                <!-- Change Calculation -->
                <div class="p-3.5 rounded-2xl border backdrop-blur-sm"
                    :class="changeAmount >= 0 ? 'bg-emerald-500/15 border-emerald-400/35 text-emerald-950' : 'bg-rose-500/15 border-rose-400/35 text-rose-950'">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider" x-text="changeAmount >= 0 ? 'Uang Kembalian:' : 'Uang Kurang:'"></span>
                        <span class="text-xl font-extrabold font-mono" x-text="'Rp ' + Math.abs(changeAmount).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Final Checkout -->
            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" @click="showPaymentModal = false" class="glass-btn px-4 py-2.5 bg-white/75 hover:bg-white text-slate-700 font-bold text-xs">
                    Batal
                </button>
                <button type="button" @click="submitCheckout()" :disabled="isProcessing || (paymentMethod === 'cash' && changeAmount < 0) || (paymentMethod === 'tempo' && !customerId)"
                    class="glass-btn px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-500/20 flex items-center gap-1.5">
                    <span x-show="!isProcessing">Selesaikan &amp; Cetak Struk 🖨</span>
                    <span x-show="isProcessing">Memproses Transaksi...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- RECALL BILL MODAL -->
    <div x-show="showHoldModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-md p-6 relative" @click.outside="showHoldModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase">Antrean Bill Tersimpan (Hold)</h3>
                <button @click="showHoldModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <div class="divide-y divide-slate-200/60 my-3 max-h-72 overflow-y-auto">
                <template x-for="bill in heldBills" :key="bill.id">
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-extrabold font-mono text-slate-900" x-text="bill.invoice_number"></span>
                            <p class="text-[10px] text-slate-500 font-semibold" x-text="bill.created_at + ' (' + bill.details.length + ' item)'"></p>
                            <span class="text-xs font-extrabold font-mono text-emerald-700" x-text="'Rp ' + Number(bill.final_amount).toLocaleString('id-ID')"></span>
                        </div>
                        <button type="button" @click="recallBill(bill.id)" class="glass-btn px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 font-bold text-xs shadow-sm shadow-emerald-500/20">
                            Ambil Kembali
                        </button>
                    </div>
                </template>

                <div x-show="heldBills.length === 0" class="py-8 text-center text-slate-500 font-semibold text-xs">
                    Tidak ada antrean belanja yang disimpan saat ini.
                </div>
            </div>

            <div class="pt-2 text-right">
                <button @click="showHoldModal = false" class="glass-btn px-4 py-2 bg-white/75 hover:bg-white text-slate-700 font-bold text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- QUICK PETTY CASH / EXPENSE MODAL -->
    <div x-show="showExpenseModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-md p-6 relative" @click.outside="showExpenseModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase">💸 Catat Kas Keluar Shift Ini</h3>
                <button @click="showExpenseModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <form @submit.prevent="submitExpense()" class="space-y-4 my-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Biaya *</label>
                    <select x-model="expenseCategory" required class="glass-input w-full px-3 py-2 text-xs font-bold uppercase">
                        <option value="Plastik & Karung Eceran">Plastik & Karung Eceran</option>
                        <option value="Upah Kuli Panggul Pakan">Upah Kuli Panggul Pakan</option>
                        <option value="Operasional Toko">Operasional Toko</option>
                        <option value="Konsumsi Karyawan">Konsumsi Karyawan</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal Kas Keluar (Rp) *</label>
                    <input type="number" x-model.number="expenseAmount" min="1" required class="glass-input w-full px-3.5 py-2.5 font-extrabold text-base" placeholder="25000">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan / Keperluan</label>
                    <input type="text" x-model="expenseDescription" placeholder="Contoh: Beli lakban & kresek 1kg" class="glass-input w-full px-3.5 py-2 text-xs font-semibold">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showExpenseModal = false" class="glass-btn px-4 py-2 bg-white/75 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="isExpenseSaving" class="glass-btn px-6 py-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white border-rose-400/50 font-bold text-xs uppercase shadow-md shadow-rose-500/20">
                        <span x-show="!isExpenseSaving">Simpan Pengeluaran</span>
                        <span x-show="isExpenseSaving">Menyimpan...</span>
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
    Alpine.data('posApp', () => ({
        products: @json($products),
        customers: @json($customers),
        cart: [],
        searchQuery: '',
        selectedCategory: '',
        discount: 0,
        paymentMethod: 'cash',
        paidAmount: 0,
        customerId: '',
        dueDate: '{{ now()->addDays(30)->toDateString() }}',
        isProcessing: false,
        showPaymentModal: false,
        showHoldModal: false,
        showExpenseModal: false,
        heldBills: @json($heldTransactions),
        activeRegisterId: {{ $activeRegister?->id ?? 'null' }},
        soundEnabled: localStorage.getItem('feedmart_sound') !== 'false',

        // Quick Expense fields
        expenseCategory: 'Plastik & Karung Eceran',
        expenseAmount: null,
        expenseDescription: '',
        isExpenseSaving: false,

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.qty * item.selling_price), 0);
        },

        get finalAmount() {
            return Math.max(0, this.subtotal - (this.discount || 0));
        },

        get changeAmount() {
            return this.paidAmount - this.finalAmount;
        },

        // Web Audio Synthesizer Sounds
        playBeep() {
            if (!this.soundEnabled) return;
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.08);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.08);
            } catch(e) {}
        },

        playBuzzer() {
            if (!this.soundEnabled) return;
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                osc.frequency.setValueAtTime(160, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.22);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.22);
            } catch(e) {}
        },

        playSuccess() {
            if (!this.soundEnabled) return;
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                [523.25, 659.25, 783.99].forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.frequency.value = freq;
                    gain.gain.setValueAtTime(0.1, ctx.currentTime + (i * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + (i * 0.08) + 0.18);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(ctx.currentTime + (i * 0.08));
                    osc.stop(ctx.currentTime + (i * 0.08) + 0.18);
                });
            } catch(e) {}
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('feedmart_sound', this.soundEnabled);
            if (this.soundEnabled) this.playBeep();
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
            } else {
                this.playBuzzer();
            }
        },

        addToCart(product) {
            const existingIndex = this.cart.findIndex(i => i.id === product.id);
            if (existingIndex > -1) {
                // Periksa stok
                const newQty = this.cart[existingIndex].qty + 1;
                if (newQty > product.stock) {
                    this.playBuzzer();
                    alert(`Stok '${product.name}' tidak mencukupi! Maksimal: ${product.stock} ${product.unit}`);
                    return;
                }
                this.cart[existingIndex].qty = newQty;
                this.playBeep();
            } else {
                if (product.stock < 1) {
                    this.playBuzzer();
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
                this.playBeep();
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
                this.playBuzzer();
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
                this.playBuzzer();
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
            this.paidAmount = this.paymentMethod === 'tempo' ? 0 : this.finalAmount;
            this.showPaymentModal = true;
            this.$nextTick(() => {
                if (this.$refs.cashInput && this.paymentMethod === 'cash') {
                    this.$refs.cashInput.select();
                }
            });
        },

        async quickAddCustomer() {
            const name = prompt('Masukkan Nama Pelanggan / Peternak Baru:');
            if (!name || !name.trim()) return;

            try {
                const res = await fetch(`{{ route('customers.quick-store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: name.trim() })
                });

                const data = await res.json();
                if (data.success && data.customer) {
                    this.customers.push(data.customer);
                    this.customerId = data.customer.id;
                    alert(`Pelanggan '${data.customer.name}' berhasil ditambahkan.`);
                }
            } catch(e) {
                alert('Gagal menambah pelanggan.');
            }
        },

        async submitExpense() {
            if (!this.expenseAmount || this.expenseAmount <= 0) {
                alert('Masukkan nominal pengeluaran kas.');
                return;
            }
            this.isExpenseSaving = true;

            try {
                const res = await fetch(`{{ route('expenses.quick-store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        category: this.expenseCategory,
                        amount: this.expenseAmount,
                        description: this.expenseDescription,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.playBeep();
                    alert(data.message);
                    this.showExpenseModal = false;
                    this.expenseAmount = null;
                    this.expenseDescription = '';
                } else {
                    alert('Gagal mencatat kas keluar.');
                }
            } catch(e) {
                alert('Terjadi kesalahan jaringan.');
            } finally {
                this.isExpenseSaving = false;
            }
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
                paid_amount: this.paymentMethod === 'tempo' ? 0 : this.paidAmount,
                discount: this.discount || 0,
                cash_register_id: this.activeRegisterId,
                customer_id: this.customerId ? Number(this.customerId) : null,
                due_date: this.paymentMethod === 'tempo' ? this.dueDate : null,
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
                    this.playSuccess();
                    this.showPaymentModal = false;
                    this.cart = [];
                    this.discount = 0;
                    this.customerId = '';
                    
                    // Buka struk thermal di popup print
                    window.open(data.receipt_url, '_blank', 'width=450,height=600');
                    
                    // Refresh data produk agar stok terbaru terlihat
                    await this.searchProducts();
                } else {
                    this.playBuzzer();
                    alert('Gagal Checkout: ' + (data.message || 'Terjadi kesalahan sistem'));
                }
            } catch (err) {
                this.playBuzzer();
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
                    this.playBeep();
                    alert(data.message);
                    this.heldBills.unshift(data.transaction);
                    this.cart = [];
                    this.discount = 0;
                } else {
                    this.playBuzzer();
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
                    this.playBeep();
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
