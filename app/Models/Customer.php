<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'secondary_phone',
        'address',
        'city',
        'customer_type',
        'company_name',
        'tax_id',
        'credit_limit',
        'total_spent',
        'total_debt',
        'loyalty_points',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'total_debt' => 'decimal:2',
        'loyalty_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRemainingCreditAttribute(): float
    {
        return $this->credit_limit - $this->total_debt;
    }

    public function getIsProfessionalAttribute(): bool
    {
        return $this->customer_type === 'professional';
    }
}
