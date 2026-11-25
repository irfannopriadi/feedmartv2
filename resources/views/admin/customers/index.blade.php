@extends('layouts.app')

@section('title', 'PELANGGAN & PIUTANG KASBON')
@section('page_title', 'Pelanggan & Piutang Kasbon')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="glass-box p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-2xl">👥</span>
                <h1 class="text-2xl font-extrabold uppercase tracking-tight text-slate-900">DATA PELANGGAN &amp; PIUTANG TOKO</h1>
            </div>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola data peternak langganan dan kontrol batas kasbon / tempo</p>
        </div>

        <div>
            <button @click="showModal = true" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-500/20 flex items-center gap-2">
                <span>➕</span> TAMBAH PELANGGAN
            </button>
        </div>
    </div>

    <!-- KPI Ringkasan Piutang -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-box bg-rose-500/12 border-rose-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-800">Total Piutang Belum Lunas</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                Rp {{ number_format($totalReceivables, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Dana kasbon yang tertahan di pelanggan</span>
        </div>

        <div class="glass-box bg-amber-400/15 border-amber-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-900">Pelanggan Memiliki Kasbon</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                {{ $customersWithDebtCount }} Orang
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Peternak dengan saldo tagihan aktif</span>
        </div>

        <div class="glass-box bg-cyan-500/12 border-cyan-400/35 p-5">
            <span class="text-xs font-bold uppercase tracking-wider text-cyan-800">Total Pelanggan Terdaftar</span>
            <div class="text-2xl font-extrabold font-mono mt-1 text-slate-900">
                {{ $customers->total() }} Peternak
            </div>
            <span class="text-[10px] font-semibold text-slate-600 uppercase">Database mitra &amp; pembeli tetap</span>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-box bg-emerald-500/15 border-emerald-400/35 p-4 font-bold text-emerald-950 flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="glass-box p-4">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="CARI NAMA PELANGGAN ATAU NOMOR TELEPON..."
                    class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
            </div>

            <div class="w-full md:w-56">
                <select name="has_debt" class="glass-input w-full px-3.5 py-2 text-xs font-semibold uppercase">
                    <option value="">SEMUA STATUS KASBON</option>
                    <option value="1" {{ request('has_debt') === '1' ? 'selected' : '' }}>HANYA YANG PUNYA KASBON</option>
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="glass-btn bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 px-5 py-2 text-xs font-bold uppercase">
                    CARI
                </button>
                @if(request()->hasAny(['search', 'has_debt']))
                    <a href="{{ route('admin.customers.index') }}" class="glass-btn bg-white/75 hover:bg-white text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="glass-box overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/70 bg-white/55 text-slate-600 text-xs font-bold uppercase">
                        <th class="p-4 border-r border-slate-200/50 w-12 text-center">NO</th>
                        <th class="p-4 border-r border-slate-200/50">NAMA PELANGGAN / PETERNAK</th>
                        <th class="p-4 border-r border-slate-200/50">NO. TELEPON / WA</th>
                        <th class="p-4 border-r border-slate-200/50">ALAMAT / KANDANG</th>
                        <th class="p-4 border-r border-slate-200/50 text-right">TOTAL KASBON</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60 text-xs font-semibold text-slate-800">
                    @forelse($customers as $index => $c)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-4 border-r border-slate-200/50 text-center text-slate-400">
                                {{ $customers->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-bold uppercase text-slate-900">
                                {{ $c->name }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 font-mono text-slate-700">
                                {{ $c->phone ?? '-' }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-slate-600">
                                {{ Str::limit($c->address, 35) ?? '-' }}
                            </td>
                            <td class="p-4 border-r border-slate-200/50 text-right font-mono font-extrabold text-sm {{ $c->debt_balance > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                Rp {{ number_format($c->debt_balance, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.customers.show', $c->id) }}" class="glass-btn bg-white/80 hover:bg-amber-400/25 text-slate-800 px-3 py-1.5 text-xs">
                                    🔍 RINCIAN &amp; BAYAR
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 uppercase font-bold">
                                Belum ada data pelanggan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pelanggan -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-lg p-6 relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase">Tambah Pelanggan Baru</h3>
                <button @click="showModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-4 my-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Peternak / Toko *</label>
                    <input type="text" name="name" required class="glass-input w-full px-3.5 py-2 font-semibold uppercase" placeholder="CONTOH: PAK SLAMET (PETERNAK AYAM)">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" class="glass-input w-full px-3.5 py-2 font-semibold" placeholder="0812-xxxx-xxxx">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat / Lokasi Kandang</label>
                    <textarea name="address" rows="2" class="glass-input w-full px-3.5 py-2 font-semibold text-xs" placeholder="Desa Karanganyar RT 02 / RW 01"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                    <input type="text" name="notes" class="glass-input w-full px-3.5 py-2 font-semibold text-xs" placeholder="Misal: Peternak bebek petelur 500 ekor">
                </div>

                <div class="pt-4 border-t border-slate-200/70 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="glass-btn bg-white/75 text-slate-700 px-4 py-2 text-xs font-bold uppercase">
                        BATAL
                    </button>
                    <button type="submit" class="glass-btn bg-gradient-to-r from-emerald-500 to-teal-500 text-white border-emerald-400/50 px-6 py-2 text-xs font-bold uppercase shadow-md shadow-emerald-500/20">
                        SIMPAN PELANGGAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
