<?php

namespace Modules\PaymentModule\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\BookingModule\Http\Traits\BookingTrait;
use Modules\UserManagement\Entities\User;
use PhpParser\Node\Expr\Cast\Double;

//use Razorpay\Api\Api;

class TapPayController_backup extends Controller
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
//                'callback_url' => $tap['callback_url'],
//                'merchant_email' => $tap['merchant_email'],
            );
            Config::set('tap_config', $config);
        }
    }

    public function index(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'zone_id' => 'required|uuid',
//            'service_schedule' => 'required|date',
//            'service_address_id' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
//        }
        $token = 'access_token=' . $request['user']->id;
        //$token = 'access_token=' . $request->user()->id;
        $token .= $request->has('callback') ? '&&callback=' . $request['callback'] : '';
        $token .= '&&zone_id=' . $request['zone_id'] . '&&service_schedule=' . $request['service_schedule'] . '&&service_address_id=' . $request['service_address_id'];

        $token = base64_encode($token);
        $userid = $request['user']->id;
        $order_amount = cart_total($request['user']->id);
        //$order_amount = cart_total($request->user()->id);

        $customer = User::find($request['user']->id);
        //$customer = User::find($request->user()->id);

        return view('paymentmodule::tap-pay', compact('token', 'order_amount', 'customer','userid'));
//        return view('paymentmodule::tap-pay', compact('token'));
    }

    public function payment(Request $request)
    {
        //dd($request);
        $order_amount = cart_total($request->id);
        $customer = User::find($request->id);
        $currency_code = (business_config('currency_code', 'business_information'))->live_values ?? null;

        $data['amount'] = $order_amount;
        $data['currency'] = $currency_code;
        $data['customer']['first_name'] = $customer->first_name;
        $data['customer']['email'] = $customer->email;
        $tkn = base64_encode($order_amount."#".$request->id);
        //$data['amount'] = 1;
//        $data['currency'] = 'KWD';
//        $data['customer']['first_name'] = 'test';
//        $data['customer']['email'] = 'test@test.com';
        $data['source']['id'] = 'src_card';
//        $data['redirect']['url'] = $this->callback('https://repair/admin/dashboard');
        $data['redirect']['url'] = route('tap-pay.callback',[$tkn]);

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

        $curl = curl_init();
        $url = "https://api.tap.company/v2/charges";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($output);
        return redirect()->to($response->transaction->url);
    }

//    public function payment(Request $request) {
//        $order_amount = cart_total($request->user()->id);
//        $customer = User::find($request->user()->id);
//
////        $data['amount'] = $order_amount;
//        $data['currency'] = 'KWD';
//        $data['customer']['first_name'] = $customer->first_name;
//        $data['customer']['email'] = $customer->email;
//        $data['amount'] = 1;
////        $data['currency'] = 'KWD';
////        $data['customer']['first_name'] = 'test';
////        $data['customer']['email'] = 'test@test.com';
//        $data['source']['id'] = 'src_card';
////        $data['redirect']['url'] = $this->callback('https://repair/admin/dashboard');
//        $data['redirect']['url'] = route('tap-pay.callback');
//
//        $headers = [
//            "authorization: Bearer sk_test_c2AdvEl1bTROVxzYwN4DBepG", // SECRET API KEY
//            "content-type: application/json"
//        ];
//
//        $curl = curl_init();
//        $url = "https://api.tap.company/v2/charges";
//        curl_setopt($curl,CURLOPT_URL,$url);
//        curl_setopt($curl,CURLOPT_POST,true);
//        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));
//        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
//        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
//        $output = curl_exec($curl);
//
//        curl_close($curl);
//        $response = json_decode($output);
//        return redirect()->to($response->transaction->url);
////        dd();
//    }

    public function callback(Request $request)
    {
        $params_tkn = explode('#', base64_decode(request()->segment(4)));
        $callback = "https://intelligent-almeida.103-50-161-197.plesk.page/";
        $params = $request['tap_id'];
//        dd($params);
//        $callback = null;
//        foreach ($params as $param) {
//            $data = explode('=', $param);
//            if ($data[0] == 'access_token') {
//                $access_token = $data[1];
//            } elseif ($data[0] == 'callback') {
//                $callback = $data[1];
//            } elseif ($data[0] == 'zone_id') {
//                $zone_id = $data[1];
//            } elseif ($data[0] == 'service_schedule') {
//                $service_schedule = $data[1];
//            } elseif ($data[0] == 'service_address_id') {
//                $service_address_id = $data[1];
//            }
//        }

        $tran_id = Str::random(6) . '-' . rand(1, 1000);
        $request['payment_method'] = 'tap_pay';
//        $request['service_address_id'] = $service_address_id;
//        $request['zone_id'] = $zone_id;
//        $request['service_schedule'] = $service_schedule;
        //$request->user()->id = "27e25c5b-2e34-457c-aed8-7ccbf5c0df44";
        //dd($request->user()->id,$request,$tran_id);
        //$response = $this->place_booking_request($request->user()->id, $request, $tran_id);

//        if($response['flag'] == 'failed') {
//            if ($callback) {
//                return redirect($callback.'?payment_status=failed');
//            }
//            else {
//                return response()->json(response_formatter(DEFAULT_204), 200);
//            }
//        }

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

            if ($response_status == 'CAPTURED' && (doubleval($response_amt)==doubleval($params_tkn[0]))){
                $response = $this->place_booking_request($params_tkn[1], $request, $response_tap_id);
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

//            if (!empty($response_tap_id)) {
////                return redirect($callback.'?payment_status=success');
//                try {
//                    if ($response) {
//                        return redirect('https://repair/admin/dashboard?payment_status=success');
//                    } else {
//                        return response()->json(response_formatter(DEFAULT_200), 200);
//                    }
//                } catch (\Exception $e) {
//                    //error
//                }
//            }
        }

//        if (count($input) && !empty($input['tap_id'])) {
//            try {
//                $response_tap_id = $response->id;
//                $response_amt = $response->amount;
//                if ($callback) {
//                    return redirect($callback . '?payment_status=success');
//                } else {
//                    return response()->json(response_formatter(DEFAULT_200), 200);
//                }
//            } catch (\Exception $e) {
//                //error
//            }
//        }


//        else {
//            return response()->json(response_formatter(DEFAULT_200), 200);
//        }

//        if(!empty($input['tap_id'])) {
//            try {
//                $response_tap_id = $input['tap_id'];
//                $response_amt = $response->amount;
//                if ($callback) {
//                    echo 'test';
//                return redirect($callback.'?payment_status=success');
//            } else {
//                return response()->json(response_formatter(DEFAULT_200), 200);
//            }
//            } catch (\Exception $e) {
//                //error
//            }
//        }
    }
}
