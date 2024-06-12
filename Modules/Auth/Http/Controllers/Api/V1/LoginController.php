<?php

namespace Modules\Auth\Http\Controllers\Api\V1;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Entities\User;
//use Twilio\Jwt\JWT;
use Firebase\JWT\JWT;

class LoginController extends Controller
{
    private User $user;
    private array $validation_array = [
        'email_or_phone' => 'required',
        'password' => 'required',
    ];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function admin_login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->validation_array);
        if ($validator->fails()) return response()->json(response_formatter(AUTH_LOGIN_403, null, error_processor($validator)), 403);

        $user = $this->user->where(['phone' => $request['email_or_phone']])
            ->orWhere('email', $request['email_or_phone'])
            ->ofType(ADMIN_USER_TYPES)->first();

        if (isset($user) && Hash::check($request['password'], $user['password'])) {
            if ($user->is_active && $user->roles->count() > 0 && $user->roles[0]->is_active || $user->user_type == 'super-admin') {
                return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, ADMIN_PANEL_ACCESS)), 200);
            }
            return response()->json(response_formatter(ACCOUNT_DISABLED), 401);
        }
        return response()->json(response_formatter(AUTH_LOGIN_401), 401);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function provider_login(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->validation_array
        );
        if ($validator->fails())
            return response()->json(response_formatter(AUTH_LOGIN_403, null, error_processor($validator)), 403);

        $user = $this->user->where(['phone' => $request['email_or_phone']])
            ->orWhere('email', $request['email_or_phone'])
            ->orWhere('mobile', $request['email_or_phone'])
            ->ofType(['provider-admin'])
            ->first();

        if (!isset($user)) {
            return response()->json(response_formatter(AUTH_LOGIN_404), 404);
        }

        if (isset($user) && $user->is_active && $user->provider != null && $user->provider->is_approved && $user->provider->is_active) {
            if (Hash::check($request['password'], $user['password'])) {
                return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, PROVIDER_PANEL_ACCESS)), 200);
            }
        } elseif (isset($user) && (!$user->is_active || $user->provider == null)) {
            return response()->json(response_formatter(DEFAULT_USER_DISABLED_401), 401);
        }

        return response()->json(response_formatter(AUTH_LOGIN_401), 401);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_login(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->validation_array
        );
        if ($validator->fails()) return response()->json(response_formatter(AUTH_LOGIN_403, null, error_processor($validator)), 403);

        $user = $this->user->where(['phone' => $request['email_or_phone']])
            ->orWhere('email', $request['email_or_phone'])
            ->orWhere('mobile', $request['email_or_phone'])
            ->ofType(CUSTOMER_USER_TYPES)
            ->first();

        //NEW LOGIC FOR MIGRATE USERS::
        if (isset($user) &&  $user['is_migration'] == '1') {
            return response()->json(response_formatter(AUTH_MIGRATION_200,['token' => 'migration','uuid' => $user['id'], 'is_active' => $user['is_migration']]), 200);
        }

        if (isset($user) && Hash::check($request['password'], $user['password'])) {
            if ($user->is_active) {
                return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, SERVICEMAN_APP_ACCESS)), 200);
            }
            return response()->json(response_formatter(DEFAULT_USER_DISABLED_401), 401);
        }

        return response()->json(response_formatter(AUTH_LOGIN_401), 401);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function serviceman_login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) return response()->json(response_formatter(AUTH_LOGIN_403, null, error_processor($validator)), 403);

        $user = $this->user->where(['phone' => $request['phone']])->ofType([SERVICEMAN_USER_TYPES])->first();

        if (isset($user) && Hash::check($request['password'], $user['password'])) {
            if ($user->is_active) {
                return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, SERVICEMAN_APP_ACCESS)), 200);
            }
            return response()->json(response_formatter(DEFAULT_USER_DISABLED_401), 401);
        }

        return response()->json(response_formatter(AUTH_LOGIN_401), 401);
    }

// 08-11-23 PC1 Old Code
//    public function social_customer_login(Request $request): JsonResponse
//    {
//        $validator = Validator::make($request->all(), [
//            'token' => 'required',
//            'unique_id' => 'required',
//            'email' => 'required',
//            'medium' => 'required|in:google,facebook',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
//        }
//
//        $client = new Client();
//        $token = $request['token'];
//        $email = $request['email'];
//        $unique_id = $request['unique_id'];
//
//        try {
//            if ($request['medium'] == 'google') {
//                $res = $client->request('GET', 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token);
//                $data = json_decode($res->getBody()->getContents(), true);
//            } elseif ($request['medium'] == 'facebook') {
//                $res = $client->request('GET', 'https://graph.facebook.com/' . $unique_id . '?access_token=' . $token . '&&fields=name,email');
//                $data = json_decode($res->getBody()->getContents(), true);
//            }
//        } catch (\Exception $exception) {
//            return response()->json(response_formatter(DEFAULT_401), 200);
//        }
//
//        if (strcmp($email, $data['email']) === 0) {
//            $user = $this->user->where('email', $request['email'])
//                ->ofType(CUSTOMER_USER_TYPES)
//                ->first();
//
//            if (!isset($user)) {
//                $name = explode(' ', $data['name']);
//                if (count($name) > 1) {
//                    $fast_name = implode(" ", array_slice($name, 0, -1));
//                    $last_name = end($name);
//                } else {
//                    $fast_name = implode(" ", $name);
//                    $last_name = '';
//                }
//
//                $user = $this->user;
//                $user->first_name = $fast_name;
//                $user->last_name = $last_name;
//                $user->email = $data['email'];
//                $user->phone = null;
//                $user->profile_image = 'def.png';
//                $user->date_of_birth = date('y-m-d');
//                $user->gender = 'others';
//                $user->password = bcrypt($request->ip());
//                $user->user_type = 'customer';
//                $user->is_active = 1;
//                $user->save();
//            }
//
//            return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, CUSTOMER_PANEL_ACCESS)), 200);
//        }
//
//        return response()->json(response_formatter(DEFAULT_404), 401);
//    }
// Close 08-11-23 Pc1 Old Code

