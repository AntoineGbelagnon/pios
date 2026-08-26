<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'shop_id', 'user_id', 'customer_id', 'invoice_number',
        'subtotal', 'discount_amount', 'discount_percent', 'tax_amount', 'tax_percent',
        'total', 'amount_paid', 'change_amount', 'payment_method', 'payment_details',
        'credit_amount', 'notes', 'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getIsCreditAttribute(): bool
    {
        return $this->credit_amount > 0;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Espèces',
            'card' => 'Carte',
            'mobile_money' => 'Mobile Money',
            'credit' => 'Crédit',
            'mixed' => 'Mixte',
            default => $this->payment_method,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'completed' => 'Complétée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée',
            default => $this->status,
        };
    }
}
