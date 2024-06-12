<?php

namespace Modules\OrderpoolManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\BookingModule\Entities\BookingDetail;
use Modules\BookingModule\Entities\BookingScheduleHistory;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\ProductManagement\Entities\Product;
use Modules\ProductManagement\Entities\Productvariant;
use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAddress;
use Modules\ZoneManagement\Entities\Zone;

class Order extends Model
{
    use HasFactory;
    use HasUuid;
    protected $table = 'order_items';
    protected $casts = [
        'order_id' => 'integer',
        'product_variant_id' => 'integer',
        'price' => 'float',
        'discounted_price' => 'double',
        'discount' => 'float',
        'sub_total' => 'float',
    ];

    protected $fillable = [
        'id',
        'order_no',
        'order_no_vendor',
        'user_id',
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
        'discounted_price',
        'discount',
        'sub_total',
        'deliver_by',
        'status',
        'active_status',
        'is_active',
        'created_at',
        'updated_at',
        'date_added',
//        'sub_category_id',
    ];

    public function scopeOfBookingStatus($query, $status)
    {
        $query->where('active_status', '=', $status);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productvariant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Productvariant::class, 'product_variant_id')->join('attributes', 'attributes.id', '=', 'product_variant.packate_measurement_attribute_id')
            ->join('attributevalues', 'attributevalues.attribute_id', '=', 'attributes.id')
            ->select('product_variant.*', 'attributes.attribute_name AS attribute_name','attributevalues.attribute_value AS attribute_value');
//            ->join('products', 'products.id', '=', 'product_variant.product_id')
//            ->select('product_variant.*', 'products.*');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

//    public static function boot()
//    {
//        parent::boot();
//
//        self::creating(function ($model) {
//            $model->readable_id = $model->count() + 100000;
//        });
//
//        self::created(function ($model) {
//            place_booking_transaction($model);
//        });
//
//        self::updating(function ($model) {
//            $booking_notification_status = business_config('booking', 'notification_settings')->live_values;
//
//            if ($model->isDirty('booking_status')) {
//                $key = null;
//                if ($model->booking_status == 'pending') {
//                    $key = 'booking_place';
//                } elseif ($model->booking_status == 'ongoing') {
//                    $key = 'booking_ongoing';
//                } elseif ($model->booking_status == 'accepted') {
//                    $key = 'booking_accepted';
//                } elseif ($model->booking_status == 'completed') {
//                    //updating payment status
//                    $model->is_paid = 1;
//                    $key = 'booking_service_complete';
//                    if ($model->payment_method == 'cash_after_service') {
//                        cash_after_service_booking_transaction($model);
//                    } else {
//                        digital_payment_booking_transaction($model);
//                    }
//                } elseif ($model->booking_status == 'canceled') {
//                    $key = 'booking_cancel';
//                } elseif ($model->booking_status == 'refund_request') {
//                    $key = 'booking_refund';
//                }
//                $data = business_config($key, 'notification_messages');
//                if (isset($data) && $data->is_active) {
//                    if (isset($booking_notification_status) && $booking_notification_status['push_notification_booking']) {
//                        if (isset($model->customer)) {
//                            device_notification($model->customer->fcm_token, $data->live_values[$key . '_message'], null, null, $model->id);
//                        }
//                        if (isset($model->provider->owner)) {
//                            device_notification($model->provider->owner->fcm_token, $data->live_values[$key . '_message'], null, null, $model->id);
//                        }
//                        if (isset($model->serviceman->user)) {
//                            device_notification($model->serviceman->user->fcm_token, $data->live_values[$key . '_message'], null, null, $model->id);
//                        }
//                    }
//                }
//            }
//
//            if ($model->isDirty('serviceman_id')) {
//                if (isset($booking_notification_status) && $booking_notification_status['push_notification_booking']) {
//                    if (isset($model->serviceman->user)) {
//                        device_notification($model->serviceman->user->fcm_token, translate('New_booking'), null, null, $model->id);
//                    }
//                }
//            }
//        });
//
//        self::updated(function ($model) {
//            // ... code here
//        });
//
//        self::deleting(function ($model) {
//            // ... code here
//        });
//
//        self::deleted(function ($model) {
//            // ... code here
//        });
//    }
//
    protected static function newFactory()
    {
        return \Modules\OrderpoolManagement\Database\factories\OrderFactory::new();
    }
}
