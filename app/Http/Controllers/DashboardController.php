<?php

namespace App\Http\Controllers;

use App\Models\AnimalCategory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. Ringkasan Omzet & Transaksi Hari Ini (Single Query with Index)
        $todayStats = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw('COALESCE(SUM(final_amount), 0) as total_sales, COUNT(*) as count')
            ->first();

        $todaySales = (float) ($todayStats->total_sales ?? 0);
        $todayCount = (int) ($todayStats->count ?? 0);

        // 2. Omzet & Estimasi Laba Kotor Bulan Ini (Fast Indexed Join)
        $monthSales = (float) Transaction::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->sum('final_amount');

        // Laba Kotor = Total Jual - Total HPP (Cost Price) via indexed JOIN
        $monthHpp = (float) TransactionDetail::join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', $thisMonth)
            ->sum(DB::raw('transaction_details.cost_price * transaction_details.qty'));

        $monthGrossProfit = $monthSales - $monthHpp;

        // 3. Alert Stok Menipis
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock_alert')
            ->with('animalCategory')
            ->orderBy('stock', 'asc')
            ->take(8)
            ->get();

        // 4. Alert Kedaluwarsa Batch (<= 30 hari atau sudah lewat)
        $nearExpiryBatches = ProductBatch::with('product')
            ->where('stock_in_batch', '>', 0)
            ->where('expired_date', '<=', Carbon::now()->addDays(30)->toDateString())
            ->orderBy('expired_date', 'asc')
            ->take(8)
            ->get();

        // 5. Transaksi Terbaru
        $recentTransactions = Transaction::with(['user', 'details.product'])
            ->latest()
            ->take(6)
            ->get();

        // 6. Produk Terlaris (Top Selling via indexed JOIN)
        $topProducts = TransactionDetail::join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.status', 'completed')
            ->select('transaction_details.product_id', DB::raw('SUM(transaction_details.qty) as total_qty'), DB::raw('SUM(transaction_details.subtotal) as total_revenue'))
            ->groupBy('transaction_details.product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->take(5)
            ->get();

        // 7. Distribusi Penjualan per Kategori Hewan (1 Single Aggregated Query, No N+1)
        $salesByCategory = TransactionDetail::join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_details.product_id')
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', $thisMonth)
            ->select('products.animal_category_id', DB::raw('SUM(transaction_details.subtotal) as total_sales'))
            ->groupBy('products.animal_category_id')
            ->pluck('total_sales', 'products.animal_category_id');

        $categorySales = AnimalCategory::withCount('products')
            ->get()
            ->map(function ($cat) use ($salesByCategory) {
                $cat->total_sales = (float) ($salesByCategory[$cat->id] ?? 0);

                return $cat;
            });

        return view('admin.dashboard', compact(
            'todaySales',
            'todayCount',
            'monthSales',
            'monthGrossProfit',
            'lowStockProducts',
            'nearExpiryBatches',
            'recentTransactions',
            'topProducts',
            'categorySales'
        ));
    }
}
