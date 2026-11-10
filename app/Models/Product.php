<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_category_id',
        'supplier_id',
        'sku',
        'barcode',
        'name',
        'unit',
        'is_repack',
        'parent_product_id',
        'conversion_ratio',
        'cost_price',
        'selling_price',
        'stock',
        'min_stock_alert',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_repack' => 'boolean',
            'is_active' => 'boolean',
            'conversion_ratio' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'decimal:2',
            'min_stock_alert' => 'decimal:2',
        ];
    }

    public function animalCategory(): BelongsTo
    {
        return $this->belongsTo(AnimalCategory::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }

    public function repackProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_product_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function isLowStock(): bool
    {
        return (float) $this->stock <= (float) $this->min_stock_alert;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock_alert');
    }
}
