<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'expired_date',
        'stock_in_batch',
    ];

    protected function casts(): array
    {
        return [
            'expired_date' => 'date',
            'stock_in_batch' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('stock_in_batch', '>', 0);
    }

    public function scopeOrderByExpiring($query)
    {
        return $query->orderBy('expired_date', 'asc');
    }
}
