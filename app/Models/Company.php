<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;
use App\Models\SalesReturn as ReturnModel;
use Spatie\Permission\PermissionRegistrar;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'legal_name', 'logo', 'email', 'phone', 'address', 'country_id', 'currency_id', 'tax_id', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::created(function (self $company): void {
            app(PermissionRegistrar::class)->setPermissionsTeamId($company->id);
            foreach (['admin', 'propriétaire', 'manager', 'caissier', 'vendeur', 'magasinier', 'comptable', 'livreur', 'responsable achats', 'responsable stocks'] as $role) {
                Role::findOrCreate($role, 'web');
            }
        });
    }

    public function country(): BelongsTo { return $this->belongsTo(Country::class); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function shops(): HasMany { return $this->hasMany(Shop::class); }
    public function warehouses(): HasMany { return $this->hasMany(Warehouse::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function settings(): HasMany { return $this->hasMany(Setting::class); }
    public function categories(): HasMany { return $this->hasMany(Category::class); }
    public function brands(): HasMany { return $this->hasMany(Brand::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function suppliers(): HasMany { return $this->hasMany(Supplier::class); }
    public function sales(): HasMany { return $this->hasMany(Sale::class); }
    public function cashRegisters(): HasMany { return $this->hasMany(CashRegister::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }
    public function returns(): HasMany { return $this->hasMany(ReturnModel::class); }
    public function warranties(): HasMany { return $this->hasMany(Warranty::class); }
    public function notifications(): HasMany { return $this->hasMany(AppNotification::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
}
