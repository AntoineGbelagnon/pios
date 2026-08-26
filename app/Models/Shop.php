<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = ['company_id', 'name', 'code', 'address', 'city', 'phone', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function warehouses(): HasMany { return $this->hasMany(Warehouse::class); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withPivot('is_default')->withTimestamps(); }
    public function scopeActive($query) { return $query->where('is_active', true); }
}
