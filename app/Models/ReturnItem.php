<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id', 'sale_item_id', 'product_id', 'quantity',
        'unit_price', 'total', 'condition', 'restock',
    ];

    protected $casts = [
        'quantity' => 'integer', 'unit_price' => 'decimal:2',
        'total' => 'decimal:2', 'restock' => 'boolean',
    ];

    public function salesReturn(): BelongsTo { return $this->belongsTo(SalesReturn::class); }
    public function saleItem(): BelongsTo { return $this->belongsTo(SaleItem::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
