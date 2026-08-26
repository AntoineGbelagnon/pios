<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'supplier_id', 'warehouse_id', 'user_id', 'reference',
        'subtotal', 'discount_amount', 'tax_amount', 'total',
        'amount_paid', 'credit_amount', 'payment_method',
        'expected_delivery_date', 'received_date', 'status', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2', 'total' => 'decimal:2',
        'amount_paid' => 'decimal:2', 'credit_amount' => 'decimal:2',
        'expected_delivery_date' => 'date', 'received_date' => 'date',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseItem::class); }
    public function payments(): MorphMany { return $this->morphMany(Payment::class, 'payable'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Brouillon', 'ordered' => 'Commande', 'partial' => 'Partielle',
            'received' => 'Receptionnee', 'cancelled' => 'Annulee', default => $this->status,
        };
    }
}
