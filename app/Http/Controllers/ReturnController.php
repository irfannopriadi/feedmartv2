<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductReturn;
use App\Models\StockMutation;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnController extends Controller
{
    /**
     * Tampilkan riwayat retur penjualan & retur pembelian
     */
    public function index(Request $request): View
    {
        $query = ProductReturn::with(['user', 'product', 'batch', 'customer', 'supplier']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $saleReturnsCount = ProductReturn::where('type', 'sale_return')->count();
        $purchaseReturnsCount = ProductReturn::where('type', 'purchase_return')->count();
        $totalRefundGiven = ProductReturn::where('type', 'sale_return')->sum('refund_amount');

        $returns = $query->latest()->paginate(15)->withQueryString();

        return view('admin.returns.index', compact(
            'returns',
            'saleReturnsCount',
            'purchaseReturnsCount',
            'totalRefundGiven'
        ));
    }

    /**
     * Tampilkan form input retur barang
     */
    public function create(): View
    {
        $products = Product::where('is_active', true)->with('batches')->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.returns.create', compact('products', 'customers', 'suppliers'));
    }

    /**
     * Proses penyimpanan retur & penyesuaian stok otomatis
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:sale_return,purchase_return'],
            'product_id' => ['required', 'exists:products,id'],
            'batch_id' => ['nullable', 'exists:product_batches,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $type = $request->type;
        $qty = (float) $request->qty;
        $refundAmount = (float) ($request->refund_amount ?? 0);

        try {
            DB::transaction(function () use ($request, $user, $type, $qty, $refundAmount) {
                $product = Product::lockForUpdate()->findOrFail($request->product_id);
                $batch = $request->batch_id ? ProductBatch::lockForUpdate()->find($request->batch_id) : null;

                $returnNumber = $this->generateReturnNumber($type);

                $return = ProductReturn::create([
                    'return_number' => $returnNumber,
                    'type' => $type,
                    'user_id' => $user->id,
                    'customer_id' => $request->customer_id,
                    'supplier_id' => $request->supplier_id,
                    'product_id' => $product->id,
                    'batch_id' => $batch?->id,
                    'qty' => $qty,
                    'refund_amount' => $refundAmount,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                ]);

                if ($type === 'sale_return') {
                    // RETUR PENJUALAN (Barang dari pembeli masuk kembali ke toko)
                    $stockBefore = (float) $product->stock;
                    $stockAfter = $stockBefore + $qty;
                    $product->update(['stock' => $stockAfter]);

                    if ($batch) {
                        $batch->increment('stock_in_batch', $qty);
                    }

                    // Audit mutasi stok IN
                    StockMutation::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'IN',
                        'qty_change' => $qty,
                        'qty_before' => $stockBefore,
                        'qty_after' => $stockAfter,
                        'reference_type' => ProductReturn::class,
                        'reference_id' => $return->id,
                        'notes' => "Retur Penjualan {$returnNumber} ({$request->reason})",
                    ]);

                    // Jika pelanggan memiliki hutang dan retur memotong kasbon
                    if ($request->customer_id && $refundAmount > 0) {
                        $customer = Customer::lockForUpdate()->find($request->customer_id);
                        if ($customer && $customer->debt_balance > 0) {
                            $deductDebt = min((float) $customer->debt_balance, $refundAmount);
                            $customer->decrement('debt_balance', $deductDebt);
                        }
                    }
                } else {
                    // RETUR PEMBELIAN (Barang cacat/bocor dikembalikan ke distributor / supplier)
                    if ((float) $product->stock < $qty) {
                        throw new Exception("Stok produk tidak mencukupi untuk diretur ke supplier (Tersedia: {$product->stock}, Diminta: {$qty}).");
                    }

                    $stockBefore = (float) $product->stock;
                    $stockAfter = $stockBefore - $qty;
                    $product->update(['stock' => $stockAfter]);

                    if ($batch) {
                        if ((float) $batch->stock_in_batch < $qty) {
                            throw new Exception("Stok pada batch terpilih tidak mencukupi untuk diretur (Tersedia: {$batch->stock_in_batch}).");
                        }
                        $batch->decrement('stock_in_batch', $qty);
                    }

                    // Audit mutasi stok OUT
                    StockMutation::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'OUT',
                        'qty_change' => -$qty,
                        'qty_before' => $stockBefore,
                        'qty_after' => $stockAfter,
                        'reference_type' => ProductReturn::class,
                        'reference_id' => $return->id,
                        'notes' => "Retur Pembelian ke Supplier {$returnNumber} ({$request->reason})",
                    ]);
                }
            });

            return redirect()->route('admin.returns.index')->with('success', 'Transaksi retur barang berhasil dicatat dan stok telah diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate Nomor Retur Unik: RET-S-YYYYMMDD-XXXX atau RET-P-YYYYMMDD-XXXX
     */
    protected function generateReturnNumber(string $type): string
    {
        $code = $type === 'sale_return' ? 'RET-S-' : 'RET-P-';
        $prefix = $code.date('Ymd').'-';

        $latest = ProductReturn::where('return_number', 'LIKE', $prefix.'%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->return_number);
            $lastSeq = (int) end($parts);
            $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $prefix.$nextSeq;
    }
}
