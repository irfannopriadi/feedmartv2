<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());
        $paymentMethod = $request->get('payment_method');
        $userId = $request->get('user_id');

        $query = $this->buildSalesQuery($dateFrom, $dateTo, $paymentMethod, $userId);

        $transactions = (clone $query)->with(['user', 'details.product'])->latest()->paginate(20)->withQueryString();

        // Ringkasan Finansial
        $financials = (clone $query)->selectRaw('COALESCE(SUM(final_amount), 0) as total_sales, COALESCE(SUM(discount), 0) as total_discount, COUNT(*) as total_count')->first();
        $totalSales = (float) ($financials->total_sales ?? 0);
        $totalDiscount = (float) ($financials->total_discount ?? 0);
        $totalTransactionsCount = (int) ($financials->total_count ?? 0);

        // Hitung total HPP transaksi terfilter via direct JOIN (jauh lebih cepat tanpa memory leak pluck ID)
        $totalHpp = (float) TransactionDetail::join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.created_at', '>=', $dateFrom)
            ->whereDate('transactions.created_at', '<=', $dateTo)
            ->when(! empty($paymentMethod), fn ($q) => $q->where('transactions.payment_method', $paymentMethod))
            ->when(! empty($userId), fn ($q) => $q->where('transactions.user_id', $userId))
            ->sum(DB::raw('transaction_details.cost_price * transaction_details.qty'));

        $grossProfit = $totalSales - $totalHpp;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;

        $totalExpenses = Expense::whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;

        $cashiers = User::where('role', 'kasir')->get();

        return view('admin.reports.sales', compact(
            'transactions',
            'totalSales',
            'totalHpp',
            'grossProfit',
            'totalExpenses',
            'netProfit',
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

    /**
     * Ekspor Laporan Penjualan & Laba Rugi ke File Excel (.csv UTF-8 BOM).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());
        $paymentMethod = $request->get('payment_method');
        $userId = $request->get('user_id');

        $query = $this->buildSalesQuery($dateFrom, $dateTo, $paymentMethod, $userId);
        $transactions = $query->with(['user', 'details.product'])->orderBy('created_at')->get();

        // Finansial
        $totalSales = $transactions->sum('final_amount');
        $totalDiscount = $transactions->sum('discount');
        $totalHpp = 0;

        foreach ($transactions as $t) {
            foreach ($t->details as $d) {
                $totalHpp += ((float) $d->cost_price * (float) $d->qty);
            }
        }

        $grossProfit = $totalSales - $totalHpp;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;

        $filename = "Laporan-Penjualan-Feedmart-{$dateFrom}-sd-{$dateTo}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions, $dateFrom, $dateTo, $totalSales, $totalHpp, $grossProfit, $profitMargin, $totalDiscount) {
            $handle = fopen('php://output', 'w');

            // Tambahkan instruksi pemisah kolom untuk Microsoft Excel & BOM UTF-8
            fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['sep=,']);

            // Header Dokumen
            fputcsv($handle, ['LAPORAN PENJUALAN & LABA KOTOR - FEEDMART']);
            fputcsv($handle, ['Periode:', $dateFrom.' s/d '.$dateTo]);
            fputcsv($handle, ['Tanggal Unduh:', Carbon::now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []);

            // Ringkasan Finansial
            fputcsv($handle, ['RINGKASAN EKSEKUTIF']);
            fputcsv($handle, ['Total Omzet Penjualan (Rp)', number_format($totalSales, 2, '.', '')]);
            fputcsv($handle, ['Total HPP Pokok Barang (Rp)', number_format($totalHpp, 2, '.', '')]);
            fputcsv($handle, ['Total Laba Kotor / Gross Profit (Rp)', number_format($grossProfit, 2, '.', '')]);
            fputcsv($handle, ['Margin Laba Kotor (%)', number_format($profitMargin, 2, '.', '').'%']);
            fputcsv($handle, ['Total Diskon Diberikan (Rp)', number_format($totalDiscount, 2, '.', '')]);
            fputcsv($handle, ['Total Transaksi', $transactions->count()]);
            fputcsv($handle, []);

            // Tabel Rincian Data Transaksi
            fputcsv($handle, [
                'No',
                'Tanggal & Waktu',
                'No. Invoice',
                'Nama Kasir',
                'Metode Pembayaran',
                'Nama Produk',
                'Kuantitas',
                'Satuan',
                'Harga Jual Satuan (Rp)',
                'HPP Satuan (Rp)',
                'Subtotal Jual (Rp)',
                'Total HPP Item (Rp)',
                'Laba Kotor Item (Rp)',
                'Diskon Faktur (Rp)',
                'Total Faktur (Rp)',
            ]);

            $no = 1;
            foreach ($transactions as $t) {
                foreach ($t->details as $d) {
                    $itemSales = (float) $d->subtotal;
                    $itemHpp = (float) $d->cost_price * (float) $d->qty;
                    $itemProfit = $itemSales - $itemHpp;

                    fputcsv($handle, [
                        $no++,
                        $t->created_at->format('Y-m-d H:i:s'),
                        $t->invoice_number,
                        $t->user->name,
                        strtoupper($t->payment_method),
                        $d->product?->name ?? 'Produk dihapus',
                        (float) $d->qty,
                        $d->product?->unit ?? 'unit',
                        (float) $d->selling_price,
                        (float) $d->cost_price,
                        $itemSales,
                        $itemHpp,
                        $itemProfit,
                        (float) $t->discount,
                        (float) $t->final_amount,
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Tampilan Cetak / Simpan PDF Laporan Penjualan Formal (A4 Landscape).
     */
    public function exportPdf(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());
        $paymentMethod = $request->get('payment_method');
        $userId = $request->get('user_id');

        $query = $this->buildSalesQuery($dateFrom, $dateTo, $paymentMethod, $userId);
        $transactions = $query->with(['user', 'details.product'])->orderBy('created_at')->get();

        $totalSales = $transactions->sum('final_amount');
        $totalDiscount = $transactions->sum('discount');
        $totalHpp = 0;

        foreach ($transactions as $t) {
            foreach ($t->details as $d) {
                $totalHpp += ((float) $d->cost_price * (float) $d->qty);
            }
        }

        $grossProfit = $totalSales - $totalHpp;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;
        $selectedCashier = $userId ? User::find($userId) : null;

        return view('admin.reports.sales_pdf', compact(
            'transactions',
            'dateFrom',
            'dateTo',
            'paymentMethod',
            'selectedCashier',
            'totalSales',
            'totalHpp',
            'grossProfit',
            'profitMargin',
            'totalDiscount'
        ));
    }

    /**
     * Query pembantu untuk filter penjualan
     */
    protected function buildSalesQuery(string $dateFrom, string $dateTo, ?string $paymentMethod = null, ?int $userId = null)
    {
        $query = Transaction::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if (! empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        if (! empty($userId)) {
            $query->where('user_id', $userId);
        }

        return $query;
    }
}
