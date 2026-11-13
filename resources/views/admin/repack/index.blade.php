@extends('layouts.app')

@section('title', 'Konversi Repack Karung ke Eceran - Feedmart')
@section('page_title', 'Konversi & Bongkar Karung Pakan (Repack)')

@section('content')
<div class="space-y-6" x-data="repackApp()">
    <!-- Explanatory Banner (Neo-Brutalist Minimalism) -->
    <div class="p-5 rounded-2xl bg-purple-200 border-2 border-black text-black shadow-[4px_4px_0px_#000] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-purple-400 border-2 border-black flex items-center justify-center text-2xl font-black shadow-[2px_2px_0px_#000] flex-shrink-0">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-wide text-black">Multi-Satuan & Repacking Karung Pakan</h3>
                <p class="text-xs font-bold text-zinc-700 mt-0.5">Bongkar stok 1 Karung (misal 50 kg) menjadi 50 item eceran secara atomik tanpa selisih akuntansi.</p>
            </div>
        </div>
        <button type="button" @click="openRepackModal()" class="neo-btn px-4 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black uppercase tracking-wider shadow-[3px_3px_0px_#000] flex-shrink-0">
            + Eksekusi Bongkar Karung
        </button>
    </div>

    <!-- Repack Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($repackProducts as $item)
            <div class="neo-box p-5 flex flex-col justify-between hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_#000] transition-all">
                <div>
                    <!-- Header Badges -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="neo-badge bg-purple-300 text-black">
                            Produk Eceran
                        </span>
                        <span class="text-xs font-bold text-zinc-600">
                            {{ $item->animalCategory?->name ?? 'Pakan' }}
                        </span>
                    </div>

                    <!-- Repack Name -->
                    <h4 class="font-black text-sm text-black">{{ $item->name }}</h4>
                    <span class="text-[10px] text-zinc-500 font-mono font-bold block mt-0.5">SKU: {{ $item->sku }}</span>

                    <!-- Visual Conversion Formula -->
                    <div class="my-3.5 p-3 rounded-lg bg-zinc-50 border-2 border-black text-xs space-y-1.5 shadow-[2px_2px_0px_#000]">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-600 font-bold">Karung Induk:</span>
                            <span class="font-black text-black text-right">{{ $item->parentProduct?->name ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t-2 border-black/10">
                            <span class="text-zinc-600 font-bold">Rasio Konversi:</span>
                            <span class="font-mono font-black text-purple-900">1 {{ $item->parentProduct?->unit ?? 'Sak' }} = {{ number_format($item->conversion_ratio, 0) }} {{ $item->unit }}</span>
                        </div>
                    </div>

                    <!-- Stock Status Comparison -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-sky-100 border-2 border-black text-black shadow-[2px_2px_0px_#000]">
                            <span class="text-[9px] text-zinc-600 block uppercase font-black">Stok Induk</span>
                            <span class="text-base font-black font-mono block">{{ number_format($item->parentProduct?->stock ?? 0, 2) }}</span>
                            <span class="text-[10px] font-bold text-zinc-600">{{ $item->parentProduct?->unit ?? 'Sak' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-purple-100 border-2 border-black text-black shadow-[2px_2px_0px_#000]">
                            <span class="text-[9px] text-zinc-600 block uppercase font-black">Stok Eceran</span>
                            <span class="text-base font-black font-mono block">{{ number_format($item->stock, 2) }}</span>
                            <span class="text-[10px] font-bold text-zinc-600">{{ $item->unit }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t-2 border-black flex items-center justify-between gap-2">
                    <span class="text-xs font-black font-mono text-black">Rp {{ number_format($item->selling_price, 0, ',', '.') }} / {{ $item->unit }}</span>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.barcode.index', ['product_id' => $item->id]) }}" target="_blank"
                            class="neo-btn px-2.5 py-1.5 bg-yellow-200 hover:bg-yellow-300 text-black font-black text-xs" title="Cetak Barcode Stiker">
                            🏷️
                        </a>
                        <button type="button" @click="selectForRepack({{ $item }})"
                            class="neo-btn px-3 py-1.5 bg-purple-300 hover:bg-purple-200 text-black font-black text-xs">
                            Bongkar Sak →
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 neo-box p-8 text-center text-zinc-500 font-bold text-xs">
                Belum ada produk pakan yang diset sebagai eceran (repack). Tambahkan atau edit produk di menu Master Produk.
            </div>
        @endforelse
    </div>

    <!-- Repack Mutation History -->
    <div class="neo-box overflow-hidden">
        <div class="p-4 bg-black text-white border-b-2 border-black flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wider">Histori Audit Konversi Repack</h3>
            <span class="neo-badge bg-yellow-300 text-black">Sinkron FIFO Batch</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-zinc-100 text-black font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($recentRepackLogs as $log)
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="p-3 font-mono text-zinc-600 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-bold">{{ $log->user->name }}</td>
                            <td class="p-3">
                                <span class="neo-badge {{ $log->type === 'REPACK_OUT' ? 'bg-amber-300 text-black' : 'bg-purple-300 text-black' }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="p-3 font-black text-black">{{ $log->product->name }}</td>
                            <td class="p-3 text-right font-mono font-black {{ $log->qty_change < 0 ? 'text-rose-600' : 'text-purple-700' }}">
                                {{ $log->qty_change > 0 ? '+' : '' }}{{ number_format($log->qty_change, 2) }} {{ $log->product->unit }}
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-black">
                                {{ number_format($log->qty_after, 2) }} {{ $log->product->unit }}
                            </td>
                            <td class="p-3 text-zinc-600 font-bold text-[11px]">{{ $log->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-zinc-500 font-bold">Belum ada riwayat aktivitas bongkar karung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- EXECUTE REPACK MODAL -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-lg p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <h3 class="text-base font-black text-black uppercase">Bongkar Karung ke Eceran (Repack)</h3>
                <button @click="showModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.repack.store') }}" class="space-y-4 my-4">
                @csrf

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Pilih Produk Eceran Target <span class="text-rose-600">*</span></label>
                    <select name="repack_product_id" x-model="selectedProductId" @change="onProductSelect()" required
                        class="neo-input px-3 py-2 text-xs font-bold">
                        <option value="">-- Pilih Produk Eceran --</option>
                        @foreach($repackProducts as $item)
                            <option value="{{ $item->id }}" data-item="{{ json_encode($item) }}">
                                {{ $item->name }} (1 {{ $item->parentProduct?->unit ?? 'Sak' }} = {{ number_format($item->conversion_ratio, 0) }} {{ $item->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="activeProduct" class="p-3.5 rounded-xl bg-purple-100 border-2 border-black text-xs space-y-1.5 shadow-[2px_2px_0px_#000]">
                    <div class="flex justify-between">
                        <span class="text-zinc-700 font-bold">Karung Induk Pengurang:</span>
                        <span class="font-black text-black" x-text="activeProduct?.parent_product?.name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-700 font-bold">Stok Karung Tersedia:</span>
                        <span class="font-mono font-black text-black" x-text="Number(activeProduct?.parent_product?.stock || 0).toFixed(2) + ' ' + (activeProduct?.parent_product?.unit || 'Sak')"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">
                        Jumlah Karung yang Dibongkar (Sak) <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.5" min="0.5" name="parent_qty" x-model.number="parentQty" required
                            class="neo-input px-3.5 py-2.5 text-base font-mono font-black">
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button type="button" @click="parentQty = 1" class="neo-btn px-2.5 py-0.5 bg-zinc-100 text-black text-xs font-bold">1 Sak</button>
                        <button type="button" @click="parentQty = 2" class="neo-btn px-2.5 py-0.5 bg-zinc-100 text-black text-xs font-bold">2 Sak</button>
                        <button type="button" @click="parentQty = 5" class="neo-btn px-2.5 py-0.5 bg-zinc-100 text-black text-xs font-bold">5 Sak</button>
                    </div>
                </div>

                <!-- Hasil Kalkulasi Penambahan Stok -->
                <div class="p-4 rounded-xl bg-emerald-100 border-2 border-black text-black shadow-[3px_3px_0px_#000]">
                    <span class="text-[10px] font-black uppercase tracking-wider block text-zinc-700">Simulasi Penambahan Stok:</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-xs font-bold">Stok Eceran Bertambah:</span>
                        <span class="text-xl font-black font-mono text-black" x-text="'+ ' + (parentQty * (activeProduct?.conversion_ratio || 0)).toFixed(2) + ' ' + (activeProduct?.unit || 'Kg')"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showModal = false" class="neo-btn px-4 py-2 bg-zinc-100 text-black font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="!activeProduct || parentQty <= 0"
                        class="neo-btn px-5 py-2 bg-purple-400 hover:bg-purple-300 disabled:opacity-50 text-black font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_#000]">
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
