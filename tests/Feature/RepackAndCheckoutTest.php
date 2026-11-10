<?php

namespace Tests\Feature;

use App\Models\AnimalCategory;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PosCheckoutService;
use App\Services\RepackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepackAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_repack_service_successfully_converts_sack_to_retail_units(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $category = AnimalCategory::create([
            'name' => 'Unggas',
            'slug' => 'unggas',
        ]);

        $parentProduct = Product::create([
            'animal_category_id' => $category->id,
            'sku' => 'SAK-001',
            'name' => 'Pakan Induk 50kg',
            'unit' => 'sak',
            'is_repack' => false,
            'stock' => 10.00,
            'cost_price' => 400000,
            'selling_price' => 450000,
        ]);

        ProductBatch::create([
            'product_id' => $parentProduct->id,
            'batch_number' => 'BATCH-SAK-1',
            'expired_date' => now()->addMonths(5)->toDateString(),
            'stock_in_batch' => 10.00,
        ]);

        $repackProduct = Product::create([
            'animal_category_id' => $category->id,
            'sku' => 'ECR-001',
            'name' => 'Pakan Eceran 1kg',
            'unit' => 'kg',
            'is_repack' => true,
            'parent_product_id' => $parentProduct->id,
            'conversion_ratio' => 50.00,
            'stock' => 0.00,
            'cost_price' => 8000,
            'selling_price' => 10000,
        ]);

        $service = new RepackService;
        $result = $service->executeRepack($repackProduct, 2.00, $user);

        // Parent should be reduced by 2 sak (10 - 2 = 8)
        $this->assertEquals(8.00, (float) $parentProduct->fresh()->stock);

        // Repack should be increased by 2 * 50 = 100 kg
        $this->assertEquals(100.00, (float) $repackProduct->fresh()->stock);

        // Verify mutations
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $parentProduct->id,
            'type' => 'REPACK_OUT',
            'qty_change' => -2.00,
        ]);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $repackProduct->id,
            'type' => 'REPACK_IN',
            'qty_change' => 100.00,
        ]);
    }

    public function test_pos_checkout_service_processes_sale_and_deducts_stock(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $register = CashRegister::create([
            'user_id' => $user->id,
            'opening_cash' => 200000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $product = Product::create([
            'sku' => 'PROD-001',
            'name' => 'Vitamin Ayam',
            'unit' => 'bungkus',
            'stock' => 20.00,
            'cost_price' => 5000,
            'selling_price' => 10000,
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'B-001',
            'expired_date' => now()->addMonths(3)->toDateString(),
            'stock_in_batch' => 20.00,
        ]);

        $service = new PosCheckoutService;
        $transaction = $service->checkout(
            user: $user,
            items: [
                ['product_id' => $product->id, 'qty' => 3.00, 'selling_price' => 10000],
            ],
            paymentMethod: 'cash',
            paidAmount: 50000,
            discount: 5000,
            status: 'completed',
            cashRegisterId: $register->id
        );

        $this->assertEquals(30000.00, (float) $transaction->subtotal);
        $this->assertEquals(5000.00, (float) $transaction->discount);
        $this->assertEquals(25000.00, (float) $transaction->final_amount);
        $this->assertEquals(50000.00, (float) $transaction->paid_amount);
        $this->assertEquals(25000.00, (float) $transaction->change_amount);

        // Product stock should decrease from 20 to 17
        $this->assertEquals(17.00, (float) $product->fresh()->stock);

        // Stock mutation OUT recorded
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'qty_change' => -3.00,
            'reference_type' => Transaction::class,
            'reference_id' => $transaction->id,
        ]);
    }
}
