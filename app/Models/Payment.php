<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'user_id', 'payable_id', 'payable_type',
        'payment_number', 'amount', 'payment_method', 'payment_details',
        'direction', 'reference', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function payable(): MorphTo { return $this->morphTo(); }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Especes',
            'card' => 'Carte',
            'mobile_money' => 'Mobile Money',
            'bank_transfer' => 'Virement',
            'credit' => 'Credit',
            'mixed' => 'Mixte',
            default => $this->payment_method,
        };
    }

    public function getDirectionLabelAttribute(): string
    {
        return $this->direction === 'in' ? 'Recette' : 'Depense';
    }

    public static function generatePaymentNumber(): string
    {
        return 'PAY-' . now()->format('Ymd') . '-' . str_pad(static::count() + 1, 5, '0', STR_PAD_LEFT);
    }
}
