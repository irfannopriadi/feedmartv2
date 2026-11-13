@extends('layouts.app')

@section('title', 'PENGELUARAN KAS TOKO')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="neo-box bg-white p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">💸</span>
                <h1 class="text-2xl font-black uppercase tracking-tight">PENGELUARAN KAS & OPERASIONAL TOKO</h1>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase mt-1">Catat biaya harian (kuli panggul, plastik kresek, listrik, lakban) untuk hitung laba bersih</p>
        </div>

        <div>
            <button @click="showModal = true" class="neo-btn bg-rose-400 text-black px-4 py-2 text-xs flex items-center gap-2">
                <span>➕</span> CATAT PENGELUARAN KAS
            </button>
        </div>
    </div>

    <!-- KPI Ringkasan Beban -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="neo-box bg-amber-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-amber-900">Total Pengeluaran Bulan Ini ({{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F Y') }})</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                Rp {{ number_format($totalMonthlyExpense, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Akumulasi seluruh pos biaya operasional</span>
        </div>

        <div class="neo-box bg-rose-200 p-5">
            <span class="text-xs font-black uppercase tracking-wider text-rose-900">Pengeluaran Hari Ini</span>
            <div class="text-2xl font-black font-mono mt-1 text-black">
                Rp {{ number_format($todayExpense, 0, ',', '.') }}
            </div>
            <span class="text-[10px] font-bold text-gray-600 uppercase">Kas keluar hari ini ({{ date('d/m/Y') }})</span>
        </div>
    </div>

    @if(session('success'))
        <div class="neo-box bg-emerald-400 p-4 font-black text-black flex items-center gap-3">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Bulan & Kategori -->
    <div class="neo-box bg-white p-4">
        <form action="{{ route('admin.expenses.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="w-full md:w-56">
                <input type="month" name="month" value="{{ $currentMonth }}"
                    class="neo-input w-full text-xs font-bold uppercase">
            </div>

            <div class="w-full md:w-64">
                <select name="category" class="neo-input w-full text-xs font-bold uppercase">
                    <option value="">SEMUA KATEGORI BIAYA</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="neo-btn bg-black text-white px-5 py-2 text-xs font-black uppercase">
                    FILTER
                </button>
                @if(request()->hasAny(['month', 'category']))
                    <a href="{{ route('admin.expenses.index') }}" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                        RESET
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengeluaran -->
    <div class="neo-box bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-black bg-yellow-200 text-xs font-black uppercase">
                        <th class="p-4 border-r-2 border-black w-12 text-center">NO</th>
                        <th class="p-4 border-r-2 border-black">TANGGAL</th>
                        <th class="p-4 border-r-2 border-black">KATEGORI BIAYA</th>
                        <th class="p-4 border-r-2 border-black">KETERANGAN / KEPERLUAN</th>
                        <th class="p-4 border-r-2 border-black">DICATAT OLEH</th>
                        <th class="p-4 border-r-2 border-black text-right">JUMLAH (RP)</th>
                        <th class="p-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black text-xs font-bold">
                    @forelse($expenses as $index => $exp)
                        <tr class="hover:bg-amber-50 transition">
                            <td class="p-4 border-r-2 border-black text-center text-gray-500">
                                {{ $expenses->firstItem() + $index }}
                            </td>
                            <td class="p-4 border-r-2 border-black font-mono">
                                {{ $exp->date->format('d/m/Y') }}
                            </td>
                            <td class="p-4 border-r-2 border-black">
                                <span class="neo-badge bg-amber-100 uppercase">{{ $exp->category }}</span>
                            </td>
                            <td class="p-4 border-r-2 border-black text-gray-700">
                                {{ $exp->description ?? '-' }}
                                @if($exp->cash_register_id)
                                    <span class="block text-[10px] text-gray-400 font-mono">Shift Kas #{{ $exp->cash_register_id }}</span>
                                @endif
                            </td>
                            <td class="p-4 border-r-2 border-black">
                                {{ $exp->user->name }}
                            </td>
                            <td class="p-4 border-r-2 border-black text-right font-mono font-black text-rose-600 text-sm">
                                Rp {{ number_format($exp->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="neo-btn bg-white hover:bg-rose-200 text-rose-700 px-3 py-1 text-xs">
                                        🗑️ HAPUS
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500 uppercase font-black">
                                Belum ada catatan pengeluaran pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-4 border-t-2 border-black bg-gray-50">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Pengeluaran -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-lg p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <h3 class="text-base font-black text-black uppercase">Catat Pengeluaran Kas Baru</h3>
                <button @click="showModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST" class="space-y-4 my-4">
                @csrf
                <div>
                    <label class="block text-xs font-black uppercase mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="neo-input w-full font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase mb-1">Kategori Pengeluaran *</label>
                    <select name="category" required class="neo-input w-full font-bold uppercase text-xs">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase mb-1">Nominal Biaya (Rp) *</label>
                    <input type="number" name="amount" min="1" required class="neo-input w-full font-black text-base" placeholder="50000">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase mb-1">Keterangan / Rincian Pengeluaran</label>
                    <textarea name="description" rows="2" class="neo-input w-full font-bold text-xs" placeholder="Contoh: Beli plastik 1kg 5 pack & lakban bening untuk repack pakan"></textarea>
                </div>

                <div class="pt-4 border-t-2 border-black flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="neo-btn bg-gray-200 text-black px-4 py-2 text-xs font-black uppercase">
                        BATAL
                    </button>
                    <button type="submit" class="neo-btn bg-rose-400 text-black px-6 py-2 text-xs font-black uppercase">
                        SIMPAN PENGELUARAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
