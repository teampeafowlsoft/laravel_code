<?php

namespace Modules\CartModule\Http\Controllers\Api\V1\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\CartModule\Entities\Cart;
use Modules\CartModule\Entities\CartServiceImages;
use Modules\CartModule\Entities\CartServiceInfo;
use Modules\CartModule\Entities\CartServicePdf;
use Modules\CartModule\Entities\CartServiceVideos;
use Modules\ServiceManagement\Entities\Service;
use Modules\ServiceManagement\Entities\Variation;

class CartController extends Controller
{
    private Cart $cart;
    private Service $service;
    private Variation $variation;
    private CartServiceImages $cartserviceimages;
    private CartServicePdf $cartservicepdf;
    private CartServiceVideos $cartservicevideos;


    public function __construct(Cart $cart, Service $service, Variation $variation, CartServiceImages $cartserviceimages, CartServicePdf $cartservicepdf, CartServiceVideos $cartservicevideos)
    {
        $this->cart = $cart;
        $this->service = $service;
        $this->variation = $variation;
        $this->cartserviceimages = $cartserviceimages;
        $this->cartservicepdf = $cartservicepdf;
        $this->cartservicevideos = $cartservicevideos;
    }

    public function upload_images(Request $request): JsonResponse
    {
        if(!$request->hasFile('service_image')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $allowedfileExtension=['pdf','jpg','png'];
        $files = $request->file('service_image');
        $errors = [];

            if($files) {
                $save = [];
                foreach($files  as $key => $mediaFiles) {
                    $extension = $mediaFiles->getClientOriginalExtension();

                    $check = in_array($extension,$allowedfileExtension);
                    $path = file_uploader('cart/', 'png', $request->file('service_image')[$key]);
                    $name = $mediaFiles->getClientOriginalName();
                    $random_code = random_int(1000000, 9999999);
                    //store image file into directory and db
                    $save[$key]['service_image'] = $path;
                    $save[$key]['code'] = $random_code;
                }
               DB::table('cart_service_images')->insert($save);
//                foreach($save as $sv) {
//                    $last_id[] = $sv->id;
//                }

//                return response()->json(['response'=>['code'=>'200','message'=>'image uploaded successfully']]);
                return response()->json(response_formatter(DEFAULT_STORE_200,$save), 200);
            } else {
                return response()->json(['invalid_file_format'], 422);
            }


//        $image=new CartServiceImages;
//        if($request->hasfile('service_image'))
//        {
//            $file=$request->file('service_image');
//            $extension=$file->getClientOriginalExtension();
////            $filename=time().'.'.$extension;
//            $filename=file_uploader('cart/', 'png', $file);
//            $image->service_image=$filename;
//
//            $image->save();
//            return response()->json(['response'=>['code'=>'200','message'=>'image uploaded successfully']]);
//
//        }
//        else
//        {
//            return response()->json(response_formatter(DEFAULT_404), 200);
////            return $request;
////            $image->service_image='';
//        }

    }

    public function upload_videos(Request $request): JsonResponse
    {
        if ($request->has('service_video')) {
            foreach($request->service_video  as $key => $video) {
                $path = file_uploader('cartvideo/', 'mp4', $request->file('service_video')[$key]);
                //store image file into directory and db
                $random_code = random_int(1000000, 9999999);
                $save[$key]['service_video'] = $path;
                $save[$key]['code'] = $random_code;
            }
            DB::table('cart_service_videos')->insert($save);
            return response()->json(response_formatter(DEFAULT_STORE_200,$save), 200);
        }
        else {
            return response()->json(response_formatter(DEFAULT_404), 200);
        }
    }

    public function upload_pdf(Request $request): JsonResponse
    {
//        $validator = Validator::make($request->all(),[
//            'service_pdf' => 'required|mimes:pdf|max:2048',
//        ]);
//
//        if($validator->fails()) {
//
//            return response()->json(['error'=>$validator->errors()], 401);
//        }
//
//        if ($file = $request->file('service_pdf')) {
////            $path = $file->store('app/public/cartpdf');
//            $path = file_uploader('cartpdf/', 'pdf', $request->file('service_pdf'));
//            $name = $file->getClientOriginalName();
//
//            //store your file into directory and db
//            $save = new CartServicePdf();
//            $save->service_pdf= $path;
//            $save->save();
//
//            return response()->json([
//                "success" => true,
//                "message" => "File successfully uploaded",
//                "file" => $file
//            ]);
//
//        }


        $file_data = $request->file('service_pdf');
        if ($request->hasfile('service_pdf')) {
            $insert = [];
                $path = file_uploader('cartpdf/', 'pdf', $request->file('service_pdf'));
                $random_code = random_int(1000000, 9999999);
                $insert['service_pdf'] = $path;
                $insert['code'] = $random_code;

                DB::table('cart_service_pdfs')->insert($insert);
            return response()->json(response_formatter(DEFAULT_STORE_200,$insert), 200);
        }
        else {
            return response()->json(response_formatter(DEFAULT_404), 200);
        }
    }
// Old Function 18-10-23 Pc1
    public function add_to_cart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|uuid',
            'category_id' => 'required|uuid',
            'sub_category_id' => 'required|uuid',
            'variant_key' => 'required',
            'quantity' => 'required|numeric|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        // Old 19-10-23 Pc1
//        $variation = $this->variation->where(['zone_id' => Config::get('zone_id'), 'service_id' => $request['service_id']])
//            ->where(['variant_key' => $request['variant_key']])
//            ->first();
        // Old 19-10-23 Pc1

        // Change New 19-10-23 Pc1
        $variation = $this->variation->withoutGlobalScopes()
            ->where(['zone_id' => Config::get('zone_id')])
            ->where(['service_id' => $request['service_id']])
            ->where(['variant_key' => $request['variant_key']])
            ->first();
        // Change New 19-10-23 Close
        // Change New 20-10-23 Pc1
//        $variation = $this->variation->withoutGlobalScopes()
//            ->where(['zone_id' => $request->zone_id])
//            ->where(['service_id' => $request['service_id']])
//            ->where(['variant_key' => $request['variant_key']])
//            ->first();
        // Change New 20-10-23 Close

        if (!empty($variation)) {
            // Old 19-10-23 Pc1
//            $service = $this->service->find($request['service_id']);
            // Old 19-10-23 Pc1

            // Change New 19-10-23 Pc1
            $service = $this->service->withoutGlobalScopes()->find($request->service_id);
            // Change New 19-10-23 Close

            $check_cart = $this->cart->where([
                'service_id' => $request['service_id'],
                'variant_key' => $request['variant_key'],
                'customer_id' => $request->user()->id])->first();
            $cart = $check_cart ?? $this->cart;
            $quantity = isset($check_cart) ? $cart->quantity + $request['quantity'] : $request['quantity'];

        //  Change New 19-10-23 Pc1
            if(!empty($service)){
                if($service->lang_id == 1){
                    $basic_discount = basic_discount_calculation($service, $variation->price * $quantity);
                    $campaign_discount = campaign_discount_calculation($service, $variation->price * $quantity);
                }else{

                    $service_arb = $this->service->withoutGlobalScopes()->where('group_id',$service->group_id)->where('lang_id',1)->first();
                    $basic_discount = basic_discount_calculation($service_arb, $variation->price * $quantity);
                    $campaign_discount = campaign_discount_calculation($service_arb, $variation->price * $quantity);
                }
            }
        // Change New 19-10-23 Close

        // Old 19-10-23 Pc1
            //calculation
//            $basic_discount = basic_discount_calculation($service, $variation->price * $quantity);
//            $campaign_discount = campaign_discount_calculation($service, $variation->price * $quantity);
        // Old 19-10-23 Pc1 Close
            $subtotal = round($variation->price * $quantity, 2);

            $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;
            $tax = round(( (($variation->price-$applicable_discount)*$service['tax'])/100 ) * $quantity, 2);

            //between normal discount & campaign discount, greater one will be calculated
            $basic_discount = $basic_discount > $campaign_discount ? $basic_discount : 0;
            $campaign_discount = $campaign_discount >= $basic_discount ? $campaign_discount : 0;

            //DB part
            $cart->customer_id = $request->user()->id;
            $cart->service_id = $request['service_id'];
            $cart->category_id = $request['category_id'];
            $cart->sub_category_id = $request['sub_category_id'];
            $cart->variant_key = $request['variant_key'];
            $cart->provider_selected_ids = $request['provider_selected_ids'];
            $cart->quantity = $quantity;
            $cart->service_cost = $variation->price;
            $cart->discount_amount = $basic_discount;
            $cart->campaign_discount = $campaign_discount;
            $cart->coupon_discount = 0;
            $cart->coupon_code = null;
            $cart->tax_amount = round($tax, 2);
            $cart->total_cost = round($subtotal - $basic_discount - $campaign_discount + $tax, 2);
            $cart->save();

            $last_inserted_id = $cart->id;
            $cart_data = $request['cart_images'];
            $imgArray = explode(',', $cart_data);

            $cart_video_data = $request['cart_video'];
            $videoArray = explode(',', $cart_video_data);

            $cart_pdf_data = $request['cart_pdf'];
            $pdfArray = explode(',', $cart_pdf_data);

            if(!empty($cart_data)) {
                foreach ($imgArray as $cart) {
//                $cart_image_data = CartServiceImages::where(['code' => $cart->code])->get();
                    //UPDATE cart_service_images TABLE
                    DB::table('cart_service_images')->where('code', $cart)->update(['cart_id' => $last_inserted_id]);
                }
            }
            if(!empty($cart_video_data)) {
                foreach ($videoArray as $video_code) {
                    //UPDATE cart_service_videos TABLE
                    DB::table('cart_service_videos')->where('code', $video_code)->update(['cart_id' => $last_inserted_id]);
                }
            }
            if(!empty($cart_pdf_data)) {
                foreach ($pdfArray as $pdf_code) {
                    //UPDATE cart_service_pdf TABLE
                    DB::table('cart_service_pdfs')->where('code', $pdf_code)->update(['cart_id' => $last_inserted_id]);
                }
            }

            return response()->json(response_formatter(DEFAULT_STORE_200), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }

// Old Function 18-10-23 Pc1 Close

// 18-10-23 Pc1
//    public function add_to_cart(Request $request): JsonResponse
//    {
//        $validator = Validator::make($request->all(), [
//            'service_id' => 'required|uuid',
//            'category_id' => 'required|uuid',
//            'sub_category_id' => 'required|uuid',
//            'variant_key' => 'required',
//            'quantity' => 'required|numeric|min:1|max:1000'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
//        }
//
//
//        $variation = DB::table('variations')->where(['zone_id'=>$request->zone_id,'service_id'=>$request->service_id,'variant_key'=>$request->variant_key])->first();
//
//        if (!empty($variation)) {
////dd($request->service_id);
//            $service_id = $request->service_id;
//
////            $service = $this->service->where('id',"61588100-7d38-49f2-90da-e48eb88ae56b")->first();
//            $service = DB::table('services')->where('id','=',$request->service_id)->first();
//
//            $check_cart = $this->cart->where([
//                'service_id' => $request['service_id'],
//                'variant_key' => $request['variant_key'],
//                'customer_id' => $request->user()->id])->first();
//            $cart = $check_cart ?? $this->cart;
//            $quantity = isset($check_cart) ? $cart->quantity + $request['quantity'] : $request['quantity'];
//
////die();
//            $calculation = ($variation->price * $quantity);
//
//            //calculation
//            $basic_discount = basic_discount_calculation($service, $calculation);
//
//            $campaign_discount = campaign_discount_calculation($service, ($variation->price * $quantity));
//            $subtotal = round($variation->price * $quantity, 2);
//
//            $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;
//            $tax = round(( (($variation->price-$applicable_discount)*$service['tax'])/100 ) * $quantity, 2);
//
//            //between normal discount & campaign discount, greater one will be calculated
//            $basic_discount = $basic_discount > $campaign_discount ? $basic_discount : 0;
//            $campaign_discount = $campaign_discount >= $basic_discount ? $campaign_discount : 0;
//
//            //DB part
//            $cart->customer_id = $request->user()->id;
//            $cart->service_id = $request['service_id'];
//            $cart->category_id = $request['category_id'];
//            $cart->sub_category_id = $request['sub_category_id'];
//            $cart->variant_key = $request['variant_key'];
//            $cart->provider_selected_ids = $request['provider_selected_ids'];
//            $cart->quantity = $quantity;
//            $cart->service_cost = $variation->price;
//            $cart->discount_amount = $basic_discount;
//            $cart->campaign_discount = $campaign_discount;
//            $cart->coupon_discount = 0;
//            $cart->coupon_code = null;
//            $cart->tax_amount = round($tax, 2);
//            $cart->total_cost = round($subtotal - $basic_discount - $campaign_discount + $tax, 2);
//            $cart->save();
//
//            $last_inserted_id = $cart->id;
//            $cart_data = $request['cart_images'];
//            $imgArray = explode(',', $cart_data);
//
//            $cart_video_data = $request['cart_video'];
//            $videoArray = explode(',', $cart_video_data);
//
//            $cart_pdf_data = $request['cart_pdf'];
//            $pdfArray = explode(',', $cart_pdf_data);
//
//            if(!empty($cart_data)) {
//                foreach ($imgArray as $cart) {
////                $cart_image_data = CartServiceImages::where(['code' => $cart->code])->get();
//                    //UPDATE cart_service_images TABLE
//                    DB::table('cart_service_images')->where('code', $cart)->update(['cart_id' => $last_inserted_id]);
//                }
//            }
//            if(!empty($cart_video_data)) {
//                foreach ($videoArray as $video_code) {
//                    //UPDATE cart_service_videos TABLE
//                    DB::table('cart_service_videos')->where('code', $video_code)->update(['cart_id' => $last_inserted_id]);
//                }
//            }
//            if(!empty($cart_pdf_data)) {
//                foreach ($pdfArray as $pdf_code) {
//                    //UPDATE cart_service_pdf TABLE
//                    DB::table('cart_service_pdfs')->where('code', $pdf_code)->update(['cart_id' => $last_inserted_id]);
//                }
//            }
//
//            return response()->json(response_formatter(DEFAULT_STORE_200), 200);
//        }
//
//        return response()->json(response_formatter(DEFAULT_404), 200);
//    }
// 18-10-23 Pc1 Close

    public function list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $cart = $this->cart->withoutGlobalScopes()->with(['customer', 'category', 'sub_category', 'service'])->where(['customer_id' => $request->user()->id])
            ->latest()->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $cart), 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update_qty(Request $request, string $id): JsonResponse
    {
        $cart = $this->cart->where(['id' => $id])->first();

        if (!isset($cart)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $cart = $this->cart->find($id);
        $service = $this->service->find($cart['service_id']);

        $basic_discount = basic_discount_calculation($service, $cart->service_cost * $request['quantity']);
        $campaign_discount = campaign_discount_calculation($service, $cart->service_cost * $request['quantity']);
        $subtotal = round($cart->service_cost * $request['quantity'], 2);

        $applicable_discount = ($campaign_discount >= $basic_discount) ? $campaign_discount : $basic_discount;
        $tax = round(( (($cart->service_cost-$applicable_discount)*$service['tax'])/100 ) * $request['quantity'], 2);

        //between normal discount & campaign discount, greater one will be calculated
        $basic_discount = $basic_discount > $campaign_discount ? $basic_discount : 0;
        $campaign_discount = $campaign_discount >= $basic_discount ? $campaign_discount : 0;

        $cart->quantity = $request->quantity;
        $cart->discount_amount = $basic_discount;
        $cart->campaign_discount = $campaign_discount;
        $cart->coupon_discount = 0;
        $cart->coupon_code = null;
        $cart->tax_amount = round($tax, 2);
        $cart->total_cost = round($subtotal - $basic_discount - $campaign_discount + $tax, 2);
        $cart->save();

        return response()->json(response_formatter(DEFAULT_UPDATE_200), 200);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function remove(Request $request, string $id): JsonResponse
    {
        $cart = $this->cart->where(['id' => $id])->first();
        $cart_images = $this->cartserviceimages->where(['cart_id' => $id])->first();
        $cart_video = $this->cartservicevideos->where(['cart_id' => $id])->first();
        $cart_pdf = $this->cartservicepdf->where(['cart_id' => $id])->first();

//        if (!isset($cart) || !isset($cart_images) || !isset($cart_video) || !isset($cart_pdf)) {
        if (!isset($cart)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $this->cart->where('id', $id)->delete();
        $this->cart->cartserviceimages('cart_id', $id)->delete();
        $this->cart->cartservicevideos('cart_id', $id)->delete();
        $this->cart->cartservicepdf('cart_id', $id)->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function empty_cart(Request $request): JsonResponse
    {
        $cart = $this->cart->where(['customer_id' => $request->user()->id]);

        $product_cart = DB::table('order_items')->where('user_id',$request->user()->id);

//        if ($cart->count() == 0) {
//            return response()->json(response_formatter(DEFAULT_204), 200);
//        }

        $cart->delete();
        $product_cart->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }
}
