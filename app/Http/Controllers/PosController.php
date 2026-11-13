<?php

namespace App\Http\Controllers;

use App\Models\AnimalCategory;
use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\PosCheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        protected PosCheckoutService $checkoutService
    ) {}

    /**
     * Tampilan utama Kasir POS
     */
    public function index(): mixed
    {
        $user = Auth::user();

        // Kasir wajib buka kas sebelum bisa bertransaksi
        $activeRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if ($user->isKasir() && ! $activeRegister) {
            return redirect()->route('cash-register.open-form')
                ->with('warning', 'Silakan masukkan modal awal kasir untuk memulai transaksi.');
        }

        $categories = AnimalCategory::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with(['animalCategory', 'batches' => fn ($q) => $q->where('stock_in_batch', '>', 0)->orderBy('expired_date')])
            ->orderBy('name')
            ->get();

        $heldTransactions = Transaction::where('user_id', $user->id)
            ->where('status', 'hold')
            ->with('details.product')
            ->latest()
            ->get();

        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact('categories', 'products', 'activeRegister', 'heldTransactions', 'customers'));
    }

    /**
     * Live search API untuk barcode scanner atau pencarian cepat nama produk
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $productsQuery = Product::where('is_active', true);

        if (! empty($categoryId)) {
            $productsQuery->where('animal_category_id', $categoryId);
        }

        if (! empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('barcode', $query)
                    ->orWhere('sku', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
            });
        }

        $products = $productsQuery->with('animalCategory')
            ->orderBy('name')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Proses checkout transaksi kasir
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,qris,transfer,tempo'],
            'paid_amount' => ['required_if:payment_method,cash', 'nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'cash_register_id' => ['nullable', 'exists:cash_registers,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        try {
            $transaction = $this->checkoutService->checkout(
                user: Auth::user(),
                items: $request->items,
                paymentMethod: $request->payment_method,
                paidAmount: (float) $request->paid_amount,
                discount: (float) ($request->discount ?? 0),
                status: 'completed',
                cashRegisterId: $request->cash_register_id,
                customerId: $request->customer_id ? (int) $request->customer_id : null,
                dueDate: $request->due_date
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'transaction' => $transaction,
                'receipt_url' => route('pos.receipt', $transaction->id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Fitur Hold Bill (Simpan antrean belanja sementara)
     */
    public function holdBill(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $transaction = $this->checkoutService->checkout(
                user: Auth::user(),
                items: $request->items,
                paymentMethod: 'cash',
                paidAmount: 0,
                discount: (float) ($request->discount ?? 0),
                status: 'hold'
            );

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil di-hold (disimpan sementara).',
                'transaction' => $transaction,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Fitur Recall Bill (Buka kembali antrean belanja yang di-hold)
     */
    public function recallBill(int $id): JsonResponse
    {
        $transaction = Transaction::where('status', 'hold')
            ->with(['details.product'])
            ->findOrFail($id);

        $cartData = [];
        foreach ($transaction->details as $detail) {
            $cartData[] = [
                'id' => $detail->product_id,
                'sku' => $detail->product->sku,
                'name' => $detail->product->name,
                'unit' => $detail->product->unit,
                'selling_price' => (float) $detail->selling_price,
                'stock' => (float) $detail->product->stock,
                'qty' => (float) $detail->qty,
            ];
        }

        // Hapus hold bill setelah di-recall kembali ke keranjang kasir
        $transaction->delete();

        return response()->json([
            'success' => true,
            'items' => $cartData,
            'discount' => (float) $transaction->discount,
        ]);
    }

    /**
     * Struk Thermal 58mm / 80mm
     */
    public function printReceipt(int $id): View
    {
        $transaction = Transaction::with(['user', 'details.product', 'cashRegister'])->findOrFail($id);

        return view('pos.receipt', compact('transaction'));
    }
}
