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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_transactions_status_created_at');
            $table->index(['user_id', 'status'], 'idx_transactions_user_id_status');
        });

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->index(['type', 'created_at'], 'idx_stock_mutations_type_created_at');
            $table->index(['product_id', 'created_at'], 'idx_stock_mutations_product_created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'stock'], 'idx_products_active_stock');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('debt_balance', 'idx_customers_debt_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_status_created_at');
            $table->dropIndex('idx_transactions_user_id_status');
        });

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropIndex('idx_stock_mutations_type_created_at');
            $table->dropIndex('idx_stock_mutations_product_created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_stock');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_debt_balance');
        });
    }
};
