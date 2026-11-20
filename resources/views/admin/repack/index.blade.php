@extends('layouts.app')

@section('title', 'Konversi Repack Karung ke Eceran - Feedmart')
@section('page_title', 'Konversi & Bongkar Karung Pakan (Repack)')

@section('content')
<div class="space-y-6" x-data="repackApp()">
    <!-- Explanatory Banner (Full Light Glassmorphism) -->
    <div class="glass-box p-5 bg-violet-500/10 border-violet-400/35 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-500 border border-white/80 flex items-center justify-center text-2xl text-white shadow-md shadow-purple-500/20 flex-shrink-0">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wide text-slate-900">Multi-Satuan &amp; Repacking Karung Pakan</h3>
                <p class="text-xs font-medium text-slate-600 mt-0.5">Bongkar stok 1 Karung (misal 50 kg) menjadi 50 item eceran secara atomik tanpa selisih akuntansi.</p>
            </div>
        </div>
        <button type="button" @click="openRepackModal()" class="glass-btn px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20 flex-shrink-0">
            + Eksekusi Bongkar Karung
        </button>
    </div>

    <!-- Repack Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($repackProducts as $item)
            <div class="glass-box p-5 flex flex-col justify-between hover:-translate-y-0.5 hover:shadow-lg transition-all">
                <div>
                    <!-- Header Badges -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="glass-badge bg-violet-500/15 text-violet-800 border-violet-400/35">
                            Produk Eceran
                        </span>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $item->animalCategory?->name ?? 'Pakan' }}
                        </span>
                    </div>

                    <!-- Repack Name -->
                    <h4 class="font-bold text-sm text-slate-900">{{ $item->name }}</h4>
                    <span class="text-[10px] text-slate-500 font-mono font-semibold block mt-0.5">SKU: {{ $item->sku }}</span>

                    <!-- Visual Conversion Formula -->
                    <div class="my-3.5 p-3 rounded-xl bg-white/55 border border-white/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Karung Induk:</span>
                            <span class="font-bold text-slate-900 text-right">{{ $item->parentProduct?->name ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60">
                            <span class="text-slate-500 font-semibold">Rasio Konversi:</span>
                            <span class="font-mono font-extrabold text-violet-800">1 {{ $item->parentProduct?->unit ?? 'Sak' }} = {{ number_format($item->conversion_ratio, 0) }} {{ $item->unit }}</span>
                        </div>
                    </div>

                    <!-- Stock Status Comparison -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-sky-500/12 border border-sky-400/35 text-slate-900">
                            <span class="text-[9px] text-sky-800 block uppercase font-bold">Stok Induk</span>
                            <span class="text-base font-extrabold font-mono block">{{ number_format($item->parentProduct?->stock ?? 0, 2) }}</span>
                            <span class="text-[10px] font-semibold text-slate-600">{{ $item->parentProduct?->unit ?? 'Sak' }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-violet-500/12 border border-violet-400/35 text-slate-900">
                            <span class="text-[9px] text-violet-800 block uppercase font-bold">Stok Eceran</span>
                            <span class="text-base font-extrabold font-mono block">{{ number_format($item->stock, 2) }}</span>
                            <span class="text-[10px] font-semibold text-slate-600">{{ $item->unit }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200/70 flex items-center justify-between gap-2">
                    <span class="text-xs font-extrabold font-mono text-slate-900">Rp {{ number_format($item->selling_price, 0, ',', '.') }} / {{ $item->unit }}</span>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.barcode.index', ['product_id' => $item->id]) }}" target="_blank"
                            class="glass-btn px-2.5 py-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-900 border-amber-400/35 font-bold text-xs" title="Cetak Barcode Stiker">
                            🏷️
                        </a>
                        <button type="button" @click="selectForRepack({{ $item }})"
                            class="glass-btn px-3 py-1.5 bg-violet-500/15 hover:bg-violet-500/25 text-violet-900 border-violet-400/35 font-bold text-xs">
                            Bongkar Sak →
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 glass-box p-8 text-center text-slate-500 font-semibold text-xs">
                Belum ada produk pakan yang diset sebagai eceran (repack). Tambahkan atau edit produk di menu Master Produk.
            </div>
        @endforelse
    </div>

    <!-- Repack Mutation History -->
    <div class="glass-box overflow-hidden">
        <div class="p-4 bg-white/45 border-b border-white/70 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wider">Histori Audit Konversi Repack</h3>
            <span class="glass-badge bg-amber-400/20 text-amber-900 border-amber-400/35">Sinkron FIFO Batch</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Operator</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Produk Terkait</th>
                        <th class="p-3 text-right">Perubahan</th>
                        <th class="p-3 text-right">Sisa Stok</th>
                        <th class="p-3">Keterangan Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($recentRepackLogs as $log)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-3 font-mono text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $log->user->name }}</td>
                            <td class="p-3">
                                <span class="glass-badge {{ $log->type === 'REPACK_OUT' ? 'bg-amber-400/25 text-amber-900 border-amber-400/40' : 'bg-violet-500/15 text-violet-900 border-violet-400/35' }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-900">{{ $log->product->name }}</td>
                            <td class="p-3 text-right font-mono font-extrabold {{ $log->qty_change < 0 ? 'text-rose-600' : 'text-violet-700' }}">
                                {{ $log->qty_change > 0 ? '+' : '' }}{{ number_format($log->qty_change, 2) }} {{ $log->product->unit }}
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-slate-800">
                                {{ number_format($log->qty_after, 2) }} {{ $log->product->unit }}
                            </td>
                            <td class="p-3 text-slate-600 font-medium text-[11px]">{{ $log->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 font-semibold">Belum ada riwayat aktivitas bongkar karung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- EXECUTE REPACK MODAL -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-lg p-6 relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase">Bongkar Karung ke Eceran (Repack)</h3>
                <button @click="showModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.repack.store') }}" class="space-y-4 my-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Eceran Target <span class="text-rose-500">*</span></label>
                    <select name="repack_product_id" x-model="selectedProductId" @change="onProductSelect()" required
                        class="glass-input px-3 py-2 text-xs font-semibold">
                        <option value="">-- Pilih Produk Eceran --</option>
                        @foreach($repackProducts as $item)
                            <option value="{{ $item->id }}" data-item="{{ json_encode($item) }}">
                                {{ $item->name }} (1 {{ $item->parentProduct?->unit ?? 'Sak' }} = {{ number_format($item->conversion_ratio, 0) }} {{ $item->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="activeProduct" class="p-3.5 rounded-2xl bg-violet-500/10 border border-violet-400/35 text-xs space-y-1.5 backdrop-blur-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-semibold">Karung Induk Pengurang:</span>
                        <span class="font-bold text-slate-900" x-text="activeProduct?.parent_product?.name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-semibold">Stok Karung Tersedia:</span>
                        <span class="font-mono font-extrabold text-slate-900" x-text="Number(activeProduct?.parent_product?.stock || 0).toFixed(2) + ' ' + (activeProduct?.parent_product?.unit || 'Sak')"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Jumlah Karung yang Dibongkar (Sak) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.5" min="0.5" name="parent_qty" x-model.number="parentQty" required
                            class="glass-input px-3.5 py-2.5 text-base font-mono font-extrabold">
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button type="button" @click="parentQty = 1" class="glass-btn px-3 py-1 bg-white/75 text-slate-700 text-xs font-bold">1 Sak</button>
                        <button type="button" @click="parentQty = 2" class="glass-btn px-3 py-1 bg-white/75 text-slate-700 text-xs font-bold">2 Sak</button>
                        <button type="button" @click="parentQty = 5" class="glass-btn px-3 py-1 bg-white/75 text-slate-700 text-xs font-bold">5 Sak</button>
                    </div>
                </div>

                <!-- Hasil Kalkulasi Penambahan Stok -->
                <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-400/35 text-emerald-950 backdrop-blur-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider block text-emerald-800">Simulasi Penambahan Stok:</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-xs font-semibold">Stok Eceran Bertambah:</span>
                        <span class="text-xl font-extrabold font-mono text-emerald-900" x-text="'+ ' + (parentQty * (activeProduct?.conversion_ratio || 0)).toFixed(2) + ' ' + (activeProduct?.unit || 'Kg')"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showModal = false" class="glass-btn px-4 py-2 bg-white/75 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="!activeProduct || parentQty <= 0"
                        class="glass-btn px-5 py-2 bg-gradient-to-r from-violet-500 to-purple-500 hover:from-violet-400 hover:to-purple-400 text-white border-violet-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-500/20">
                        Bongkar Karung Sekarang
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
    Alpine.data('repackApp', () => ({
        showModal: false,
        selectedProductId: '',
        activeProduct: null,
        parentQty: 1,

        openRepackModal() {
            this.showModal = true;
        },

        selectForRepack(product) {
            this.selectedProductId = product.id;
            this.activeProduct = product;
            this.parentQty = 1;
            this.showModal = true;
        },

        onProductSelect() {
            const select = document.querySelector('select[name=repack_product_id]');
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.item) {
                this.activeProduct = JSON.parse(option.dataset.item);
            } else {
                this.activeProduct = null;
            }
        }
    }));
});
</script>
@endpush
