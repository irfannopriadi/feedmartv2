@extends('layouts.app')

@section('title', 'CATAT RETUR BARANG')

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
    <div class="neo-box bg-white p-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">🔄</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">CATAT RETUR BARANG BARU</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">Pengembalian pakan cacat, bocor, atau salah ambil</p>
        </div>
        <a href="{{ route('admin.returns.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs">
            ← KEMBALI
        </a>
    </div>

    @if(session('error'))
        <div class="neo-box bg-rose-400 p-4 font-black text-black flex items-center gap-3">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.returns.store') }}" method="POST" class="neo-box bg-white p-6 space-y-5">
        @csrf

        <!-- Pilih Tipe Retur -->
        <div>
            <label class="block text-xs font-black uppercase mb-1">Tipe Retur *</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="neo-box p-4 flex items-center gap-3 cursor-pointer transition"
                    :class="type === 'sale_return' ? 'bg-emerald-100 border-black' : 'bg-gray-50'">
                    <input type="radio" name="type" value="sale_return" x-model="type" class="w-4 h-4 text-black">
                    <div>
                        <div class="text-xs font-black uppercase">RETUR PENJUALAN</div>
                        <div class="text-[10px] text-gray-500 font-bold">Dari pembeli/peternak masuk kembali ke stok toko</div>
                    </div>
                </label>
                <label class="neo-box p-4 flex items-center gap-3 cursor-pointer transition"
                    :class="type === 'purchase_return' ? 'bg-purple-100 border-black' : 'bg-gray-50'">
                    <input type="radio" name="type" value="purchase_return" x-model="type" class="w-4 h-4 text-black">
                    <div>
                        <div class="text-xs font-black uppercase">RETUR PEMBELIAN</div>
                        <div class="text-[10px] text-gray-500 font-bold">Karung cacat/reject dikembalikan ke supplier</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Terkait Siapa -->
        <div x-show="type === 'sale_return'">
            <label class="block text-xs font-black uppercase mb-1">Pelanggan / Peternak Terkait (Opsional)</label>
            <select name="customer_id" class="neo-input w-full font-bold uppercase text-xs">
                <option value="">-- PELANGGAN UMUM / NON-MEMBER --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} (Kasbon: Rp {{ number_format($c->debt_balance, 0, ',', '.') }})</option>
                @endforeach
            </select>
            <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">Jika pelanggan memiliki kasbon, nominal refund dapat langsung memotong sisa hutang.</p>
        </div>

        <div x-show="type === 'purchase_return'">
            <label class="block text-xs font-black uppercase mb-1">Supplier / Distributor Tujuan Retur *</label>
            <select name="supplier_id" class="neo-input w-full font-bold uppercase text-xs">
                <option value="">-- PILIH SUPPLIER --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Pilih Produk & Batch -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-black uppercase mb-1">Pilih Produk Pakan *</label>
                <select name="product_id" x-model="selectedProductId" @change="onProductChange()" required
                    class="neo-input w-full font-bold uppercase text-xs">
                    <option value="">-- PILIH PRODUK --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ number_format($p->stock, 2) }} {{ $p->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-black uppercase mb-1">Pilih Batch Kedaluwarsa (Opsional)</label>
                <select name="batch_id" class="neo-input w-full font-bold uppercase text-xs">
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
                <label class="block text-xs font-black uppercase mb-1">Jumlah / Kuantitas Yang Diretur *</label>
                <input type="number" step="0.01" min="0.01" name="qty" required
                    class="neo-input w-full font-black text-base" placeholder="Contoh: 1.00">
            </div>

            <div x-show="type === 'sale_return'">
                <label class="block text-xs font-black uppercase mb-1">Pengembalian Dana / Potong Kasbon (Rp)</label>
                <input type="number" name="refund_amount" min="0"
                    class="neo-input w-full font-black text-base" placeholder="0">
                <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">Isi 0 jika hanya tukar barang dengan jenis pakan yang sama.</p>
            </div>
        </div>

        <!-- Alasan & Catatan -->
        <div>
            <label class="block text-xs font-black uppercase mb-1">Alasan Retur *</label>
            <input type="text" name="reason" required
                class="neo-input w-full font-bold" placeholder="Contoh: Kemasan karung robek, pakan berjamur, atau pembeli salah jenis konsentrat">
        </div>

        <div>
            <label class="block text-xs font-black uppercase mb-1">Catatan Tambahan</label>
            <textarea name="notes" rows="2" class="neo-input w-full font-bold text-xs" placeholder="Keterangan pendukung (misal no nota / persetujuan distributor)"></textarea>
        </div>

        <div class="pt-4 border-t-2 border-black flex justify-end gap-3">
            <a href="{{ route('admin.returns.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                BATAL
            </a>
            <button type="submit" class="neo-btn bg-black text-white px-8 py-3 text-xs font-black uppercase tracking-wider">
                SIMPAN TRANSAKSI RETUR
            </button>
        </div>
    </form>
</div>
@endsection
