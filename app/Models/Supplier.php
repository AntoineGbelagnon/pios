<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'contact_name',
        'email',
        'phone',
        'secondary_phone',
        'address',
        'city',
        'country',
        'tax_id',
        'payment_terms',
        'total_purchases',
        'total_debt',
        'next_payment_date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'total_purchases' => 'decimal:2',
        'total_debt' => 'decimal:2',
        'next_payment_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRemainingDebtAttribute(): float
    {
        return $this->total_debt;
    }
}
