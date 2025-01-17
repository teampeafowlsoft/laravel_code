<?php

namespace Modules\PromotionManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Product;
use Modules\ServiceManagement\Entities\Service;

class ProductBanner extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [];

    protected $casts = [
        'is_active' => 'integer'
    ];

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Productcategory::class, 'resource_id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'resource_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('zone_wise_data', function (Builder $builder) {
            if (request()->is('api/*/customer?*') || request()->is('api/*/customer/*')) {
                $builder->whereHas('category.zones', function ($query) {
                    $query->where('zone_id', Config::get('zone_id'));
                })->orWhereHas('product.category.zones', function ($query) {
                    $query->where('zone_id', Config::get('zone_id'));
                })->orWhere('resource_type', 'link');
            }
        });
    }
}
