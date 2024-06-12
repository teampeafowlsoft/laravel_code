<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Productsubcategory extends Model
{
    use HasFactory;
    protected $table = 'product_subcategory';
    protected $fillable = ['product_id', 'group_id', 'subcategory_id', 'created_at', 'updated_at'];

}
