@extends('layouts.app')

@section('title', 'Audit Mutasi & Stock Opname - Feedmart')
@section('page_title', 'Audit Mutasi Stok & Penyesuaian Opname')

@section('content')
<div class="space-y-6" x-data="opnameApp()">
    <!-- Top Action Card: Penyesuaian Fisik Opname -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Form Penyesuaian Fisik (Stock Opname)</h3>
                <p class="text-xs text-slate-500">Sesuaikan stok fisik akibat selisih opname berkala, susut pakan timbangan, karung bocor, atau pakan rusak.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-opname.adjust') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf

            <!-- Pilih Produk -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Pakan <span class="text-rose-500">*</span></label>
                <select name="product_id" x-model="selectedProductId" @change="onSelectProduct()" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-amber-500">
                    <option value="">-- Pilih Produk Pakan --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-stock="{{ $p->stock }}" data-unit="{{ $p->unit }}">
                            {{ $p->name }} (Sistem: {{ number_format($p->stock, 2) }} {{ $p->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stok Fisik Nyata -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Stok Fisik Nyata <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" name="actual_physical_stock" x-model.number="actualStock" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-black text-slate-800 focus:bg-white focus:border-amber-500">
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400" x-text="unit"></span>
                </div>
            </div>

            <!-- Alasan Penyesuaian -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Penyesuaian <span class="text-rose-500">*</span></label>
                <input type="text" name="adjustment_reason" placeholder="Contoh: Susut pakan / Karung bocor / Opname" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-amber-500">
            </div>

            <!-- Live Difference Card -->
            <div x-show="selectedProductId" class="lg:col-span-3 p-3 rounded-xl border text-xs flex items-center justify-between"
                :class="diff === 0 ? 'bg-slate-50 border-slate-200 text-slate-600' : (diff > 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800')">
                <div>
                    <span class="font-bold">Stok Sistem: <span x-text="currentStock.toFixed(2)"></span> <span x-text="unit"></span></span>
                    <span class="mx-2">➔</span>
                    <span class="font-bold">Fisik Nyata: <span x-text="Number(actualStock || 0).toFixed(2)"></span> <span x-text="unit"></span></span>
                </div>
                <div class="font-black text-sm">
                    Selisih: <span x-text="(diff > 0 ? '+' : '') + diff.toFixed(2) + ' ' + unit"></span>
                </div>
            </div>

            <div x-show="selectedProductId" class="flex items-end">
                <button type="submit" :disabled="diff === 0"
                    class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs shadow-md transition transform active:scale-95">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>

    <!-- Filter & Complete Audit Mutations Log -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-sm text-slate-800">Jurnal Audit Mutasi Inventaris Lengkap</h3>
                <p class="text-[11px] text-slate-400">Pencatatan transparan semua pergerakan stok (Penjualan, Repack, Penerimaan, Opname).</p>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.stock-opname.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-700">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>IN (Penerimaan)</option>
                    <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>OUT (Penjualan POS)</option>
                    <option value="REPACK_OUT" {{ request('type') === 'REPACK_OUT' ? 'selected' : '' }}>REPACK_OUT (Karung Bongkar)</option>
                    <option value="REPACK_IN" {{ request('type') === 'REPACK_IN' ? 'selected' : '' }}>REPACK_IN (Eceran Masuk)</option>
                    <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT (Opname)</option>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-700">
                <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-700">
                <a href="{{ route('admin.stock-opname.excel', request()->all()) }}" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-xs" title="Unduh Jurnal Mutasi ke Excel">
                    <span>📊</span>
                    <span>Ekspor Excel</span>
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3 text-right">Stok Awal</th>
                        <th class="p-3 text-right">Perubahan</th>
                        <th class="p-3 text-right">Stok Akhir</th>
                        <th class="p-3">Catatan Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mutations as $m)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 text-slate-400 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $m->user->name }}</td>
                            <td class="p-3 font-bold text-[10px]">
                                <span class="px-2 py-0.5 rounded
                                    {{ $m->type === 'IN' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $m->type === 'OUT' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $m->type === 'REPACK_OUT' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $m->type === 'REPACK_IN' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $m->type === 'ADJUSTMENT' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $m->type }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-900">{{ $m->product->name }}</td>
                            <td class="p-3 text-right text-slate-500">{{ number_format($m->qty_before, 2) }}</td>
                            <td class="p-3 text-right font-black {{ $m->qty_change < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $m->qty_change > 0 ? '+' : '' }}{{ number_format($m->qty_change, 2) }} {{ $m->product->unit }}
                            </td>
                            <td class="p-3 text-right font-bold text-slate-800">{{ number_format($m->qty_after, 2) }}</td>
                            <td class="p-3 text-slate-500 text-[11px]">{{ $m->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Tidak ada riwayat mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $mutations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('opnameApp', () => ({
        selectedProductId: '',
        currentStock: 0,
        actualStock: 0,
        unit: '',

        get diff() {
            return this.actualStock - this.currentStock;
        },

        onSelectProduct() {
            const select = document.querySelector('select[name=product_id]');
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.stock !== undefined) {
                this.currentStock = parseFloat(option.dataset.stock);
                this.actualStock = this.currentStock;
                this.unit = option.dataset.unit || '';
            } else {
                this.currentStock = 0;
                this.actualStock = 0;
                this.unit = '';
            }
        }
    }));
});
</script>
@endpush
