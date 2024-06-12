<?php

namespace Modules\ProductManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
//use Modules\ReviewModule\Entities\Review;
use Modules\BusinessSettingsModule\Entities\BusinessSettings;
use Modules\CategoryManagement\Entities\Maincategories;
use Modules\ProductManagement\Entities\ProductCartBooking;
use Modules\ProductManagement\Entities\ProductCartBookingDetail;
use Modules\ProductManagement\Entities\ProductCartBookingDetailsAmount;
use Modules\ProductManagement\Entities\ProductCartBookingScheduleHistory;
use Modules\ProductManagement\Entities\ProductCartBookingStatusHistory;
use Modules\ProductManagement\Entities\Productshipping;
use Modules\ProductManagement\Events\ProductCartBookingRequested;
use Modules\CartModule\Entities\Cart;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Product;
use Modules\ProductManagement\Entities\Productvariant;
use Modules\OrderpoolManagement\Entities\Order;
use Modules\ServiceManagement\Entities\RecentSearch;
use Modules\ServiceManagement\Entities\Service;
use Modules\ZoneManagement\Entities\Zone;
use mysql_xdevapi\Exception;
use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
{
    private $product;
    private $productcategory;
    private $productvariant;
    private $productShipping;
    private $order;
    private $ProductCartBooking;
    private $ProductCartBookingStatusHistory;

    private $main_category;

    private $recent_search;
    private $zones;
    // private Review $review;


    public function __construct(Product $product,Productcategory $productcategory, Productvariant $productvariant, Order $order, ProductCartBooking $ProductCartBooking, ProductCartBookingStatusHistory $ProductCartBookingStatusHistory,Productshipping $productShipping, RecentSearch $recent_search,Zone $zones, Maincategories $main_category)
    {
        $this->product = $product;
        $this->productcategory = $productcategory;
        $this->productvariant = $productvariant;
        $this->order = $order;
        $this->ProductCartBooking = $ProductCartBooking;
        $this->ProductCartBookingStatusHistory = $ProductCartBookingStatusHistory;
        $this->productShipping = $productShipping;
        $this->recent_search = $recent_search;
        $this->zones = $zones;
        $this->main_category = $main_category;
        // $this->review = $review;
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $products = $this->product->with(['variations'])
            ->ofStatus(1)
            ->where('lang_id', $lang_id)
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $products), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param string $main_category_id
     * @return JsonResponse
     */
    public function product_by_maincategory(Request $request, string $main_category_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        //First Get ID Product ID from main category table:
        $main_category = $this->main_category->where('id',$main_category_id)
            ->where('lang_id', $lang_id)
            ->where('is_active', '1')
            ->first();

        if (!empty($main_category)){

            $products = $this->product->with(['variations','provider'])
                ->whereHas('provider', function ($query) {
                    $query->where('is_active', 1);
                })
                ->ofStatus(1)
                ->where('category_id', $main_category->product_id)
                ->groupBy('group_id')
                ->latest()
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            return response()->json(response_formatter(DEFAULT_200, $products), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);

    }



    public function company_wise(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|uuid',
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $products = $this->product->with(['variations'])
            ->ofStatus(1)
            ->where('vendor', $request['company_id'])
            ->where('lang_id', $lang_id)
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $products), 200);
    }

//    public function search(Request $request): JsonResponse
//    {
//        $validator = Validator::make($request->all(), [
//            'limit' => 'required|numeric|min:1|max:200',
//            'offset' => 'required|numeric|min:1|max:100000',
//            'string' => 'required'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
//        }
//
//        $auth_user = auth('api')->user();
//        if ($auth_user) {
//            $this->recent_search->Create(['user_id' => $auth_user->id, 'keyword' => base64_decode($request['string'])]);
//        }
//
//        //dd(base64_decode($request['string']));
//        $keys = explode(' ', base64_decode($request['string']));
//        try {
//            $services = $this->product->with(['category.zonesBasicInfo', 'variations'])
//                ->where(function ($query) use ($keys) {
//                    foreach ($keys as $key) {
//                        $query->orWhere('name', 'LIKE', '%' . $key . '%');
//                    }
//                })
//                ->active()
////                ->get()
//                ->latest()
//                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
//        } catch (Exception $ex){
//            echo $ex;
//        }
//
////        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
//        return response()->json(response_formatter(DEFAULT_200, $services), 200);
//    }

