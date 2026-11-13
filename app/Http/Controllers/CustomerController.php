<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\CustomerDebtPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Daftar pelanggan & rekap piutang / kasbon toko
     */
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                    ->orWhere('phone', 'LIKE', "%{$s}%");
            });
        }

        if ($request->has_debt === '1') {
            $query->where('debt_balance', '>', 0);
        }

        $totalReceivables = Customer::sum('debt_balance');
        $customersWithDebtCount = Customer::where('debt_balance', '>', 0)->count();

        $customers = $query->orderByDesc('debt_balance')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers', 'totalReceivables', 'customersWithDebtCount'));
    }

    /**
     * Simpan pelanggan baru (bisa dipanggil via form biasa atau AJAX dari POS)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'debt_balance' => 0.00,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pelanggan '{$customer->name}' berhasil ditambahkan.",
                'customer' => $customer,
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', "Pelanggan '{$customer->name}' berhasil ditambahkan.");
    }

    /**
     * Detail pelanggan, histori transaksi belanja, dan riwayat cicilan kasbon
     */
    public function show(Customer $customer): View
    {
        $transactions = $customer->transactions()
            ->with(['user', 'details.product'])
            ->latest()
            ->paginate(10, ['*'], 'tx_page');

        $debtPayments = $customer->debtPayments()
            ->with(['user'])
            ->latest()
            ->paginate(10, ['*'], 'pay_page');

        return view('admin.customers.show', compact('customer', 'transactions', 'debtPayments'));
    }

    /**
     * Perbarui data profil pelanggan
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $customer->update($validated);

        return redirect()->back()->with('success', "Data pelanggan '{$customer->name}' berhasil diperbarui.");
    }

    /**
     * Pembayaran / Pelunasan Kasbon Pelanggan
     */
    public function payDebt(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:'.$customer->debt_balance],
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = (float) $request->amount;
        $user = Auth::user();

        // Cari register aktif jika pembayaran tunai
        $activeRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        DB::transaction(function () use ($customer, $user, $amount, $request, $activeRegister) {
            CustomerDebtPayment::create([
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'cash_register_id' => $activeRegister?->id,
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            $customer->decrement('debt_balance', $amount);
        });

        return redirect()->back()->with('success', 'Pembayaran kasbon sebesar Rp '.number_format($amount, 0, ',', '.')." untuk '{$customer->name}' berhasil dicatat.");
    }
}
