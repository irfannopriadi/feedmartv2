@extends('layouts.app')

@section('title', 'Audit Mutasi & Stock Opname - Feedmart')
@section('page_title', 'Audit Mutasi Stok & Penyesuaian Opname')

@section('content')
<div class="space-y-6" x-data="opnameApp()">
    <!-- Top Action Card: Penyesuaian Fisik Opname (Neo-Brutalist Minimalism) -->
    <div class="neo-box p-6">
        <div class="flex items-center space-x-3.5 pb-4 mb-4 border-b-2 border-black">
            <div class="w-10 h-10 rounded-xl bg-amber-300 border-2 border-black flex items-center justify-center text-xl font-black shadow-[2px_2px_0px_#000]">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-black text-black uppercase tracking-wide">Form Penyesuaian Fisik (Stock Opname)</h3>
                <p class="text-xs font-bold text-zinc-600">Sesuaikan stok fisik akibat selisih opname berkala, susut pakan timbangan, karung bocor, atau pakan rusak.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-opname.adjust') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf

            <!-- Pilih Produk -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-black text-black uppercase mb-1">Pilih Produk Pakan <span class="text-rose-600">*</span></label>
                <select name="product_id" x-model="selectedProductId" @change="onSelectProduct()" required
                    class="neo-input px-3 py-2 text-xs font-bold">
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
                <label class="block text-xs font-black text-black uppercase mb-1">
                    Stok Fisik Nyata <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" name="actual_physical_stock" x-model.number="actualStock" required
                        class="neo-input px-3 py-2 text-xs font-mono font-black">
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-black text-zinc-500" x-text="unit"></span>
                </div>
            </div>

            <!-- Alasan Penyesuaian -->
            <div>
                <label class="block text-xs font-black text-black uppercase mb-1">Alasan Penyesuaian <span class="text-rose-600">*</span></label>
                <input type="text" name="adjustment_reason" placeholder="Contoh: Susut timbangan / Karung bocor" required
                    class="neo-input px-3 py-2 text-xs font-bold">
            </div>

            <!-- Live Difference Card -->
            <div x-show="selectedProductId" class="lg:col-span-3 p-3.5 rounded-xl border-2 border-black text-xs flex items-center justify-between shadow-[3px_3px_0px_#000]"
                :class="diff === 0 ? 'bg-zinc-100 text-black' : (diff > 0 ? 'bg-emerald-200 text-black' : 'bg-rose-200 text-black')">
                <div class="font-bold">
                    <span>Stok Sistem: <strong class="font-mono" x-text="currentStock.toFixed(2)"></strong> <span x-text="unit"></span></span>
                    <span class="mx-2 font-black">➔</span>
                    <span>Fisik Nyata: <strong class="font-mono" x-text="Number(actualStock || 0).toFixed(2)"></strong> <span x-text="unit"></span></span>
                </div>
                <div class="font-black font-mono text-sm">
                    Selisih: <span x-text="(diff > 0 ? '+' : '') + diff.toFixed(2) + ' ' + unit"></span>
                </div>
            </div>

            <div x-show="selectedProductId" class="flex items-end">
                <button type="submit" :disabled="diff === 0"
                    class="neo-btn w-full py-2.5 px-4 bg-amber-300 hover:bg-amber-200 disabled:opacity-50 text-black text-xs font-black uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>

    <!-- Filter & Complete Audit Mutations Log -->
    <div class="neo-box overflow-hidden">
        <div class="p-4 border-b-2 border-black flex flex-col md:flex-row md:items-center justify-between gap-3 bg-zinc-100">
            <div>
                <h3 class="font-black text-sm text-black uppercase tracking-wide">Jurnal Audit Mutasi Inventaris Lengkap</h3>
                <p class="text-[11px] font-bold text-zinc-600">Pencatatan transparan semua pergerakan stok (Penjualan, Repack, Penerimaan, Opname).</p>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.stock-opname.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="neo-input px-2.5 py-1.5 text-xs font-bold w-auto">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>IN (Penerimaan)</option>
                    <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>OUT (Penjualan POS)</option>
                    <option value="REPACK_OUT" {{ request('type') === 'REPACK_OUT' ? 'selected' : '' }}>REPACK_OUT (Karung Bongkar)</option>
                    <option value="REPACK_IN" {{ request('type') === 'REPACK_IN' ? 'selected' : '' }}>REPACK_IN (Eceran Masuk)</option>
                    <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT (Opname)</option>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()" class="neo-input px-2.5 py-1.5 text-xs font-bold w-auto">
                <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()" class="neo-input px-2.5 py-1.5 text-xs font-bold w-auto">
                
                <a href="{{ route('admin.stock-opname.excel', request()->all()) }}" class="neo-btn px-3 py-1.5 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black shadow-[2px_2px_0px_#000]" title="Unduh Jurnal Mutasi ke Excel">
                    <span>📊 Ekspor Excel</span>
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-black text-white font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($mutations as $m)
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="p-3 font-mono text-zinc-600 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-bold">{{ $m->user->name }}</td>
                            <td class="p-3">
                                <span class="neo-badge
                                    {{ $m->type === 'IN' ? 'bg-sky-300 text-black' : '' }}
                                    {{ $m->type === 'OUT' ? 'bg-emerald-300 text-black' : '' }}
                                    {{ $m->type === 'REPACK_OUT' ? 'bg-amber-300 text-black' : '' }}
                                    {{ $m->type === 'REPACK_IN' ? 'bg-purple-300 text-black' : '' }}
                                    {{ $m->type === 'ADJUSTMENT' ? 'bg-rose-300 text-black' : '' }}
                                ">
                                    {{ $m->type }}
                                </span>
                            </td>
                            <td class="p-3 font-black text-black">{{ $m->product->name }}</td>
                            <td class="p-3 text-right font-mono text-zinc-600 font-bold">{{ number_format($m->qty_before, 2) }}</td>
                            <td class="p-3 text-right font-mono font-black {{ $m->qty_change < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ $m->qty_change > 0 ? '+' : '' }}{{ number_format($m->qty_change, 2) }} {{ $m->product->unit }}
                            </td>
                            <td class="p-3 text-right font-mono font-black text-black">{{ number_format($m->qty_after, 2) }}</td>
                            <td class="p-3 text-zinc-600 font-bold text-[11px]">{{ $m->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-zinc-500 font-bold">Tidak ada riwayat mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutations->hasPages())
            <div class="p-4 border-t-2 border-black bg-zinc-50">
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
