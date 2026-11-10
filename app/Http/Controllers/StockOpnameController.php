<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMutation;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $query = StockMutation::with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mutations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.stock_opname.index', compact('products', 'mutations'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'actual_physical_stock' => ['required', 'numeric', 'min:0'],
            'adjustment_reason' => ['required', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $actualStock = (float) $request->actual_physical_stock;

        try {
            DB::transaction(function () use ($product, $actualStock, $request) {
                $stockBefore = (float) $product->stock;
                $diff = $actualStock - $stockBefore;

                if ($diff == 0) {
                    throw new Exception("Stok fisik sama dengan stok sistem saat ini ({$stockBefore} {$product->unit}). Tidak ada penyesuaian.");
                }

                $product->update(['stock' => $actualStock]);

                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'ADJUSTMENT',
                    'qty_change' => $diff,
                    'qty_before' => $stockBefore,
                    'qty_after' => $actualStock,
                    'notes' => "Stock Opname: {$request->adjustment_reason} (Selisih: ".($diff > 0 ? "+{$diff}" : $diff)." {$product->unit})",
                ]);
            });

            return back()->with('success', "Penyesuaian stok untuk {$product->name} berhasil disimpan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
