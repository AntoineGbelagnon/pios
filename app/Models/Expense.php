<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'shop_id', 'cash_register_id', 'user_id',
        'category', 'amount', 'description', 'expense_date',
        'receipt_path', 'payment_method', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function shop(): BelongsTo { return $this->belongsTo(Shop::class); }
    public function cashRegister(): BelongsTo { return $this->belongsTo(CashRegister::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public const CATEGORIES = [
        'transport', 'electricite', 'internet', 'salaire', 'entretien',
        'loyer', 'fournitures', 'reparation', 'autres',
    ];
}
