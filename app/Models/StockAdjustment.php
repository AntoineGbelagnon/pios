<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id', 'product_id', 'warehouse_id', 'previous_quantity',
        'new_quantity', 'difference', 'reason', 'created_by',
    ];

    protected $casts = [
        'previous_quantity' => 'integer',
        'new_quantity' => 'integer',
        'difference' => 'integer',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
