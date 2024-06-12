<?php

namespace Modules\CartModule\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CategoryManagement\Entities\Category;
use Modules\ServiceManagement\Entities\Service;
use Modules\UserManagement\Entities\User;

class Cart extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'service_cost' => 'float',
        'quantity' => 'integer',
        'discount_amount' => 'float',
        'coupon_discount' => 'float',
        'campaign_discount' => 'float',
        'tax_amount' => 'float',
        'total_cost' => 'float',
    ];

    protected $fillable = [];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->withoutGlobalScopes();
    }

    public function sub_category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id')->withoutGlobalScopes();
    }

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id')->withoutGlobalScopes();
    }
}
