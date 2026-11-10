@extends('layouts.app')

@section('title', 'Konversi Repack Karung ke Eceran - Feedmart')
@section('page_title', 'Konversi & Bongkar Karung Pakan (Repack)')

@section('content')
<div class="space-y-6" x-data="repackApp()">
    <!-- Explanatory Banner -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-900 to-indigo-900 text-white shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-purple-500/20 border border-purple-400/30 flex items-center justify-center text-2xl font-bold">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wide">Multi-Satuan & Repacking Karung Pakan</h3>
                <p class="text-xs text-purple-200 mt-0.5">Bongkar stok 1 Karung (misal 50 kg) menjadi 50 item eceran secara atomik tanpa selisih akuntansi.</p>
            </div>
        </div>
        <button type="button" @click="openRepackModal()" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold text-xs rounded-xl shadow transition">
            + Eksekusi Bongkar Karung
        </button>
    </div>

    <!-- Repack Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($repackProducts as $item)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between hover:border-purple-300 transition">
                <div>
                    <!-- Header Badges -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-bold text-[10px] border border-purple-200 uppercase">
                            Produk Eceran
                        </span>
                        <span class="text-xs font-semibold text-slate-400">
                            {{ $item->animalCategory?->name ?? 'Pakan' }}
                        </span>
                    </div>

                    <!-- Repack Name -->
                    <h4 class="font-bold text-sm text-slate-900">{{ $item->name }}</h4>
                    <span class="text-[10px] text-slate-400 font-mono block">SKU: {{ $item->sku }}</span>

                    <!-- Visual Conversion Formula -->
                    <div class="my-4 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Karung Induk:</span>
                            <span class="font-bold text-slate-800 text-right">{{ $item->parentProduct?->name ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                            <span class="text-slate-500">Rasio Konversi:</span>
                            <span class="font-extrabold text-purple-700">1 {{ $item->parentProduct?->unit ?? 'Sak' }} = {{ number_format($item->conversion_ratio, 0) }} {{ $item->unit }}</span>
                        </div>
                    </div>

                    <!-- Stock Status Comparison -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-blue-50 border border-blue-100 text-blue-900">
                            <span class="text-[10px] text-blue-600 block uppercase font-bold">Stok Induk</span>
                            <span class="text-base font-black">{{ number_format($item->parentProduct?->stock ?? 0, 2) }}</span>
                            <span class="text-[10px] font-semibold text-blue-500">{{ $item->parentProduct?->unit ?? 'Sak' }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-purple-50 border border-purple-100 text-purple-900">
                            <span class="text-[10px] text-purple-600 block uppercase font-bold">Stok Eceran</span>
                            <span class="text-base font-black">{{ number_format($item->stock, 2) }}</span>
                            <span class="text-[10px] font-semibold text-purple-500">{{ $item->unit }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-700">Rp {{ number_format($item->selling_price, 0, ',', '.') }} / {{ $item->unit }}</span>
                    <button type="button" @click="selectForRepack({{ $item }})"
                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Bongkar Sak →
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                Belum ada produk pakan yang diset sebagai eceran (repack). Tambahkan atau edit produk di menu Master Produk.
            </div>
        @endforelse
    </div>

    <!-- Repack Mutation History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">Histori Audit Konversi Repack Terkini</h3>
            <span class="text-xs text-slate-400">Sinkronisasi Otomatis FIFO Batch</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentRepackLogs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-medium text-slate-800">{{ $log->user->name }}</td>
                            <td class="p-3 font-bold text-[10px]">
                                <span class="px-2 py-0.5 rounded {{ $log->type === 'REPACK_OUT' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-800">{{ $log->product->name }}</td>
                            <td class="p-3 text-right font-black {{ $log->qty_change < 0 ? 'text-amber-600' : 'text-purple-600' }}">
                                {{ $log->qty_change > 0 ? '+' : '' }}{{ number_format($log->qty_change, 2) }} {{ $log->product->unit }}
                            </td>
                            <td class="p-3 text-right font-semibold text-slate-700">
                                {{ number_format($log->qty_after, 2) }} {{ $log->product->unit }}
                            </td>
                            <td class="p-3 text-slate-500 text-[11px]">{{ $log->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Belum ada riwayat aktivitas bongkar karung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- EXECUTE REPACK MODAL -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-800">Bongkar Karung ke Eceran (Repack)</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.repack.store') }}" class="space-y-4 my-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Eceran Target <span class="text-rose-500">*</span></label>
                    <select name="repack_product_id" x-model="selectedProductId" @change="onProductSelect()" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-purple-500">
                        <option value="">-- Pilih Produk Eceran --</option>
                        @foreach($repackProducts as $item)
                            <option value="{{ $item->id }}" data-item="{{ json_encode($item) }}">
                                {{ $item->name }} (1 {{ $item->parentProduct?->unit ?? 'Sak' }} = {{ number_format($item->conversion_ratio, 0) }} {{ $item->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="activeProduct" class="p-3.5 rounded-xl bg-purple-50 border border-purple-200 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Karung Induk Pengurang:</span>
                        <span class="font-bold text-slate-800" x-text="activeProduct?.parent_product?.name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Stok Karung Tersedia:</span>
                        <span class="font-extrabold text-blue-700" x-text="Number(activeProduct?.parent_product?.stock || 0).toFixed(2) + ' ' + (activeProduct?.parent_product?.unit || 'Sak')"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                        Jumlah Karung yang Dibongkar (Sak) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.5" min="0.5" name="parent_qty" x-model.number="parentQty" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-base font-black text-slate-800 focus:bg-white focus:border-purple-500">
                    </div>
                    <div class="flex gap-2 mt-1.5">
                        <button type="button" @click="parentQty = 1" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-xs font-bold">1 Sak</button>
                        <button type="button" @click="parentQty = 2" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-xs font-bold">2 Sak</button>
                        <button type="button" @click="parentQty = 5" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 rounded text-xs font-bold">5 Sak</button>
                    </div>
                </div>

                <!-- Hasil Kalkulasi Penambahan Stok -->
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900">
                    <span class="text-[10px] font-bold uppercase tracking-wider block text-emerald-700">Simulasi Penambahan Stok:</span>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-xs font-semibold">Stok Eceran Bertambah:</span>
                        <span class="text-xl font-black text-emerald-700" x-text="'+ ' + (parentQty * (activeProduct?.conversion_ratio || 0)).toFixed(2) + ' ' + (activeProduct?.unit || 'Kg')"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="!activeProduct || parentQty <= 0"
                        class="px-5 py-2 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs shadow-md transition">
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
