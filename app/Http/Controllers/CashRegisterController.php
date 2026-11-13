<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    /**
     * Tampilkan form buka kas (modal awal)
     */
    public function openForm(): View|RedirectResponse
    {
        $user = Auth::user();
        $activeRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($activeRegister) {
            return redirect()->route('pos.index')->with('info', 'Sesi laci kas Anda sudah aktif.');
        }

        return view('cash_register.open');
    }

    /**
     * Buka sesi kasir baru dengan modal awal
     */
    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        // Pastikan tidak ada sesi ganda yang masih 'open'
        $existing = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return redirect()->route('pos.index')->with('info', 'Sesi kasir sudah aktif.');
        }

        CashRegister::create([
            'user_id' => $user->id,
            'opening_cash' => $request->opening_cash,
            'opened_at' => now(),
            'status' => 'open',
            'notes' => $request->notes,
        ]);

        return redirect()->route('pos.index')->with('success', 'Laci kas berhasil dibuka. Selamat bertugas!');
    }

    /**
     * Tampilkan form tutup kas / pergantian shift
     */
    public function closeForm(): View|RedirectResponse
    {
        $user = Auth::user();
        $register = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (! $register) {
            return redirect()->route('cash-register.open-form')->with('warning', 'Tidak ada sesi kasir yang sedang aktif.');
        }

        // Hitung total transaksi tunai selama sesi shift ini
        $cashSales = Transaction::where('cash_register_id', $register->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('final_amount');

        $nonCashSales = Transaction::where('cash_register_id', $register->id)
            ->where('status', 'completed')
            ->whereIn('payment_method', ['qris', 'transfer'])
            ->sum('final_amount');

        $totalTransactions = Transaction::where('cash_register_id', $register->id)
            ->where('status', 'completed')
            ->count();

        $cashExpenses = Expense::where('cash_register_id', $register->id)->sum('amount');

        $expectedCash = (float) $register->opening_cash + (float) $cashSales - (float) $cashExpenses;

        return view('cash_register.close', compact(
            'register',
            'cashSales',
            'nonCashSales',
            'totalTransactions',
            'cashExpenses',
            'expectedCash'
        ));
    }

    /**
     * Simpan penutupan kas dan catat selisih fisik
     */
    public function close(Request $request): RedirectResponse
    {
        $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $register = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->firstOrFail();

        $cashSales = Transaction::where('cash_register_id', $register->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('final_amount');

        $cashExpenses = Expense::where('cash_register_id', $register->id)->sum('amount');

        $expectedCash = (float) $register->opening_cash + (float) $cashSales - (float) $cashExpenses;

        $register->update([
            'closing_cash' => $request->closing_cash,
            'expected_cash' => $expectedCash,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $request->notes,
        ]);

        return redirect()->route('cash-register.shift-report', $register->id)
            ->with('success', 'Laci kas berhasil ditutup dan shift selesai.');
    }

    /**
     * Ringkasan rekonsiliasi shift
     */
    public function shiftReport(int $id): View
    {
        $register = CashRegister::with('user')->findOrFail($id);

        if (! Auth::user()->isAdmin() && $register->user_id !== Auth::id()) {
            abort(403);
        }

        $cashSales = Transaction::where('cash_register_id', $register->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('final_amount');

        $nonCashSales = Transaction::where('cash_register_id', $register->id)
            ->where('status', 'completed')
            ->whereIn('payment_method', ['qris', 'transfer'])
            ->sum('final_amount');

        $cashExpenses = Expense::where('cash_register_id', $register->id)->sum('amount');

        $transactions = Transaction::where('cash_register_id', $register->id)
            ->with('details.product')
            ->latest()
            ->get();

        return view('cash_register.report', compact('register', 'cashSales', 'nonCashSales', 'cashExpenses', 'transactions'));
    }
}
