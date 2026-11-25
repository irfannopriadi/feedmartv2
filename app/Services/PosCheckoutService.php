<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class PosCheckoutService
{
    /**
     * Memproses checkout transaksi POS.
     *
     * @param  User  $user  Kasir / Admin
     * @param  array  $items  Array of ['product_id' => int, 'qty' => float, 'selling_price' => ?float]
     * @param  string  $paymentMethod  'cash' | 'qris' | 'transfer' | 'tempo'
     * @param  string  $status  'completed' | 'hold'
     *
     * @throws Exception
     */
    public function checkout(
        User $user,
        array $items,
        string $paymentMethod = 'cash',
        float $paidAmount = 0.00,
        float $discount = 0.00,
        string $status = 'completed',
        ?int $cashRegisterId = null,
        ?int $customerId = null,
        ?string $dueDate = null
    ): Transaction {
        if (empty($items)) {
            throw new Exception('Keranjang belanja masih kosong.');
        }

        if ($status === 'completed' && $paymentMethod === 'tempo' && ! $customerId) {
            throw new Exception('Untuk transaksi Kasbon / Tempo, nama pelanggan/peternak wajib dipilih!');
        }

        // Jika cash register tidak disuplai, cari kasir shift aktif jika status completed
        if (! $cashRegisterId && $status === 'completed') {
            $activeRegister = CashRegister::where('user_id', $user->id)
                ->where('status', 'open')
                ->latest()
                ->first();
            $cashRegisterId = $activeRegister?->id;
        }

        return DB::transaction(function () use ($user, $items, $paymentMethod, $paidAmount, $discount, $status, $cashRegisterId, $customerId, $dueDate) {
            $subtotal = 0.00;
            $preparedDetails = [];

            // 1. Kunci dan periksa ketersediaan stok produk
            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $qty = (float) $item['qty'];

                if ($qty <= 0) {
                    throw new Exception('Kuantitas produk tidak valid.');
                }

                /** @var Product $product */
                $product = Product::lockForUpdate()->find($productId);
                if (! $product) {
                    throw new Exception("Produk dengan ID #{$productId} tidak ditemukan.");
                }

                if ($status === 'completed' && (float) $product->stock < $qty) {
                    throw new Exception("Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock} {$product->unit}, Diminta: {$qty}).");
                }

                $sellingPrice = isset($item['selling_price']) && (float) $item['selling_price'] > 0
                    ? (float) $item['selling_price']
                    : (float) $product->selling_price;

                $itemSubtotal = $qty * $sellingPrice;
                $subtotal += $itemSubtotal;

                $preparedDetails[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'cost_price' => (float) $product->cost_price,
                    'selling_price' => $sellingPrice,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = max(0.00, $discount);
            $finalAmount = max(0.00, $subtotal - $discount);

            if ($status === 'completed') {
                if ($paymentMethod === 'cash' && $paidAmount < $finalAmount) {
                    throw new Exception('Uang pembayaran kurang! (Total: Rp '.number_format($finalAmount, 0, ',', '.').', Diberikan: Rp '.number_format($paidAmount, 0, ',', '.').')');
                }

                if ($paymentMethod === 'tempo') {
                    $paidAmount = 0.00;
                    $changeAmount = 0.00;

                    // Tambahkan ke saldo kasbon pelanggan
                    if ($customerId) {
                        $customer = Customer::lockForUpdate()->find($customerId);
                        if ($customer) {
                            $customer->increment('debt_balance', $finalAmount);
                        }
                    }
                } else {
                    $changeAmount = ($paymentMethod === 'cash' && $paidAmount >= $finalAmount)
                        ? ($paidAmount - $finalAmount)
                        : 0.00;
                }
            } else {
                $changeAmount = 0.00;
            }

            // 2. Buat Nomor Invoice Unik
            $invoiceNumber = $this->generateInvoiceNumber();

            // 3. Simpan Transaksi Induk
            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
                'cash_register_id' => $cashRegisterId,
                'customer_id' => $customerId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'final_amount' => $finalAmount,
                'paid_amount' => $status === 'completed' ? ($paymentMethod === 'tempo' ? 0.00 : ($paymentMethod === 'cash' ? $paidAmount : $finalAmount)) : 0.00,
                'change_amount' => $status === 'completed' ? $changeAmount : 0.00,
                'payment_method' => $paymentMethod,
                'due_date' => $paymentMethod === 'tempo' ? ($dueDate ?? now()->addDays(30)->toDateString()) : null,
                'status' => $status,
            ]);

            // 4. Proses Tiap Detail & Pengurangan Stok
            foreach ($preparedDetails as $detail) {
                /** @var Product $product */
                $product = $detail['product'];
                $qty = $detail['qty'];

                $chosenBatchId = null;

                if ($status === 'completed') {
                    // FIFO Batch Deduction
                    $remainingDeduct = $qty;
                    $batches = ProductBatch::where('product_id', $product->id)
                        ->where('stock_in_batch', '>', 0)
                        ->orderBy('expired_date', 'asc')
                        ->lockForUpdate()
                        ->get();

                    foreach ($batches as $batch) {
                        if ($chosenBatchId === null) {
                            $chosenBatchId = $batch->id;
                        }

                        $batchQty = (float) $batch->stock_in_batch;
                        if ($batchQty >= $remainingDeduct) {
                            $batch->update(['stock_in_batch' => $batchQty - $remainingDeduct]);
                            $remainingDeduct = 0;
                            break;
                        } else {
                            $remainingDeduct -= $batchQty;
                            $batch->update(['stock_in_batch' => 0]);
                        }
                    }

                    // Update stok fisik master produk
                    $stockBefore = (float) $product->stock;
                    $stockAfter = $stockBefore - $qty;
                    $product->update(['stock' => $stockAfter]);

                    // Catat Audit Mutasi Stok (OUT)
                    StockMutation::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'OUT',
                        'qty_change' => -$qty,
                        'qty_before' => $stockBefore,
                        'qty_after' => $stockAfter,
                        'reference_type' => Transaction::class,
                        'reference_id' => $transaction->id,
                        'notes' => "Penjualan Faktur {$transaction->invoice_number}",
                    ]);
                }

                // Simpan baris detail penjualan
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'batch_id' => $chosenBatchId,
                    'cost_price' => $detail['cost_price'],
                    'selling_price' => $detail['selling_price'],
                    'qty' => $qty,
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            return $transaction->fresh(['details.product', 'user', 'cashRegister']);
        });
    }

    /**
     * Generate format nomor faktur unik: INV-YYYYMMDD-XXXX
     */
    protected function generateInvoiceNumber(): string
    {
        $todayPrefix = 'INV-'.date('Ymd').'-';
        $latest = Transaction::where('invoice_number', 'LIKE', $todayPrefix.'%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->invoice_number);
            $lastSeq = (int) end($parts);
            $nextSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return $todayPrefix.$nextSeq;
    }
}
