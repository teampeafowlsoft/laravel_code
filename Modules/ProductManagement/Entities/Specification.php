<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Specification extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'group_id', 'specification_type', 'specification_name','specification_status'];
    protected $table = 'product_specification';
//    protected static function newFactory()
//    {
//        return \Modules\ProductManagement\Database\factories\SpecificationFactory::new();
//    }
}
