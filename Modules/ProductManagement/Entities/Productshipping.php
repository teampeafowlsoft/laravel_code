<?php

namespace Modules\ProductManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Productshipping extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'group_id', 'zone_id', 'delivery_charge', 'created_at', 'updated_at'];
    protected $table = 'product_shipping';
}
