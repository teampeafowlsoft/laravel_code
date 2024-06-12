<?php

namespace Modules\AttributeManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
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