// New Code
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'string' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $auth_user = auth('api')->user();
        if ($auth_user) {
            $this->recent_search->Create(['user_id' => $auth_user->id, 'keyword' => base64_decode($request['string'])]);
        }

        $keys = explode(' ', base64_decode($request['string']));
        $services = $this->product->withoutGlobalScopes()->with(['category.zonesBasicInfo', 'variations','provider'])
            ->where(function ($query) use ($keys) {
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->where('is_active',1)
            ->where('lang_id',$lang_id)
            ->whereHas('provider', function ($query) {
                $query->where('is_active',1);
            })
//            ->active()
//            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }

    private function variation_mapper($services)
    {
        $services->map(function ($service) {
            $service['variations_app_format'] = self::variations_app_format($service);
            return $service;
        });
        return $services;
    }

    private function variations_app_format($service): array
    {
        $formatting = [];
        $filtered = $service['variations']->where('zone_id', Config::get('zone_id'));
        $formatting['zone_id'] = Config::get('zone_id');
        $formatting['default_price'] = $filtered->first() ? $filtered->first()->price : 0;
        foreach ($filtered as $data) {
            $formatting['zone_wise_variations'][] = [
                'variant_key' => $data['variant_key'],
                'variant_name' => $data['variant'],
                'price' => $data['price']
            ];
        }
        return $formatting;
    }
// Close New Code

    public function popular(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $productCartDetails = ProductCartBookingDetail::count();

        $query = Product::leftJoin('productcategories', 'productcategories.id', '=', 'products.category_id')
            ->select('products.*','productcategories.name as catNm')
            ->where('products.is_active', 1)
            ->where('products.lang_id', $lang_id);

            if($productCartDetails != 0){
                $query->rightJoin('product_cart_booking_details', 'product_cart_booking_details.product_id', '=', 'products.id');
            }

        $products = $query->with(['variations'])
            ->join('providers', function($join){
                $join->on('providers.id', '=', 'products.vendor')
                    ->where('providers.is_active', '=', 1);
            })
            ->groupBy('group_id')
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');


//        $products = Product::leftJoin('productcategories', 'productcategories.id', '=', 'products.category_id')
//            ->rightJoin('product_variant', 'product_variant.product_id', '=', 'products.id')
//            ->rightJoin('order_items', 'order_items.product_variant_id', '=', 'product_variant.id')
//            ->select('products.*', 'product_variant.*', 'productcategories.name as catNm', DB::raw('COUNT(order_items.product_variant_id) as count_order'))
//            ->where('products.is_active', 1)
//            ->groupBy('products.id') // Add groupBy clause to prevent duplicate rows
//            ->orderBy('count_order', 'DESC')
//            ->paginate($request->input('limit', 10), ['*'], 'offset', $request->input('offset', 0))
//            ->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $products), 200);
    }

    public function recommended(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

//        $products = $this->product
//            ->with(['variations'])
////            ->leftjoin('productcategories','productcategories.id','=','products.category_id')
//            ->orderBy('avg_rating', 'DESC')
//            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        $products = Product::leftJoin('productcategories', 'productcategories.id', '=', 'products.category_id')
            ->select('products.*','productcategories.name as catNm')
            ->where('products.is_active', 1)
            ->where('products.lang_id', $lang_id)
            ->orderBy('products.avg_rating', 'DESC')
            ->with(['variations'])
            ->join('providers', function($join){
                $join->on('providers.id', '=', 'products.vendor')
                    ->where('providers.is_active', '=', 1);
            })
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200,$products), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function search_recommended(Request $request): JsonResponse
    {
        $products = $this->product->select('id', 'name')
            ->ofStatus(1)
            ->inRandomOrder()
            ->take(5)->get();

        return response()->json(response_formatter(DEFAULT_200, $products), 200);
    }

    /**
     * Trending products (Last 30days order based)
     * @param Request $request
     * @return JsonResponse
     */
    public function trending(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $products = $this->product->with(['variations'])
            ->ofStatus(1)
            ->orderBy('avg_rating', 'DESC')
            ->where('created_at', '>', now()->subDays(30)->endOfDay())
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $products), 200);
    }

    /**
     * Recently viewed by customer (service view based)
     * @param Request $request
     * @return JsonResponse
     */
