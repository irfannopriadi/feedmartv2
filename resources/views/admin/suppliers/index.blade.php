@extends('layouts.app')

@section('title', 'Master Supplier Distributor - Feedmart')
@section('page_title', 'Master Data Supplier & Distributor')

@section('content')
<div class="space-y-5" x-data="supplierPage()">
    <!-- Action Bar & Search (Full Light Glassmorphism) -->
    <div class="glass-box p-4 flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="w-full md:w-auto md:flex-1 flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama supplier / kontak / telepon..."
                class="glass-input px-3.5 py-2 text-xs">
            <button type="submit" class="glass-btn px-4 py-2 bg-slate-900/90 hover:bg-slate-800 text-white border-white/25 text-xs font-bold">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('admin.suppliers.index') }}" class="glass-btn px-3 py-2 bg-white/75 hover:bg-white text-slate-700 text-xs font-bold">
                    Reset
                </a>
            @endif
        </form>

        <button type="button" @click="openCreateModal()" class="glass-btn w-full md:w-auto px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 text-xs font-bold uppercase tracking-wider shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5 flex-shrink-0">
            <span>+ Tambah Supplier Baru</span>
        </button>
    </div>

    <!-- Supplier Table -->
    <div class="glass-box overflow-hidden">
        <div class="p-4 bg-white/45 border-b border-white/70 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wide">Daftar Rekanan Distributor Pakan</h3>
            <span class="glass-badge bg-amber-400/20 text-amber-950 border-amber-400/35 font-mono">Total {{ $suppliers->total() }} Supplier</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-white/55 text-slate-600 font-bold border-b border-slate-200/70 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5">Nama Distributor / Supplier</th>
                        <th class="p-3.5">Kontak Person (PIC)</th>
                        <th class="p-3.5">Telepon / WhatsApp</th>
                        <th class="p-3.5">Alamat / Kota</th>
                        <th class="p-3.5 text-center">Produk Terkait</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60">
                    @forelse($suppliers as $s)
                        <tr class="hover:bg-white/60 transition">
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900 block text-sm">{{ $s->name }}</span>
                                <span class="text-[10px] text-slate-500 font-mono font-semibold">ID: #{{ $s->id }}</span>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-700">
                                {{ $s->contact_person ?: '-' }}
                            </td>
                            <td class="p-3.5 font-mono font-semibold">
                                @if($s->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->phone) }}" target="_blank" class="text-slate-800 hover:text-emerald-700 underline flex items-center gap-1">
                                        <span>📞</span>
                                        <span>{{ $s->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 max-w-xs text-slate-600 font-medium">
                                {{ $s->address ?: '-' }}
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="glass-badge bg-sky-500/15 text-sky-800 border-sky-400/35 font-mono">
                                    {{ $s->products_count }} Produk
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" @click="openEditModal({{ json_encode($s) }})" class="glass-btn p-1.5 bg-amber-400/20 hover:bg-amber-400/35 text-amber-900 border-amber-400/35 text-xs" title="Edit Supplier">
                                        ✏️
                                    </button>
                                    <form method="POST" action="{{ route('admin.suppliers.destroy', $s->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus supplier {{ $s->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="glass-btn p-1.5 bg-rose-500/15 hover:bg-rose-500/25 text-rose-700 border-rose-400/35 text-xs" title="Hapus Supplier">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-semibold text-xs">
                                Belum ada data supplier distributor. Klik tombol "+ Tambah Supplier Baru" untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            <div class="p-4 border-t border-slate-200/70 bg-white/40">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE / EDIT MODAL (Full Light Glassmorphism) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 glass-modal-backdrop flex items-center justify-center p-4">
        <div class="glass-modal w-full max-w-md p-6 relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <h3 class="text-base font-extrabold text-slate-900 uppercase" x-text="isEdit ? 'Edit Supplier Distributor' : 'Tambah Supplier Distributor Baru'"></h3>
                <button @click="showModal = false" class="w-8 h-8 rounded-full bg-white/70 hover:bg-rose-500/15 text-slate-500 hover:text-rose-600 text-base font-bold flex items-center justify-center transition">✕</button>
            </div>

            <form :action="isEdit ? updateUrl : '{{ route('admin.suppliers.store') }}'" method="POST" class="space-y-4 my-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Supplier / PT / CV <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: PT Charoen Pokphand Indonesia" required
                        class="glass-input px-3.5 py-2 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kontak Person / Sales (Opsional)</label>
                    <input type="text" name="contact_person" x-model="formData.contact_person" placeholder="Contoh: Bpk. Bambang Sales"
                        class="glass-input px-3.5 py-2 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Telepon / WhatsApp (Opsional)</label>
                    <input type="text" name="phone" x-model="formData.phone" placeholder="Contoh: 081234567890"
                        class="glass-input px-3.5 py-2 text-xs font-mono font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Gudang / Kantor (Opsional)</label>
                    <textarea name="address" x-model="formData.address" rows="2" placeholder="Alamat lengkap distributor..."
                        class="glass-input px-3.5 py-2 text-xs font-semibold"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showModal = false" class="glass-btn px-4 py-2 bg-white/75 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="glass-btn px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white border-emerald-400/50 font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-500/20">
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Supplier'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('supplierPage', () => ({
        showModal: false,
        isEdit: false,
        updateUrl: '',
        formData: {
            name: '',
            contact_person: '',
            phone: '',
            address: ''
        },

        openCreateModal() {
            this.isEdit = false;
            this.updateUrl = '';
            this.formData = {
                name: '',
                contact_person: '',
                phone: '',
                address: ''
            };
            this.showModal = true;
        },

        openEditModal(supplier) {
            this.isEdit = true;
            this.updateUrl = `{{ url('admin/suppliers') }}/${supplier.id}`;
            this.formData = {
                name: supplier.name || '',
                contact_person: supplier.contact_person || '',
                phone: supplier.phone || '',
                address: supplier.address || ''
            };
            this.showModal = true;
        }
    }));
});
</script>
@endpush
