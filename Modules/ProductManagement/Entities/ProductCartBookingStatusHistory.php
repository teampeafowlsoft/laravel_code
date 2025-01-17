<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserManagement\Entities\User;

class ProductCartBookingStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    protected static function newFactory()
    {
        return \Modules\ProductManagement\Database\factories\ProductCartBookingStatusHistoryFactory::new();
    }
}
