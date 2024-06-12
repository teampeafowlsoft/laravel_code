<?php

use Illuminate\Support\Facades\DB;
use Modules\CartModule\Entities\Cart;
use Modules\CartModule\Entities\CartServiceInfo;
use Modules\OrderpoolManagement\Entities\Order;
use Modules\ProductManagement\Http\Controllers\Api\V1\Customer\ProductController;

if (!function_exists('cart_items')) {
    function cart_items($user_id)
    {
        return Cart::where(['customer_id' => $user_id])->get();
    }
}

if (!function_exists('product_cart_items')) {
    function product_cart_items($user_id)
    {
        return ProductController::cart_product_total($user_id);
    }
}

if (!function_exists('cart_total')) {
    function cart_total($user_id): float
    {
        return (cart_items($user_id))->sum('total_cost');
    }
}

if (!function_exists('cart_product_total')) {
    function cart_product_total($user_id): float
    {
        return (product_cart_items($user_id));
    }
}



/*if (!function_exists('cart_data')) {
    function cart_data($user_id)
    {
        $cart = cart_items($user_id);
        $cart_total = $cart->sum('total_cost');
        $cart_total_tax = $cart->sum('tax_amount');
        $cart_total_discount = $cart->sum('discount_amount');
        $cart_total_campaign_discount = $cart->sum('campaign_discount');
        $cart_total_coupon_discount = $cart->sum('coupon_discount');

        return [
            'cart' => $cart,
            'cart_total' => $cart_total,
            'cart_total_tax' => $cart_total_tax,
            'cart_total_discount' => $cart_total_discount,
            'cart_total_campaign_discount' => $cart_total_campaign_discount,
            'cart_total_coupon_discount' => $cart_total_coupon_discount,
        ];
    }
}*/

if (!function_exists('cart_clean')) {
    function cart_clean($user_id)
    {
        Cart::where(['customer_id' => $user_id])->delete();
        return [
            'flag' => 'success'
        ];
    }
}

if (!function_exists('product_cart_clean')) {
    function product_cart_clean($user_id)
    {
        Order::where(['user_id' => $user_id])->delete();
        return [
            'flag' => 'success'
        ];
    }
}
