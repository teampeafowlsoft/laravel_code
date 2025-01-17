<?php

namespace Modules\PromotionManagement\Http\Controllers\Web\Admin;

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
use Modules\CategoryManagement\Entities\Category;
use Modules\PromotionManagement\Entities\Coupon;
use Modules\PromotionManagement\Entities\Discount;
use Modules\PromotionManagement\Entities\DiscountType;
use Modules\ServiceManagement\Entities\Service;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CouponController extends Controller
{
    protected $discount, $coupon, $discountType, $service, $category, $zone, $discount_types;

    public function __construct(Coupon $coupon, Discount $discount, DiscountType $discountType, Service $service, Category $category, Zone $zone, DiscountType $discount_types)
    {
        $this->discount = $discount;
        $this->discountQuery = $discount->ofPromotionTypes('coupon');
        $this->coupon = $coupon;
        $this->discountType = $discountType;
        $this->service = $service;
        $this->category = $category;
        $this->zone = $zone;
        $this->discount_types = $discount_types;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Factory|View|Application
     */
    public function index(Request $request): Factory|View|Application
    {
        $search = $request->has('search') ? $request['search'] : '';
        $discount_type = $request->has('discount_type') ? $request['discount_type'] : 'all';
        $query_param = ['search' => $search, 'discount_type' => $discount_type];

        $coupons = $this->coupon->with(['discount', 'discount.category_types', 'discount.service_types', 'discount.zone_types'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('coupon_code', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('coupon_type') && $request['coupon_type'] != 'all', function ($query) use ($request) {
                return $query->where(['coupon_type' => $request['coupon_type']]);
            })->when($request->has('discount_type') && $request['discount_type'] != 'all', function ($query) use ($request) {
                return $query->whereHas('discount', function ($query) use ($request) {
                    $query->where(['discount_type' => $request['discount_type']]);
                });
            })->latest()->paginate(pagination_limit())->appends($query_param);

        return view('promotionmanagement::admin.coupons.list', compact('coupons', 'search', 'discount_type'));
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request): View|Factory|Application
    {
        $categories = $this->category->ofStatus(1)->ofType('main')->where('lang_id',1)->latest()->get();
        $zones = $this->zone->ofStatus(1)->where('lang_id',1)->latest()->get();
        $services = $this->service->where('lang_id',1)->active()->latest()->get();

        return view('promotionmanagement::admin.coupons.create', compact('categories', 'zones', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'coupon_code' => 'required|unique:coupons',
            'discount_type' => 'required|in:category,service,zone,mixed',
            'discount_title' => 'required',
            'coupon_type' => 'required|in:' . implode(',', array_keys(COUPON_TYPES)),
            'discount_amount' => 'required|numeric',
            'discount_amount_type' => 'required|in:percent,amount',
            'min_purchase' => 'required|numeric',
            'max_discount_amount' => $request['discount_amount_type'] == 'amount' ? '' : 'required' . '|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'limit_per_user' => 'numeric',
        ]);

        DB::transaction(function () use ($request) {
            $discount = $this->discount;
            $discount->discount_type = $request['discount_type'];
            $discount->discount_title = $request['discount_title'];
            $discount->discount_amount = $request['discount_amount'];
            $discount->discount_amount_type = $request['discount_amount_type'];
            $discount->min_purchase = $request['min_purchase'];
            $discount->max_discount_amount = !is_null($request['max_discount_amount']) ? $request['max_discount_amount'] : 0;
            $discount->limit_per_user = $request['limit_per_user'] ?? 0;
            $discount->promotion_type = 'coupon';
            $discount->start_date = $request['start_date'];
            $discount->end_date = $request['end_date'];
            $discount->is_active = 1;
            $discount->save();

            $coupon = $this->coupon;
            $coupon->coupon_code = $request['coupon_code'];
            $coupon->coupon_type = $request['coupon_type'];
            $coupon->discount_id = $discount['id'];
            $coupon->is_active = 1;
            $coupon->save();

            $dis_types = ['category', 'service', 'zone'];
            foreach ((array)$dis_types as $dis_type) {
                $types = [];
                foreach ((array)$request[$dis_type . '_ids'] as $id) {
                    $types[] = [
                        'discount_id' => $discount['id'],
                        'discount_type' => $dis_type,
                        'type_wise_id' => $id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                $discount->discount_types()->createMany($types);
            }
        });

        Toastr::success(DEFAULT_STORE_200['message']);
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id): View|Factory|Application
    {
        $coupon = $this->coupon->with(['discount', 'discount.category_types', 'discount.service_types', 'discount.zone_types'])->where('id', $id)->first();
        $categories = $this->category->ofStatus(1)->ofType('main')->where('lang_id',1)->latest()->get();
        $zones = $this->zone->ofStatus(1)->where('lang_id',1)->latest()->get();
        $services = $this->service->where('lang_id',1)->active()->latest()->get();

        return view('promotionmanagement::admin.coupons.edit', compact('categories', 'zones', 'services', 'coupon'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'coupon_code' => ['nullable', 'unique:coupons,coupon_code,' . $id . ',id'],
            'discount_type' => 'required|in:category,service,zone,mixed',
            'discount_title' => 'required',
            'coupon_type' => 'required|in:' . implode(',', array_keys(COUPON_TYPES)),
            'discount_amount' => 'required|numeric',
            'discount_amount_type' => 'required|in:percent,amount',
            'min_purchase' => 'required|numeric',
            'max_discount_amount' => $request['discount_amount_type'] == 'amount' ? '' : 'required' . '|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'limit_per_user' => 'numeric',
        ]);

        DB::transaction(function () use ($request, $id) {
            $coupon = $this->coupon->where(['id' => $id])->first();
            $coupon->coupon_code = $request['coupon_code'];
            $coupon->coupon_type = $request['coupon_type'];
            $coupon->save();

            $discount = $this->discountQuery->where('id', $coupon['discount_id'])->first();
            $discount->discount_type = $request['discount_type'];
            $discount->discount_title = $request['discount_title'];
            $discount->discount_amount = $request['discount_amount'];
            $discount->discount_amount_type = $request['discount_amount_type'];
            $discount->min_purchase = $request['min_purchase'];
            $discount->max_discount_amount = !is_null($request['max_discount_amount']) ? $request['max_discount_amount'] : 0;
            $discount->limit_per_user = $request['limit_per_user'] ?? 0;
            $discount->promotion_type = 'coupon';
            $discount->start_date = $request['start_date'];
            $discount->end_date = $request['end_date'];
            $discount->is_active = 1;
            $discount->save();

            $this->discountType->where(['discount_id' => $discount['id']])->delete();

            if ($request['discount_type'] == 'service') {
                $dis_types = ['service', 'zone'];
            } elseif ($request['discount_type'] == 'category') {
                $dis_types = ['category', 'zone'];
            } else {
                $dis_types = ['category', 'service', 'zone'];
            }

            foreach ($dis_types as $dis_type) {
                $types = [];
                foreach ((array)$request[$dis_type . '_ids'] as $id) {
                    $types[] = [
                        'discount_id' => $discount['id'],
                        'discount_type' => $dis_type,
                        'type_wise_id' => $id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                $discount->discount_types()->createMany($types);
            }
        });


        Toastr::success(COUPON_UPDATE_200['message']);
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
        $coupon = $this->coupon->where('id', $id)->first();
        $this->discount->where('id', $coupon['discount_id'])->delete();
        $this->discountType->where('discount_id', $coupon['discount_id'])->delete();
        $this->coupon->where('id', $id)->delete();
        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function status_update(Request $request, $id): JsonResponse
    {
        $coupon = $this->coupon->where('id', $id)->first();
        $this->coupon->where('id', $id)->update(['is_active' => !$coupon->is_active]);
        $this->discount->where('id', $coupon->discount_id)->update(['is_active' => !$coupon->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->coupon->with(['discount', 'discount.category_types', 'discount.service_types', 'discount.zone_types'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('coupon_code', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('coupon_type') && $request['coupon_type'] != 'all', function ($query) use ($request) {
                return $query->where(['coupon_type' => $request['coupon_type']]);
            })->when($request->has('discount_type') && $request['discount_type'] != 'all', function ($query) use ($request) {
                return $query->whereHas('discount', function ($query) use ($request) {
                    $query->where(['discount_type' => $request['discount_type']]);
                });
            })->latest()->get();

        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }
}
