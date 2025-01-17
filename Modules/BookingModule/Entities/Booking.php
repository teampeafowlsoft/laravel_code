<?php

namespace Modules\BookingModule\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\BookingModule\Events\BookingRequested;
use Modules\BookingModule\Http\Traits\BookingTrait;
use Modules\CartModule\Entities\CartServiceImages;
use Modules\CartModule\Entities\CartServicePdf;
use Modules\CartModule\Entities\CartServiceVideos;
use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAddress;
use Modules\ZoneManagement\Entities\Zone;

class Booking extends Model
{
    use HasFactory;
    use HasUuid;
    use BookingTrait;

    protected $casts = [
        'readable_id' => 'integer',
        'is_paid' => 'integer',
        'total_booking_amount' => 'float',
        'total_booking_unpaid' => 'float',
        'total_tax_amount' => 'float',
        'total_discount_amount' => 'float',
        'total_campaign_discount_amount' => 'float',
        'total_coupon_discount_amount' => 'float',
        'is_checked' => 'integer',
    ];

    protected $fillable = [
        'id',
        'readable_id',
        'customer_id',
        'provider_id',
        'zone_id',
        'booking_status',
        'is_paid',
        'payment_method',
        'transaction_id',
        'total_booking_amount',
        'total_booking_unpaid',
        'total_tax_amount',
        'total_discount_amount',
        'service_schedule',
        'service_address_id',
        'created_at',
        'updated_at',
        'category_id',
        'sub_category_id',
        'serviceman_id',
        'total_campaign_discount_amount',
        'total_coupon_discount_amount',
        'coupon_code',
        'is_checked',
    ];

    public function scopeOfBookingStatus($query, $status)
    {
        $query->where('booking_status', '=', $status);
    }

    public function service_address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'service_address_id');
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function serviceman(): BelongsTo
    {
        return $this->belongsTo(Serviceman::class, 'serviceman_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function booking_details_amounts(): hasOne
    {
        return $this->hasOne(BookingDetailsAmount::class);
    }

    public function details_amounts(): hasMany
    {
        return $this->hasMany(BookingDetailsAmount::class);
    }

    public function schedule_histories(): HasMany
    {
        return $this->hasMany(BookingScheduleHistory::class);
    }

    public function status_histories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }
    // New
    public function status_histories_list(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class)
            /*->where('changed_by','441af01d-831e-4c3e-8088-3f3edd94213d')
            ->where('booking_id', '1494f146-8112-4018-ab24-dba5afab295e')
            ->where('booking_status','=','decline')*/;
    }
    // Close
    public function cart_images(): HasMany
    {
        return $this->hasMany(CartServiceImages::class);
    }

    public function cart_videos(): HasMany
    {
        return $this->hasMany(CartServiceVideos::class);
    }

    public function cart_pdf(): HasMany
    {
        return $this->hasMany(CartServicePdf::class);
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->readable_id = $model->count() + 100000;
        });

        self::created(function ($model) {
            place_booking_transaction($model);
        });

        self::updating(function ($model) {
            $booking_notification_status = business_config('booking', 'notification_settings')->live_values;

            if ($model->isDirty('booking_status')) {
                $key = null;
                if ($model->booking_status == 'pending') {
                    $key = 'booking_place';
                } elseif ($model->booking_status == 'ongoing') {
                    $key = 'booking_ongoing';
                } elseif ($model->booking_status == 'accepted') {
                    $key = 'booking_accepted';
                } elseif ($model->booking_status == 'completed') {
                    //updating payment status
                    $model->is_paid = 1;
                    $key = 'booking_service_complete';

                    //update admin commission (booking_details_amounts table)
                    $model->update_admin_commission($model, $model->total_booking_amount, $model->provider_id);

                    //transaction for booking
                    if ($model->payment_method == 'cash_after_service') {
                        cash_after_service_booking_transaction($model);
                    } else {
                        digital_payment_booking_transaction_without_item($model);
                    }

                } elseif ($model->booking_status == 'canceled') {
                    $key = 'booking_cancel';
                } elseif ($model->booking_status == 'refund_request') {
                    $key = 'booking_refund';
                }
                $data = business_config($key, 'notification_messages');
                if (isset($data) && $data->is_active) {
                    if (isset($booking_notification_status) && $booking_notification_status['push_notification_booking']) {
                        if (isset($model->customer)) {
                            device_notification($model->customer->fcm_token, $data->live_values[$key . '_message'], null, null, $model->id, 'booking');
                        }
                        if (isset($model->provider->owner)) {
                            device_notification($model->provider->owner->fcm_token, $data->live_values[$key . '_message'], null, null, $model->id, 'booking');
                        }
                        if (isset($model->serviceman->user)) {
                            device_notification($model->serviceman->user->fcm_token, $data->live_values[$key . '_message'], null, null, $model->id, 'booking');
                        }
                    }
                }
            }

            if ($model->isDirty('serviceman_id')) {
                if (isset($booking_notification_status) && $booking_notification_status['push_notification_booking']) {
                    if (isset($model->serviceman->user)) {
                        device_notification($model->serviceman->user->fcm_token, translate('New_booking'), null, null, $model->id, 'booking');
                    }
                }
            }
        });

        self::updated(function ($model) {
            // ... code here
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            // ... code here
        });
    }
}
