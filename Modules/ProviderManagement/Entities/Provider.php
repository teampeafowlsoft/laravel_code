<?php

namespace Modules\ProviderManagement\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Events\BookingRequested;
use Modules\ProductManagement\Entities\ProductCartBooking;
use Modules\ReviewModule\Entities\Review;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;
use App\Traits\HasUuid;
use Modules\ZoneManagement\Entities\Zone;

class Provider extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'order_count' => 'integer',
        'service_man_count' => 'integer',
        'service_capacity_per_day' => 'integer',
        'rating_count' => 'integer',
        'avg_rating' => 'float',
        'commission_status' => 'integer',
        'commission_percentage' => 'float',
        'is_active' => 'integer',
        'is_approved' => 'integer',
    ];

    protected $fillable = [];

    protected $hidden = [];

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

    public function scopeOfApproval($query, $status)
    {
        $query->where('is_approved', '=', $status);
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->where('user_type', 'provider-admin');
    }

    public function zone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function zones(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'zones');
    }

    public function bank_detail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BankDetail::class, 'provider_id');
    }

    public function bookings($booking_status = null): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        if ($booking_status == null) {
            return $this->hasMany(Booking::class, 'provider_id');
        }

        return $this->hasMany(Booking::class, 'provider_id')->where('booking_status', $booking_status);
    }

    public function order_booking($booking_status = null): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        if ($booking_status == null) {
            return $this->hasMany(ProductCartBooking::class, 'provider_id');
        }

        return $this->hasMany(ProductCartBooking::class, 'provider_id')->where('booking_status', $booking_status);
    }

    public function bookings_completed($booking_status = 'completed'): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        if ($booking_status == 'completed') {
//            return $this->hasMany(Booking::class, 'provider_id');
            return $this->hasMany(Booking::class, 'provider_id')->where('booking_status', $booking_status);
        }

        return $this->hasMany(Booking::class, 'provider_id');
    }

    public function subscribed_services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubscribedService::class, 'provider_id')
            ->where('is_subscribed', 1);
    }

    public function servicemen(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Serviceman::class, 'provider_id');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class, 'provider_id', 'id');
    }

    protected static function booted()
    {
        static::addGlobalScope('zone_wise_data', function (Builder $builder) {
            if (request()->is('api/*/customer?*') || request()->is('api/*/customer/*')) {
                $builder->whereHas('zone', function ($query) {
                    $query->where('zone_id', Config::get('zone_id'));
                });
            }
        });
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // ... code here
        });

        self::created(function ($model) {
            // ... code here
        });

        self::updating(function ($model) {
            if ($model->isDirty('zone_id')) {
                $except_categories = DB::table('category_zone')->where(['zone_id' => $model->zone_id])->pluck('category_id')->toArray();
                DB::table('subscribed_services')->where(['provider_id' => $model->id])
                    ->whereNotIn('category_id', $except_categories)
                    ->update([
                        'is_subscribed' => 0
                    ]);
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
