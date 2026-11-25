<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_category_id')->nullable()->constrained('animal_categories')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique()->index();
            $table->string('name')->index();
            $table->string('unit', 50)->default('pcs'); // sak, kg, bungkus, pcs, dll
            $table->boolean('is_repack')->default(false);
            $table->foreignId('parent_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('conversion_ratio', 10, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->default(0.00);
            $table->decimal('stock', 10, 2)->default(0.00);
            $table->decimal('min_stock_alert', 10, 2)->default(5.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->date('expired_date')->index();
            $table->decimal('stock_in_batch', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
        Schema::dropIfExists('products');
    }
};
