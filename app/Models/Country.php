<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'iso_code', 'currency_id', 'phone_prefix', 'tax_rules', 'payment_providers'];
    protected $casts = ['tax_rules' => 'array', 'payment_providers' => 'array'];

    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function companies(): HasMany { return $this->hasMany(Company::class); }
}
