<?php

namespace Modules\ProviderManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\ProductManagement\Entities\ProductCartBooking;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProviderManagement\Entities\BankDetail;
use Modules\ProviderManagement\Entities\Provider;
use Modules\ProviderManagement\Entities\SubscribedService;
use Modules\ReviewModule\Entities\Review;
use Modules\ServiceManagement\Entities\Service;
use Modules\TransactionModule\Entities\Account;
use Modules\TransactionModule\Entities\Transaction;
use Modules\UserManagement\Entities\Serviceman;
use Modules\UserManagement\Entities\User;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProviderController extends Controller
{
    protected Provider $provider;
    protected User $owner;
    protected User $user;
    protected $service;
    protected $subscribedService;
    private Booking $booking;
    private ProductCartBooking $productcartbooking;
    private $serviceman;
    private $review;
    protected Transaction $transaction;
    protected Zone $zone;
    protected BankDetail $bank_detail;

    public function __construct(Transaction $transaction, Review $review, Serviceman $serviceman, Provider $provider, User $owner, Service $service, SubscribedService $subscribedService, Booking $booking, ProductCartBooking $productcartbooking, Zone $zone, BankDetail $bank_detail, Category $category)
    {
        $this->provider = $provider;
        $this->owner = $owner;
        $this->user = $owner;
        $this->service = $service;
        $this->subscribedService = $subscribedService;
        $this->booking = $booking;
        $this->productcartbooking = $productcartbooking;
        $this->serviceman = $serviceman;
        $this->review = $review;
        $this->transaction = $transaction;
        $this->zone = $zone;
        $this->bank_detail = $bank_detail;
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        Validator::make($request->all(), [
            'search' => 'string',
            'status' => 'required|in:active,inactive,all'
        ]);

        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $providers = $this->provider->with(['owner', 'zone'])->where(['is_approved' => 1])->withCount(['subscribed_services', 'bookings'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('company_phone', 'LIKE', '%' . $key . '%')
                            ->orWhere('company_email', 'LIKE', '%' . $key . '%')
                            ->orWhere('company_name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->ofApproval(1)
            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
            })->latest()
            ->paginate(pagination_limit())->appends($query_param);

        $top_cards = [];
        $top_cards['total_providers'] = $this->provider->ofApproval(1)->count();
        $top_cards['total_onboarding_requests'] = $this->provider->ofApproval(2)->count();
        $top_cards['total_active_providers'] = $this->provider->ofApproval(1)->ofStatus(1)->count();
        $top_cards['total_inactive_providers'] = $this->provider->ofApproval(1)->ofStatus(0)->count();

        return view('providermanagement::admin.provider.index', compact('providers', 'top_cards', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $categories = $this->category->where('lang_id', 1)->ofStatus(1)->ofType('main')->latest()->get();
        $zones = $this->zone->where('lang_id', 1)->latest()->get();
        return view('providermanagement::admin.provider.create', compact('zones', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {

        $checkEmail = $this->owner->where('user_type','provider-admin')->where('email',$request->account_email)->count();
        $checkPhone = $this->owner->where('user_type','provider-admin')->where('phone',$request->signaccountphone)->count();


        $request->validate([
            'contact_person_name' => 'required',
            'contact_person_phone' => 'required',
            'contact_person_email' => 'required',

            'account_first_name' => 'required',
            'account_last_name' => 'required',
            'account_email' => ($checkEmail > 0) ? 'required|email|unique:users,email' : 'required|email',
            'account_phone' => ($checkPhone > 0) ? 'required|unique:users,phone' : 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',

            'company_name' => 'required',
            'company_phone' => 'required|unique:providers',
            'company_address' => 'required',
            'company_email' => 'required|email|unique:providers',
            'logo' => 'required|image|mimes:jpeg,jpg,png,gif',

            'identity_type' => 'required|in:passport,driving_license,nid,trade_license,company_id',
            'identity_number' => 'required',
            'identity_images' => 'array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif',

            'zone_id' => 'required|uuid',
        ]);
        $identity_images = [];
        if ($request->has('identity_images')) {
            foreach ($request->identity_images as $image) {
                $identity_images[] = file_uploader('provider/identity/', 'png', $image);
            }
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
        $provider->destination_id = $request->destination_id;
        $provider->response_business_id = $request->response_business_id;
        $provider->business_entity_id = $request->business_entity_id;
        $provider->is_approved = 1;
        $provider->is_active = 1;
        $provider->zone_id = $request['zone_id'];

        $provider->short_description = $request->short_description;
        $provider->about_company = !empty($request->about_company) ? $request->about_company : null;
        $provider->start_time = !empty($request->start_time) ? $request->start_time : null;
        $provider->end_time = !empty($request->end_time) ? $request->end_time : null;
        $provider->working_with = !empty($request->working_with) ? $request->working_with : null;
        $provider->company_commission_type = !empty($request->company_commission_type) ? $request->company_commission_type : null;
        $provider->company_commission = !empty($request->company_commission) ? $request->company_commission : 0;
        $provider->estimation_commission_type = !empty($request->estimation_commission_type) ? $request->estimation_commission_type : null;
        $provider->estimation_commission = !empty($request->estimation_commission) ? $request->estimation_commission : 0;

        $owner = $this->owner;
        $owner->first_name = $request->account_first_name;
        $owner->last_name = $request->account_last_name;
        $owner->email = $request->account_email;
//        $owner->phone = $request->account_phone;
        $owner->phone = $request->signaccountphone;
        $owner->country_code = $request->sign.$request->country_code;
        $owner->mobile = $request->account_phone;
        $owner->identification_number = $request->identity_number;
        $owner->identification_type = $request->identity_type;
        $owner->is_active = 1;
        $owner->identification_image = $identity_images;
        $owner->password = bcrypt($request->password);
        $owner->user_type = 'provider-admin';

        DB::transaction(function () use ($provider, $owner, $request) {
            $owner->save();
            $owner->zones()->sync($request->zone_id);
            $provider->user_id = $owner->id;
            $provider->save();

            $last_id = $provider->id;

//            if(!empty($request->sub_category_id)) {
//                $provider['sub_category_id'] = implode(',', $request->sub_category_id);
//                $sub_cat_explode = explode(',', $provider['sub_category_id']);
//                foreach ($sub_cat_explode as $sub_id) {
//                    $provider_sub_cat[] = [
//                        'provider_id' => $last_id,
//                        'sub_category_id' => $sub_id,
//                    ];
//                }
//                DB::table('provider_sub_category')->insert($provider_sub_cat);
//            }
//
//            if(!empty($request->category_id)) {
//                $provider['category_id'] = implode(',', $request->category_id);
//                $cat_explode = explode(',', $provider['category_id']);
//                foreach ($cat_explode as $sub_id) {
//                    $provider_cat[] = [
//                        'provider_id' => $last_id,
//                        'category_id' => $sub_id,
//                    ];
//                }
//                DB::table('provider_category')->insert($provider_cat);
//            }
        });

        Toastr::success(CAMPAIGN_UPDATE_200['message']);
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return RedirectResponse
     */
    public function details($id, Request $request)
    {
        $request->validate([
            'web_page' => 'in:overview,subscribed_services,bookings,order,serviceman_list,settings,bank_information,reviews',
        ]);

        $web_page = $request->has('web_page') ? $request['web_page'] : 'overview';

        //overview
        if ($request->web_page == 'overview') {
            $provider = $this->provider->with('owner.account')->withCount(['bookings'])->find($id);
            $booking_overview = DB::table('bookings')->where('provider_id', $id)
                ->select('booking_status', DB::raw('count(*) as total'))
                ->groupBy('booking_status')
                ->get();

            $status = ['accepted', 'ongoing', 'completed', 'canceled'];
            $total = [];
            foreach ($status as $item) {
                if ($booking_overview->where('booking_status', $item)->first() !== null) {
                    $total[] = $booking_overview->where('booking_status', $item)->first()->total;
                } else {
                    $total[] = 0;
                }
            }

            return view('providermanagement::admin.provider.detail.overview', compact('provider', 'web_page', 'total'));

        } //subscribed_services
        elseif ($request->web_page == 'subscribed_services') {
            $search = $request->has('search') ? $request['search'] : '';
            $status = $request->has('status') ? $request['status'] : 'all';
            $query_param = ['web_page' => $web_page, 'status' => $status, 'search' => $search];


            $sub_categories = $this->subscribedService->where('provider_id', $id)
                ->with(['sub_category' => function ($query) {
                    return $query->withCount('services')->with(['services']);
                }])
                ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                    return $query->where('is_subscribed', (($request['status'] == 'subscribed') ? 1 : 0));
                })
                ->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->orWhereHas('sub_category', function ($query) use ($key) {
                            $query->where('name', 'LIKE', '%' . $key . '%');
                        });
                    }
                })
                ->latest()->paginate(pagination_limit())->appends($query_param);

            //$subscribed_services = $this->subscribedService->with(['sub_category'])->withCount(['services'])->where('provider_id', $id)->latest()->paginate(pagination_limit())->appends($query_param);

            return view('providermanagement::admin.provider.detail.subscribed-services', compact('sub_categories', 'web_page', 'status', 'search'));

        } //bookings
        elseif ($request->web_page == 'bookings') {

            $search = $request->has('search') ? $request['search'] : '';
            $query_param = ['web_page' => $web_page, 'search' => $search];

            $bookings = $this->booking->where('provider_id', $id)
                ->with(['customer'])
                ->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->where('readable_id', 'LIKE', '%' . $key . '%');
                    }
                })
                ->latest()
                ->paginate(pagination_limit())->appends($query_param);

            return view('providermanagement::admin.provider.detail.bookings', compact('bookings', 'web_page', 'search'));

        }
        //order bookings
        elseif ($request->web_page == 'order') {

            $search = $request->has('search') ? $request['search'] : '';
            $query_param = ['web_page' => $web_page, 'search' => $search];

            $bookings = $this->productcartbooking->where('provider_id', $id)
                ->with(['customer'])
                ->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->where('readable_id', 'LIKE', '%' . $key . '%');
                    }
                })
                ->latest()
                ->paginate(pagination_limit())->appends($query_param);

            return view('providermanagement::admin.provider.detail.orderbooking', compact('bookings', 'web_page', 'search'));

        }
        //serviceman_list
        elseif ($request->web_page == 'serviceman_list') {
            $query_param = ['web_page' => $web_page];

            $servicemen = $this->serviceman
                ->with(['user'])
                ->where('provider_id', $id)
                ->latest()
                ->paginate(pagination_limit())->appends($query_param);

            return view('providermanagement::admin.provider.detail.serviceman-list', compact('servicemen', 'web_page'));

        } //settings
        elseif ($request->web_page == 'settings') {
            $provider = $this->provider->find($id);
            return view('providermanagement::admin.provider.detail.settings', compact('web_page', 'provider'));

        } //bank_info
        elseif ($request->web_page == 'bank_information') {
            $provider = $this->provider->with('owner.account', 'bank_detail')->find($id);
            return view('providermanagement::admin.provider.detail.bank-information', compact('web_page', 'provider'));

        } //reviews
        elseif ($request->web_page == 'reviews') {

            $search = $request->has('search') ? $request['search'] : '';
            $query_param = ['search' => $search, 'web_page' => $request['web_page']];

            $provider = $this->provider->with(['reviews'])->where('user_id', $request->user()->id)->first();
            $reviews = $this->review->with(['booking'])
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->whereHas('booking', function ($query) use ($request) {
                        $keys = explode(' ', $request['search']);
                        foreach ($keys as $key) {
                            $query->orWhere('readable_id', 'LIKE', '%' . $key . '%');
                        }
                    });
                })
                ->where('provider_id', $id)
                ->latest()
                ->paginate(pagination_limit())->appends($query_param);

            $provider = $this->provider->with('owner.account')->withCount(['bookings'])->find($id);

            $booking_overview = DB::table('bookings')
                ->where('provider_id', $id)
                ->select('booking_status', DB::raw('count(*) as total'))
                ->groupBy('booking_status')
                ->get();

            $status = ['accepted', 'ongoing', 'completed', 'canceled'];
            $total = [];
            foreach ($status as $item) {
                if ($booking_overview->where('booking_status', $item)->first() !== null) {
                    $total[] = $booking_overview->where('booking_status', $item)->first()->total;
                } else {
                    $total[] = 0;
                }
            }

            return view('providermanagement::admin.provider.detail.reviews', compact('web_page', 'provider', 'reviews', 'search', 'provider', 'total'));

        }
        return back();
    }


    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_account_info($id, Request $request): RedirectResponse
    {
        $this->bank_detail::updateOrCreate(
            ['provider_id' => $id],
            [
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'acc_no' => $request->acc_no,
                'acc_holder_name' => $request->acc_holder_name,
            ]
        );

        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }


    /**
     * Show the form for editing the specified resource.
     * @param $id
     * @return JsonResponse
     */
    public function delete_account_info($id, Request $request): JsonResponse
    {
        $provider = $this->provider->with(['bank_detail'])->find($id);

        if (!$provider->bank_detail) {
            return response()->json(DEFAULT_404, 200);
        }
        $provider->bank_detail->delete();
        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }


    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function update_subscription($id): JsonResponse
    {
        $subscribedService = $this->subscribedService->find($id);
        $this->subscribedService->where('id', $id)->update(['is_subscribed' => !$subscribedService->is_subscribed]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }


    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id)
    {
        $categories = $this->category->where('lang_id', 1)->ofStatus(1)->ofType('main')->latest()->get();
        $sub_categories = $this->category->where('lang_id', 1)->ofStatus(1)->ofType('sub')->latest()->get();
        $catID = DB::table('provider_category')->selectRaw("provider_id,category_id")
            ->where('provider_id',$id)
            ->get();
        $subcatID = DB::table('provider_sub_category')->selectRaw("provider_id,sub_category_id")
            ->where('provider_id',$id)
            ->get();
//        $zones = $this->zone->all();
        $zones = $this->zone->where('lang_id', 1)->latest()->get();
        $provider = $this->provider->with(['owner', 'zone'])->find($id);
        return view('providermanagement::admin.provider.edit', compact('provider', 'zones','categories','catID','sub_categories','subcatID'));
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $provider = $this->provider->with('owner')->find($id);

        $checkEmail = $this->owner->where('user_type','provider-admin')->where('email',$request->account_email)->count();
        $checkPhone = $this->owner->where('user_type','provider-admin')->where('phone',$request->signaccountphone)->count();
        $providerId = $provider->user_id;
        $provider_check = $this->owner->where('user_type','provider-admin')->where('id',$providerId)->first();

        Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_phone' => 'required',
            'contact_person_email' => 'required',

            'password' => !is_null($request->password) ? 'string|min:8' : '',
            'confirm_password' => !is_null($request->password) ? 'required|same:password' : '',
            'account_first_name' => 'required',
            'account_last_name' => 'required',
            'account_email' => ($checkEmail > 0) ?
                ($request->account_email === $provider_check->account_email) ?
                    'required|unique:users,email,' . $provider->user_id . ',id'
                    : '' :
                'required|email',
            'account_phone' => ($checkEmail > 0) ? ($request->signaccountphone === $provider_check->account_phone) ?'required|unique:users,phone,' . $provider->user_id . ',id' : '' : 'required',

            'company_name' => 'required',
            'company_phone' => 'required|unique:providers,company_phone,' . $provider->id . ',id',
            'company_address' => 'required',
            'company_email' => 'required|email|unique:providers,company_email,' . $provider->id . ',id',
            'logo' => 'image|mimes:jpeg,jpg,png,gif',
//            'logo' => 'image|mimes:jpeg,jpg,png,gif|max:10000',

            'identity_type' => 'required|in:passport,driving_license,nid,trade_license,company_id',
            'identity_number' => 'required',
            'identity_images' => 'array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif',

            'zone_id' => 'required|uuid'
        ])->validate();

        $identity_images = [];
        if (!is_null($request->identity_images)) {
            foreach ($request->identity_images as $image) {
                $identity_images[] = file_uploader('provider/identity/', 'png', $image);
            }
        }

        $provider->company_name = $request->company_name;
        $provider->company_name_arabic = $request->company_name_arabic;
        $provider->company_phone = $request->company_phone;
        $provider->company_email = $request->company_email;
        if ($request->has('logo')) {
            $provider->logo = file_uploader('provider/logo/', 'png', $request->file('logo'));
        }
        $provider->company_address = $request->company_address;
        $provider->contact_person_name = $request->contact_person_name;
        $provider->contact_person_phone = $request->contact_person_phone;
        $provider->contact_person_email = $request->contact_person_email;
        $provider->destination_id = $request->destination_id;
        $provider->response_business_id = $request->response_business_id;
        $provider->business_entity_id = $request->business_entity_id;
        $provider->zone_id = $request['zone_id'];

        $provider->short_description = $request->short_description;
        $provider->about_company = !empty($request->about_company) ? $request->about_company : null;
        $provider->start_time = !empty($request->start_time) ? $request->start_time : null;
        $provider->end_time = !empty($request->end_time) ? $request->end_time : null;
        $provider->working_with = !empty($request->working_with) ? $request->working_with : null;
        $provider->company_commission_type = !empty($request->company_commission_type) ? $request->company_commission_type : null;
        $provider->company_commission = !empty($request->company_commission) ? $request->company_commission : 0;
        $provider->estimation_commission_type = !empty($request->estimation_commission_type) ? $request->estimation_commission_type : null;
        $provider->estimation_commission = !empty($request->estimation_commission) ? $request->estimation_commission : 0;

        $owner = $provider->owner()->first();
        $owner->first_name = $request->account_first_name;
        $owner->last_name = $request->account_last_name;
        $owner->email = $request->account_email;
