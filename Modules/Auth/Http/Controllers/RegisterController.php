<?php

namespace Modules\Auth\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProviderManagement\Entities\Provider;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;
use Modules\ZoneManagement\Entities\Zone;

class RegisterController extends Controller
{
    protected Provider $provider;
    protected User $owner;
    protected $user;
    protected $serviceman;
    protected Zone $zone;

    public function __construct(Provider $provider, User $owner, User $user, Serviceman $serviceman, Zone $zone, Category $category)
    {
        $this->provider = $provider;
        $this->owner = $owner;
        $this->user = $user;
        $this->serviceman = $serviceman;
        $this->zone = $zone;
        $this->category = $category;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function customer_register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'password' => 'required|min:8',
            'gender' => 'in:male,female,others',
            'confirm_password' => 'required|same:password',
            'profile_image' =>  'image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 403);
        }

        $user = $this->user;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->profile_image = $request->has('profile_image') ? file_uploader('user/profile_image/', 'png', $request->profile_image) : 'default.png';
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender??'male';
        $user->password = bcrypt($request->password);
        $user->user_type = 'customer';
        $user->is_active = 1;
        $user->save();

        return response()->json(response_formatter(REGISTRATION_200), 200);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function provider_self_register_form(Request $request): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $zones = $this->zone->where('lang_id', 1)->latest()->get();
        $categories = $this->category->where('lang_id', 1)->ofStatus(1)->ofType('main')->latest()->get();
        return view('auth::provider-register', compact('zones','categories'));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function provider_self_register(Request $request): RedirectResponse
    {
        $checkEmail = $this->user->where('user_type','provider-admin')->where('email',$request->account_email)->count();
        $checkPhone = $this->user->where('user_type','provider-admin')->where('phone',$request->signaccountphone)->count();

        $request->validate([
            'contact_person_name' => 'required',
            'contact_person_phone' => 'required',
            'contact_person_email' => 'required',

            'account_first_name' => 'required',
            'account_last_name' => 'required',
            'zone_id' => 'required|uuid',
            'account_email' => ($checkEmail > 0) ? 'required|email|unique:users,email' : 'required|email',
            'account_phone' => ($checkPhone > 0) ? 'required|unique:users,phone' : 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',

            'company_name' => 'required',
            'company_phone' => 'required|unique:providers',
            'company_address' => 'required',
            'company_email' => 'required|email|unique:providers',
            'logo' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000',

            'identity_type' => 'required|in:passport,driving_licence,nid,trade_license,company_id',
            'identity_number' => 'required',
            'identity_images' => 'required|array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $identity_images = [];
        foreach ($request->identity_images as $image) {
            $identity_images[] = file_uploader('provider/identity/', 'png', $image);
        }


        $provider = $this->provider;
        $provider->company_name = $request->company_name;
        $provider->company_name_arabic = $request->company_name_arabic;
        $provider->company_phone = $request->company_phone;
        $provider->company_email = $request->company_email;
        $provider->logo = file_uploader('provider/logo/', 'png', $request->file('logo'));
        $provider->company_address = $request->company_address;

        $provider->contact_person_name = $request->contact_person_name;
        $provider->contact_person_phone = $request->contact_person_phone;
        $provider->contact_person_email = $request->contact_person_email;
        $provider->is_approved = 2;
        $provider->is_active = 0;
        $provider->zone_id = $request['zone_id'];

        $provider->about_company = !empty($request->about_company) ? $request->about_company : null;
        $provider->start_time = !empty($request->start_time) ? $request->start_time : null;
        $provider->end_time = !empty($request->end_time) ? $request->end_time : null;
        $provider->working_with = !empty($request->working_with) ? $request->working_with : null;
        $provider->company_commission_type = null;
        $provider->company_commission = 0;
        $provider->estimation_commission_type = null;
        $provider->estimation_commission = 0;

        $owner = $this->owner;
        $owner->first_name = $request->account_first_name;
        $owner->last_name = $request->account_last_name;
        $owner->email = $request->account_email;
        $owner->phone = $request->account_phone;
        $owner->identification_number = $request->identity_number;
        $owner->identification_type = $request->identity_type;
        $owner->identification_image = $identity_images;
        $owner->password = bcrypt($request->password);
        $owner->user_type = 'provider-admin';
        $owner->is_active = 0;

        DB::transaction(function () use ($provider, $owner, $request) {
            $owner->save();
            $provider->user_id = $owner->id;
            $provider->save();

            $last_id = $provider->id;
            if(!empty($request->sub_category_id)) {
                $provider['sub_category_id'] = implode(',', $request->sub_category_id);
                $sub_cat_explode = explode(',', $provider['sub_category_id']);
                foreach ($sub_cat_explode as $sub_id) {
                    $provider_sub_cat[] = [
                        'provider_id' => $last_id,
                        'sub_category_id' => $sub_id,
                    ];
                }
                DB::table('provider_sub_category')->insert($provider_sub_cat);
            }
            if(!empty($request->category_id)) {
                $provider['category_id'] = implode(',', $request->category_id);
                $cat_explode = explode(',', $provider['category_id']);
                foreach ($cat_explode as $sub_id) {
                    $provider_cat[] = [
                        'provider_id' => $last_id,
                        'category_id' => $sub_id,
                    ];
                }
                DB::table('provider_category')->insert($provider_cat);
            }
        });

        Toastr::success(PROVIDER_REGISTERED_200['message']);
        return redirect(route('provider.auth.login'));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function provider_register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_phone' => 'required',
            'contact_person_email' => 'required',

            'account_first_name' => 'required',
            'account_last_name' => 'required',
            'zone_id' => 'required|uuid',
            'account_email' => 'required|email|unique:users,email',
            'account_phone' => 'required|unique:users,phone',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',

            'company_name' => 'required',
            'company_phone' => 'required|unique:providers',
            'company_address' => 'required',
            'company_email' => 'required|email|unique:providers',
            'logo' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000',

            'identity_type' => 'required|in:passport,driving_licence,nid,trade_license,company_id',
            'identity_number' => 'required',
            'identity_images' => 'required|array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $identity_images = [];
        foreach ($request->identity_images as $image) {
            $identity_images[] = file_uploader('provider/identity/', 'png', $image);
        }

        $provider = $this->provider;
        $provider->company_name = $request->company_name;
        $provider->company_phone = $request->company_phone;
        $provider->company_email = $request->company_email;
        $provider->logo = file_uploader('provider/logo/', 'png', $request->file('logo'));
        $provider->company_address = $request->company_address;

        $provider->contact_person_name = $request->contact_person_name;
        $provider->contact_person_phone = $request->contact_person_phone;
        $provider->contact_person_email = $request->contact_person_email;
        $provider->is_approved = 2;
        $provider->is_active = 0;
        $provider->zone_id = $request['zone_id'];

        $owner = $this->owner;
        $owner->first_name = $request->account_first_name;
        $owner->last_name = $request->account_last_name;
        $owner->email = $request->account_email;
        $owner->phone = $request->account_phone;
        $owner->identification_number = $request->identity_number;
        $owner->identification_type = $request->identity_type;
        $owner->identification_image = $identity_images;
        $owner->password = bcrypt($request->password);
        $owner->user_type = 'provider-admin';
        $owner->is_active = 0;

        DB::transaction(function () use ($provider, $owner, $request) {
            $owner->save();
            $provider->user_id = $owner->id;
            $provider->save();
        });

        return response()->json(response_formatter(PROVIDER_STORE_200), 200);
    }


    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user_verification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $data = DB::table('user_verifications')
            ->where('identity', $request['identity'])
            ->where(['otp' => $request['otp']])->first();

        if (isset($data)) {
            $this->user->whereIn('user_type', CUSTOMER_USER_TYPES)
                ->where('phone', $request['identity'])
                ->update([
                    'is_phone_verified' => 1
                ]);

            DB::table('user_verifications')
                ->where('identity', $request['identity'])
                ->where(['otp' => $request['otp']])->delete();

            return response()->json(response_formatter(DEFAULT_VERIFIED_200), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }

    public function getSubCatIDs(Request $request): JsonResponse
    {
        $catID = $request->category_id;
        $cat_ids = explode(',', $catID);
//        DB::enableQueryLog();
        $categories = $this->category->where('lang_id', 1)->whereIn('parent_id', $cat_ids)->ofStatus(1)->ofType('sub')->latest()->get();
        $subcatIDs = "";
        if (!empty($categories)) {
            foreach ($categories as $res) {
                $subcatIDs .= "<option value='$res->id'>" . $res->name . "</option>";
            }
        }
        else {
            $subcatIDs = "";
        }
        return response()->json($subcatIDs);
    }
}
