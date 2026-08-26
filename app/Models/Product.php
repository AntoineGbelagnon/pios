<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'category_id', 'brand_id', 'sku', 'barcode', 'name', 'slug',
        'description', 'purchase_price', 'sale_price', 'promo_price',
        'stock_quantity', 'alert_threshold', 'unit', 'warranty_months',
        'is_serialized', 'is_active', 'image', 'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'alert_threshold' => 'integer',
        'warranty_months' => 'integer',
        'is_serialized' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $model): void {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function (Product $model): void {
            if ($model->isDirty('name') && ! $model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->promo_price && $this->promo_price > 0 && $this->promo_price < $this->sale_price
            ? $this->promo_price
            : $this->sale_price;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->alert_threshold;
    }

    public function getStockStatusLabelAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Rupture';
        }
        if ($this->is_low_stock) {
            return 'Stock faible';
        }

        return 'En stock';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'danger';
        }
        if ($this->is_low_stock) {
            return 'warning';
        }

        return 'success';
    }

    public function getMarginPercentAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }

        return round((($this->effective_price - $this->purchase_price) / $this->purchase_price) * 100, 1);
    }
}
