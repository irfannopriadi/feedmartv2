@extends('layouts.app')

@section('title', 'PENGATURAN TOKO & STRUK')
@section('page_title', 'Pengaturan Toko & Struk Kasir')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">⚙️</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">PENGATURAN TOKO &amp; STRUK KASIR</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Konfigurasi identitas gerai dan format cetak struk thermal</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.backup.download') }}" class="glass-btn bg-amber-400/25 hover:bg-amber-400/40 text-amber-950 border-amber-400/40 px-4 py-2.5 text-xs font-bold flex items-center gap-2">
                <span>💾</span> BACKUP DATABASE (1-KLIK)
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-box bg-emerald-500/15 border-emerald-400/35 p-4 font-bold text-emerald-950 flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Settings -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="glass-box p-6 space-y-6">
                @csrf

                <!-- Section: Identitas Toko -->
                <div>
                    <h2 class="text-base font-extrabold uppercase text-slate-900 pb-2 border-b border-slate-200/70 flex items-center gap-2">
                        <span>🏬</span> IDENTITAS TOKO
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Toko / Usaha *</label>
                            <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? 'FEEDMART') }}" required
                                class="glass-input w-full px-3.5 py-2 font-semibold uppercase" placeholder="CONTOH: FEEDMART JAYA">
                            @error('store_name') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Slogan / Sub-judul Toko</label>
                            <input type="text" name="store_tagline" value="{{ old('store_tagline', $settings['store_tagline'] ?? 'PUSAT PAKAN TERNAK & HOBI') }}"
                                class="glass-input w-full px-3.5 py-2 font-semibold uppercase" placeholder="CONTOH: PUSAT PAKAN TERNAK & HOBI">
                            @error('store_tagline') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Lengkap Gerai *</label>
                            <textarea name="store_address" rows="2" required
                                class="glass-input w-full px-3.5 py-2 font-semibold text-sm" placeholder="Jl. Raya Peternakan No. 88, Kec. Sukamaju">{{ old('store_address', $settings['store_address'] ?? 'Jl. Raya Peternakan No. 88, Jawa Timur') }}</textarea>
                            @error('store_address') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Telepon / WhatsApp Kasir *</label>
                            <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '0812-3456-7890') }}" required
                                class="glass-input w-full px-3.5 py-2 font-semibold" placeholder="0812-3456-7890">
                            @error('store_phone') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Format Struk Thermal -->
                <div class="pt-4">
                    <h2 class="text-base font-extrabold uppercase text-slate-900 pb-2 border-b border-slate-200/70 flex items-center gap-2">
                        <span>🧾</span> FORMAT STRUK THERMAL
                    </h2>
                    <div class="space-y-4 mt-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Ukuran Kertas Thermal Printer *</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="glass-box-sm p-3 flex items-center gap-3 cursor-pointer bg-amber-400/12 hover:bg-amber-400/20 border-amber-400/35 transition">
                                    <input type="radio" name="receipt_paper_size" value="58mm" {{ (old('receipt_paper_size', $settings['receipt_paper_size'] ?? '58mm') === '58mm') ? 'checked' : '' }} class="w-4 h-4 text-emerald-600">
                                    <div>
                                        <div class="text-xs font-bold uppercase text-slate-900">58mm (Kecil / Standar)</div>
                                        <div class="text-[10px] text-slate-500 font-medium">Printer mini Bluetooth / USB POS-58</div>
                                    </div>
                                </label>
                                <label class="glass-box-sm p-3 flex items-center gap-3 cursor-pointer bg-cyan-500/12 hover:bg-cyan-500/20 border-cyan-400/35 transition">
                                    <input type="radio" name="receipt_paper_size" value="80mm" {{ (old('receipt_paper_size', $settings['receipt_paper_size'] ?? '58mm') === '80mm') ? 'checked' : '' }} class="w-4 h-4 text-emerald-600">
                                    <div>
                                        <div class="text-xs font-bold uppercase text-slate-900">80mm (Lebar / Desktop)</div>
                                        <div class="text-[10px] text-slate-500 font-medium">Printer Kasir EPSON TM-T82 / Auto-Cutter</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Kaki Struk (Footer Note)</label>
                            <textarea name="receipt_footer" rows="3"
                                class="glass-input w-full px-3.5 py-2 font-semibold text-xs" placeholder="Barang yang dibeli tidak dapat ditukar kecuali ada perjanjian. Terima kasih!">{{ old('receipt_footer', $settings['receipt_footer'] ?? 'Barang yang dibeli tidak dapat ditukar kecuali ada perjanjian. Terima kasih atas kunjungan Anda!') }}</textarea>
                            <p class="text-[10px] font-semibold text-slate-500 mt-1 uppercase">Pesan ini akan tercetak di bagian paling bawah struk pembayaran.</p>
                            @error('receipt_footer') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 px-8 py-3 text-sm font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20 flex items-center gap-2">
                        <span>💾</span> SIMPAN PENGATURAN
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Preview Struk -->
        <div class="space-y-4">
            <div class="glass-box bg-amber-400/15 border-amber-400/35 p-4">
                <div class="flex items-center gap-2 font-bold text-xs uppercase text-amber-950 mb-1">
                    <span>👁️</span> PRATINJAU STRUK KASIR
                </div>
                <p class="text-[11px] font-medium text-amber-900/80">Gambaran hasil cetak struk kasir dengan data saat ini.</p>
            </div>

            <div class="glass-box bg-white/80 p-6 font-mono text-[11px] space-y-3 leading-tight text-slate-800">
                <div class="text-center">
                    <div class="font-extrabold text-sm tracking-wider uppercase text-slate-900">{{ $settings['store_name'] ?? 'FEEDMART' }}</div>
                    <div class="text-[9px] font-bold uppercase text-slate-600">{{ $settings['store_tagline'] ?? 'PUSAT PAKAN TERNAK & HOBI' }}</div>
                    <div class="text-[9px] text-slate-500">{{ $settings['store_address'] ?? 'Jl. Raya Peternakan No. 88, Jawa Timur' }}</div>
                    <div class="text-[9px] text-slate-500">TELP/WA: {{ $settings['store_phone'] ?? '0812-3456-7890' }}</div>
                </div>

                <div class="border-t border-b border-dashed border-slate-300 py-1 text-[10px]">
                    <div class="flex justify-between">
                        <span>INV-20261113-0001</span>
                        <span>13/11/26 14:30</span>
                    </div>
                    <div class="flex justify-between">
                        <span>KASIR: Kasir 1</span>
                        <span>PELANGGAN: UMUM</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <div>
                        <div class="font-bold text-slate-900">Konsentrat Ayam Petelur KLK</div>
                        <div class="flex justify-between text-[10px] text-slate-500">
                            <span>2.00 sak x 385.000</span>
                            <span class="font-bold text-slate-900">770.000</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">Vita Stress 100gr</div>
                        <div class="flex justify-between text-[10px] text-slate-500">
                            <span>3.00 bks x 12.000</span>
                            <span class="font-bold text-slate-900">36.000</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-300 pt-2 space-y-1">
                    <div class="flex justify-between font-bold text-slate-900">
                        <span>TOTAL</span>
                        <span>Rp 806.000</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span>BAYAR (TUNAI)</span>
                        <span>Rp 810.000</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold text-slate-900">
                        <span>KEMBALI</span>
                        <span>Rp 4.000</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-slate-300 pt-2 text-center text-[9px] text-slate-500 italic">
                    {{ $settings['receipt_footer'] ?? 'Barang yang dibeli tidak dapat ditukar kecuali ada perjanjian. Terima kasih atas kunjungan Anda!' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
