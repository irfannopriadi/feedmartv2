<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMutation;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockOpnameController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $query = StockMutation::with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mutations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.stock_opname.index', compact('products', 'mutations'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'actual_physical_stock' => ['required', 'numeric', 'min:0'],
            'adjustment_reason' => ['required', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $actualStock = (float) $request->actual_physical_stock;

        try {
            DB::transaction(function () use ($product, $actualStock, $request) {
                $stockBefore = (float) $product->stock;
                $diff = $actualStock - $stockBefore;

                if ($diff == 0) {
                    throw new Exception("Stok fisik sama dengan stok sistem saat ini ({$stockBefore} {$product->unit}). Tidak ada penyesuaian.");
                }

                $product->update(['stock' => $actualStock]);

                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'ADJUSTMENT',
                    'qty_change' => $diff,
                    'qty_before' => $stockBefore,
                    'qty_after' => $actualStock,
                    'notes' => "Stock Opname: {$request->adjustment_reason} (Selisih: ".($diff > 0 ? "+{$diff}" : $diff)." {$product->unit})",
                ]);
            });

            return back()->with('success', "Penyesuaian stok untuk {$product->name} berhasil disimpan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Ekspor Log Audit Mutasi Stok ke Excel (.csv UTF-8 BOM).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $query = StockMutation::with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mutations = $query->orderBy('created_at')->get();

        $filename = 'Jurnal-Mutasi-Stok-Feedmart-'.Carbon::now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($mutations) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 & sep=, untuk Microsoft Excel
            fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['sep=,']);

            // Header Dokumen
            fputcsv($handle, ['JURNAL AUDIT MUTASI INVENTARIS & STOK OPNAME - FEEDMART']);
            fputcsv($handle, ['Tanggal Unduh:', Carbon::now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, ['Total Catatan Mutasi:', $mutations->count()]);
            fputcsv($handle, []);

            // Baris Judul Kolom
            fputcsv($handle, [
                'No',
                'Tanggal & Waktu',
                'Nama Pengguna / Operator',
                'Tipe Mutasi',
                'Nama Produk Pakan',
                'Satuan',
                'Stok Awal',
                'Jumlah Perubahan (+/-)',
                'Stok Akhir',
                'Catatan / Keterangan Audit',
            ]);

            $no = 1;
            foreach ($mutations as $m) {
                fputcsv($handle, [
                    $no++,
                    $m->created_at->format('Y-m-d H:i:s'),
                    $m->user?->name ?? '-',
                    $m->type,
                    $m->product?->name ?? '-',
                    $m->product?->unit ?? '-',
                    (float) $m->qty_before,
                    (float) $m->qty_change,
                    (float) $m->qty_after,
                    $m->notes ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
