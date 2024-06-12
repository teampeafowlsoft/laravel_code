<?php

namespace Modules\ProductManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use Modules\AttributeManagement\Entities\Attribute;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Feature;
use Modules\ProductManagement\Entities\Productvariant;
use Modules\ProductManagement\Entities\Specification;
use Modules\ProductManagement\Entities\ProductdiscountType;
use Modules\ProviderManagement\Entities\Provider;
use Modules\OrderpoolManagement\Entities\Order;

class Product extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'is_active' => 'integer',
    ];

    protected $fillable = ['group_id','lang_id','name','name','description','category_id','indicator','sku','tags','vendor','made_in','manufacturer','manufacturer_part_no','brand_ids','weight','length','width','height','return_status','promo_status','cancelable_status','till_status','bstatus','image','videoURL','brochure','seoPageNm','sMetaTitle','sMetaKeywords','sMetaDescription','published_status','show_home_page_status','review_status','availableStartDt','availableEndDt','mark_as_new_status','topseller_status','indemand_status','bapprovalst','approvalDt','block_product_status','block_comment','adminComment','approve_status','rating_count','avg_rating','is_active','created_at','updated_at'];

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

    public function scopeOfApproval($query, $status)
    {
        $query->where('bapprovalst', '=', $status);
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'categories');
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'product_id', 'id');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(Specification::class, 'product_id', 'id');
    }

    public function shipping(): HasMany
    {
        return $this->hasMany(Productshipping::class, 'product_id', 'id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Productsmedia::class, 'product_id', 'id');
    }

    public function subcategory(): HasMany
    {
        return $this->hasMany(Productsubcategory::class, 'product_id', 'id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'vendor')->withoutGlobalScopes();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Productcategory::class, 'category_id', 'id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(Productvariant::class, 'product_id', 'id')
            ->join('attributes', 'attributes.id', '=', 'product_variant.packate_measurement_attribute_id')
            ->join('attributevalues', 'attributevalues.id', '=', 'product_variant.packate_measurement_attribute_value')
            ->select('product_variant.*', 'attributes.attribute_name AS attribute_name','attributevalues.attribute_value AS attribute_value');
    }

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Productvariant::class, 'product_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'packate_measurement_attribute_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_variant_id', 'id');
    }

    public function campaign_discount(): HasMany
    {
        return $this->hasMany(ProductdiscountType::class, 'type_wise_id')
            ->whereHas('discount', function ($query) {
                $query->where('promotion_type', 'campaign')
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->where('is_active', 1);
            })->whereHas('discount.discount_types', function ($query) {
                if (request()->is('api/*/provider?*') || request()->is('api/*/provider/*')) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => request()->user()->provider->zone_id]);
                } elseif (request()->is('api/*/customer?*') || request()->is('api/*/customer/*')) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                }
            })->with(['discount'])->latest();
    }

    protected static function booted()
    {
        static::addGlobalScope('category_wise_data', function (Builder $builder) {
            if (request()->is('api/*/customer?*') || request()->is('api/*/customer/*')) {
//                $builder->whereHas('category', function ($query) {
//                    $query->where('category_id', Config::get('category_id'));
//                });
                //                $builder->whereHas('category', function ($query) {
//                    $query->where('category_id', Config::get('category_id'));
//                });
            } elseif (request()->is('api/*/provider?*') || request()->is('api/*/provider/*')) {
                if (auth()->check() && request()->user()->provider != null) {
//                    $builder->whereHas('category.zones', function ($query) {
//                        $query->where('zone_id', request()->user()->provider->zone_id);
//                    });
                }
            }
        });
    }
}
