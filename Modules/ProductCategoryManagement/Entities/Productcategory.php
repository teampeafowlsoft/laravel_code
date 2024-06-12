<?php

namespace Modules\ProductCategoryManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;
use Modules\ProductManagement\Entities\Product;
use Modules\ProductManagement\Entities\ProductdiscountType;
use Modules\ProductManagement\Entities\Productsubcategory;
use Modules\ProductManagement\Entities\Productvariant;
use Modules\PromotionManagement\Entities\DiscountType;
use Modules\ServiceManagement\Entities\Service;
use Modules\ZoneManagement\Entities\Zone;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Productcategory extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'position' => 'integer',
        'is_active' => 'integer',
    ];

    protected $fillable = [];

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

    public function scopeOfType($query, $type)
    {
        $value = ($type == 'main') ? 1 : 2;
        $query->where(['position' => $value]);
    }

    public function zones(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'product_category_zone');
    }

    public function zonesBasicInfo(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'product_category_zone');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Productcategory::class, 'parent_id');
    }

    public function category_discount(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiscountType::class, 'type_wise_id')
            ->whereHas('discount', function ($query) {
                $query->whereIn('discount_type', ['category', 'mixed'])
                    ->where('promotion_type', 'discount')
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->where('is_active', 1);
            })->whereHas('discount.discount_types', function ($query) {
                $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
            })->with(['discount'])->latest();
    }

    public function campaign_discount(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductdiscountType::class, 'type_wise_id')
            ->whereHas('discount', function ($query) {
                $query->where('promotion_type', 'campaign')
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->where('is_active', 1);
            })->whereHas('discount.discount_types', function ($query) {
                $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
            })->with(['discount'])->latest();
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Productcategory::class, 'parent_id')->withoutGlobalScopes();
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Service::class, 'sub_category_id');
    }

    public function totalproducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Productsubcategory::class, 'subcategory_id')
            ->join('products', function($join){
                $join->on('products.id', '=', 'product_subcategory.product_id')
                    ->where('products.is_active', '=', 1);
             })
            ->join('providers', function($join){
                $join->on('providers.id', '=', 'products.vendor')
                    ->where('providers.is_active', '=', 1);
            })


//            ->select('product_variant.*', 'attributes.attribute_name AS attribute_name','attributevalues.attribute_value AS attribute_value')
            ;
    }

    public function productvariants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Productsubcategory::class, 'subcategory_id')
            ->join('products', 'products.id', '=', 'product_subcategory.product_id')
            ->join('product_variant', 'products.id', '=', 'product_variant.product_id')
            ->join('attributes', 'attributes.id', '=', 'product_variant.packate_measurement_attribute_id')
            ->join('attributevalues', 'attributevalues.attribute_id', '=', 'attributes.id')
            ->select('product_subcategory.*','products.*','products.id as pID','product_variant.*','product_variant.id as pvID', 'attributes.attribute_name AS attribute_name','attributevalues.attribute_value AS attribute_value')
;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'category_id', 'id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_subcategory');
    }

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Productvariant::class, 'product_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('zone_wise_data', function (Builder $builder) {
            if (request()->is('api/*/customer?*') || request()->is('api/*/customer/*')) {
                $builder->whereHas('zones', function ($query) {
                    $query->where('zone_id', Config::get('zone_id'));
                })->with(['category_discount', 'campaign_discount']);
            }
        });
    }
}
