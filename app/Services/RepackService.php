<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMutation;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class RepackService
{
    /**
     * Bongkar karung induk menjadi produk eceran.
     *
     * @param  Product  $repackProduct  Produk eceran (is_repack = true)
     * @param  float  $parentQty  Jumlah sak/karung yang dibongkar (biasanya 1 atau lebih)
     * @param  User  $user  Pengguna yang mengeksekusi repack
     *
     * @throws Exception
     */
    public function executeRepack(Product $repackProduct, float $parentQty, User $user): array
    {
        if (! $repackProduct->is_repack || ! $repackProduct->parent_product_id) {
            throw new Exception("Produk '{$repackProduct->name}' bukan merupakan produk eceran (repack).");
        }

        $parentProduct = Product::lockForUpdate()->findOrFail($repackProduct->parent_product_id);

        if ((float) $parentProduct->stock < $parentQty) {
            throw new Exception("Stok karung induk '{$parentProduct->name}' tidak mencukupi (Tersedia: {$parentProduct->stock} {$parentProduct->unit}, Diminta: {$parentQty} {$parentProduct->unit}).");
        }

        $conversionRatio = (float) $repackProduct->conversion_ratio;
        if ($conversionRatio <= 0) {
            throw new Exception("Rasio konversi untuk produk '{$repackProduct->name}' belum valid.");
        }

        $addedQty = $parentQty * $conversionRatio;

        return DB::transaction(function () use ($parentProduct, $repackProduct, $parentQty, $addedQty, $user) {
            // 1. Kurangi stok karung induk
            $parentBefore = (float) $parentProduct->stock;
            $parentAfter = $parentBefore - $parentQty;
            $parentProduct->update(['stock' => $parentAfter]);

            // Potong batch induk secara FIFO dan ambil tanggal kedaluwarsanya untuk batch eceran
            $remainingDeduct = $parentQty;
            $expDateForRepack = null;

            $parentBatches = ProductBatch::where('product_id', $parentProduct->id)
                ->where('stock_in_batch', '>', 0)
                ->orderBy('expired_date', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($parentBatches as $batch) {
                if ($expDateForRepack === null) {
                    $expDateForRepack = $batch->expired_date;
                }

                $batchStock = (float) $batch->stock_in_batch;
                if ($batchStock >= $remainingDeduct) {
                    $batch->update(['stock_in_batch' => $batchStock - $remainingDeduct]);
                    $remainingDeduct = 0;
                    break;
                } else {
                    $remainingDeduct -= $batchStock;
                    $batch->update(['stock_in_batch' => 0]);
                }
            }

            // 2. Tambah stok eceran
            $repackBefore = (float) $repackProduct->stock;
            $repackAfter = $repackBefore + $addedQty;
            $repackProduct->update(['stock' => $repackAfter]);

            // Buat atau update batch eceran
            $expDate = $expDateForRepack ?: now()->addMonths(6)->toDateString();
            $batchRepackNumber = 'RPK-'.date('Ymd').'-'.$repackProduct->id;

            $repackBatch = ProductBatch::firstOrCreate(
                [
                    'product_id' => $repackProduct->id,
                    'batch_number' => $batchRepackNumber,
                    'expired_date' => $expDate,
                ],
                ['stock_in_batch' => 0]
            );
            $repackBatch->increment('stock_in_batch', $addedQty);

            // 3. Catat StockMutation REPACK_OUT pada induk
            $mutationOut = StockMutation::create([
                'product_id' => $parentProduct->id,
                'user_id' => $user->id,
                'type' => 'REPACK_OUT',
                'qty_change' => -$parentQty,
                'qty_before' => $parentBefore,
                'qty_after' => $parentAfter,
                'reference_type' => Product::class,
                'reference_id' => $repackProduct->id,
                'notes' => "Repack {$parentQty} {$parentProduct->unit} menjadi {$addedQty} {$repackProduct->unit} untuk {$repackProduct->name}",
            ]);

            // 4. Catat StockMutation REPACK_IN pada eceran
            $mutationIn = StockMutation::create([
                'product_id' => $repackProduct->id,
                'user_id' => $user->id,
                'type' => 'REPACK_IN',
                'qty_change' => $addedQty,
                'qty_before' => $repackBefore,
                'qty_after' => $repackAfter,
                'reference_type' => Product::class,
                'reference_id' => $parentProduct->id,
                'notes' => "Hasil repack dari {$parentQty} {$parentProduct->unit} ({$parentProduct->name})",
            ]);

            return [
                'parent_product' => $parentProduct->fresh(),
                'repack_product' => $repackProduct->fresh(),
                'parent_qty_reduced' => $parentQty,
                'added_qty' => $addedQty,
                'mutation_out' => $mutationOut,
                'mutation_in' => $mutationIn,
            ];
        });
    }
}
