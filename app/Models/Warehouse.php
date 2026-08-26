<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id', 'shop_id', 'name', 'is_default'];
    protected $casts = ['is_default' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function shop(): BelongsTo { return $this->belongsTo(Shop::class); }
}
