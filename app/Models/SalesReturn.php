<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $table = 'returns';

    protected $fillable = [
        'company_id', 'sale_id', 'customer_id', 'user_id', 'return_number',
        'type', 'total_refund', 'refund_method', 'reason', 'notes', 'status',
    ];

    protected $casts = [
        'total_refund' => 'decimal:2',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(ReturnItem::class); }
}
