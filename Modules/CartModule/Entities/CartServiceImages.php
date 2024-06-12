<?php

namespace Modules\CartModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartServiceImages extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'booking_id', 'service_image', 'cart_clean_status'];

//    protected static function newFactory()
//    {
//        return \Modules\CartModule\Database\factories\CartServiceImagesFactory::new();
//    }
}
