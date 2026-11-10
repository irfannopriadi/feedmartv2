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
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. Ringkasan Omzet & Transaksi Hari Ini
        $todaySales = Transaction::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('final_amount');

        $todayCount = Transaction::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->count();

        // 2. Omzet & Estimasi Laba Kotor Bulan Ini
        $monthSales = Transaction::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->sum('final_amount');

        // Laba Kotor = Total Jual - Total HPP (Cost Price)
        $monthHpp = TransactionDetail::whereHas('transaction', function ($q) use ($thisMonth) {
            $q->where('status', 'completed')->where('created_at', '>=', $thisMonth);
        })->sum(DB::raw('cost_price * qty'));

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

        // 6. Produk Terlaris (Top Selling)
        $topProducts = TransactionDetail::select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('transaction', fn ($q) => $q->where('status', 'completed'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->take(5)
            ->get();

        // 7. Distribusi Penjualan per Kategori Hewan
        $categorySales = AnimalCategory::withCount('products')
            ->get()
            ->map(function ($cat) {
                $total = TransactionDetail::whereHas('product', fn ($p) => $p->where('animal_category_id', $cat->id))
                    ->whereHas('transaction', fn ($t) => $t->where('status', 'completed'))
                    ->sum('subtotal');
                $cat->total_sales = $total;

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
