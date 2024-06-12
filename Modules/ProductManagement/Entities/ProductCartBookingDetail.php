<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCartBookingDetail extends Model
{
    use HasFactory;

    protected $casts = [
        'booking_id' => 'string',
        'quantity' => 'integer',
        'service_cost' => 'float',
        'discount_amount' => 'float',
        'shipping_charge' => 'float',
        'tax_amount' => 'float',
        'total_cost' => 'float',
        'campaign_discount_amount' => 'float',
        'overall_coupon_discount_amount' => 'float',
    ];

    protected $fillable = [];

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->join('providers', 'providers.id', '=', 'products.vendor')
            ->select('products.*', 'providers.contact_person_name AS contact_person_name', 'providers.contact_person_phone AS contact_person_phone', 'providers.company_address AS company_address');
    }

    public function variation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Productvariant::class, 'product_variant_id', 'id')->join('attributes', 'attributes.id', '=', 'product_variant.packate_measurement_attribute_id')
            ->join('attributevalues', 'attributevalues.attribute_id', '=', 'attributes.id')
            ->select('product_variant.*', 'attributes.attribute_name AS attribute_name','attributevalues.attribute_value AS attribute_value');
    }


    protected static function newFactory()
    {
        return \Modules\ProductManagement\Database\factories\ProductCartBookingDetailFactory::new();
    }
}
