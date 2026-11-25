<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Seed default store settings
        $defaults = [
            ['key' => 'store_name', 'value' => 'FEEDMART', 'group' => 'store'],
            ['key' => 'store_tagline', 'value' => 'PUSAT PAKAN TERNAK & HOBI', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Raya Peternakan No. 88, Jawa Timur', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '0812-3456-7890', 'group' => 'store'],
            ['key' => 'receipt_footer', 'value' => 'Barang yang dibeli tidak dapat ditukar kecuali ada perjanjian. Terima kasih atas kunjungan Anda!', 'group' => 'receipt'],
            ['key' => 'receipt_paper_size', 'value' => '58mm', 'group' => 'receipt'],
        ];

        foreach ($defaults as $item) {
            DB::table('settings')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
