<?php

namespace Modules\CountryManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;

class Country extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'is_active' => 'integer',
    ];

    protected $fillable = [];

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

}
