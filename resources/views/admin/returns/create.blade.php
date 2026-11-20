@extends('layouts.app')

@section('title', 'CATAT RETUR BARANG')
@section('page_title', 'Catat Retur Barang Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
    type: 'sale_return',
    selectedProductId: '',
    products: {{ json_encode($products) }},
    activeBatches: [],
    onProductChange() {
        const prod = this.products.find(p => p.id == this.selectedProductId);
        this.activeBatches = prod && prod.batches ? prod.batches : [];
    }
}">
    <!-- Header -->
    <div class="glass-box p-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">🔄</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">CATAT RETUR BARANG BARU</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Pengembalian pakan cacat, bocor, atau salah ambil</p>
        </div>
        <a href="{{ route('admin.returns.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold">
            ← KEMBALI
        </a>
    </div>

    @if(session('error'))
        <div class="glass-box bg-rose-500/15 border-rose-400/35 p-4 font-bold text-rose-950 flex items-center gap-3">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.returns.store') }}" method="POST" class="glass-box-lg p-6 space-y-5">
        @csrf

        <!-- Pilih Tipe Retur -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipe Retur *</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="glass-box-sm p-4 flex items-center gap-3 cursor-pointer transition"
                    :class="type === 'sale_return' ? 'bg-emerald-500/15 border-emerald-400/45' : 'bg-white/50'">
                    <input type="radio" name="type" value="sale_return" x-model="type" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500/30">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-900">RETUR PENJUALAN</div>
                        <div class="text-[10px] text-slate-500 font-medium">Dari pembeli/peternak masuk kembali ke stok toko</div>
                    </div>
                </label>
                <label class="glass-box-sm p-4 flex items-center gap-3 cursor-pointer transition"
                    :class="type === 'purchase_return' ? 'bg-violet-500/15 border-violet-400/45' : 'bg-white/50'">
                    <input type="radio" name="type" value="purchase_return" x-model="type" class="w-4 h-4 text-violet-600 focus:ring-violet-500/30">
                    <div>
                        <div class="text-xs font-bold uppercase text-slate-900">RETUR PEMBELIAN</div>
                        <div class="text-[10px] text-slate-500 font-medium">Karung cacat/reject dikembalikan ke supplier</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Terkait Siapa -->
        <div x-show="type === 'sale_return'">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pelanggan / Peternak Terkait (Opsional)</label>
            <select name="customer_id" class="glass-input w-full px-3.5 py-2 font-semibold uppercase text-xs">
                <option value="">-- PELANGGAN UMUM / NON-MEMBER --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} (Kasbon: Rp {{ number_format($c->debt_balance, 0, ',', '.') }})</option>
                @endforeach
            </select>
            <p class="text-[10px] text-slate-500 font-semibold uppercase mt-1">Jika pelanggan memiliki kasbon, nominal refund dapat langsung memotong sisa hutang.</p>
        </div>

        <div x-show="type === 'purchase_return'">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Supplier / Distributor Tujuan Retur *</label>
            <select name="supplier_id" class="glass-input w-full px-3.5 py-2 font-semibold uppercase text-xs">
                <option value="">-- PILIH SUPPLIER --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Pilih Produk & Batch -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Pakan *</label>
                <select name="product_id" x-model="selectedProductId" @change="onProductChange()" required
                    class="glass-input w-full px-3.5 py-2 font-semibold uppercase text-xs">
                    <option value="">-- PILIH PRODUK --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ number_format($p->stock, 2) }} {{ $p->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Batch Kedaluwarsa (Opsional)</label>
                <select name="batch_id" class="glass-input w-full px-3.5 py-2 font-semibold uppercase text-xs">
                    <option value="">-- PILIH BATCH JIKA TAHU --</option>
                    <template x-for="b in activeBatches" :key="b.id">
                        <option :value="b.id" x-text="`${b.batch_number || 'Tanpa No'} | Exp: ${b.expired_date} | Sisa: ${b.stock_in_batch}`"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Qty & Refund -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah / Kuantitas Yang Diretur *</label>
                <input type="number" step="0.01" min="0.01" name="qty" required
                    class="glass-input w-full px-3.5 py-2.5 font-extrabold text-base" placeholder="Contoh: 1.00">
            </div>

            <div x-show="type === 'sale_return'">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pengembalian Dana / Potong Kasbon (Rp)</label>
                <input type="number" name="refund_amount" min="0"
                    class="glass-input w-full px-3.5 py-2.5 font-extrabold text-base" placeholder="0">
                <p class="text-[10px] text-slate-500 font-semibold uppercase mt-1">Isi 0 jika hanya tukar barang dengan jenis pakan yang sama.</p>
            </div>
        </div>

        <!-- Alasan & Catatan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Retur *</label>
            <input type="text" name="reason" required
                class="glass-input w-full px-3.5 py-2 font-semibold" placeholder="Contoh: Kemasan karung robek, pakan berjamur, atau pembeli salah jenis konsentrat">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
            <textarea name="notes" rows="2" class="glass-input w-full px-3.5 py-2 font-semibold text-xs" placeholder="Keterangan pendukung (misal no nota / persetujuan distributor)"></textarea>
        </div>

        <div class="pt-4 border-t border-slate-200/70 flex justify-end gap-3">
            <a href="{{ route('admin.returns.index') }}" class="glass-btn bg-white/75 text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                BATAL
            </a>
            <button type="submit" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 px-8 py-3 text-xs font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20">
                SIMPAN TRANSAKSI RETUR
            </button>
        </div>
    </form>
</div>
@endsection
