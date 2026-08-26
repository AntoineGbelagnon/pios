<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'symbol', 'decimal_places'];

    public function countries(): HasMany { return $this->hasMany(Country::class); }
    public function companies(): HasMany { return $this->hasMany(Company::class); }
}
