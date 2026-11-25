<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Tampilkan riwayat pengeluaran kas operasional toko
     */
    public function index(Request $request): View
    {
        $query = Expense::with(['user', 'cashRegister']);

        $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($currentMonth)->startOfMonth();
        $endOfMonth = Carbon::parse($currentMonth)->endOfMonth();

        $query->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $totalMonthlyExpense = (clone $query)->sum('amount');
        $todayExpense = Expense::whereDate('date', Carbon::today())->sum('amount');

        $expenses = $query->orderByDesc('date')->orderByDesc('id')->paginate(15)->withQueryString();

        $categories = [
            'Operasional Toko',
            'Plastik & Karung Eceran',
            'Upah Kuli Panggul Pakan',
            'Listrik & Air Toko',
            'Konsumsi Karyawan',
            'Transportasi / Bahan Bakar',
            'Lain-lain',
        ];

        return view('admin.expenses.index', compact(
            'expenses',
            'totalMonthlyExpense',
            'todayExpense',
            'currentMonth',
            'categories'
        ));
    }

    /**
     * Simpan pengeluaran kas baru (bisa dari Admin atau AJAX POS)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['nullable', 'date'],
        ]);

        $user = Auth::user();

        // Cari register aktif jika ada untuk mengikat pengeluaran shift kasir ini
        $activeRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $expense = Expense::create([
            'cash_register_id' => $activeRegister?->id,
            'user_id' => $user->id,
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran kas berhasil dicatat.',
                'expense' => $expense,
            ]);
        }

        return redirect()->back()->with('success', 'Pengeluaran kas sebesar Rp '.number_format($expense->amount, 0, ',', '.').' berhasil dicatat.');
    }

    /**
     * Hapus catatan pengeluaran
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->back()->with('success', 'Catatan pengeluaran kas berhasil dihapus.');
    }
}
