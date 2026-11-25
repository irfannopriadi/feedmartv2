@extends('layouts.app')

@section('title', 'RINCIAN PELANGGAN - ' . $customer->name)
@section('page_title', 'Rincian Pelanggan & Kasbon')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">👤</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">{{ $customer->name }}</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">
                Telp: {{ $customer->phone ?? '-' }} | Alamat: {{ $customer->address ?? '-' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold">
                ← KEMBALI KE DAFTAR
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
        <!-- Saldo Kasbon & Form Bayar Hutang -->
        <div class="space-y-6">
            <div class="glass-box {{ $customer->debt_balance > 0 ? 'bg-rose-500/12 border-rose-400/35' : 'bg-emerald-500/12 border-emerald-400/35' }} p-6 space-y-4">
                <span class="text-xs font-bold uppercase text-slate-600 tracking-wider">SALDO KASBON AKTIF</span>
                <div class="text-3xl font-extrabold font-mono {{ $customer->debt_balance > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                    Rp {{ number_format($customer->debt_balance, 0, ',', '.') }}
                </div>
                @if($customer->debt_balance > 0)
                    <p class="text-xs font-semibold text-rose-800">
                        Peternak ini memiliki tagihan tempo yang belum dilunasi.
                    </p>
                @else
                    <p class="text-xs font-semibold text-emerald-800">
                        Tidak ada tanggungan kasbon (Lunas).
                    </p>
                @endif
            </div>

            <!-- Form Bayar Kasbon -->
            @if($customer->debt_balance > 0)
                <div class="glass-box p-6 space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-slate-900 border-b border-slate-200/70 pb-2 flex items-center gap-2">
                        <span>💵</span> BAYAR / CICIL KASBON
                    </h3>

                    <form action="{{ route('admin.customers.pay-debt', $customer->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal Pembayaran (Rp) *</label>
                            <input type="number" name="amount" max="{{ (float) $customer->debt_balance }}" min="1" required
                                value="{{ (float) $customer->debt_balance }}"
                                class="glass-input w-full px-3.5 py-2.5 font-extrabold text-base">
                            <p class="text-[10px] text-slate-500 font-semibold uppercase mt-1">Maks: Rp {{ number_format($customer->debt_balance, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Metode Pembayaran *</label>
                            <select name="payment_method" class="glass-input w-full px-3.5 py-2 font-semibold uppercase text-xs">
                                <option value="cash">TUNAI / CASH DI KASIR</option>
                                <option value="transfer">TRANSFER BANK</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan</label>
                            <input type="text" name="notes" placeholder="Misal: Cicilan panen ayam broiler"
                                class="glass-input w-full px-3.5 py-2 font-semibold text-xs">
                        </div>

                        <button type="submit" class="glass-btn w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 py-2.5 text-xs font-bold uppercase shadow-md shadow-emerald-500/20">
                            CATAT PEMBAYARAN KASBON
                        </button>
                    </form>
                </div>
            @endif

            <!-- Form Edit Profil Pelanggan -->
            <div class="glass-box p-6 space-y-4">
                <h3 class="text-sm font-extrabold uppercase text-slate-900 border-b border-slate-200/70 pb-2 flex items-center gap-2">
                    <span>✏️</span> EDIT DATA PELANGGAN
                </h3>

                <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Peternak</label>
                        <input type="text" name="name" value="{{ $customer->name }}" required class="glass-input w-full px-3 py-2 font-semibold uppercase">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Telepon / WhatsApp</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" class="glass-input w-full px-3 py-2 font-semibold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Alamat</label>
                        <textarea name="address" rows="2" class="glass-input w-full px-3 py-2 font-semibold">{{ $customer->address }}</textarea>
                    </div>

                    <button type="submit" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 px-4 py-2 font-bold uppercase">
                        PERBARUI DATA
                    </button>
                </form>
            </div>
        </div>

        <!-- Histori Transaksi & Riwayat Pembayaran -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Riwayat Belanja -->
            <div class="glass-box overflow-hidden">
                <div class="p-4 bg-amber-400/15 border-b border-white/70 flex items-center justify-between">
                    <h3 class="font-bold text-xs text-slate-900 uppercase tracking-wider">RIWAYAT TRANSAKSI BELANJA</h3>
                    <span class="glass-badge bg-white/80 text-slate-800 text-[10px]">{{ $transactions->total() }} Transaksi</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs text-slate-800">
                        <thead>
                            <tr class="border-b border-slate-200/70 bg-white/50 text-[11px] font-bold text-slate-600 uppercase">
                                <th class="p-3 border-r border-slate-200/50">NO FAKTUR</th>
                                <th class="p-3 border-r border-slate-200/50">TANGGAL</th>
                                <th class="p-3 border-r border-slate-200/50 text-center">METODE</th>
                                <th class="p-3 border-r border-slate-200/50 text-right">TOTAL</th>
                                <th class="p-3 text-center">STRUK</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 font-semibold">
                            @forelse($transactions as $t)
                                <tr class="hover:bg-white/60 transition">
                                    <td class="p-3 border-r border-slate-200/50 font-mono font-bold text-slate-900">
                                        {{ $t->invoice_number }}
                                    </td>
                                    <td class="p-3 border-r border-slate-200/50 text-slate-500">
                                        {{ $t->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-3 border-r border-slate-200/50 text-center">
                                        @if($t->payment_method === 'tempo')
                                            <span class="glass-badge bg-rose-500/15 text-rose-800 border-rose-400/35">TEMPO</span>
                                        @else
                                            <span class="glass-badge bg-emerald-500/15 text-emerald-800 border-emerald-400/35">{{ strtoupper($t->payment_method) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 border-r border-slate-200/50 text-right font-mono font-extrabold text-slate-900">
                                        Rp {{ number_format($t->final_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="glass-btn bg-white/80 hover:bg-amber-400/20 px-2.5 py-1 text-[11px] text-slate-700">
                                            🖨️ LIHAT
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500 uppercase font-bold">
                                        Belum ada riwayat transaksi belanja untuk pelanggan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="p-3 border-t border-slate-200/70 bg-white/40">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

            <!-- Riwayat Cicilan Kasbon -->
            <div class="glass-box overflow-hidden">
                <div class="p-4 bg-emerald-500/15 border-b border-white/70 flex items-center justify-between">
                    <h3 class="font-bold text-xs text-slate-900 uppercase tracking-wider">RIWAYAT PEMBAYARAN KASBON</h3>
                    <span class="glass-badge bg-white/80 text-slate-800 text-[10px]">{{ $debtPayments->total() }} Pembayaran</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs text-slate-800">
                        <thead>
                            <tr class="border-b border-slate-200/70 bg-white/50 text-[11px] font-bold text-slate-600 uppercase">
                                <th class="p-3 border-r border-slate-200/50">WAKTU</th>
                                <th class="p-3 border-r border-slate-200/50">DITERIMA OLEH</th>
                                <th class="p-3 border-r border-slate-200/50 text-center">METODE</th>
                                <th class="p-3 border-r border-slate-200/50 text-right">JUMLAH DIBAYAR</th>
                                <th class="p-3">CATATAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 font-semibold">
                            @forelse($debtPayments as $pay)
                                <tr class="hover:bg-white/60 transition">
                                    <td class="p-3 border-r border-slate-200/50 text-slate-500">
                                        {{ $pay->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-3 border-r border-slate-200/50 text-slate-700">
                                        {{ $pay->user->name }}
                                    </td>
                                    <td class="p-3 border-r border-slate-200/50 text-center">
                                        <span class="glass-badge bg-cyan-500/15 text-cyan-800 border-cyan-400/35">{{ strtoupper($pay->payment_method) }}</span>
                                    </td>
                                    <td class="p-3 border-r border-slate-200/50 text-right font-mono font-extrabold text-emerald-700">
                                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-slate-600">
                                        {{ $pay->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500 uppercase font-bold">
                                        Belum ada riwayat pembayaran kasbon.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($debtPayments->hasPages())
                    <div class="p-3 border-t border-slate-200/70 bg-white/40">
                        {{ $debtPayments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
