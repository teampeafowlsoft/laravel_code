<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Productvariant extends Model
{
    use HasFactory;
    protected $table = 'product_variant';
    protected $casts = [
        'packate_measurement_sell_price' => 'float',
    ];

    protected $fillable = ['product_id', 'group_id', 'packate_measurement_attribute_id', 'packate_measurement_attribute_value', 'packate_measurement_sell_price', 'packate_measurement_cost_price','packate_measurement_discount_price','packate_measurement_shelf_life_val','packate_measurement_shelf_life_unit','packate_measurement_barcode','packate_measurement_fssai_number','packate_measurement_qty','packate_measurement_images'];


//    public function attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
//        return $this->belongsTo(Attribute::class);
//    }

    protected static function booted()
    {
//        static::addGlobalScope('zone_wise_data', function (Builder $builder) {
//            if (request()->is('api/*/customer?*') || request()->is('api/*/customer/*')) {
//                $builder->where(['zone_id' => Config::get('zone_id')])->with(['zone:id,name']);
//            }
//            elseif (request()->is('api/*/provider?*') || request()->is('api/*/provider/*')) {
//                if (auth()->check() && auth()->user()->provider != null) {
//                    $builder->where(['zone_id' => auth()->user()->provider->zone_id])->with(['zone:id,name']);
//                }
//            }
//        });
    }
}
