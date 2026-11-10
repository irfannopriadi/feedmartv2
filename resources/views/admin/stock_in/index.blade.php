@extends('layouts.app')

@section('title', 'Penerimaan Stok Barang - Feedmart')
@section('page_title', 'Penerimaan Stok & Batch Kedaluwarsa (Stock In)')

@section('content')
<div class="space-y-6">
    <!-- Form Penerimaan Stok Baru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <div class="flex items-center space-x-3 pb-4 mb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold">
                📥
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Input Faktur Penerimaan Barang Distributor</h3>
                <p class="text-xs text-slate-500">Tambahkan stok pakan masuk sekaligus catat nomor batch dan tanggal kedaluwarsa untuk rotasi FIFO.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-in.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf

            <!-- Pilih Produk -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Produk Pakan <span class="text-rose-500">*</span></label>
                <select name="product_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-blue-500">
                    <option value="">-- Pilih Produk Pakan --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} (Stok saat ini: {{ $p->stock }} {{ $p->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah Masuk -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Masuk <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="qty" value="{{ old('qty', '10.00') }}" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-black text-slate-800 focus:bg-white focus:border-blue-500">
            </div>

            <!-- Harga Beli Baru -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Harga Beli / HPP Baru (Rp)</label>
                <input type="number" step="100" min="0" name="cost_price" value="{{ old('cost_price') }}" placeholder="Kosongkan jika tetap"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-blue-500">
            </div>

            <!-- Nomor Batch -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Batch Pabrik</label>
                <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="Contoh: B-2026-X1"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:border-blue-500">
            </div>

            <!-- Tanggal Kedaluwarsa FIFO -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Kedaluwarsa (Exp) <span class="text-rose-500">*</span></label>
                <input type="date" name="expired_date" value="{{ old('expired_date', \Carbon\Carbon::now()->addMonths(6)->toDateString()) }}" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-blue-500">
            </div>

            <!-- Nomor Faktur -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Faktur / Surat Jalan</label>
                <input type="text" name="invoice_ref" value="{{ old('invoice_ref') }}" placeholder="Contoh: SJ-CP-9921"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500">
            </div>

            <!-- Tombol Submit -->
            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition transform active:scale-95">
                    + Simpan Penerimaan Stok
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Penerimaan Stok Terkini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">Riwayat Transaksi Masuk (Stock In)</h3>
            <span class="text-xs text-slate-400">Total {{ $recentStockIns->total() }} Log</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Penerima</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3 text-right">Jumlah Masuk</th>
                        <th class="p-3 text-right">Stok Setelahnya</th>
                        <th class="p-3">Faktur & Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentStockIns as $in)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 text-slate-400 whitespace-nowrap">{{ $in->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $in->user->name }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ $in->product->name }}</td>
                            <td class="p-3 text-right font-black text-blue-600">
                                +{{ number_format($in->qty_change, 2) }} {{ $in->product->unit }}
                            </td>
                            <td class="p-3 text-right font-semibold text-slate-800">
                                {{ number_format($in->qty_after, 2) }} {{ $in->product->unit }}
                            </td>
                            <td class="p-3 text-slate-500">{{ $in->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada riwayat penerimaan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentStockIns->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $recentStockIns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
