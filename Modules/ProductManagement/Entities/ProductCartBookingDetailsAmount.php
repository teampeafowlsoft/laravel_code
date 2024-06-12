<?php

namespace Modules\ProductManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCartBookingDetailsAmount extends Model
{
    use HasFactory, HasUuid;

    protected $casts = [
        'service_unit_cost' => 'float',

        'service_quantity' => 'integer',
        'service_tax' => 'float',
        'discount_by_admin' => 'float',
        'discount_by_provider' => 'float',

        'coupon_discount_by_admin' => 'float',
        'coupon_discount_by_provider' => 'float',

        'campaign_discount_by_admin' => 'float',
        'campaign_discount_by_provider' => 'float',

        'admin_commission' => 'float',
        'provider_earning' => 'float',
    ];

    protected $fillable = [
        'id',
        'booking_details_id',
        'booking_id',
        'service_unit_cost',

        'discount_by_admin',
        'discount_by_provider',

        'coupon_discount_by_admin',
        'coupon_discount_by_provider',

        'campaign_discount_by_admin',
        'campaign_discount_by_provider',
    ];

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductCartBooking::class,  'product_cart_booking_id', 'id');
    }

    protected static function newFactory()
    {
        return \Modules\ProductManagement\Database\factories\ProductCartBookingDetailsAmountFactory::new();
    }
}
