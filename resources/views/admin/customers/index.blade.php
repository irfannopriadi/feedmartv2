@extends('layouts.app')

@section('title', 'PELANGGAN & PIUTANG KASBON')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="neo-box bg-white p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">👥</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">DATA PELANGGAN & PIUTANG TOKO</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">Kelola data peternak langganan dan kontrol batas kasbon / tempo</p>
        </div>

        <div>
            <button @click="showModal = true" class="neo-btn bg-emerald-400 text-black px-4 py-2 text-xs flex items-center gap-2">
                <span>➕</span> TAMBAH PELANGGAN
            </button>
        </div>
    </div>

    <!-- KPI Ringkasan Piutang -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="neo-box bg-rose-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-rose-800">Total Piutang Belum Lunas</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                Rp {{ number_format($totalReceivables, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Dana kasbon yang tertahan di pelanggan</span>
        </div>

        <div class="neo-box bg-yellow-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-yellow-800">Pelanggan Memiliki Kasbon</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                {{ $customersWithDebtCount }} Orang
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Peternak dengan saldo tagihan aktif</span>
        </div>

        <div class="neo-box bg-cyan-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-cyan-800">Total Pelanggan Terdaftar</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                {{ $customers->total() }} Peternak
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Database mitra & pembeli tetap</span>
        </div>
    </div>

    @if(session('success'))
        <div class="neo-box bg-emerald-400 p-4 font-black text-black flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="neo-box bg-white p-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="CARI NAMA PELANGGAN ATAU NOMOR TELEPON..."
                    class="neo-input w-full text-xs font-bold uppercase">
            </div>

            <div class="w-full md:w-56">
                <select name="has_debt" class="neo-input w-full text-xs font-bold uppercase">
                    <option value="">SEMUA STATUS KASBON</option>
                    <option value="1" {{ request('has_debt') === '1' ? 'selected' : '' }}>HANYA YANG PUNYA KASBON</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="neo-btn bg-black text-white px-5 py-2 text-xs font-black uppercase">
                    CARI
                </button>
                @if(request()->hasAny(['search', 'has_debt']))
                    <a href="{{ route('admin.customers.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="neo-box bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-black bg-yellow-200 text-xs font-black uppercase">
                        <th class="p-4 border-r-2 border-black w-12 text-center">NO</th>
                        <th class="p-4 border-r-2 border-black">NAMA PELANGGAN / PETERNAK</th>
                        <th class="p-4 border-r-2 border-black">NO. TELEPON / WA</th>
                        <th class="p-4 border-r-2 border-black">ALAMAT / KANDANG</th>
                        <th class="p-4 border-r-2 border-black text-right">TOTAL KASBON</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black text-xs font-bold">
                    @forelse($customers as $index => $c)
                        <tr class="hover:bg-amber-50 transition">
                            <td class="p-4 border-r-2 border-black text-center text-gray-500">
                                {{ $customers->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r-2 border-black font-black uppercase">
                                {{ $c->name }}
                            </td>
                            <td class="p-4 border-r-2 border-black font-mono">
                                {{ $c->phone ?? '-' }}
                            </td>
                            <td class="p-4 border-r-2 border-black text-gray-600">
                                {{ Str::limit($c->address, 35) ?? '-' }}
                            </td>
                            <td class="p-4 border-r-2 border-black text-right font-mono font-black text-sm {{ $c->debt_balance > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                Rp {{ number_format($c->debt_balance, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.customers.show', $c->id) }}" class="neo-btn bg-white hover:bg-yellow-200 px-3 py-1 text-xs">
                                    🔍 RINCIAN & BAYAR
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 uppercase font-black">
                                Belum ada data pelanggan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t-2 border-black bg-gray-50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pelanggan -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-lg p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <h3 class="text-base font-black text-black uppercase">Tambah Pelanggan Baru</h3>
                <button @click="showModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-4 my-4">
                @csrf
                <div>
                    <label class="block text-xs font-black uppercase mb-1">Nama Peternak / Toko *</label>
                    <input type="text" name="name" required class="neo-input w-full font-bold uppercase" placeholder="CONTOH: PAK SLAMET (PETERNAK AYAM)">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" class="neo-input w-full font-bold" placeholder="0812-xxxx-xxxx">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase mb-1">Alamat / Lokasi Kandang</label>
                    <textarea name="address" rows="2" class="neo-input w-full font-bold text-xs" placeholder="Desa Karanganyar RT 02 / RW 01"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase mb-1">Catatan Tambahan</label>
                    <input type="text" name="notes" class="neo-input w-full font-bold text-xs" placeholder="Misal: Peternak bebek petelur 500 ekor">
                </div>

                <div class="pt-4 border-t-2 border-black flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                        BATAL
                    </button>
                    <button type="submit" class="neo-btn bg-emerald-400 text-black px-6 py-2 text-xs font-black uppercase">
                        SIMPAN PELANGGAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
