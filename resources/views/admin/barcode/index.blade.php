@extends('layouts.app')

@section('title', 'CETAK LABEL BARCODE')
@section('page_title', 'Cetak Label & Stiker Barcode')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">🏷️</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">GENERATOR LABEL &amp; STIKER BARCODE</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Cetak label harga &amp; barcode untuk produk eceran / repack karung</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Konfigurasi Cetak -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.barcode.print') }}" method="GET" target="_blank" class="glass-box p-6 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Yang Ingin Dicetak *</label>
                    <select name="product_id" id="product_select" required class="glass-input w-full px-3.5 py-2.5 font-semibold uppercase text-sm">
                        <option value="">-- PILIH PRODUK --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id === $p->id) ? 'selected' : '' }}
                                data-barcode="{{ $p->barcode ?? $p->sku }}"
                                data-price="{{ number_format($p->selling_price, 0, ',', '.') }}"
                                data-unit="{{ $p->unit }}"
                                data-is-repack="{{ $p->is_repack ? '1' : '0' }}">
                                {{ $p->name }} [{{ $p->barcode ?? $p->sku }}] - Rp {{ number_format($p->selling_price, 0, ',', '.') }} / {{ $p->unit }}
                                @if($p->is_repack) (REPACK) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Label / Stiker *</label>
                        <input type="number" name="qty" value="{{ $defaultQty }}" min="1" max="200" required
                            class="glass-input w-full px-3.5 py-2.5 font-extrabold text-lg">
                        <p class="text-[10px] text-slate-500 font-semibold uppercase mt-1">Maksimal 200 label per cetakan</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Format Ukuran Kertas *</label>
                        <select name="format" class="glass-input w-full px-3.5 py-2.5 font-semibold uppercase text-xs">
                            <option value="thermal_40x30">Thermal Label Barcode (40 x 30 mm) - 1 Kolom</option>
                            <option value="thermal_50x30">Thermal Label Barcode (50 x 30 mm) - 1 Kolom</option>
                            <option value="a4_grid">Kertas HVS A4 Grid (Multi Stiker Tom &amp; Jerry)</option>
                        </select>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-amber-400/15 border border-amber-400/35 flex items-start gap-3 backdrop-blur-sm">
                    <span class="text-xl">💡</span>
                    <div class="text-xs font-medium text-amber-950 space-y-1">
                        <div class="font-bold uppercase">Tips Toko Pakan:</div>
                        <div>Setelah membongkar pakan karungan 50kg menjadi kantong plastik 1kg/2kg di menu <strong>Repack</strong>, segera cetak label stiker ini dan tempelkan di setiap kemasan plastik agar proses checkout di kasir tinggal scan tanpa ketik manual.</div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200/70 flex justify-end">
                    <button type="submit" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 px-8 py-3 text-sm font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20 flex items-center gap-2">
                        <span>🖨️</span> BUKA &amp; CETAK LABEL BARCODE
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Stiker Label -->
        <div>
            <div class="glass-box p-6 space-y-4">
                <div class="flex items-center gap-2 font-bold text-xs uppercase text-slate-700 border-b border-slate-200/70 pb-2">
                    <span>👁️</span> CONTOH STIKER LABEL
                </div>

                <div class="barcode-sticker-preview border border-dashed border-slate-400/60 p-4 bg-white text-center rounded-2xl space-y-2 shadow-sm">
                    <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-600 !text-slate-600">FEEDMART</div>
                    <div id="preview_name" class="font-extrabold text-xs uppercase truncate !text-slate-900">
                        {{ $selectedProduct ? $selectedProduct->name : 'NAMA PRODUK PAKAN' }}
                    </div>

                    <!-- SVG Barcode Mock -->
                    <div class="py-1 flex justify-center">
                        <svg id="preview_barcode" class="max-w-[160px] h-10"></svg>
                    </div>

                    <div class="flex justify-between items-center text-xs font-bold border-t border-slate-200 pt-1.5">
                        <span id="preview_unit" class="text-[10px] !text-slate-500 uppercase">
                            1 {{ $selectedProduct ? $selectedProduct->unit : 'KG' }}
                        </span>
                        <span id="preview_price" class="text-sm font-extrabold !text-slate-900">
                            Rp {{ $selectedProduct ? number_format($selectedProduct->selling_price, 0, ',', '.') : '15.000' }}
                        </span>
                    </div>
                </div>

                <p class="text-[11px] font-semibold text-slate-500 uppercase text-center">
                    Label siap ditempel pada plastik eceran atau karung pakan.
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('product_select');
        const previewName = document.getElementById('preview_name');
        const previewPrice = document.getElementById('preview_price');
        const previewUnit = document.getElementById('preview_unit');

        function updatePreview() {
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                previewName.textContent = opt.text.split(' [')[0];
                previewPrice.textContent = 'Rp ' + (opt.dataset.price || '0');
                previewUnit.textContent = '1 ' + (opt.dataset.unit || 'pcs');
                const barcode = opt.dataset.barcode || 'FEED-001';
                try {
                    JsBarcode('#preview_barcode', barcode, {
                        format: "CODE128",
                        height: 35,
                        displayValue: true,
                        fontSize: 10,
                        margin: 0
                    });
                } catch(e) {}
            } else {
                try {
                    JsBarcode('#preview_barcode', 'FEED-DEMO', {
                        format: "CODE128",
                        height: 35,
                        displayValue: true,
                        fontSize: 10,
                        margin: 0
                    });
                } catch(e) {}
            }
        }

        select.addEventListener('change', updatePreview);
        updatePreview();
    });
</script>
@endsection
