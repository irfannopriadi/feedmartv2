<?php

namespace App\Http\Controllers;

use App\Models\AnimalCategory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['animalCategory', 'supplier', 'parentProduct', 'batches']);

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($sub) use ($s) {
                $sub->where('name', 'LIKE', "%{$s}%")
                    ->orWhere('sku', 'LIKE', "%{$s}%")
                    ->orWhere('barcode', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('animal_category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock', '<=', 'min_stock_alert');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '<=', 0);
            }
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = AnimalCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create(): View
    {
        $categories = AnimalCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $parentCandidates = Product::where('is_repack', false)->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'suppliers', 'parentCandidates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'animal_category_id' => ['nullable', 'exists:animal_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'unit' => ['required', 'string', 'max:50'],
            'is_repack' => ['nullable', 'boolean'],
            'parent_product_id' => ['nullable', 'required_if:is_repack,1', 'exists:products,id'],
            'conversion_ratio' => ['nullable', 'required_if:is_repack,1', 'numeric', 'min:0.01'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'min_stock_alert' => ['nullable', 'numeric', 'min:0'],
            'initial_batch_number' => ['nullable', 'string', 'max:100'],
            'initial_expired_date' => ['nullable', 'date'],
        ]);

        $validated['is_repack'] = $request->boolean('is_repack');
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['min_stock_alert'] = $validated['min_stock_alert'] ?? 5;

        $product = Product::create($validated);

        // Jika ada stok awal dan expired date
        if ((float) $product->stock > 0 && ! empty($request->initial_expired_date)) {
            ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => $request->initial_batch_number ?: 'BATCH-'.date('Ymd'),
                'expired_date' => $request->initial_expired_date,
                'stock_in_batch' => $product->stock,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', "Produk '{$product->name}' berhasil ditambahkan.");
    }

    public function edit(Product $product): View
    {
        $categories = AnimalCategory::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $parentCandidates = Product::where('id', '!=', $product->id)
            ->where('is_repack', false)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories', 'suppliers', 'parentCandidates'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,'.$product->id],
            'name' => ['required', 'string', 'max:255'],
            'animal_category_id' => ['nullable', 'exists:animal_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'unit' => ['required', 'string', 'max:50'],
            'is_repack' => ['nullable', 'boolean'],
            'parent_product_id' => ['nullable', 'required_if:is_repack,1', 'exists:products,id'],
            'conversion_ratio' => ['nullable', 'required_if:is_repack,1', 'numeric', 'min:0.01'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock_alert' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_repack'] = $request->boolean('is_repack');
        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', "Data produk '{$product->name}' berhasil diperbarui.");
    }

    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);
        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status produk '{$product->name}' berhasil {$status}.");
    }
}
