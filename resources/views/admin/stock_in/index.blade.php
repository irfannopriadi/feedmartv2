@extends('layouts.app')

@section('title', 'Penerimaan Stok Barang - Feedmart')
@section('page_title', 'Penerimaan Stok & Batch Kedaluwarsa (Stock In)')

@section('content')
<div class="space-y-6" x-data="stockInApp()">
    <!-- Form Penerimaan Stok Baru (Neo-Brutalist Minimalism) -->
    <div class="neo-box p-6">
        <div class="flex items-center space-x-3.5 pb-4 mb-4 border-b-2 border-black">
            <div class="w-10 h-10 rounded-xl bg-sky-300 border-2 border-black flex items-center justify-center text-xl font-black shadow-[2px_2px_0px_#000]">
                📥
            </div>
            <div>
                <h3 class="text-sm font-black text-black uppercase tracking-wide">Input Faktur Penerimaan Barang Distributor</h3>
                <p class="text-xs font-bold text-zinc-600">Catat penerimaan stok, supplier distributor, nomor batch dan tanggal kedaluwarsa FIFO.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-in.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf

            <!-- Pilih Produk -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-black text-black uppercase mb-1">Pilih Produk Pakan <span class="text-rose-600">*</span></label>
                <select name="product_id" required class="neo-input px-3 py-2 text-xs font-bold">
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
                    <label class="block text-xs font-black text-black uppercase">Supplier Distributor</label>
                    <button type="button" @click="showSupplierModal = true" class="neo-btn px-2 py-0.5 bg-yellow-300 hover:bg-yellow-200 text-black text-[10px] font-black shadow-[1.5px_1.5px_0px_#000]">
                        + Buat Baru
                    </button>
                </div>

                <div x-show="!showDirectInput">
                    <select name="supplier_id" x-ref="supplierSelect" class="neo-input px-3 py-2 text-xs font-bold">
                        <option value="">-- Pilih Supplier Distributor (Opsional) --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="showDirectInput" x-cloak class="mt-1">
                    <input type="text" name="new_supplier_name" placeholder="Ketik nama distributor baru..."
                        class="neo-input px-3.5 py-2 text-xs font-bold border-emerald-600">
                </div>

                <button type="button" @click="showDirectInput = !showDirectInput" class="text-[10px] font-bold text-zinc-600 hover:text-black underline mt-1 inline-block">
                    <span x-text="showDirectInput ? '← Kembali pilih dari daftar supplier' : '⚡ Atau ketik langsung distributor baru'"></span>
                </button>
            </div>

            <!-- Jumlah Masuk -->
            <div>
                <label class="block text-xs font-black text-black uppercase mb-1">Jumlah Masuk <span class="text-rose-600">*</span></label>
                <input type="number" step="0.01" min="0.01" name="qty" value="{{ old('qty', '10.00') }}" required
                    class="neo-input px-3 py-2 text-xs font-mono font-black">
            </div>

            <!-- Harga Beli Baru -->
            <div>
                <label class="block text-xs font-black text-black uppercase mb-1">Harga Beli / HPP Baru (Rp)</label>
                <input type="number" step="100" min="0" name="cost_price" value="{{ old('cost_price') }}" placeholder="Kosongkan jika tetap"
                    class="neo-input px-3 py-2 text-xs font-mono font-bold">
            </div>

            <!-- Nomor Batch -->
            <div>
                <label class="block text-xs font-black text-black uppercase mb-1">Nomor Batch Pabrik</label>
                <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="Contoh: B-2026-X1"
                    class="neo-input px-3 py-2 text-xs font-mono font-bold">
            </div>

            <!-- Tanggal Kedaluwarsa FIFO -->
            <div>
                <label class="block text-xs font-black text-black uppercase mb-1">Tanggal Kedaluwarsa (Exp) <span class="text-rose-600">*</span></label>
                <input type="date" name="expired_date" value="{{ old('expired_date', \Carbon\Carbon::now()->addMonths(6)->toDateString()) }}" required
                    class="neo-input px-3 py-2 text-xs font-bold">
            </div>

            <!-- Nomor Faktur -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-black text-black uppercase mb-1">Nomor Faktur / Surat Jalan</label>
                <input type="text" name="invoice_ref" value="{{ old('invoice_ref') }}" placeholder="Contoh: SJ-CP-9921"
                    class="neo-input px-3 py-2 text-xs font-mono font-bold">
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-end">
                <button type="submit" class="neo-btn w-full py-2.5 px-4 bg-sky-300 hover:bg-sky-200 text-black text-xs font-black uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                    + Simpan Penerimaan
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

    <!-- Riwayat Penerimaan Stok Terkini -->
    <div class="neo-box overflow-hidden">
        <div class="p-4 bg-black text-white border-b-2 border-black flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wide">Riwayat Transaksi Masuk (Stock In)</h3>
            <span class="neo-badge bg-sky-300 text-black">Total {{ $recentStockIns->total() }} Log</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-zinc-100 text-black font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Penerima</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3 text-right">Jumlah Masuk</th>
                        <th class="p-3 text-right">Stok Setelahnya</th>
                        <th class="p-3">Faktur & Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($recentStockIns as $in)
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="p-3 font-mono text-zinc-600 whitespace-nowrap">{{ $in->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-bold">{{ $in->user->name }}</td>
                            <td class="p-3 font-black text-black">{{ $in->product->name }}</td>
                            <td class="p-3 text-right font-mono font-black text-black">
                                +{{ number_format($in->qty_change, 2) }} {{ $in->product->unit }}
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-zinc-700">
                                {{ number_format($in->qty_after, 2) }} {{ $in->product->unit }}
                            </td>
                            <td class="p-3 text-zinc-600 font-bold text-[11px]">{{ $in->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-zinc-500 font-bold">Belum ada riwayat penerimaan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentStockIns->hasPages())
            <div class="p-4 border-t-2 border-black bg-zinc-50">
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
