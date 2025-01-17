<?php

namespace Modules\ProductManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ProductManagement\Entities\ProductdiscountType;

class Productdiscount extends Model
{
    use HasFactory, HasUuid;

    protected $casts = [
        'discount_amount' => 'float',
        'min_purchase' => 'float',
        'max_discount_amount' => 'float',
        'limit_per_user' => 'integer',
        'is_active' => 'integer',
    ];

    protected $fillable = [];

    public function discount_types(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductdiscountType::class, 'discount_id');
    }

    public function category_types(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductdiscountType::class, 'discount_id')
            ->where('discount_type','category')
            ->with('category.zones');
    }

    public function service_types(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductdiscountType::class, 'discount_id')
            ->where('discount_type','product')
            ->with('product.category.zones');
    }

    public function zone_types(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductdiscountType::class, 'discount_id')
            ->where('discount_type','zone')
            ->with('zone');
    }

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

    public function scopeOfPromotionTypes($query, $type)
    {
        $query->where('promotion_type', '=', $type);
    }
}
