<?php

namespace Modules\CustomerModule\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
//use Modules\SMSModule\Emails\TestEmailSender;
//use Modules\SMSModule\Http\Controllers\Api\V1\Admin\TestController;
use Modules\SMSModule\Lib\SMS_gateway;
//use Modules\SMSModule\Lib\Email_gateway;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAddress;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Modules\CustomerModule\Emails\PasswordResetMail;

//Load Composer's autoloader
require 'vendor/autoload.php';

class CustomerController extends Controller
{

    private $customer;

    public function __construct(User $user)
    {
        $this->customer = $user;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (in_array($request->user()->user_type, CUSTOMER_USER_TYPES)) {
            $customer = $this->customer->withCount('bookings')->where('id', auth()->user()->id)->first();
            return response()->json(response_formatter(DEFAULT_200, $customer), 200);
        }
        return response()->json(response_formatter(DEFAULT_403), 401);
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function forgot_password(Request $request): JsonResponse
    {
        $mail = new PHPMailer(true);

        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required'
        ]);
        $phone_or_email = trim($request['phone_or_email']);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        if (str_contains($phone_or_email, '+'))
        {
            DB::table('password_resets')->where('phone', $phone_or_email)->delete();
            $customer = $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)
                ->where(['phone' => $phone_or_email])
                ->first();
            if (isset($customer)) {
                $token = env('APP_ENV') != 'live' ? '1234' : rand(1000, 9999);
                DB::table('password_resets')->insert([
                    'phone' => $customer['phone'],
                    'token' => $token,
                    'created_at' => now(),
                    'expires_at' => now(),
                ]);
                SMS_gateway::send($customer->phone, $token);
            } else {
                return response()->json(response_formatter(DEFAULT_404), 200);
            }
        }
        else {

            $validator = Validator::make($request->all(), [
                'phone_or_email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
            }

            DB::table('password_resets')->where('email', $phone_or_email)->delete();
            $customer = $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)
                ->where(['email' => $phone_or_email])
                ->first();
            if (isset($customer)) {
                $token = env('APP_ENV') != 'live' ? '1234' : rand(1000, 9999);
                DB::table('password_resets')->insert([
                    'email' => $customer['email'],
                    'token' => $token,
                    'created_at' => now(),
                    'expires_at' => now(),
                ]);
                //Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'download.repair';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'noreply@download.repair';                     //SMTP username
                $mail->Password   = 'a*y%Eemtn2qS';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                         //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                $mail->setFrom('noreply@download.repair', 'Repair App');
                $mail->addAddress($customer['email'], ($customer['first_name']. " ". $customer['last_name']));
                $user_name = ($customer['first_name']. " ". $customer['last_name']);
                //Add a recipient
                $mail->addAddress($customer['email']);

                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Your One-Time Password for Account Verification';
                $mail->Body    = "<p>Dear ".$user_name."</p><br/><p>Thank you for initiating the password recovery process for your account. Please use the following One-Time Password : <b>". $token ."</b>, To verify your identity and reset your password.</p>";
                $mail->AltBody = 'This email send from Download.repair system. If you have any query then contact us on info@download.repair';

                $mail->send();
//                Mail::to('sahil@peafowlsoft.com')->send(new PasswordResetMail($token,$customer->first_name));

            } else {
                return response()->json(response_formatter(DEFAULT_404), 200);
            }
        }



        return response()->json(response_formatter(DEFAULT_SENT_OTP_200), 200);
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function otp_verification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        if (str_contains($request['phone_or_email'],'@')){
            $data = DB::table('password_resets')
                ->where('email', $request['phone_or_email'])
                ->where(['token' => $request['otp']])->first();
        } else {
            $data = DB::table('password_resets')
                ->where('phone', $request['phone_or_email'])
                ->where(['token' => $request['otp']])->first();
        }

        if (isset($data)) {
            return response()->json(response_formatter(DEFAULT_VERIFIED_200), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reset_password(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required',
            'otp' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        //print_r($request->toArray());
        //die();
        if (!str_contains($request['phone_or_email'],'@')){
            $data = DB::table('password_resets')
                ->where('phone', $request['phone_or_email'])
                ->where(['token' => $request['otp']])->first();
        } else {
            $data = DB::table('password_resets')
                ->where('email', $request['phone_or_email'])
                ->where(['token' => $request['otp']])->first();
        }

        if (isset($data)) {
            if (!str_contains($request['phone_or_email'],'@')){
                $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)
                    ->where('phone', $request['phone_or_email'])
                    ->update([
                        'password' => bcrypt(str_replace(' ', '', $request['password']))
                    ]);
                DB::table('password_resets')
                    ->where('phone', $request['phone_or_email'])
                    ->where(['token' => $request['otp']])->delete();
            } else {
                $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)
                    ->where('email', $request['phone_or_email'])
                    ->update([
                        'password' => bcrypt(str_replace(' ', '', $request['password']))
                    ]);
                DB::table('password_resets')
                    ->where('email', $request['phone_or_email'])
                    ->where(['token' => $request['otp']])->delete();
            }
        } else {
            return response()->json(response_formatter(DEFAULT_404), 200);
        }

        return response()->json(response_formatter(DEFAULT_PASSWORD_RESET_200), 200);
    }

    public function reset_migration_password(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required',
            'uuid' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $data = $this->customer->where('id' , $request['uuid'])->first();

        if (isset($data)) {
            if (!str_contains($request['phone_or_email'],'@')){
                $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)
                    ->where('phone', $request['phone_or_email'])
                    ->orWhere('mobile', $request['phone_or_email'])
                    ->where('id' , $request['uuid'])
                    ->update([
                        'password' => bcrypt(str_replace(' ', '', $request['password'])),
                        'is_migration' => 0
                    ]);
            } else {
                $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)
                    ->where('email', $request['phone_or_email'])
                    ->where('id' , $request['uuid'])
                    ->update([
                        'password' => bcrypt(str_replace(' ', '', $request['password'])),
                        'is_migration' => 0
                    ]);
            }
        } else {
            return response()->json(response_formatter(DEFAULT_404), 200);
        }

        return response()->json(response_formatter(DEFAULT_PASSWORD_RESET_200), 200);
    }

    /**
     * Modify provider information
     * @param Request $request
     * @return JsonResponse
     */
    public function update_profile(Request $request): JsonResponse
    {
        $customer = $this->customer::find($request->user()->id);
        if (!isset($customer)) {
            return response()->json(response_formatter(DEFAULT_400), 400);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => '',
            'password' => '',
            'profile_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;

        if ($request->has('profile_image')) {
            $customer->profile_image = file_uploader('user/profile_image/', 'png', $request->file('profile_image'), $customer->profile_image);;
        }

        if (!is_null($request['password'])) {
            $customer->password = bcrypt($request->password);
        }
        $customer->save();

        return response()->json(response_formatter(DEFAULT_UPDATE_200), 200);
    }


    /**
     * Modify provider information
     * @param Request $request
     * @return JsonResponse
     */
    public function update_fcm_token(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $customer = $this->customer::find($request->user()->id);
        $customer->fcm_token = $request->fcm_token;
        $customer->save();

        return response()->json(response_formatter(DEFAULT_UPDATE_200), 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_account(Request $request): JsonResponse
    {
        $customer = $this->customer->whereIn('user_type', CUSTOMER_USER_TYPES)->find($request->user()->id);
        if (!isset($customer)) {
            return response()->json(response_formatter(DEFAULT_404), 200);
        }

        file_remover('user/profile_image/', $customer->profile_image);
        foreach ($customer->identification_image as $image_name){
            file_remover('user/identity/', $image_name);
        }
        $customer->forceDelete();

        return response()->json(response_formatter(DEFAULT_204), 200);
    }

}
