<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'group_id', 'features_name', 'features_status'];
    protected $table = 'product_features';
//    protected static function newFactory()
//    {
//        return \Modules\ProductManagement\Database\factories\FeatureFactory::new();
//    }
}
