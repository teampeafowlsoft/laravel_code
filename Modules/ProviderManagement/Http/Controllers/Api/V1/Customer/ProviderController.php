<?php

namespace Modules\ProviderManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ProviderManagement\Entities\Provider;
use Modules\ReviewModule\Entities\Review;
use Modules\UserManagement\Entities\Serviceman;
use Modules\ZoneManagement\Entities\Zone;
use Modules\ProviderManagement\Entities\SubscribedService;
use Modules\BookingModule\Entities\Booking;
use Modules\CategoryManagement\Entities\Category;

class ProviderController extends Controller
{
    private $provider,$subscribedService,$booking,$category;

    public function __construct(Provider $provider,Zone $zone,SubscribedService $subscribedService,Booking $booking, Review $review, Serviceman $serviceman,Category $category)
    {
        $this->provider = $provider;
        $this->subscribedService = $subscribedService;
        $this->booking = $booking;
        $this->serviceman = $serviceman;
        $this->review = $review;
        $this->zone = $zone;
        $this->category = $category;
    }

    public function index(Request $request): JsonResponse
    {
        $aa = array();
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'subcategory_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        $categories_id = '';
        $sub_categories = DB::table('categories')->where('id','=',$request->subcategory_id)->first();
        if(!empty($sub_categories)){
            if($sub_categories->lang_id == 1){
                $categories_id = $request->subcategory_id;
            }else{
                $sub_categories_eng = DB::table('categories')->where('group_id','=',$sub_categories->group_id)->where('lang_id','=',1)->first();
                $categories_id = $sub_categories_eng->id;
            }
        }

        $providers = $this->provider->ofStatus(1)
            ->with(['owner', 'zone'])
            ->with(['subscribed_services' => function ($query) use ($request,$categories_id) {
                $query->where('sub_category_id', $categories_id);
            }])
            ->withCount(['subscribed_services', 'bookings','bookings_completed','order_booking'])
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        if (isset($providers) && !empty($providers)) {
            foreach ($providers as $provider) {
                if (count($provider['subscribed_services']))
                {
                    if(($provider['subscribed_services'][0]['sub_category_id']) == $categories_id) {
                        $aa[] = [$provider];
                    }
                }
            }
        }
        return response()->json(response_formatter(DEFAULT_200, $aa), 200);

//        return response()->json(response_formatter(DEFAULT_204), 200);

    }

    public function details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'id' => 'required|uuid',
            'web_page' => 'in:overview,subscribed_services,bookings,serviceman_list,reviews',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

//        $request->validate([
//            'web_page' => 'in:overview,subscribed_services,bookings,serviceman_list,settings,bank_information,reviews',
//        ]);

        $web_page = $request->has('web_page') ? $request['web_page'] : 'overview';

        //overview
        if ($request['web_page'] == 'overview') {

//            $provider = $this->provider->ofStatus(1)
//                ->with(['owner', 'zone'])->ofStatus(1)
//                ->withCount(['subscribed_services', 'bookings'])
////            ->where(['zone_id' => $request['zoneId']])
//                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            $provider = $this->provider->with('owner.account')->withCount(['bookings'])->find($request['id']);

            $booking_overview = DB::table('bookings')->where('provider_id', $request['id'])
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
            return response()->json(response_formatter(DEFAULT_200, [$provider,$total,$web_page]), 200);
//            return response()->json(response_formatter(DEFAULT_200, [$provider,$web_page]), 200);

        }

        //subscribed_services
        elseif ($request['web_page'] == 'subscribed_services') {
            $sub_categories = $this->subscribedService->where('provider_id', $request['id'])
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
                ->latest()->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            return response()->json(response_formatter(DEFAULT_200, [$sub_categories,$web_page]), 200);

        }

        //bookings
        elseif ($request['web_page'] == 'bookings') {
            $bookings = $this->booking->where('provider_id', $request['id'])
                ->with(['customer'])
                ->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->where('readable_id', 'LIKE', '%' . $key . '%');
                    }
                })
                ->latest()->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            return response()->json(response_formatter(DEFAULT_200, [$bookings,$web_page]), 200);

        }

        //serviceman_list
        elseif ($request['web_page'] == 'serviceman_list') {
            $servicemen = $this->serviceman
                ->with(['user'])
                ->where('provider_id', $request['id'])
                ->latest()->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
            return response()->json(response_formatter(DEFAULT_200, [$servicemen,$web_page]), 200);

        }

        //reviews
        elseif ($request['web_page'] == 'reviews') {
//            $provider = $this->provider->with(['reviews'])->where('user_id', $request->user()->id)->first();
            $reviews = $this->review->with(['booking'])
                ->where('provider_id', $request['id'])
                ->latest()->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            $provider = $this->provider->with('owner.account')->withCount(['bookings'])->find($request['id']);

            $booking_overview = DB::table('bookings')
                ->where('provider_id', $request['id'])
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

            return response()->json(response_formatter(DEFAULT_200, [$provider,$web_page,$reviews,$total]), 200);

        }
//        return back();
    }


}
