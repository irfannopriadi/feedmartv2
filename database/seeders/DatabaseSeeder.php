<?php

namespace Database\Seeders;

use App\Models\AnimalCategory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Default
        $admin = User::firstOrCreate(
            ['email' => 'admin@feedmart.test'],
            [
                'name' => 'Admin Feedmart',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@feedmart.test'],
            [
                'name' => 'Kasir Feedmart',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'is_active' => true,
            ]
        );

        // 2. Supplier Sampel
        $supplierCp = Supplier::firstOrCreate(
            ['name' => 'PT Charoen Pokphand Indonesia'],
            [
                'contact_person' => 'Budi Santoso',
                'phone' => '081234567890',
                'address' => 'Kawasan Industri Rungkut, Surabaya',
            ]
        );

        $supplierJapfa = Supplier::firstOrCreate(
            ['name' => 'PT Japfa Comfeed Indonesia'],
            [
                'contact_person' => 'Hendra Wijaya',
                'phone' => '081298765432',
                'address' => 'Sidoarjo, Jawa Timur',
            ]
        );

        // 3. 6 Kategori Hewan Dasar
        $categoriesData = [
            ['name' => 'Unggas', 'slug' => 'unggas'],
            ['name' => 'Kucing', 'slug' => 'kucing'],
            ['name' => 'Anjing', 'slug' => 'anjing'],
            ['name' => 'Burung', 'slug' => 'burung'],
            ['name' => 'Ikan', 'slug' => 'ikan'],
            ['name' => 'Ruminansia / Ternak', 'slug' => 'ruminansia-ternak'],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = AnimalCategory::firstOrCreate(
                ['slug' => $cData['slug']],
                ['name' => $cData['name']]
            );
        }

        // 4. Master Produk & Konversi Repack Sampel
        // Karung Induk: Pakan Ayam Bravo 511 50kg
        $karungAyam = Product::firstOrCreate(
            ['sku' => 'PK-AYM-511-50KG'],
            [
                'animal_category_id' => $categories['unggas']->id,
                'supplier_id' => $supplierCp->id,
                'barcode' => '899100100101',
                'name' => 'Pakan Ayam Bravo 511 Karung 50kg',
                'unit' => 'sak',
                'is_repack' => false,
                'parent_product_id' => null,
                'conversion_ratio' => null,
                'cost_price' => 430000.00,
                'selling_price' => 475000.00,
                'stock' => 12.00,
                'min_stock_alert' => 3.00,
                'is_active' => true,
            ]
        );

        ProductBatch::firstOrCreate(
            ['product_id' => $karungAyam->id, 'batch_number' => 'BATCH-511-2026A'],
            [
                'expired_date' => Carbon::now()->addMonths(6)->toDateString(),
                'stock_in_batch' => 12.00,
            ]
        );

        // Produk Eceran Repack: 1 Sak = 50 Kg
        $eceranAyam = Product::firstOrCreate(
            ['sku' => 'PK-AYM-511-1KG'],
            [
                'animal_category_id' => $categories['unggas']->id,
                'supplier_id' => $supplierCp->id,
                'barcode' => '899100100102',
                'name' => 'Pakan Ayam Bravo 511 Eceran (1 Kg)',
                'unit' => 'kg',
                'is_repack' => true,
                'parent_product_id' => $karungAyam->id,
                'conversion_ratio' => 50.00,
                'cost_price' => 8600.00,
                'selling_price' => 11000.00,
                'stock' => 35.50,
                'min_stock_alert' => 10.00,
                'is_active' => true,
            ]
        );
        $eceranAyam = Product::firstOrCreate(
            ['sku' => 'PK-AYM-511-2KG'],
            [
                'animal_category_id' => $categories['unggas']->id,
                'supplier_id' => $supplierCp->id,
                'barcode' => '899100100103',
                'name' => 'Pakan Ayam Bravo 511 Eceran (2 Kg)',
                'unit' => 'kg',
                'is_repack' => true,
                'parent_product_id' => $karungAyam->id,
                'conversion_ratio' => 25.00,
                'cost_price' => 17200.00,
                'selling_price' => 22000.00,
                'stock' => 45.50,
                'min_stock_alert' => 10.00,
                'is_active' => true,
            ]
        );

        ProductBatch::firstOrCreate(
            ['product_id' => $eceranAyam->id, 'batch_number' => 'REPACK-511-001'],
            [
                'expired_date' => Carbon::now()->addMonths(6)->toDateString(),
                'stock_in_batch' => 35.50,
            ]
        );

        // Produk Lainnya untuk variasi POS
        $catChoize = Product::firstOrCreate(
            ['sku' => 'CAT-CHOIZE-TUNA-800G'],
            [
                'animal_category_id' => $categories['kucing']->id,
                'supplier_id' => $supplierJapfa->id,
                'barcode' => '885012401201',
                'name' => 'Cat Choize Adult Tuna 800gr',
                'unit' => 'bungkus',
                'is_repack' => false,
                'cost_price' => 20000.00,
                'selling_price' => 25000.00,
                'stock' => 24.00,
                'min_stock_alert' => 5.00,
                'is_active' => true,
            ]
        );
        ProductBatch::firstOrCreate(
            ['product_id' => $catChoize->id, 'batch_number' => 'CC-TUNA-2026'],
            [
                'expired_date' => Carbon::now()->addMonths(12)->toDateString(),
                'stock_in_batch' => 24.00,
            ]
        );

        $fancyGold = Product::firstOrCreate(
            ['sku' => 'BRG-FANCY-GOLD-450G'],
            [
                'animal_category_id' => $categories['burung']->id,
                'supplier_id' => $supplierCp->id,
                'barcode' => '899345678901',
                'name' => 'Fancy Gold Food Pakan Burung 450gr',
                'unit' => 'bungkus',
                'is_repack' => false,
                'cost_price' => 12000.00,
                'selling_price' => 16000.00,
                'stock' => 15.00,
                'min_stock_alert' => 4.00,
                'is_active' => true,
            ]
        );
        ProductBatch::firstOrCreate(
            ['product_id' => $fancyGold->id, 'batch_number' => 'FG-2026B'],
            [
                'expired_date' => Carbon::now()->addMonths(8)->toDateString(),
                'stock_in_batch' => 15.00,
            ]
        );

        $takariIkan = Product::firstOrCreate(
            ['sku' => 'IKN-TAKARI-500G'],
            [
                'animal_category_id' => $categories['ikan']->id,
                'supplier_id' => $supplierCp->id,
                'barcode' => '899555666777',
                'name' => 'Takari Pelet Ikan Hias 500gr',
                'unit' => 'bungkus',
                'is_repack' => false,
                'cost_price' => 9500.00,
                'selling_price' => 13500.00,
                'stock' => 30.00,
                'min_stock_alert' => 6.00,
                'is_active' => true,
            ]
        );
        ProductBatch::firstOrCreate(
            ['product_id' => $takariIkan->id, 'batch_number' => 'TAKARI-2026'],
            [
                'expired_date' => Carbon::now()->addMonths(9)->toDateString(),
                'stock_in_batch' => 30.00,
            ]
        );

        // Produk dengan stok mendekati kedaluwarsa untuk alert kedaluwarsa
        $vitaminAyam = Product::firstOrCreate(
            ['sku' => 'VIT-VITA-STRESS-100G'],
            [
                'animal_category_id' => $categories['unggas']->id,
                'supplier_id' => $supplierCp->id,
                'barcode' => '899777888999',
                'name' => 'Vita Stress Serbuk Vitamin Unggas 100gr',
                'unit' => 'bungkus',
                'is_repack' => false,
                'cost_price' => 8000.00,
                'selling_price' => 12000.00,
                'stock' => 18.00,
                'min_stock_alert' => 5.00,
                'is_active' => true,
            ]
        );
        ProductBatch::firstOrCreate(
            ['product_id' => $vitaminAyam->id, 'batch_number' => 'VITA-EXP-NEAR'],
            [
                'expired_date' => Carbon::now()->addDays(20)->toDateString(), // 20 hari lagi kedaluwarsa
                'stock_in_batch' => 18.00,
            ]
        );
    }
}
