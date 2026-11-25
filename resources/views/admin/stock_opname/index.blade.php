@extends('layouts.app')

@section('title', 'Audit Mutasi & Stock Opname - Feedmart')
@section('page_title', 'Audit Mutasi Stok & Penyesuaian Opname')

@section('content')
<div class="space-y-6" x-data="opnameApp()">
    <!-- Top Action Card: Penyesuaian Fisik Opname (Full Light Glassmorphism) -->
    <div class="glass-box p-6">
        <div class="flex items-center space-x-3.5 pb-4 mb-4 border-b border-slate-200/70">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 border border-white/80 flex items-center justify-center text-xl text-white shadow-sm shadow-amber-500/20">
                ⚖️
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">Form Penyesuaian Fisik (Stock Opname)</h3>
                <p class="text-xs font-medium text-slate-500">Sesuaikan stok fisik akibat selisih opname berkala, susut pakan timbangan, karung bocor, atau pakan rusak.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-opname.adjust') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf

            <!-- Pilih Produk -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Pakan <span class="text-rose-500">*</span></label>
                <select name="product_id" x-model="selectedProductId" @change="onSelectProduct()" required
                    class="glass-input px-3 py-2 text-xs font-semibold">
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
                        class="glass-input px-3 py-2 text-xs font-mono font-extrabold">
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-500" x-text="unit"></span>
                </div>
            </div>

            <!-- Alasan Penyesuaian -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alasan Penyesuaian <span class="text-rose-500">*</span></label>
                <input type="text" name="adjustment_reason" placeholder="Contoh: Susut timbangan / Karung bocor" required
                    class="glass-input px-3 py-2 text-xs font-semibold">
            </div>

            <!-- Live Difference Card -->
            <div x-show="selectedProductId" class="lg:col-span-3 p-3.5 rounded-2xl border backdrop-blur-md text-xs flex items-center justify-between"
                :class="diff === 0 ? 'bg-white/60 border-white/80 text-slate-800' : (diff > 0 ? 'bg-emerald-500/15 border-emerald-400/35 text-emerald-950' : 'bg-rose-500/15 border-rose-400/35 text-rose-950')">
                <div class="font-semibold">
                    <span>Stok Sistem: <strong class="font-mono" x-text="currentStock.toFixed(2)"></strong> <span x-text="unit"></span></span>
                    <span class="mx-2 font-bold">➔</span>
                    <span>Fisik Nyata: <strong class="font-mono" x-text="Number(actualStock || 0).toFixed(2)"></strong> <span x-text="unit"></span></span>
                </div>
                <div class="font-extrabold font-mono text-sm">
                    Selisih: <span x-text="(diff > 0 ? '+' : '') + diff.toFixed(2) + ' ' + unit"></span>
                </div>
            </div>

            <div x-show="selectedProductId" class="flex items-end">
                <button type="submit" :disabled="diff === 0"
                    class="glass-btn w-full py-2.5 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white border-amber-400/50 text-xs font-bold uppercase tracking-wider shadow-md shadow-amber-500/20">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>

    <!-- Filter & Complete Audit Mutations Log -->
    <div class="glass-box overflow-hidden">
        <div class="p-4 border-b border-white/70 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white/45">
            <div>
                <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Jurnal Audit Mutasi Inventaris Lengkap</h3>
                <p class="text-[11px] font-medium text-slate-500">Pencatatan transparan semua pergerakan stok (Penjualan, Repack, Penerimaan, Opname).</p>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.stock-opname.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="glass-input px-2.5 py-1.5 text-xs font-semibold w-auto">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="IN" {{ request('type') === 'IN' ? 'selected' : '' }}>IN (Penerimaan)</option>
                    <option value="OUT" {{ request('type') === 'OUT' ? 'selected' : '' }}>OUT (Penjualan POS)</option>
                    <option value="REPACK_OUT" {{ request('type') === 'REPACK_OUT' ? 'selected' : '' }}>REPACK_OUT (Karung Bongkar)</option>
                    <option value="REPACK_IN" {{ request('type') === 'REPACK_IN' ? 'selected' : '' }}>REPACK_IN (Eceran Masuk)</option>
                    <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>ADJUSTMENT (Opname)</option>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()" class="glass-input px-2.5 py-1.5 text-xs font-semibold w-auto">
                <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()" class="glass-input px-2.5 py-1.5 text-xs font-semibold w-auto">
                
                <a href="{{ route('admin.stock-opname.excel', request()->all()) }}" class="glass-btn px-3.5 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold shadow-sm shadow-emerald-500/20" title="Unduh Jurnal Mutasi ke Excel">
                    <span>📊 Ekspor Excel</span>
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
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
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($mutations as $m)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-3 font-mono text-slate-500 whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $m->user->name }}</td>
                            <td class="p-3">
                                <span class="glass-badge
                                    {{ $m->type === 'IN' ? 'bg-sky-500/15 text-sky-800 border-sky-400/35' : '' }}
                                    {{ $m->type === 'OUT' ? 'bg-emerald-500/15 text-emerald-800 border-emerald-400/35' : '' }}
                                    {{ $m->type === 'REPACK_OUT' ? 'bg-amber-400/25 text-amber-900 border-amber-400/40' : '' }}
                                    {{ $m->type === 'REPACK_IN' ? 'bg-violet-500/15 text-violet-800 border-violet-400/35' : '' }}
                                    {{ $m->type === 'ADJUSTMENT' ? 'bg-rose-500/15 text-rose-800 border-rose-400/35' : '' }}
                                ">
                                    {{ $m->type }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-900">{{ $m->product->name }}</td>
                            <td class="p-3 text-right font-mono text-slate-500 font-semibold">{{ number_format($m->qty_before, 2) }}</td>
                            <td class="p-3 text-right font-mono font-extrabold {{ $m->qty_change < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ $m->qty_change > 0 ? '+' : '' }}{{ number_format($m->qty_change, 2) }} {{ $m->product->unit }}
                            </td>
                            <td class="p-3 text-right font-mono font-extrabold text-slate-900">{{ number_format($m->qty_after, 2) }}</td>
                            <td class="p-3 text-slate-600 font-medium text-[11px]">{{ $m->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 font-semibold">Tidak ada riwayat mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutations->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
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
