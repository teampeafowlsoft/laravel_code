<?php

namespace Modules\ServiceManagement\Http\Controllers\Api\V1\Customer;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CategoryManagement\Entities\Maincategories;
use Modules\ReviewModule\Entities\Review;
use Modules\ServiceManagement\Entities\RecentSearch;
use Modules\ServiceManagement\Entities\RecentView;
use Modules\ServiceManagement\Entities\Service;

class ServiceController extends Controller
{
    private $service;
    private Review $review;
    private RecentView $recent_view;
    private RecentSearch $recent_search;
    private $main_category;

    public function __construct(Service $service, Review $review, RecentView $recent_view, RecentSearch $recent_search, Maincategories $main_category)
    {
        $this->service = $service;
        $this->review = $review;
        $this->recent_view = $recent_view;
        $this->recent_search = $recent_search;
        $this->main_category = $main_category;
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
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->withoutGlobalScopes()
            ->where('lang_id',$lang_id)
            ->where('is_active',1)
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
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
        $services = $this->service->withoutGlobalScopes()->with(['category.zonesBasicInfo', 'variations'])
            ->where(function ($query) use ($keys) {
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->where('lang_id',$lang_id)
            ->active()
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
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

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->withoutGlobalScopes()
            ->orderBy('order_count', 'DESC')
            ->where('lang_id',$lang_id)
            ->where('is_active',1)
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
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

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->withoutGlobalScopes()
            ->orderBy('avg_rating', 'DESC')
            ->where('lang_id',$lang_id)
            ->where('is_active',1)
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function search_recommended(Request $request): JsonResponse
    {

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $services = $this->service->select('id', 'name')
            ->active()
            ->withoutGlobalScopes()
            ->where('lang_id',$lang_id)
            ->inRandomOrder()
            ->take(5)->get();

        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
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

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->active()
            ->orderBy('avg_rating', 'DESC')
            ->where('created_at', '>', now()->subDays(30)->endOfDay())
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }

    /**
     * Recently viewed by customer (service view based)
     * @param Request $request
     * @return JsonResponse
     */
    public function recently_viewed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $service_ids = $this->recent_view
            ->where('user_id', $request->user()->id)
            ->select(
                DB::raw('count(total_service_view) as total_service_view'),
                DB::raw('service_id as service_id')
            )
            ->groupBy('total_service_view', 'service_id')
            ->pluck('service_id')
            ->toArray();

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->withoutGlobalScopes()
            ->where('lang_id',$lang_id)
            ->whereIn('id', $service_ids)
            ->where('is_active',1)
            ->orderBy('avg_rating', 'DESC')
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);
    }

    /**
     * Recently searched keywords by customer
     * @param Request $request
     * @return JsonResponse
     */
    public function recently_searched_keywords(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $searched_keywords = $this->recent_search
            ->where('user_id', $request->user()->id)
            ->select('id', 'keyword')
            ->withoutGlobalScopes()
            ->where('lang_id',$lang_id)
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        if (count($searched_keywords) > 0) {
            return response()->json(response_formatter(DEFAULT_200, $searched_keywords), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 404);
    }

    /**
     * Remove searched keywords by customer
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_searched_keywords(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'array',
            'id.*' => 'uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $this->recent_search
            ->where('user_id', $request->user()->id)
            ->when($request->has('id'), function ($query) use ($request) {
                $query->whereIn('id', $request->id);
            })
            ->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function offers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->whereHas('service_discount')->orWhereHas('category.category_discount')->active()
            ->orderBy('avg_rating', 'DESC')
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

    /**
     * Show the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request,string $id): JsonResponse
    {
        //echo $id;
//        $services = $this->service
//            ->where('id', $id)
//            ->with(['category.zonesBasicInfo','category.children', 'variations', 'faqs' => function ($query) {
//                return $query->where('is_active', 1);
//            }])
//            ->ofStatus(1)
//            ->first();

            $service = $this->service->with(['variations', 'category.children', 'faqs' => function ($query) {
                return $query->where('is_active', 1);
            }])
            ->withoutGlobalScopes()
            ->where('is_active',1)
            ->where('id', $id)
            ->first();

        if (isset($service)) {
            //update service view count
            $auth_user = auth('api')->user();
            if ($auth_user) {
                $recent_view = $this->recent_view->firstOrNew(['service_id' =>  $service->id, 'user_id' => $auth_user->id]);
                $recent_view->total_service_view += 1;
                $recent_view->save();
            }

            $service['variations_app_format'] = self::variations_app_format($service);
            return response()->json(response_formatter(DEFAULT_200, $service), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param string $service_id
     * @return JsonResponse
     */
    public function review(Request $request, string $service_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $reviews = $this->review->with(['provider', 'customer'])->where('service_id', $service_id)->ofStatus(1)->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        $rating_group_count = DB::table('reviews')->where('service_id', $service_id)
            ->select('review_rating', DB::raw('count(*) as total'))
            ->groupBy('review_rating')
            ->get();

        $total_rating = 0;
        $rating_count = 0;
        foreach ($rating_group_count as $count) {
            $total_rating += round($count->review_rating * $count->total, 2);
            $rating_count += $count->total;
        }

        $rating_info = [
            'rating_count' => $rating_count,
            'average_rating' => round(divnum($total_rating, $rating_count), 2),
            'rating_group_count' => $rating_group_count,
        ];

        if ($reviews->count() > 0) {
            return response()->json(response_formatter(DEFAULT_200, ['reviews' => $reviews, 'rating' => $rating_info]), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param string $sub_category_id
     * @return JsonResponse
     */
    public function services_by_subcategory(Request $request, string $sub_category_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);
//        return response()->json(response_formatter(DEFAULT_200, $sub_category_id), 200);
        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
            ->withoutGlobalScopes()
            ->where(['sub_category_id' => $sub_category_id])
            ->where(['is_active' => 1])
            ->groupBy('group_id')
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        if (count($services) > 0) {
            //update sub-category view count
            $auth_user = auth('api')->user();
            if ($auth_user) {
                $recent_view = $this->recent_view->firstOrNew(['sub_category_id' =>  $sub_category_id, 'user_id' => $auth_user->id]);
                $recent_view->total_sub_category_view += 1;
                $recent_view->save();
            }

            return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);

        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param string $main_category_id
     * @return JsonResponse
     */
    public function service_by_maincategory(Request $request, string $main_category_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        //First Get ID Service ID from main category table:
        $main_category = $this->main_category->where('id',$main_category_id)
            ->where('lang_id', $lang_id)
            ->where('is_active', '1')
            ->withoutGlobalScopes()
            ->first();

        if (!empty($main_category)){
            $services = $this->service->with(['category.zonesBasicInfo', 'variations'])
                ->where(['category_id' => $main_category->service_id])
                ->groupBy('group_id')
                ->where(['is_active' => 1])
                ->withoutGlobalScopes()
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

            if (count($services) > 0) {
                //update sub-category view count
                $auth_user = auth('api')->user();
                if ($auth_user) {
                    $recent_view = $this->recent_view->firstOrNew(['category_id' =>  $main_category->service_id, 'user_id' => $auth_user->id]);
                    $recent_view->total_sub_category_view += 1;
                    $recent_view->save();
                }

                return response()->json(response_formatter(DEFAULT_200, self::variation_mapper($services)), 200);

            }
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }
}