// New Code 08-11-23 Pc1
    public function social_customer_login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'unique_id' => 'required',
            'email' => 'required_if:medium,google,facebook',
            'medium' => 'required|in:google,facebook,apple',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $client = new Client();
        $token = $request['token'];
        $email = $request['email'];
        $unique_id = $request['unique_id'];

        try {
            if ($request['medium'] == 'google') {
                $res = $client->request('GET', 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token);
                $data = json_decode($res->getBody()->getContents(), true);
            } elseif ($request['medium'] == 'facebook') {
                $res = $client->request('GET', 'https://graph.facebook.com/' . $unique_id . '?access_token=' . $token . '&&fields=name,email');
                $data = json_decode($res->getBody()->getContents(), true);
            }elseif ($request['medium'] == 'apple') {
                $apple_login = (business_config('apple_login', 'third_party'))->live_values;

                $teamId = $apple_login['team_id'];
                $keyId = $apple_login['key_id'];
                $sub = $apple_login['client_id'];
                $aud = 'https://appleid.apple.com';
                $iat = strtotime('now');
                $exp = strtotime('+60days');
                $keyContent = file_get_contents('storage/app/public/apple-login/'.$apple_login['service_file']);

//                $keyArr = [
//                    "kid" => $keyId
//                ];
                $tokens = JWT::encode([
                    'iss' => $teamId,
                    'iat' => $iat,
                    'exp' => $exp,
                    'aud' => $aud,
                    'sub' => $sub,
                ], $keyContent, 'ES256', $keyId);

                $redirect_uri = $apple_login['redirect_url']??'www.example.com/apple-callback';

                $res = Http::asForm()->post('https://appleid.apple.com/auth/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $unique_id,
                    'redirect_uri' => $redirect_uri,
                    'client_id' => $sub,
                    'client_secret' => $tokens,
                ]);

                $claims = explode('.', $res['id_token'])[1];
                $data = json_decode(base64_decode($claims),true);
            }
        } catch (\Exception $exception) {
            return response()->json(response_formatter(DEFAULT_401), 200);
        }

        if(!isset($claims)){

            if (strcmp($email, $data['email']) != 0 || (!isset($data['id']) && !isset($data['kid']))) {
                return response()->json(['error' => translate('messages.email_does_not_match')],403);
            }
        }

        $user = $this->user->where('email', $data['email'])
            ->ofType(CUSTOMER_USER_TYPES)
            ->first();

        if ($request['medium'] == 'apple') {

            if (!isset($user)) {
                $user = $this->user;
                $user->first_name = implode('@', explode('@', $data['email'], -1));
                $user->last_name = '';
                $user->email = $data['email'];
                $user->phone = null;
                $user->profile_image = 'def.png';
                $user->date_of_birth = date('y-m-d');
                $user->gender = 'others';
                $user->password = bcrypt($request->ip());
                $user->user_type = 'customer';
                $user->is_active = 1;
                $user->save();
            }

            return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, CUSTOMER_PANEL_ACCESS)), 200);
        }

        if ($request['medium'] != 'apple' && strcmp($email, $data['email']) === 0) {
            $user = $this->user->where('email', $request['email'])
                ->ofType(CUSTOMER_USER_TYPES)
                ->first();

            if (!isset($user)) {
                $name = explode(' ', $data['name']);
                if (count($name) > 1) {
                    $fast_name = implode(" ", array_slice($name, 0, -1));
                    $last_name = end($name);
                } else {
                    $fast_name = implode(" ", $name);
                    $last_name = '';
                }

                $user = $this->user;
                $user->first_name = $fast_name;
                $user->last_name = $last_name;
                $user->email = $data['email'];
                $user->phone = null;
                $user->profile_image = 'def.png';
                $user->date_of_birth = date('y-m-d');
                $user->gender = 'others';
                $user->password = bcrypt($request->ip());
                $user->user_type = 'customer';
                $user->is_active = 1;
                $user->save();
            }

            return response()->json(response_formatter(AUTH_LOGIN_200, self::authenticate($user, CUSTOMER_PANEL_ACCESS)), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 401);
    }
    // Close New Code 08-11-23 Pc1

    /**
     * Show the form for creating a new resource.
     * @return array
     */
    protected function authenticate($user, $access_type)
    {
        return ['token' => $user->createToken($access_type)->accessToken, 'is_active' => $user['is_active']];
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user() !== null) {
            $request->user()->token()->revoke();
        }
        return response()->json(response_formatter(AUTH_LOGOUT_200), 200);
    }
}
