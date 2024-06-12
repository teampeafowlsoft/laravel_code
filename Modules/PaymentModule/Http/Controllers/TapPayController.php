<?php

namespace Modules\PaymentModule\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\BookingModule\Http\Traits\BookingTrait;
use Modules\OrderpoolManagement\Entities\Order;
use Modules\ProductManagement\Http\Controllers\Api\V1\Customer\ProductController;
use Modules\UserManagement\Entities\User;
use PhpParser\Node\Expr\Cast\Double;

//use Razorpay\Api\Api;

class TapPayController extends Controller
{
    use BookingTrait;

    public function __construct()
    {
        $config = business_config('tap_pay', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $tap = $config->live_values;
        } elseif (!is_null($config) && $config->mode == 'test') {
            $tap = $config->test_values;
        }

        if ($tap) {
            $config = array(
                'public_key' => $tap['public_key'],
                'secret_key' => $tap['secret_key'],
            );
            Config::set('tap_config', $config);
        }
    }

    public function index(Request $request)
    {
        $token = 'access_token=' . $request['user']->id;
        $token .= $request->has('callback') ? '&&callback=' . $request['callback'] : '';
        $token .= '&&zone_id=' . $request['zone_id'] . '&&service_schedule=' . $request['service_schedule'] . '&&service_address_id=' . $request['service_address_id'];

        $token = base64_encode($token);
        $userid = $request['user']->id;
        if ($request['from']=="service"){
            $order_amount = cart_total($request['user']->id);
        } else {
            $order_amount = cart_product_total($request['user']->id);
        }


        $customer = User::find($request['user']->id);
        $zone_id = $request['zone_id'];
        $address_id = $request['service_address_id'];
        $service_schedule = $request['service_schedule'];
        $call_from =$request['from'];
        return view('paymentmodule::tap-pay', compact('token', 'order_amount', 'customer','userid','call_from','zone_id','address_id','service_schedule'));
    }

    public function payment(Request $request)
    {
        if ($request['call_from']=="service"){
            $order_amount = cart_total($request->id);
        } else {
            $order_amount = cart_product_total($request->id);
        }

        $customer = User::find($request->id);
        $currency_code = (business_config('currency_code', 'business_information'))->live_values ?? null;

        //Supplier Commission Code::
//        $get_date = DB::table('order_items')
//            ->selectRaw('product_variant.packate_measurement_discount_price as product_price,providers.destination_id, providers.commission_status, providers.commission_percentage, providers.commission_percentage_product ')
//            ->join('product_variant', 'order_items.product_variant_id', '=', 'product_variant.id')
//            ->join('products', 'product_variant.product_id', '=', 'products.id')
//            ->join('providers', 'products.vendor', '=', 'providers.id')
//            ->where('order_items.user_id', $request->id)
//            ->get();

//        print_r($get_date->toArray());
//        die();

        // Position Function
        function findValuePosition($array, $searchValue, $keyToSearch): array|bool
        {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result = findValuePosition($value, $searchValue, $keyToSearch);
                    if ($result !== false) {
                        array_unshift($result, $key);
                        return $result;
                    }
                } elseif ($key === $keyToSearch && $value === $searchValue) {
                    return array($key);
                }
            }
            return false; // Value not found in the array.
        }
        // Close Position Function
        $handyman_id = 0;
        $service_type = 'normal';
        $destination = [];
        if ($request['call_from']=="product"){
//            $order_amount = cart_total($request->id);
            $cnt = 0;
            $orderItems = DB::table('order_items')->where('user_id',$request->id)->get();

            foreach ($orderItems as $orderItem){
                $provider = DB::table('order_items as oi')
                    ->select('ps.*','oi.*')
                    ->join('product_variant as pv', 'oi.product_variant_id', '=', 'pv.id')
                    ->join('products as p', 'p.id', '=', 'pv.product_id')
                    ->join('providers as ps', 'ps.id', '=', 'p.vendor')
                    ->where('oi.id',$orderItem->id)->first();

                if(!empty($provider)){
                    if ($provider->destination_id != null && $provider->destination_id != ''){
                        //Transition Commission
                        $provider_commission = ($provider->price * $provider->quantity) + $provider->delivery_charge;

                        if (array_search($provider->destination_id,array_column($destination,'id')) !== FALSE){
                            // Same destination_id found

                            $position = findValuePosition($destination, $provider->destination_id, 'id');
                            //dd($position);
                            $destination[$position[0]]['amount'] = ($destination[$position[0]]['amount'] + $provider_commission);
                        }else{
                            // add new array entry
                            $destination[$cnt]['id'] = $provider->destination_id;
                            $destination[$cnt]['amount'] = $provider_commission;
                            $destination[$cnt]['currency'] = 'KWD';
                        }
                        $cnt++;
                    }
                }
            }
        }



        $service_type_int = 1;
        $src_id = "src_card";
        $tkn = base64_encode($order_amount."#".$request->id."#".$request['call_from']."#".$request['zone_id']."#".$request['address_id']."#".$request['service_schedule']);
        $OrderID = "ODR".rand(100000,999999);
