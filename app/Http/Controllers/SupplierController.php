<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Tampilkan daftar supplier distribusi.
     */
    public function index(Request $request): View
    {
        $query = Supplier::withCount('products');

        if ($request->filled('q')) {
            $keyword = '%'.$request->q.'%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('contact_person', 'like', $keyword)
                    ->orWhere('phone', 'like', $keyword)
                    ->orWhere('address', 'like', $keyword);
            });
        }

        $suppliers = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Simpan supplier baru (mendukung request form biasa & AJAX).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier = Supplier::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Supplier '{$supplier->name}' berhasil ditambahkan.",
                'data' => $supplier,
            ], 201);
        }

        return redirect()->route('admin.suppliers.index')->with('success', "Supplier '{$supplier->name}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data supplier distribusi.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name,'.$supplier->id],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', "Data supplier '{$supplier->name}' berhasil diperbarui.");
    }

    /**
     * Hapus data supplier distribusi.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', "Supplier '{$name}' telah dihapus.");
    }
}
