<?php

namespace Modules\PromotionManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CartModule\Entities\Cart;
use Modules\PromotionManagement\Entities\Coupon;
use Modules\PromotionManagement\Entities\Discount;
use Modules\PromotionManagement\Entities\DiscountType;
use Modules\ServiceManagement\Entities\Service;
use Modules\ProductManagement\Entities\Productcoupon;
use Modules\ProductManagement\Entities\Product;

class CouponController extends Controller
{
    protected $discount, $coupon, $discountType, $cart, $service, $productCoupon, $products;

    public function __construct(Coupon $coupon, Discount $discount, DiscountType $discountType, Cart $cart, Service $service, Productcoupon $productCoupon, Product $products)
    {
        $this->discount = $discount;
        $this->discountQuery = $discount->ofPromotionTypes('coupon');
        $this->coupon = $coupon;
        $this->discountType = $discountType;
        $this->cart = $cart;
        $this->service = $service;
        $this->productCoupon = $productCoupon;
        $this->products = $products;
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'method' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        if($request['method'] == 'service'){

            $active_coupons = $this->coupon->with(['discount'])
                ->when(!is_null($request->status), function ($query) use($request) {
                    $query->ofStatus(1);
                })
                ->whereHas('discount', function ($query) {
                    $query->where(['promotion_type' => 'coupon'])
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now())
                        ->where('is_active', 1);
                })
                ->whereHas('discount.discount_types', function ($query) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                })
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            $expired_coupons = $this->coupon->with(['discount'])
                ->when(!is_null($request->status), function ($query) use($request) {
                    $query->ofStatus(1);
                })
                ->whereHas('discount', function ($query) {
                    $query->where(['promotion_type' => 'coupon'])
                        ->whereDate('end_date', '<', now())
                        ->where('is_active', 1);
                })
                ->whereHas('discount.discount_types', function ($query) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                })
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            return response()->json(response_formatter(DEFAULT_200, ['active_coupons' => $active_coupons, 'expired_coupons' => $expired_coupons]), 200);
        }else{
            $active_coupons = $this->productCoupon->with(['discount'])
                ->when(!is_null($request->status), function ($query) use($request) {
                    $query->ofStatus(1);
                })
                ->whereHas('discount', function ($query) {
                    $query->where(['promotion_type' => 'coupon'])
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now())
                        ->where('is_active', 1);
                })
                ->whereHas('discount.discount_types', function ($query) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                })
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            $expired_coupons = $this->productCoupon->with(['discount'])
                ->when(!is_null($request->status), function ($query) use($request) {
                    $query->ofStatus(1);
                })
                ->whereHas('discount', function ($query) {
                    $query->where(['promotion_type' => 'coupon'])
                        ->whereDate('end_date', '<', now())
                        ->where('is_active', 1);
                })
                ->whereHas('discount.discount_types', function ($query) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                })
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            return response()->json(response_formatter(DEFAULT_200, ['active_coupons' => $active_coupons, 'expired_coupons' => $expired_coupons]), 200);
        }


    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function apply_coupon(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required',
            'method' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        if($request['method'] == 'service'){
            $cart_items = $this->cart->where(['customer_id' => $request->user()->id])->get();
            $type_wise_id = [];
            $serviceId = '';
            $categoryId = '';
            foreach ($cart_items as $item) {
//                $type_wise_id[] = $item['service_id'];
//                $type_wise_id[] = $item['category_id'];

                $service_find = DB::table('services')->where('id',$item->service_id)->first();
                if($service_find->lang_id == 1){
                    $serviceId = $service_find->id;
                }else{
                    $service_Arb = DB::table('services')->where('group_id',$service_find->group_id)->where('lang_id',1)->first();
                    $serviceId = $service_Arb->id;
                }

                $catgory_find = DB::table('categories')->where('id',$item->category_id)->first();

                if($catgory_find->lang_id == 1){
                    $categoryId = $catgory_find->id;
                }else{
                    $catgory_Arb = DB::table('categories')->where('group_id',$catgory_find->group_id)->where(['lang_id'=>1,'position'=>1])->first();
                    $categoryId = $catgory_Arb->id;

                }

                $type_wise_id[] = $serviceId;
                $type_wise_id[] = $categoryId;
            }

            $coupon = $this->coupon->where(['coupon_code' => $request['coupon_code']])
                ->whereHas('discount', function ($query) {
                    $query->where(['promotion_type' => 'coupon'])
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now())
                        ->where('is_active', 1);
                })->whereHas('discount.discount_types', function ($query) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                })->with('discount.discount_types', function ($query) use ($type_wise_id) {
                    $query->whereIn('type_wise_id', array_unique($type_wise_id));
                })->latest()->first();

            $discounted_ids = [];
            if (isset($coupon) && isset($coupon->discount) && $coupon->discount->discount_types->count() > 0) {
                $discounted_ids = $coupon->discount->discount_types->pluck('type_wise_id')->toArray();
            }
            //dd(in_array($serviceId, $discounted_ids),in_array($categoryId, $discounted_ids));
            $applied = 0;
            if (isset($coupon)) {
                foreach ($cart_items as $item) {
                    //if (in_array($item->service_id, $discounted_ids) || in_array($item->category_id, $discounted_ids)) {
                    if (in_array($serviceId, $discounted_ids) || in_array($categoryId, $discounted_ids)) {

                        $cart_item = $this->cart->where('id', $item['id'])->first();

                        $serviceId_Dis = "";
                        $service_find = DB::table('services')->where('id',$cart_item['service_id'])->first();
                        if($service_find->lang_id == 1){
                            $serviceId_Dis = $service_find->id;
                        }else{
                            $service_Arb = DB::table('services')->where('group_id',$service_find->group_id)->where('lang_id',1)->first();
                            $serviceId_Dis = $service_Arb->id;
                        }

                        $service = $this->service->find($serviceId_Dis);

                        $coupon_discount_amount = booking_discount_calculator($coupon->discount, $cart_item->service_cost * $cart_item['quantity']);

                        $basic_discount = $cart_item->discount_amount;
                        $campaign_discount = $cart_item->campaign_discount;
                        $subtotal = round($cart_item->service_cost * $cart_item['quantity'], 2);

                        $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;
                        $tax = round(( (($cart_item->service_cost - $applicable_discount - $coupon_discount_amount)*$service['tax'])/100 ) * $cart_item['quantity'], 2);

                        $cart_item->coupon_discount = $coupon_discount_amount;
                        $cart_item->coupon_code = $coupon->coupon_code;

                        $cart_item->tax_amount = $tax;

                        $cart_item->total_cost = round($subtotal - $applicable_discount - $coupon_discount_amount + $tax, 2);
                        $cart_item->save();
                        $applied = 1;
                    }
                }
                if ($applied) {
                    return response()->json(response_formatter(DEFAULT_200), 200);
                }
                return response()->json(response_formatter(COUPON_NOT_VALID_FOR_CART), 200);
            }

            return response()->json(response_formatter(DEFAULT_404), 200);
        }else{
            $cart_items = DB::table('order_items as oi')
                ->join('product_variant as pv', 'oi.product_variant_id', '=', 'pv.id')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->where('user_id',$request->user()->id)->get();

            $type_wise_id = [];
            foreach ($cart_items as $item) {
                $productId = "";
                $categoryId = "";
                $product_find = DB::table('products')->where('id',$item->product_id)->first();
                if($product_find->lang_id == 1){
                    $productId = $product_find->id;
                }else{
                    $product_Arb = DB::table('products')->where('group_id',$product_find->group_id)->where('lang_id',1)->first();
                    $productId = $product_Arb->id;
                }

                $catgory_find = DB::table('productcategories')->where('id',$item->category_id)->first();
                if($catgory_find->lang_id == 1){
                    $categoryId = $catgory_find->id;
                }else{
                    $catgory_Arb = DB::table('productcategories')->where('group_id',$catgory_find->group_id)->where(['lang_id'=>1,'position'=>1])->first();
                    $categoryId = $catgory_Arb->id;

                }

                $type_wise_id[] = $productId;
                $type_wise_id[] = $categoryId;
            }


            $coupon = $this->productCoupon->where(['coupon_code' => $request['coupon_code']])
                ->whereHas('discount', function ($query) {
                    $query->where(['promotion_type' => 'coupon'])
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now())
                        ->where('is_active', 1);
                })->whereHas('discount.discount_types', function ($query) {
                    $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
                })->with('discount.discount_types', function ($query) use ($type_wise_id) {
                    $query->whereIn('type_wise_id', array_unique($type_wise_id));
                })->latest()->first();

            $discounted_ids = [];
            if (isset($coupon) && isset($coupon->discount) && $coupon->discount->discount_types->count() > 0) {
                $discounted_ids = $coupon->discount->discount_types->pluck('type_wise_id')->toArray();
            }
            $applied = 0;
            if (isset($coupon)) {
                foreach ($cart_items as $item) {

                    $productId = "";
                    $categoryId = "";
                    $product_find = DB::table('products')->where('id',$item->product_id)->first();
                    if($product_find->lang_id == 1){
                        $productId = $product_find->id;
                    }else{
                        $product_Arb = DB::table('products')->where('group_id',$product_find->group_id)->where('lang_id',1)->first();
                        $productId = $product_Arb->id;
                    }

                    $catgory_find = DB::table('productcategories')->where('id',$item->category_id)->first();
                    if($catgory_find->lang_id == 1){
                        $categoryId = $catgory_find->id;
                    }else{
                        $catgory_Arb = DB::table('productcategories')->where('group_id',$catgory_find->group_id)->where(['lang_id'=>1,'position'=>1])->first();
                        $categoryId = $catgory_Arb->id;

                    }

                    if (in_array($productId, $discounted_ids) || in_array($categoryId, $discounted_ids)) {
                        $cart_item = DB::table('order_items')->where('order_no', $item->order_no)->first();
//                        echo "<pre>";
//                        print_r($cart_item);
//                        exit();
                        $service = $this->products->find($cart_item->product_variant_id);
                        $coupon_discount_amount = booking_discount_calculator($coupon->discount, $cart_item->discounted_price * $cart_item->quantity);

                        $basic_discount = $cart_item->discount;
                        $campaign_discount = $cart_item->campaign_discount;
                        $subtotal = round($cart_item->discounted_price * $cart_item->quantity, 2);

                        $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;
                        //$tax = round(( (($cart_item->discount_price - $applicable_discount - $coupon_discount_amount)*$service['tax'])/100 ) * $cart_item->quantity, 2);
                        $tax = round(0.00);

//                        $cart_item->coupon_discount = $coupon_discount_amount;
//                        $cart_item->coupon_code = $coupon->coupon_code;
//
//                        $cart_item->tax_amount = $tax;
//
//                        $cart_item->sub_total = round($subtotal - $applicable_discount - $coupon_discount_amount + $tax, 2);
//                        $cart_item->save();
                        DB::table('order_items')
                            ->where('id', $cart_item->id)
                            ->update([
                                'coupon_discount' => $coupon_discount_amount,
                                'coupon_code' => $coupon->coupon_code,
                                'tax_amount' => $tax,
                                'sub_total' => round($subtotal - $applicable_discount - $coupon_discount_amount + $tax + $cart_item->shipping_charge + $cart_item->delivery_charge, 2),
                            ]);
//                        $cart_item = DB::table('order_items')->where('order_no', $item->order_no)->update(array($cart_item));
                        $applied = 1;

                    }
                }
                if ($applied) {
                    return response()->json(response_formatter(DEFAULT_200), 200);
                }
                return response()->json(response_formatter(COUPON_NOT_VALID_FOR_CART), 200);
            }

            return response()->json(response_formatter(DEFAULT_404), 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_coupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        if($request['method'] == 'service'){

            $cart_items = $this->cart->where('customer_id', $request->user()->id)->get();
            if (!isset($cart_items)) {
                return response()->json(response_formatter(DEFAULT_204), 200);
            }

            foreach ($cart_items as $cart) {

                $service = DB::table('services')->where('id',$cart->service_id)->first();
                $basic_discount = $cart->discount_amount;
                $campaign_discount = $cart->campaign_discount;
                $subtotal = round($cart->service_cost * $cart['quantity'], 2);
                $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;

                $tax = round(( (($cart->service_cost - $applicable_discount)*$service->tax)/100 ) * $cart['quantity'], 2);

                //updated values
                $cart->tax_amount = $tax;
                $cart->total_cost = round($subtotal - $applicable_discount + $tax, 2);
                $cart->coupon_discount = 0;
                $cart->coupon_code = null;
                $cart->save();
            }

            return response()->json(response_formatter(DEFAULT_200), 200);
        }else{
            $cart_items = DB::table('order_items as oi')
                ->select('oi.*')
                ->join('product_variant as pv', 'oi.product_variant_id', '=', 'pv.id')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->where('user_id',$request->user()->id)->get();
            if (!isset($cart_items)) {
                return response()->json(response_formatter(DEFAULT_204), 200);
            }

            foreach ($cart_items as $cart) {

                $service = DB::table('products')->where('id',$cart->id)->first();

                $basic_discount = $cart->discount;
                $campaign_discount = $cart->campaign_discount;
                $subtotal = round($cart->discounted_price * $cart->quantity, 2);
                $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;
//                $tax = round(( (($cart->service_cost - $applicable_discount)*$service->tax)/100 ) * $cart['quantity'], 2);
                $tax = round(0.00);

                //updated values
                DB::table('order_items')
                    ->where('id', $cart->id)
                    ->update([
                        'coupon_discount' => 0,
                        'coupon_code' => null,
                        'tax_amount' => $tax,
                        'sub_total' => round($subtotal - $applicable_discount + $tax, 2),
                    ]);
            }

            return response()->json(response_formatter(DEFAULT_200), 200);
        }
    }

}