// 3-11-23 Pc1 Comment This Code
//        $data['amount'] = $order_amount;
//        $data['currency'] = $currency_code;
//        $data['threeDSecure'] = true;
//        $data['save_card'] = false;
//        $data['description'] = $handyman_id;
//        $data['statement_descriptor'] = "Sample";
//        $data['metadata'] = array("udf1"=>$service_type_int,"udf2"=>$request->id);
//        $data['reference'] = array("transaction"=>"txn_0001","order"=>"ODR".rand(100000,999999));
//        $data['receipt'] = array("email"=>false,"sms"=>true);
//        $data['customer']['first_name'] = $customer->first_name;
//        $data['customer']['email'] = $customer->email;
//        $data['merchant']['id'] = "";
//        $data['source']['id'] = $src_id;
//        $data['destinations'] = $destination;
//        $data['redirect']['url'] = route('tap-pay.callback',[$tkn]);
// 3-11-23 Close Comment This Code
        $tap_post_data = array(
            "amount"=>$order_amount,
            "currency"=>$currency_code,
            "threeDSecure"=>true,
            "save_card"=>false,
            "description"=>$handyman_id,
            "statement_descriptor"=>"Sample",
            //"metadata"=>array("udf1"=>$service_type_int,"udf2"=>$customer),
            "reference"=>array("transaction"=>"txn_0001","order"=>$OrderID),
            "receipt"=>array("email"=>false,"sms"=>true),
            "customer"=>array("first_name"=>"test","middle_name"=>"test","last_name"=>"test","email"=>"test@test.com","phone"=>array("country_code"=>"965","number"=>"50000000")),
            "merchant"=>array("id"=>""),
            "source"=>array("id"=>$src_id),
            "destinations"=>$destination,
            "redirect"=>array("url"=>route('tap-pay.callback',[$tkn,$request['zone_id'],$request['address_id'],$request['service_schedule']]))
        );

        $config = business_config('tap_pay', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $tap = $config->live_values;
        } elseif (!is_null($config) && $config->mode == 'test') {
            $tap = $config->test_values;
        }

        $headers = [
//            "authorization: Bearer sk_test_c2AdvEl1bTROVxzYwN4DBepG", // SECRET API KEY
            "authorization: Bearer " . $tap['secret_key'], // SECRET API KEY
            "content-type: application/json"
        ];

        //dd($tap_post_data);
        $curl = curl_init();
        $url = "https://api.tap.company/v2/charges";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($tap_post_data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($output);
        //dd($response);
        //echo $response->transaction->url;
        return redirect()->to($response->transaction->url);
    }

    public function callback(Request $request)
    {
        $params_tkn = explode('#', base64_decode(request()->segment(4)));
        $callback = "https://www.download.repair/pay-redirect";

        $tran_id = Str::random(6) . '-' . rand(1, 1000);
        $request['payment_method'] = 'tap_pay';

        $config = business_config('tap_pay', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $tap = $config->live_values;
        } elseif (!is_null($config) && $config->mode == 'test') {
            $tap = $config->test_values;
        }

        $headers = [
//            "authorization: Bearer sk_test_c2AdvEl1bTROVxzYwN4DBepG", // SECRET API KEY
            "authorization: Bearer " . $tap['secret_key'], // SECRET API KEY
            "content-type: application/json"
        ];
        $input = $request->all();

        $curl = curl_init();
        $url = "https://api.tap.company/v2/charges/" . $input['tap_id'];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($output);
        if (isset($input) && (isset($input['tap_id']))) {
            $response_tap_id = $response->id;
            $response_amt = $response->amount;
            $response_status = $response->status;
            //&& (doubleval($response_amt)==doubleval($params_tkn[0]))
            if ($response_status == 'CAPTURED'){
                $request['zone_id'] = $params_tkn[3];
                $request['service_schedule'] = $params_tkn[5];
                $request['service_address_id'] = $params_tkn[4];
                if ($params_tkn[2]=="product"){
                    $response = ProductController::place_order_request($params_tkn[1], $request, $response_tap_id);
                    if($response['flag'] == 'success') {

                    }
                } else {
                    $response = $this->place_booking_request($params_tkn[1], $request, $response_tap_id);
                }
                if($response['flag'] == 'failed') {
                    if ($callback) {
                        return redirect($callback.'?payment_status=failed');
                    }
                    else {
                        return response()->json(response_formatter(DEFAULT_204), 200);
                    }
                }

                return redirect($callback.'?payment_status=success');

            } else {
                return redirect($callback.'?payment_status=failed');
            }

        }

    }
}
