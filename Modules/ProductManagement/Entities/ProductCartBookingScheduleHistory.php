<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserManagement\Entities\User;

class ProductCartBookingScheduleHistory extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
