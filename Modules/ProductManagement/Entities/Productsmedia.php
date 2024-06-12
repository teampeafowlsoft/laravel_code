<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Productsmedia extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'group_id', 'other_images', 'created_at', 'updated_at'];
    protected $table = 'products_media';
}
