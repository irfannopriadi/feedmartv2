@extends('layouts.app')

@section('title', 'Penerimaan Stok Barang - Feedmart')
@section('page_title', 'Penerimaan Stok & Batch Kedaluwarsa (Stock In)')

@section('content')
<div class="space-y-6" x-data="stockInApp()">
    <!-- Form Penerimaan Stok Baru (Full Light Glassmorphism) -->
    <div class="glass-box p-6">
        <div class="flex items-center space-x-3.5 pb-4 mb-4 border-b border-slate-200/70">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500 border border-white/80 flex items-center justify-center text-xl text-white shadow-sm shadow-sky-500/20">
                📥
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">Input Faktur Penerimaan Barang Distributor</h3>
                <p class="text-xs font-medium text-slate-500">Catat penerimaan stok, supplier distributor, nomor batch dan tanggal kedaluwarsa FIFO.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-in.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf

            <!-- Pilih Produk -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Pakan <span class="text-rose-500">*</span></label>
                <select name="product_id" required class="glass-input px-3 py-2 text-xs font-semibold">
                    <option value="">-- Pilih Produk Pakan --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} (Stok saat ini: {{ $p->stock }} {{ $p->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Supplier Distributor (Pilih atau Buat Baru) -->
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Supplier Distributor</label>
                    <button type="button" @click="showSupplierModal = true" class="glass-btn px-2.5 py-0.5 bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 text-[10px] font-bold">
                        + Buat Baru
                    </button>
                </div>

                <div x-show="!showDirectInput">
                    <select name="supplier_id" x-ref="supplierSelect" class="glass-input px-3 py-2 text-xs font-semibold">
                        <option value="">-- Pilih Supplier Distributor (Opsional) --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="showDirectInput" x-cloak class="mt-1">
                    <input type="text" name="new_supplier_name" placeholder="Ketik nama distributor baru..."
                        class="glass-input px-3.5 py-2 text-xs font-semibold border-emerald-500/60">
                </div>

                <button type="button" @click="showDirectInput = !showDirectInput" class="text-[10px] font-semibold text-slate-500 hover:text-slate-900 underline mt-1 inline-block">
                    <span x-text="showDirectInput ? '← Kembali pilih dari daftar supplier' : '⚡ Atau ketik langsung distributor baru'"></span>
                </button>
            </div>

            <!-- Jumlah Masuk -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Masuk <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="qty" value="{{ old('qty', '10.00') }}" required
                    class="glass-input px-3 py-2 text-xs font-mono font-bold">
            </div>

            <!-- Harga Beli Baru -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli / HPP Baru (Rp)</label>
                <input type="number" step="100" min="0" name="cost_price" value="{{ old('cost_price') }}" placeholder="Kosongkan jika tetap"
                    class="glass-input px-3 py-2 text-xs font-mono font-semibold">
            </div>

            <!-- Nomor Batch -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Batch Pabrik</label>
                <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="Contoh: B-2026-X1"
                    class="glass-input px-3 py-2 text-xs font-mono font-semibold">
            </div>

            <!-- Tanggal Kedaluwarsa FIFO -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Kedaluwarsa (Exp) <span class="text-rose-500">*</span></label>
                <input type="date" name="expired_date" value="{{ old('expired_date', \Carbon\Carbon::now()->addMonths(6)->toDateString()) }}" required
                    class="glass-input px-3 py-2 text-xs font-semibold">
            </div>

            <!-- Nomor Faktur -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Faktur / Surat Jalan</label>
                <input type="text" name="invoice_ref" value="{{ old('invoice_ref') }}" placeholder="Contoh: SJ-CP-9921"
                    class="glass-input px-3 py-2 text-xs font-mono font-semibold">
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-end">
                <button type="submit" class="glass-btn w-full py-2.5 px-4 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white border-sky-400/50 text-xs font-bold uppercase tracking-wider shadow-md shadow-sky-500/20">
                    + Simpan Penerimaan
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

    <!-- Riwayat Penerimaan Stok Terkini -->
    <div class="glass-box overflow-hidden">
        <div class="p-4 bg-white/45 border-b border-white/70 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Riwayat Transaksi Masuk (Stock In)</h3>
            <span class="glass-badge bg-sky-500/15 text-sky-800 border-sky-400/35">Total {{ $recentStockIns->total() }} Log</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Penerima</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3 text-right">Jumlah Masuk</th>
                        <th class="p-3 text-right">Stok Setelahnya</th>
                        <th class="p-3">Faktur &amp; Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($recentStockIns as $in)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-3 font-mono text-slate-500 whitespace-nowrap">{{ $in->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $in->user->name }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ $in->product->name }}</td>
                            <td class="p-3 text-right font-mono font-extrabold text-emerald-700">
                                +{{ number_format($in->qty_change, 2) }} {{ $in->product->unit }}
                            </td>
                            <td class="p-3 text-right font-mono font-semibold text-slate-700">
                                {{ number_format($in->qty_after, 2) }} {{ $in->product->unit }}
                            </td>
                            <td class="p-3 text-slate-600 font-medium text-[11px]">{{ $in->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500 font-semibold">Belum ada riwayat penerimaan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentStockIns->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $recentStockIns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('stockInApp', () => ({
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
