<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id', 'product_id', 'warehouse_id', 'type', 'quantity',
        'unit_price', 'reference', 'notes', 'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'entry' => 'Entrée',
            'exit' => 'Sortie',
            'adjustment' => 'Ajustement',
            'transfer' => 'Transfert',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'entry' => 'success',
            'exit' => 'danger',
            'adjustment' => 'warning',
            'transfer' => 'info',
            default => 'secondary',
        };
    }

    public function getTotalValueAttribute(): float
    {
        return $this->unit_price ? $this->quantity * $this->unit_price : 0;
    }
}
