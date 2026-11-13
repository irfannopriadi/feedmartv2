@extends('layouts.app')

@section('title', 'RINCIAN PELANGGAN - ' . $customer->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="neo-box bg-white p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">👤</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">{{ $customer->name }}</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">
                Telp: {{ $customer->phone ?? '-' }} | Alamat: {{ $customer->address ?? '-' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs">
                ← KEMBALI KE DAFTAR
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="neo-box bg-emerald-400 p-4 font-black text-black flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Saldo Kasbon & Form Bayar Hutang -->
        <div class="space-y-6">
            <div class="neo-box {{ $customer->debt_balance > 0 ? 'bg-rose-100 border-rose-600' : 'bg-emerald-100' }} p-6 space-y-4">
                <span class="text-xs font-black uppercase text-gray-600 tracking-wider">SALDO KASBON AKTIF</span>
                <div class="text-3xl font-black font-mono {{ $customer->debt_balance > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                    Rp {{ number_format($customer->debt_balance, 0, ',', '.') }}
                </div>
                @if($customer->debt_balance > 0)
                    <p class="text-xs font-bold text-rose-800">
                        Peternak ini memiliki tagihan tempo yang belum dilunasi.
                    </p>
                @else
                    <p class="text-xs font-bold text-emerald-800">
                        Tidak ada tanggungan kasbon (Lunas).
                    </p>
                @endif
            </div>

            <!-- Form Bayar Kasbon -->
            @if($customer->debt_balance > 0)
                <div class="neo-box bg-white p-6 space-y-4">
                    <h3 class="text-sm font-black uppercase border-b-2 border-black pb-2 flex items-center gap-2">
                        <span>💵</span> BAYAR / CICIL KASBON
                    </h3>

                    <form action="{{ route('admin.customers.pay-debt', $customer->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Nominal Pembayaran (Rp) *</label>
                            <input type="number" name="amount" max="{{ (float) $customer->debt_balance }}" min="1" required
                                value="{{ (float) $customer->debt_balance }}"
                                class="neo-input w-full font-black text-base">
                            <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">Maks: Rp {{ number_format($customer->debt_balance, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Metode Pembayaran *</label>
                            <select name="payment_method" class="neo-input w-full font-bold uppercase text-xs">
                                <option value="cash">TUNAI / CASH DI KASIR</option>
                                <option value="transfer">TRANSFER BANK</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase mb-1">Catatan</label>
                            <input type="text" name="notes" placeholder="Misal: Cicilan panen ayam broiler"
                                class="neo-input w-full font-bold text-xs">
                        </div>

                        <button type="submit" class="neo-btn w-full bg-emerald-400 text-black py-2.5 text-xs font-black uppercase">
                            CATAT PEMBAYARAN KASBON
                        </button>
                    </form>
                </div>
            @endif

            <!-- Form Edit Profil Pelanggan -->
            <div class="neo-box bg-white p-6 space-y-4">
                <h3 class="text-sm font-black uppercase border-b-2 border-black pb-2 flex items-center gap-2">
                    <span>✏️</span> EDIT DATA PELANGGAN
                </h3>

                <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-black uppercase mb-1">Nama Peternak</label>
                        <input type="text" name="name" value="{{ $customer->name }}" required class="neo-input w-full font-bold uppercase">
                    </div>

                    <div>
                        <label class="block font-black uppercase mb-1">Telepon / WhatsApp</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" class="neo-input w-full font-bold">
                    </div>

                    <div>
                        <label class="block font-black uppercase mb-1">Alamat</label>
                        <textarea name="address" rows="2" class="neo-input w-full font-bold">{{ $customer->address }}</textarea>
                    </div>

                    <button type="submit" class="neo-btn bg-black text-white px-4 py-2 font-black uppercase">
                        PERBARUI DATA
                    </button>
                </form>
            </div>
        </div>

        <!-- Histori Transaksi & Riwayat Pembayaran -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Riwayat Belanja -->
            <div class="neo-box bg-white overflow-hidden">
                <div class="p-4 bg-yellow-200 border-b-2 border-black flex items-center justify-between">
                    <h3 class="font-black text-xs uppercase tracking-wider">RIWAYAT TRANSAKSI BELANJA</h3>
                    <span class="neo-badge bg-white text-black text-[10px]">{{ $transactions->total() }} Transaksi</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b-2 border-black bg-gray-50 text-[11px] font-black uppercase">
                                <th class="p-3 border-r-2 border-black">NO FAKTUR</th>
                                <th class="p-3 border-r-2 border-black">TANGGAL</th>
                                <th class="p-3 border-r-2 border-black text-center">METODE</th>
                                <th class="p-3 border-r-2 border-black text-right">TOTAL</th>
                                <th class="p-3 text-center">STRUK</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold">
                            @forelse($transactions as $t)
                                <tr class="hover:bg-amber-50">
                                    <td class="p-3 border-r-2 border-black font-mono font-black">
                                        {{ $t->invoice_number }}
                                    </td>
                                    <td class="p-3 border-r-2 border-black text-gray-600">
                                        {{ $t->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-3 border-r-2 border-black text-center">
                                        @if($t->payment_method === 'tempo')
                                            <span class="neo-badge bg-rose-300 text-rose-900">TEMPO</span>
                                        @else
                                            <span class="neo-badge bg-emerald-200">{{ strtoupper($t->payment_method) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 border-r-2 border-black text-right font-mono font-black">
                                        Rp {{ number_format($t->final_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="neo-btn bg-white hover:bg-yellow-200 px-2.5 py-1 text-[11px]">
                                            🖨️ LIHAT
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-500 uppercase font-black">
                                        Belum ada riwayat transaksi belanja untuk pelanggan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="p-3 border-t-2 border-black bg-gray-50">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

            <!-- Riwayat Cicilan Kasbon -->
            <div class="neo-box bg-white overflow-hidden">
                <div class="p-4 bg-emerald-200 border-b-2 border-black flex items-center justify-between">
                    <h3 class="font-black text-xs uppercase tracking-wider">RIWAYAT PEMBAYARAN KASBON</h3>
                    <span class="neo-badge bg-white text-black text-[10px]">{{ $debtPayments->total() }} Pembayaran</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b-2 border-black bg-gray-50 text-[11px] font-black uppercase">
                                <th class="p-3 border-r-2 border-black">WAKTU</th>
                                <th class="p-3 border-r-2 border-black">DITERIMA OLEH</th>
                                <th class="p-3 border-r-2 border-black text-center">METODE</th>
                                <th class="p-3 border-r-2 border-black text-right">JUMLAH DIBAYAR</th>
                                <th class="p-3">CATATAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-black font-bold">
                            @forelse($debtPayments as $pay)
                                <tr class="hover:bg-emerald-50">
                                    <td class="p-3 border-r-2 border-black text-gray-600">
                                        {{ $pay->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-3 border-r-2 border-black">
                                        {{ $pay->user->name }}
                                    </td>
                                    <td class="p-3 border-r-2 border-black text-center">
                                        <span class="neo-badge bg-cyan-200">{{ strtoupper($pay->payment_method) }}</span>
                                    </td>
                                    <td class="p-3 border-r-2 border-black text-right font-mono font-black text-emerald-700">
                                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-gray-600">
                                        {{ $pay->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-500 uppercase font-black">
                                        Belum ada riwayat pembayaran kasbon.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($debtPayments->hasPages())
                    <div class="p-3 border-t-2 border-black bg-gray-50">
                        {{ $debtPayments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
