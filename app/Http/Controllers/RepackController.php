<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMutation;
use App\Services\RepackService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RepackController extends Controller
{
    public function __construct(
        protected RepackService $repackService
    ) {}

    public function index(): View
    {
        // Ambil semua produk yang bertipe repack beserta karung induknya
        $repackProducts = Product::where('is_repack', true)
            ->with(['parentProduct', 'animalCategory'])
            ->orderBy('name')
            ->get();

        // Riwayat mutasi repack terakhir
        $recentRepackLogs = StockMutation::whereIn('type', ['REPACK_OUT', 'REPACK_IN'])
            ->with(['product', 'user'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.repack.index', compact('repackProducts', 'recentRepackLogs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'repack_product_id' => ['required', 'exists:products,id'],
            'parent_qty' => ['required', 'numeric', 'min:0.1'],
        ]);

        $repackProduct = Product::findOrFail($request->repack_product_id);

        try {
            $result = $this->repackService->executeRepack(
                repackProduct: $repackProduct,
                parentQty: (float) $request->parent_qty,
                user: Auth::user()
            );

            return back()->with('success', "Berhasil membongkar {$result['parent_qty_reduced']} {$result['parent_product']->unit} '{$result['parent_product']->name}' menjadi +{$result['added_qty']} {$result['repack_product']->unit} '{$result['repack_product']->name}'.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
