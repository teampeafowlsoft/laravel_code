<?php

namespace Modules\CategoryManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maincategories extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'position' => 'integer',
        'is_active' => 'integer',
    ];

    protected $fillable = [];

    public function scopeOfStatus($query, $status)
    {
        $query->where('is_active', '=', $status);
    }

    public function scopeOfType($query, $type)
    {
        $value = ($type == 'main') ? 1 : 2;
        $query->where(['position' => $value]);
    }

    protected static function newFactory()
    {
        return \Modules\CategoryManagement\Database\factories\MaincategoriesFactory::new();
    }
}
