<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMutation;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        $recentStockIns = StockMutation::where('type', 'IN')
            ->with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.stock_in.index', compact('products', 'suppliers', 'recentStockIns'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'batch_number' => ['nullable', 'string', 'max:100'],
            'expired_date' => ['required', 'date'],
            'invoice_ref' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = (float) $request->qty;

        try {
            DB::transaction(function () use ($product, $qty, $request) {
                $stockBefore = (float) $product->stock;
                $stockAfter = $stockBefore + $qty;

                $updateData = ['stock' => $stockAfter];
                if ($request->filled('cost_price') && (float) $request->cost_price > 0) {
                    $updateData['cost_price'] = (float) $request->cost_price;
                }
                $product->update($updateData);

                // Buat Batch Kedaluwarsa Baru
                $batchNumber = $request->batch_number ?: 'BATCH-'.date('Ymd').'-'.rand(100, 999);
                ProductBatch::create([
                    'product_id' => $product->id,
                    'batch_number' => $batchNumber,
                    'expired_date' => $request->expired_date,
                    'stock_in_batch' => $qty,
                ]);

                // Catat Log Mutasi
                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'IN',
                    'qty_change' => $qty,
                    'qty_before' => $stockBefore,
                    'qty_after' => $stockAfter,
                    'notes' => 'Faktur/Ref: '.($request->invoice_ref ?: '-').'. '.($request->notes ?: 'Penerimaan stok dari supplier')." [Exp: {$request->expired_date}]",
                ]);
            });

            return back()->with('success', "Stok sebanyak {$qty} {$product->unit} berhasil diterima untuk {$product->name}.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses penerimaan barang: '.$e->getMessage())->withInput();
        }
    }
}