//        $owner->phone = $request->account_phone;
        $owner->phone = $request->signaccountphone;
        $owner->country_code = $request->sign.$request->country_code;
        $owner->mobile = $request->account_phone;
        $owner->identification_number = $request->identity_number;
        $owner->identification_type = $request->identity_type;
        if (count($identity_images) > 0) {
            $owner->identification_image = $identity_images;
        }
        if (!is_null($request->password)) {
            $owner->password = bcrypt($request->password);
        }
        $owner->user_type = 'provider-admin';

        DB::transaction(function () use ($provider, $owner, $request) {
            $owner->save();
            $owner->zones()->sync($request->zone_id);
            $provider->save();

//            if(!empty($request->sub_category_id)) {
//                $provider['sub_category_id'] = implode(',', $request->sub_category_id);
//                $sub_cat_explode = explode(',', $provider['sub_category_id']);
//                foreach ($sub_cat_explode as $sub_id) {
//                    $provider_sub_cat[] = [
//                        'provider_id' => $provider->id,
//                        'sub_category_id' => $sub_id,
//                    ];
//                }
//                DB::table('provider_sub_category')->where('provider_id', $provider->id)->delete();
//                DB::table('provider_sub_category')->insert($provider_sub_cat);
//            }

//            if(!empty($request->category_id)) {
//                $provider['category_id'] = implode(',', $request->category_id);
//                $cat_explode = explode(',', $provider['category_id']);
//                foreach ($cat_explode as $sub_id) {
//                    $provider_cat[] = [
//                        'provider_id' => $provider->id,
//                        'category_id' => $sub_id,
//                    ];
//                }
//                DB::table('provider_category')->where('provider_id', $provider->id)->delete();
//                DB::table('provider_category')->insert($provider_cat);
//            }
        });

        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        Validator::make($request->all(), [
            'provider_id' => 'required'
        ]);

        $providers = $this->provider->where('id', $id);
        if ($providers->count() > 0) {
            foreach ($providers->get() as $provider) {
                file_remover('provider/logo/', $provider->logo);
                if (!empty($provider->owner->identification_image)) {
                    foreach ($provider->owner->identification_image as $image) {
                        file_remover('provider/identity/', $image);
                    }
                }
                $provider->owner()->delete();
            }
            $providers->delete();
            Toastr::success(DEFAULT_DELETE_200['message']);
            return back();
        }

        Toastr::error(DEFAULT_FAIL_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return JsonResponse
     */
    public function status_update($id): JsonResponse
    {
        $provider = $this->provider->where('id', $id)->first();
        $this->provider->where('id', $id)->update(['is_active' => !$provider->is_active]);
        $this->owner->where('id', $provider->user_id)->update(['is_active' => !$provider->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return RedirectResponse
     */
    public function commission_update($id, Request $request)
    {
        $provider = $this->provider->where('id', $id)->first();
        $provider->commission_status = $request->commission_status == 'default' ? 0 : 1;
        if ($request->commission_status == 'custom') {
            $provider->commission_percentage = $request->custom_commission_value;
            $provider->commission_percentage_product = $request->product_commission_value;
        }
        $provider->save();

        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    public function onboarding_request(Request $request)
    {
        $status = $request->status == 'denied' ? 'denied' : 'onboarding';
        $search = $request['search'];
        $query_param = ['status' => $status, 'search' => $request['search']];

        $providers = $this->provider->with(['owner', 'zone'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('company_name', 'LIKE', '%' . $key . '%')
                        ->orWhere('contact_person_name', 'LIKE', '%' . $key . '%')
                        ->orWhere('contact_person_phone', 'LIKE', '%' . $key . '%')
                        ->orWhere('contact_person_email', 'LIKE', '%' . $key . '%');
                }
            })
            ->ofApproval($status == 'onboarding' ? 2 : 0)
            ->latest()
            ->paginate(pagination_limit())
            ->appends($query_param);

        $providers_count = [
            'onboarding' => $this->provider->ofApproval(2)->get()->count(),
            'denied' => $this->provider->ofApproval(0)->get()->count(),
        ];

        return View('providermanagement::admin.provider.onboarding', compact('providers', 'search', 'status', 'providers_count'));
    }

    public function update_approval($id, $status, Request $request)
    {
        if ($status == 'approve') {
            $this->provider->where('id', $id)->update(['is_active' => 1, 'is_approved' => 1]);
            $provider = $this->provider->with('owner')->where('id', $id)->first();
            $provider->owner->is_active = 1;
            $provider->owner->save();

        } elseif ($status == 'deny') {
            $this->provider->where('id', $id)->update(['is_active' => 0, 'is_approved' => 0]);
            $provider = $this->provider->with('owner')->where('id', $id)->first();
            $provider->owner->is_active = 0;
            $provider->owner->save();

        } else {
            return response()->json(DEFAULT_400, 200);
        }

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->provider->with(['owner', 'zone'])->where(['is_approved' => 1])->withCount(['subscribed_services', 'bookings'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('company_phone', 'LIKE', '%' . $key . '%')
                            ->orWhere('company_email', 'LIKE', '%' . $key . '%')
                            ->orWhere('company_name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->ofApproval(1)
            ->when($request->has('status') && $request['status'] != 'all', function ($query) use ($request) {
                return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
            })->latest()
            ->latest()->get();

        return (new FastExcel($items))->download(time() . '-file.xlsx');
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

    // 03-11-23 Pc1
    public function create_business(Request $request,$id){
        if(!empty($id)){
            $providers = $this->provider->with('owner')->where('id',$id)->first();
//            dd($providers);
            if(!empty($providers) && $providers != null){

                // Business Create
                $business_create_data = array(
                    "name"=> array(
                        "en"=> $providers->company_name,
                        "ar"=> $providers->company_name_arabic
                    ),
                    "type"=> "corp",//ind
                    "entity"=> array(
                        "legal_name"=> array(
                            "en"=> $providers->company_name,
                            "ar"=> $providers->company_name_arabic
                        ),
                        "country"=> "KW"
                    ),
                    "contact_person"=> array(
                        "name"=> array(
                            "title"=> $providers->company_name,
                            "first"=> $providers->owner->first_name,
                            "middle"=> $providers->owner->first_name,
                            "last"=>  $providers->owner->last_name,
                        ),
                        "contact_info"=> array(
                            "primary"=> array(
                                "email"=> $providers->contact_person_email ,
                                "phone"=> array(
                                    "country_code"=> "965",
                                    "number"=> $providers->owner->mobile
                                )
                            )
                        )
                    ),
                    "brands"=>  [
                        array(
                            "name"=> array(
                                "en"=> $providers->company_name,
                                "ar"=> $providers->company_name_arabic
                            )
                        )
                    ]
                );


                $curl = curl_init();
                $url = "https://api.tap.company/v2/business";
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_ENCODING, "");
                curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
                curl_setopt($curl, CURLOPT_TIMEOUT, 30);
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($business_create_data));
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    "authorization: Bearer sk_live_BueKoLimpxGnlgIDFd0OrENh",//sk_test_BPmcTgEfuK1dHslMaLGY42Ry",
                    "content-type: application/json"
                ));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                if($err){
                    Toastr::error(PROVIDER_420['message']);
                    return redirect(route('admin.provider.list'));
                }else{
                    $response = json_decode($output,true);
                    if(!empty($response)){
                        if(array_key_exists("errors",$response)){
                            Toastr::error(PROVIDER_420['message']);
                            return redirect(route('admin.provider.list'));
                        }else{
                            $providers->destination_id = $response['destination_id'];
                            $providers->response_business_id = $response['id'];
                            $providers->business_entity_id = $response['entity']['id'];
                            $providers->save();
                            Toastr::success(PROVIDER_STORE_200['message']);
                            return redirect(route('admin.provider.list'));
                        }
                    }
                }
            }

        }
        Toastr::error(PROVIDER_400['message']);
        return redirect(route('admin.provider.list'));
    }
    // 03-11-23 Close

    // 27-11-2023 Pc1
    public function deleteIdentification(Request $request,$rm,$uid){

        $userList = User::where('id',$uid)->first();

        if(!empty($userList)){
            $identificationImage = $userList->identification_image;

            $key = array_search($rm, $identificationImage);

            if ($key !== false) {
                $path = 'app/public/provider/identity/' . $rm;
                Storage::delete($path);
                unset($identificationImage[$key]);
            }

            $identificationSave = array_values($identificationImage);

            $userList->identification_image = $identificationSave;
            $userList->save();
        }
        return response()->json(['message' => 'Image deleted successfully','status'=>1],200);
    }
    // Close 27-11-2023 Close
}
