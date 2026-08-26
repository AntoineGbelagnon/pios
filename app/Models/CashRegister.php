<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'shop_id', 'user_id', 'cashier_id',
        'opening_amount', 'closing_amount', 'expected_amount', 'difference',
        'total_sales', 'total_expenses', 'total_cash_in', 'total_cash_out',
        'opening_notes', 'closing_notes', 'status',
    ];

    protected $casts = [
        'opening_amount' => 'decimal:2',
        'closing_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'total_cash_in' => 'decimal:2',
        'total_cash_out' => 'decimal:2',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function shop(): BelongsTo { return $this->belongsTo(Shop::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function cashier(): BelongsTo { return $this->belongsTo(User::class, 'cashier_id'); }

    public function scopeOpen($query) { return $query->where('status', 'open'); }
    public function scopeClosed($query) { return $query->where('status', 'closed'); }

    public function getBalanceAttribute(): float
    {
        return $this->opening_amount + $this->total_sales - $this->total_expenses + $this->total_cash_in - $this->total_cash_out;
    }
}
