<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());
        $paymentMethod = $request->get('payment_method');
        $userId = $request->get('user_id');

        $query = Transaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if (! empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        if (! empty($userId)) {
            $query->where('user_id', $userId);
        }

        $transactions = (clone $query)->with(['user', 'details.product'])->latest()->paginate(20)->withQueryString();

        // Ringkasan Finansial
        $totalSales = (clone $query)->sum('final_amount');
        $totalDiscount = (clone $query)->sum('discount');
        $totalTransactionsCount = (clone $query)->count();

        // Hitung total HPP transaksi terfilter
        $transactionIds = (clone $query)->pluck('id');
        $totalHpp = TransactionDetail::whereIn('transaction_id', $transactionIds)
            ->sum(DB::raw('cost_price * qty'));

        $grossProfit = $totalSales - $totalHpp;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;

        $cashiers = User::where('role', 'kasir')->get();

        return view('admin.reports.sales', compact(
            'transactions',
            'totalSales',
            'totalHpp',
            'grossProfit',
            'profitMargin',
            'totalDiscount',
            'totalTransactionsCount',
            'dateFrom',
            'dateTo',
            'paymentMethod',
            'userId',
            'cashiers'
        ));
    }
}
