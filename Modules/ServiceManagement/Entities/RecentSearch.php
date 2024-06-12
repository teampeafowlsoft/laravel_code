<?php

namespace Modules\ServiceManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecentSearch extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $casts = [];

    protected $fillable = [
        'user_id',
        'keyword',
    ];


    protected static function newFactory()
    {
        return \Modules\ServiceManagement\Database\factories\RecentSearchFactory::new();
    }
}
