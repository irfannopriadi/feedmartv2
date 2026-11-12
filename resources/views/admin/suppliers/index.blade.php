@extends('layouts.app')

@section('title', 'Master Supplier Distributor - Feedmart')
@section('page_title', 'Master Data Supplier & Distributor')

@section('content')
<div class="space-y-5" x-data="supplierPage()">
    <!-- Action Bar & Search (Neo-Brutalist Minimalism) -->
    <div class="neo-box p-4 flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="w-full md:w-auto md:flex-1 flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama supplier / kontak / telepon..."
                class="neo-input px-3.5 py-2 text-xs">
            <button type="submit" class="neo-btn px-4 py-2 bg-black hover:bg-zinc-800 text-white text-xs font-black">
                Cari
            </button>
            @if(request('q'))
                <a href="{{ route('admin.suppliers.index') }}" class="neo-btn px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-black text-xs font-bold">
                    Reset
                </a>
            @endif
        </form>

        <button type="button" @click="openCreateModal()" class="neo-btn w-full md:w-auto px-4 py-2 bg-emerald-400 hover:bg-emerald-300 text-black text-xs font-black uppercase tracking-wider shadow-[3px_3px_0px_#000] flex items-center justify-center gap-1.5 flex-shrink-0">
            <span>+ Tambah Supplier Baru</span>
        </button>
    </div>

    <!-- Supplier Table -->
    <div class="neo-box overflow-hidden">
        <div class="p-4 bg-black text-white border-b-2 border-black flex items-center justify-between">
            <h3 class="font-black text-sm uppercase tracking-wide">Daftar Rekanan Distributor Pakan</h3>
            <span class="neo-badge bg-yellow-300 text-black font-mono">Total {{ $suppliers->total() }} Supplier</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-black">
                <thead class="bg-zinc-100 text-black font-black border-b-2 border-black uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5">Nama Distributor / Supplier</th>
                        <th class="p-3.5">Kontak Person (PIC)</th>
                        <th class="p-3.5">Telepon / WhatsApp</th>
                        <th class="p-3.5">Alamat / Kota</th>
                        <th class="p-3.5 text-center">Produk Terkait</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black/10">
                    @forelse($suppliers as $s)
                        <tr class="hover:bg-yellow-50/50 transition">
                            <td class="p-3.5">
                                <span class="font-black text-black block text-sm">{{ $s->name }}</span>
                                <span class="text-[10px] text-zinc-500 font-mono font-bold">ID: #{{ $s->id }}</span>
                            </td>
                            <td class="p-3.5 font-bold">
                                {{ $s->contact_person ?: '-' }}
                            </td>
                            <td class="p-3.5 font-mono font-bold">
                                @if($s->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->phone) }}" target="_blank" class="text-black hover:text-emerald-700 underline flex items-center gap-1">
                                        <span>📞</span>
                                        <span>{{ $s->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 max-w-xs text-zinc-700 font-medium">
                                {{ $s->address ?: '-' }}
                            </td>
                            <td class="p-3.5 text-center">
                                <span class="neo-badge bg-sky-200 text-black font-mono font-black">
                                    {{ $s->products_count }} Produk
                                </span>
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" @click="openEditModal({{ json_encode($s) }})" class="neo-btn p-1.5 bg-yellow-200 hover:bg-yellow-300 text-black text-xs" title="Edit Supplier">
                                        ✏️
                                    </button>
                                    <form method="POST" action="{{ route('admin.suppliers.destroy', $s->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus supplier {{ $s->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="neo-btn p-1.5 bg-rose-200 hover:bg-rose-300 text-black text-xs" title="Hapus Supplier">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-zinc-500 font-bold text-xs">
                                Belum ada data supplier distributor. Klik tombol "+ Tambah Supplier Baru" untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            <div class="p-4 border-t-2 border-black bg-zinc-50">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE / EDIT MODAL (Neo-Brutalist Minimalism) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border-3 border-black rounded-2xl w-full max-w-md p-6 shadow-[8px_8px_0px_#000] relative" @click.outside="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                <h3 class="text-base font-black text-black uppercase" x-text="isEdit ? 'Edit Supplier Distributor' : 'Tambah Supplier Distributor Baru'"></h3>
                <button @click="showModal = false" class="text-black hover:text-rose-600 text-lg font-black">✕</button>
            </div>

            <form :action="isEdit ? updateUrl : '{{ route('admin.suppliers.store') }}'" method="POST" class="space-y-4 my-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Nama Supplier / PT / CV <span class="text-rose-600">*</span></label>
                    <input type="text" name="name" x-model="formData.name" placeholder="Contoh: PT Charoen Pokphand Indonesia" required
                        class="neo-input px-3.5 py-2 text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Kontak Person / Sales (Opsional)</label>
                    <input type="text" name="contact_person" x-model="formData.contact_person" placeholder="Contoh: Bpk. Bambang Sales"
                        class="neo-input px-3.5 py-2 text-xs font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">No. Telepon / WhatsApp (Opsional)</label>
                    <input type="text" name="phone" x-model="formData.phone" placeholder="Contoh: 081234567890"
                        class="neo-input px-3.5 py-2 text-xs font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-black text-black uppercase mb-1">Alamat Gudang / Kantor (Opsional)</label>
                    <textarea name="address" x-model="formData.address" rows="2" placeholder="Alamat lengkap distributor..."
                        class="neo-input px-3.5 py-2 text-xs font-bold"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" @click="showModal = false" class="neo-btn px-4 py-2 bg-zinc-100 text-black font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="neo-btn px-5 py-2 bg-emerald-400 hover:bg-emerald-300 text-black font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_#000]">
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