//    public function recently_viewed(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'limit' => 'required|numeric|min:1|max:200',
//            'offset' => 'required|numeric|min:1|max:100000'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
//        }
//
//        $service_ids = $this->recent_view
//            ->where('user_id', $request->user()->id)
//            ->select(
//                DB::raw('count(total_service_view) as total_service_view'),
//                DB::raw('service_id as service_id')
//            )
//            ->groupBy('total_service_view', 'service_id')
//            ->pluck('service_id')
//            ->toArray();
//
//        $products = $this->service->with(['variations'])
//            ->whereIn('id', $service_ids)
//            ->ofStatus(1)
//            ->orderBy('avg_rating', 'DESC')
//            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
//
//        return response()->json(response_formatter(DEFAULT_200, $products), 200);
//    }



    public function product_details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|uuid',
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $products = $this->product->with(['variations','features','specifications','shipping','media','subcategory'])
            ->selectRaw("products.*,productcategories.name as catName,providers.company_name")
            ->join('product_variant', 'product_variant.product_id', '=', 'products.id')
            ->join('productcategories', 'productcategories.id', '=', 'products.category_id')
            ->join('providers', 'providers.id', '=', 'products.vendor')
            ->join('attributes', 'attributes.id', '=', 'product_variant.packate_measurement_attribute_id')
            ->join('attributevalues', 'attributevalues.id', '=', 'product_variant.packate_measurement_attribute_value')
            ->where('products.is_active', 1)
            ->where('products.id', $request['product_id'])
            ->where('products.lang_id', $lang_id)
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $products), 200);
    }

    public function add_to_cart_product(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|uuid',
            'variant_id' => 'required',
            'quantity' => 'required|numeric|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        $qtyCheck = $this->productvariant->where(['id' => $request['variant_id']])->first();

        if($qtyCheck->packate_measurement_qty < $request->quantity){
            return response()->json(response_formatter(PRODUCT_OUT_OF_STOCK_410), 410);
        }

        $zone_id = ($request->header('zoneId')!='') ? ($request->header('zoneId')) : null;

        //New
        if($zone_id){
            $shipping_charges = $this->zones->where('id',$zone_id)->first();
            $zone_shipping_charge =  $shipping_charges->shipping_charge;
        }else{
            $zone_shipping_charge= 0;
        }
        //
        $variation = $this->productvariant->where(['product_id' => $request['product_id']])
            ->where(['id' => $request['variant_id']])
            ->first();
        $Shipping_charge = 0;
        if(!empty($zone_id) && $zone_id != null){
            $Shipping = $this->productShipping
                ->where(['product_id' => $request['product_id']])
                ->where(['zone_id' => $zone_id])
                ->first();
            if(!empty($Shipping)){
                $Shipping_charge = $Shipping->delivery_charge;
            }else{
                $Shipping_charge = 0.0;
            }

        }

        if (isset($variation)) {

        // Check Is Product Is Available Is Card Is user
            $orderItems = DB::table('order_items')->where('user_id',$request->user()->id)->get();
            $setAreaCharge = (count($orderItems)==0)?$zone_shipping_charge:0;

            $productvariant = $this->product->find($request['product_id']);

            $check_cart = $this->order->where([
                'product_variant_id' => $request['variant_id'],
                'user_id' => $request->user()->id])->first();
            $order = $check_cart ?? $this->order;
            $quantity = isset($check_cart) ? $order->quantity + $request['quantity'] : $request['quantity'];

            $price = DB::table('product_variant')->where('id',$request['variant_id'])->get();

            $subtotal = round((($price[0]->packate_measurement_discount_price) * $quantity) + (($Shipping_charge * $quantity) + $setAreaCharge), 2);

            $campaign_discount = product_campaign_discount_calculation($productvariant, $variation->packate_measurement_discount_price * $quantity);

            //DB part
            $string = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

            $order->order_no = $this->get_random_string($string,14);
            $order->order_no_vendor = $this->get_random_string($string,15);
            $order->user_id = $request->user()->id;
            $order->order_id = null;
            $order->product_variant_id = $request['variant_id'];
            $order->quantity = $quantity;
            $order->price = $price[0]->packate_measurement_sell_price;
            $order->discounted_price = $price[0]->packate_measurement_discount_price;
            $order->discount = 0;
            $order->campaign_discount = $campaign_discount;
            $order->tax_amount = 0.00;
            $order->coupon_discount = 0;
            $order->shipping_charge = $setAreaCharge;
            $order->delivery_charge = ($Shipping_charge * $quantity);
            $order->coupon_code = null;
            $order->sub_total = $subtotal;
            $order->status = 'processed';
            $order->active_status = 'active';
            $order->save();

            return response()->json(response_formatter(DEFAULT_STORE_200), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }

    public function cart_product_list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $cart = $this->order->with(['customer', 'productvariant'])
            ->selectRaw("order_items.*,products.name as product_name,products.image")
            ->join('product_variant', 'product_variant.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->where(['user_id' => $request->user()->id])
            ->where('products.lang_id',$lang_id)
            ->latest()->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $cart), 200);
    }

    static public function cart_product_total($user_id)
    {

        $cart_amt = DB::table('order_items')
            ->where('user_id', '=', $user_id)
            //->sum(DB::raw(' + shipping_charge + delivery_charge'));
            ->sum('sub_total');
        if ($cart_amt > 0)
        {
            return $cart_amt;
        } else {
            return 0.00;
        }

    }

    public function update_product_qty(Request $request, string $id): JsonResponse
    {
        echo "Call";
        return response()->json(response_formatter(DEFAULT_UPDATE_200), 200);
    }

    public function remove_cart(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $cart = $this->order->where(['id' => $request['id']])->first();

        if (!isset($cart)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $this->order->where('id', $request['id'])->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }

    public function cart_update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|uuid',
            'quantity' => 'required|numeric|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $cart = $this->order->where(['id' => $request['id']])->first();

        if (!isset($cart)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        //echo "order_no Cart :".$cart['order_no']."<br/>";
        //echo "variant_id Cart :".$cart['product_variant_id']."<br/>";
        //$cart = $this->order->find($request['id']);
        //$productvariant = $this->productvariant->find($cart['product_variant_id']);

        $price = DB::table('product_variant')->join('product_shipping as ps','ps.product_id','=','product_variant.product_id')->where('product_variant.id',$cart['product_variant_id'])->get();

        if($price[0]->packate_measurement_qty < $request->quantity){
            return response()->json(response_formatter(PRODUCT_OUT_OF_STOCK_410), 410);
        }

        $subtotal = round($cart->price * $request['quantity'], 2);

        $cart->quantity = $request->quantity;
        $cart->price = $price[0]->packate_measurement_sell_price;
        $cart->discounted_price = $price[0]->packate_measurement_discount_price;
        $cart->discount = 0;
        $cart->delivery_charge = ($price[0]->delivery_charge * $request['quantity']);
        $cart->sub_total = ($subtotal + $cart->shipping_charge + $cart->delivery_charge);
//        $cart->status = 'processed';
//        $cart->active_status = 'active';
        $cart->save();

        return response()->json(response_formatter(DEFAULT_UPDATE_200), 200);
    }

    public function remove(Request $request, string $id): JsonResponse
    {
        $cart = $this->order->where(['id' => $id])->first();

        if (!isset($cart)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $this->order->where('id', $id)->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }

    public function empty_cart(Request $request): JsonResponse
    {
        $cart = $this->order->where(['user_id' => $request->user()->id]);

        if ($cart->count() == 0) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $cart->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }

    public function place_request(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:' . implode(',', array_column(PAYMENT_METHODS, 'key')),
            'zone_id' => 'required|uuid',
            'service_schedule' => 'required|date',
            'service_address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $this->place_booking_request($request->user()->id, $request, 'cash-payment');

        return response()->json(response_formatter(DEFAULT_200), 200);
    }

    protected function place_booking_request($user_id, $request, $transaction_id)
    {
        $productID = $request->product_id;
        $variantID = $request->variant_id;
        $productQty = $request->quantity;
//        {"product_id":"af54b947-0f7a-4e80-9dac-79594a3e3265","variant_id":172,"quantity":2}

        $cart_data = Order::where(['user_id' => $user_id])->get();

        if ($cart_data->count() == 0) {
            return [
                'flag' => 'failed',
                'message' => 'no data found'
            ];
        }
//        User:5abdef6c-2256-4523-9c70-e9c7ff21925f variant:172

        $order_item = DB::table('order_items')
            ->select('order_items.*' , 'product_variant.product_id','product_subcategory.subcategory_id', 'products.category_id', 'products.vendor')
            ->join('product_variant', 'product_variant.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->leftjoin('product_subcategory', 'product_subcategory.product_id', '=', 'product_variant.product_id')
            ->where('order_items.user_id', $user_id)
            ->get();

        $booking_ids = [];

        foreach ($order_item->pluck('subcategory_id')->unique() as $sub_category) {

            $booking = new ProductCartBooking();

            DB::transaction(function () use ($sub_category, $booking, $transaction_id, $request, $order_item, $user_id) {
                $order_item = $order_item->where('subcategory_id', $sub_category);

                //product_cart_bookings
                $booking->customer_id = $user_id;
                $booking->category_id = $order_item->first()->category_id;
                $booking->sub_category_id = $sub_category;
                $booking->provider_id = $order_item->first()->vendor;
                $booking->provider_selected_ids = $order_item->first()->vendor;
                $booking->zone_id = config('zone_id') == null ? $request['zone_id'] : config('zone_id');
                $booking->booking_status = 'received';
                $booking->is_paid = ($request->has('payment_method') && $request['payment_method'] == 'cash_on_delivery') ? 0 : 1;
                $booking->payment_method = $request['payment_method'];
                $booking->transaction_id = ($request->has('payment_method') && $request['payment_method'] == 'cash_on_delivery') ? 'cash-payment' : $transaction_id;
                $booking->total_booking_amount = $order_item->sum('sub_total');
                $booking->total_tax_amount = $order_item->sum('tax_amount');
                $booking->total_qty = $order_item->sum('quantity');
                $booking->total_shipping_charge = $order_item->sum('shipping_charge');
                $booking->total_delivery_charge = $order_item->sum('delivery_charge');
                $booking->total_discount_amount = $order_item->sum('discounted_price');
                $booking->total_cost_amount = $order_item->sum('price');
                $booking->total_campaign_discount_amount = $order_item->sum('campaign_discount');
                $booking->total_coupon_discount_amount = $order_item->sum('coupon_discount');
                $booking->service_schedule = $request->service_schedule ?? now()->addHours(5);
                $booking->service_address_id = $request->service_address_id ?? '';
                $booking->save();

                foreach ($order_item->all() as $datum) {
                    //product_cart_booking_details
                    $detail = new ProductCartBookingDetail();
                    $detail->product_cart_booking_id = $booking->id;
                    $detail->product_id = $datum->product_id;
                    $detail->product_name = Product::find($datum->product_id)->name ?? 'service-not-found';
                    $detail->product_variant_id = $datum->product_variant_id;
                    $detail->quantity = $datum->quantity;
                    $detail->service_cost = $datum->price;
                    $detail->discount_amount = $datum->discounted_price;
//                    $detail->discount_amount = $datum->discount;
                    $detail->campaign_discount_amount = $datum->campaign_discount;
                    $detail->overall_coupon_discount_amount = $datum->coupon_discount;
                    $detail->tax_amount = $datum->tax_amount;
                    $detail->shipping_charge = $datum->shipping_charge;
                    $detail->delivery_charge = $datum->delivery_charge;
                    $detail->total_cost = $datum->sub_total;
                    $detail->save();

                    //product_cart_booking_details_amount
                    $booking_details_amount = new ProductCartBookingDetailsAmount();
                    $booking_details_amount->booking_details_id = $detail->id;
                    $booking_details_amount->product_cart_booking_id = $booking->id;
                    $booking_details_amount->service_unit_cost = 0.000;
                    $booking_details_amount->service_quantity = $datum->quantity;
                    $booking_details_amount->service_tax = $datum->tax_amount;
                    $booking_details_amount->discount_by_admin = $this->calculate_discount_cost($datum->discounted_price)['admin'];
                    $booking_details_amount->discount_by_provider = $this->calculate_discount_cost($datum->discounted_price)['provider'];
                    $booking_details_amount->campaign_discount_by_admin = $this->calculate_campaign_cost($datum->campaign_discount)['admin'];
                    $booking_details_amount->campaign_discount_by_provider = $this->calculate_campaign_cost($datum->campaign_discount)['provider'];
                    $booking_details_amount->coupon_discount_by_admin = $this->calculate_coupon_cost($datum->coupon_discount)['admin'];
                    $booking_details_amount->coupon_discount_by_provider = $this->calculate_coupon_cost($datum->coupon_discount)['provider'];
                    //admin commission will update after complete the service
                    $booking_details_amount->save();
                }

                //product_cart_booking_schedule_histories
                $schedule = new ProductCartBookingScheduleHistory();
                $schedule->product_cart_booking_id = $booking->id;
                $schedule->changed_by = $user_id;
                $schedule->schedule = $request->service_schedule ?? now()->addHours(5);
                $schedule->save();

                //product_cart_booking_status_histories
//                $status_history = new ProductCartBookingStatusHistory();
//                $status_history->product_cart_booking_id =  $booking->id;
//                $status_history->changed_by = $user_id;
//                $status_history->booking_status = 'received';
//                $status_history->save();
            });
            $booking_ids[] = $booking->id;
        }
//        echo 'User:' . $user_id;
//        echo 'booking:' . $booking;

        product_cart_clean($user_id);
        event(new ProductCartBookingRequested($booking));

        return [
            'flag' => 'success',
            'booking_id' => $booking_ids
        ];
    }

    static public function place_order_request($user_id, $request, $transaction_id)
    {
        $productID = $request->product_id;
        $variantID = $request->variant_id;
        $productQty = $request->quantity;

        $cart_data = Order::where(['user_id' => $user_id])->get();

        if ($cart_data->count() == 0) {
            return [
                'flag' => 'failed',
                'message' => 'no data found'
            ];
        }

        $order_item = DB::table('order_items')
            ->select('order_items.*' , 'product_variant.product_id','product_subcategory.subcategory_id', 'products.category_id', 'products.vendor')
            ->join('product_variant', 'product_variant.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variant.product_id')
            ->leftjoin('product_subcategory', 'product_subcategory.product_id', '=', 'product_variant.product_id')
            ->where('order_items.user_id', $user_id)
//            ->where('order_items.product_variant_id', $variantID)
            ->get();

        $booking_ids = [];

        foreach ($order_item->pluck('subcategory_id')->unique() as $sub_category) {

            $booking = new ProductCartBooking();

            DB::transaction(function () use ($sub_category, $booking, $transaction_id, $request, $order_item, $user_id) {

                $order_item = $order_item->where('subcategory_id', $sub_category);

                //product_cart_bookings
                $booking->customer_id = $user_id;
                $booking->category_id = $order_item->first()->category_id;
                $booking->sub_category_id = $sub_category;
                $booking->provider_id = $order_item->first()->vendor;
                $booking->provider_selected_ids = $order_item->first()->vendor;
                $booking->zone_id = config('zone_id') == null ? $request['zone_id'] : config('zone_id');
                $booking->booking_status = 'received';
                $booking->is_paid = ($request->has('payment_method') && $request['payment_method'] == 'cash_on_delivery') ? 0 : 1;
                $booking->payment_method = $request['payment_method'];
                $booking->transaction_id = ($request->has('payment_method') && $request['payment_method'] == 'cash_on_delivery') ? 'cash-payment' : $transaction_id;
                $booking->total_booking_amount = $order_item->sum('sub_total');
                $booking->total_tax_amount = $order_item->sum('tax_amount');
                $booking->total_qty = $order_item->sum('quantity');
                $booking->total_shipping_charge = $order_item->sum('shipping_charge');
                $booking->total_delivery_charge = $order_item->sum('delivery_charge');
                $booking->total_discount_amount = $order_item->sum('discounted_price');
                $booking->total_cost_amount = $order_item->sum('price');
                $booking->total_campaign_discount_amount = $order_item->sum('campaign_discount');
                $booking->total_coupon_discount_amount = $order_item->sum('coupon_discount');
                $booking->service_schedule = $request->service_schedule ?? now()->addHours(5);
                $booking->service_address_id = $request->service_address_id ?? '';
                $booking->save();

                foreach ($order_item->all() as $datum) {

                    $eng_product_variantid = '';
                    $arb_product_variantid = '';

                    $productVariantData = Productvariant::where('id',$datum->product_variant_id)->first();
                    if(!empty($productVariantData)){
                        $variantData = Productvariant::where('group_id',$productVariantData->group_id)->get();
                        foreach ($variantData as $vd){
                            $product_variant = Productvariant::where('id',$vd->id)->first();
                            $product_variant->packate_measurement_qty = ($product_variant->packate_measurement_qty - $datum->quantity);
                            $product_variant->save();
                        }
                    }

//                    $product_variant = Productvariant::where('id',$datum->product_variant_id)->first();


                    //product_cart_booking_details
                    $detail = new ProductCartBookingDetail();
                    $detail->product_cart_booking_id = $booking->id;
                    $detail->product_id = $datum->product_id;
                    $detail->product_name = Product::find($datum->product_id)->name ?? 'service-not-found';
                    $detail->product_variant_id = $datum->product_variant_id;
                    $detail->quantity = $datum->quantity;
                    $detail->service_cost = $datum->price;
                    $detail->discount_amount = $datum->discounted_price;
//                    $detail->discount_amount = $datum->discount;
                    $detail->campaign_discount_amount = $datum->campaign_discount;
                    $detail->overall_coupon_discount_amount = $datum->coupon_discount;
                    $detail->tax_amount = $datum->tax_amount;
                    $detail->shipping_charge = $datum->shipping_charge;
                    $detail->delivery_charge = $datum->delivery_charge;
                    $detail->total_cost = $datum->sub_total;
                    $detail->save();

                    //product_cart_booking_details_amount
                    $booking_details_amount = new ProductCartBookingDetailsAmount();
                    $booking_details_amount->booking_details_id = $detail->id;
                    $booking_details_amount->product_cart_booking_id = $booking->id;
                    $booking_details_amount->service_unit_cost = 0.000;
                    $booking_details_amount->service_quantity = $datum->quantity;
                    $booking_details_amount->service_tax = $datum->tax_amount;
                    $booking_details_amount->discount_by_admin = ProductController::calculate_product_discount_cost($datum->discounted_price)['admin'];
                    $booking_details_amount->discount_by_provider = ProductController::calculate_product_discount_cost($datum->discounted_price)['provider'];
                    $booking_details_amount->campaign_discount_by_admin = ProductController::calculate_product_campaign_cost($datum->campaign_discount)['admin'];
                    $booking_details_amount->campaign_discount_by_provider = ProductController::calculate_product_campaign_cost($datum->campaign_discount)['provider'];
                    $booking_details_amount->coupon_discount_by_admin = ProductController::calculate_product_coupon_cost($datum->coupon_discount)['admin'];
                    $booking_details_amount->coupon_discount_by_provider = ProductController::calculate_product_coupon_cost($datum->coupon_discount)['provider'];
                    //admin commission will update after complete the service
                    $booking_details_amount->save();
                }

                //product_cart_booking_schedule_histories
                $schedule = new ProductCartBookingScheduleHistory();
                $schedule->product_cart_booking_id = $booking->id;
                $schedule->changed_by = $user_id;
                $schedule->schedule = $request->service_schedule ?? now()->addHours(5);
                $schedule->save();

                //product_cart_booking_status_histories
//                $status_history = new ProductCartBookingStatusHistory();
//                $status_history->product_cart_booking_id =  $booking->id;
//                $status_history->changed_by = $user_id;
//                $status_history->booking_status = 'processed';
//                $status_history->save();
            });
            $booking_ids[] = $booking->id;
        }
//        echo 'User:' . $user_id;
//        echo 'booking:' . $booking;

        product_cart_clean($user_id);
        event(new ProductCartBookingRequested($booking));

        return [
            'flag' => 'success',
            'booking_id' => $booking_ids
        ];
    }
    public function order_item_list(Request $request): JsonResponse
    {
//        $validator = Validator::make($request->all(), [
//            'user_id' => 'required'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
//        }
//
//        $order = $this->order->where(['user_id' => $request['id']])->first();
//
//        if (!isset($order)) {
//            return response()->json(response_formatter(DEFAULT_204), 200);
//        }

        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $order = $this->order->with(['customer', 'productvariant'])
            ->selectRaw("order_items.*,products.name as product_name")
            ->join('product_variant', 'product_variant.id', '=', 'order_items.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variant.product_id')
//            ->where(['user_id' => $request->user()->id])
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $order), 200);
    }

    public function product_booking_list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'booking_status' => 'required|in:all,' . implode(',', array_column(ORDER_STATUSES, 'key')),
            'string' => 'string'
        ]);

        $user = auth()->user()->id;

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
//        echo "customer_id : " . $request->user()->id;
        $bookings = $this->ProductCartBooking->with(['customer'])
            ->where(['customer_id' => $request->user()->id])
            ->when($request->has('string'), function ($query) use ($request) {
                $keys = explode(' ', base64_decode($request['string']));
                foreach ($keys as $key) {
                    $query->orWhere('id', 'LIKE', '%' . $key . '%');
                }
            })
            ->when($request['booking_status'] != 'all', function ($query) use ($request) {
                return $query->ofBookingStatus($request['booking_status']);
            })
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $bookings), 200);

    }

    public function show(Request $request, string $id): JsonResponse
    {
        $booking = $this->ProductCartBooking
            ->where(['customer_id' => $request->user()->id])
            ->with(['detail.service', 'schedule_histories.user', 'status_histories.user','service_address', 'customer', 'provider', 'zone', 'serviceman.user'
            ])
            ->where(['id' => $id])->first();
        if (isset($booking)) {
            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    public function status_update(Request $request, string $order_booking_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_status' => 'required|in:cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->ProductCartBooking->where('id', $order_booking_id)->where('customer_id', $request->user()->id)->first();

        if (isset($booking)) {
            $booking->booking_status = $request['booking_status'];
            $booking_status_history = $this->ProductCartBookingStatusHistory;
            $booking_status_history->product_cart_booking_id = $order_booking_id;
            $booking_status_history->changed_by = $request->user()->id;
            $booking_status_history->booking_status = $request['booking_status'];

            DB::transaction(function () use ($booking_status_history, $booking) {
                $booking->save();
                $booking_status_history->save();
            });

            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    private function calculate_discount_cost(float $discount_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'discount_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if ($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($discount_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($discount_amount * $data['provider_percentage'])/100;
        }
        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    static public function calculate_product_discount_cost(float $discount_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'discount_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if ($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($discount_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($discount_amount * $data['provider_percentage'])/100;
        }
        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    private function calculate_campaign_cost(float $campaign_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'campaign_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($campaign_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($campaign_amount * $data['provider_percentage'])/100;
        }

        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    static public function calculate_product_campaign_cost(float $campaign_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'campaign_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($campaign_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($campaign_amount * $data['provider_percentage'])/100;
        }

        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    private function calculate_coupon_cost(float $coupon_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'coupon_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($coupon_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($coupon_amount * $data['provider_percentage'])/100;
        }

        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    static public function calculate_product_coupon_cost(float $coupon_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'coupon_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($coupon_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($coupon_amount * $data['provider_percentage'])/100;
        }

        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    function get_random_string($valid_chars, $length){

        // start with an empty random string
        $random_string = "";

        // count the number of chars in the valid chars string so we know how many choices we have
        $num_valid_chars = strlen($valid_chars);

        // repeat the steps until we've created a string of the right length
        for ($i = 0; $i < $length; $i++)
        {
            // pick a random number from 1 up to the number of valid chars
            $random_pick = mt_rand(1, $num_valid_chars);

            // take the random character out of the string of valid chars
            // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
            $random_char = $valid_chars[$random_pick-1];

            // add the randomly-chosen char onto the end of our string so far
            $random_string .= $random_char;
        }

        // return our finished random string
        return $random_string;
    }// end of get_random_string()
}
