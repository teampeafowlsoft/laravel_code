<?php

namespace Modules\ProductManagement\Http\Controllers\Web\Admin;

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
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Productcampaign;
use Modules\ProductManagement\Entities\Productdiscount;
use Modules\ProductManagement\Entities\ProductdiscountType;
use Modules\ServiceManagement\Entities\Service;
use Modules\ProductManagement\Entities\Product;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductcampaignController extends Controller
{
    protected $productdiscount, $productcampaign, $productdiscountType, $service, $productcategory, $zone, $discount_types, $product;

    public function __construct(Productcampaign $productcampaign, Productdiscount $productdiscount, ProductdiscountType $productdiscountType, Service $service,Product $product, Productcategory $productcategory, Zone $zone)
    {
        $this->productdiscount = $productdiscount;
        $this->productcampaign = $productcampaign;
        $this->productdiscountType = $productdiscountType;
        $this->service = $service;
        $this->productcategory = $productcategory;
        $this->zone = $zone;
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request): Factory|View|Application
    {
        $search = $request->has('search') ? $request['search'] : '';
        $discount_type = $request->has('discount_type') ? $request['discount_type'] : 'all';
        $query_param = ['search' => $search, 'discount_type' => $discount_type];

        $campaigns = $this->productcampaign->with(['discount', 'discount.category_types', 'discount.service_types', 'discount.zone_types'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('campaign_name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('discount_type') && $request['discount_type'] != 'all', function ($query) use ($request) {
                return $query->whereHas('discount',function ($query) use ($request) {
                    $query->where(['discount_type' => $request['discount_type']]);
                });
            })->latest()->paginate(pagination_limit())->appends($query_param);

        return view('productmanagement::admin.campaigns.list', compact('campaigns', 'search', 'discount_type'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request): View|Factory|Application
    {
        $categories = $this->productcategory->ofStatus(1)->ofType('main')->where('lang_id',1)->latest()->get();
        $zones = $this->zone->ofStatus(1)->where('lang_id',1)->latest()->get();
        $products = $this->product->where('lang_id',1)->ofStatus(1)->latest()->get();

        return view('productmanagement::admin.campaigns.create', compact('categories', 'zones', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'campaign_name' => 'required',
            'cover_image' => 'required|image|mimes:jpeg,jpg,png,gif|required|max:10000',
            'thumbnail' => 'required|image|mimes:jpeg,jpg,png,gif|required|max:10000',
            'discount_type' => 'required',
            'discount_title' => 'required',
            'discount_amount' => 'required|numeric',
            'discount_amount_type' => 'required|in:percent,amount',
            'min_purchase' => 'required|numeric',
            'max_discount_amount' => $request['discount_amount_type'] == 'amount' ? '' : 'required' . '|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'limit_per_user' => 'numeric',
        ]);

        DB::transaction(function () use ($request) {
            $discount = $this->productdiscount;
            $discount->discount_type = $request['discount_type'];
            $discount->discount_title = $request['discount_title'];
            $discount->discount_amount = $request['discount_amount'];
            $discount->discount_amount_type = $request['discount_amount_type'];
            $discount->min_purchase = $request['min_purchase'];
            $discount->max_discount_amount = !is_null($request['max_discount_amount']) ? $request['max_discount_amount'] : 0;
            $discount->limit_per_user = $request['limit_per_user']??0;
            $discount->promotion_type = 'campaign';
            $discount->start_date = $request['start_date'];
            $discount->end_date = $request['end_date'];
            $discount->is_active = 1;
            $discount->save();

            $campaign = $this->productcampaign;
            $campaign->thumbnail = file_uploader('productcampaign/', 'png', $request->file('thumbnail'));
            $campaign->cover_image = file_uploader('productcampaign/', 'png', $request->file('cover_image'));
            $campaign->campaign_name = $request['campaign_name'];
            $campaign->discount_id = $discount['id'];
            $campaign->is_active = 1;
            $campaign->save();

            $dis_types = ['category', 'product', 'zone'];
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
     * @param int $id
     * @return Renderable
     */
    public function edit(string $id): View|Factory|Application
    {
        $campaign = $this->productcampaign->with(['discount', 'discount.category_types', 'discount.service_types', 'discount.zone_types'])->where('id', $id)->first();
        $categories = $this->productcategory->ofStatus(1)->ofType('main')->where('lang_id',1)->latest()->get();
        $zones = $this->zone->ofStatus(1)->where('lang_id',1)->latest()->get();
        $products = $this->product->where('lang_id',1)->ofStatus(1)->latest()->get();

        return view('productmanagement::admin.campaigns.edit', compact('categories', 'zones', 'products', 'campaign'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'campaign_name' => 'required',
            'cover_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'thumbnail' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'discount_type' => 'required',
            'discount_title' => 'required',
            'discount_amount' => 'required|numeric',
            'discount_amount_type' => 'required|in:percent,amount',
            'min_purchase' => 'required|numeric',
            'max_discount_amount' => $request['discount_amount_type'] == 'amount' ? '' : 'required' . '|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'limit_per_user' => 'numeric',
        ]);

        DB::transaction(function () use ($request, $id) {
            $campaign = $this->productcampaign->where(['id' => $id])->first();
            if ($request->has('thumbnail')) {
                $campaign->thumbnail = file_uploader('productcampaign/', 'png', $request->file('thumbnail'), $campaign->thumbnail);
            }
            if ($request->has('cover_image')) {
                $campaign->cover_image = file_uploader('productcampaign/', 'png', $request->file('cover_image'), $campaign->cover_image);
            }
            $campaign->campaign_name = $request['campaign_name'];
            $campaign->save();

            $discount = $this->productdiscount->find($campaign['discount_id']);
            $discount->discount_type = $request['discount_type'];
            $discount->discount_title = $request['discount_title'];
            $discount->discount_amount = $request['discount_amount'];
            $discount->discount_amount_type = $request['discount_amount_type'];
            $discount->min_purchase = $request['min_purchase'];
            $discount->max_discount_amount = !is_null($request['max_discount_amount']) ? $request['max_discount_amount'] : 0;
            $discount->limit_per_user = $request['limit_per_user']??0;
            $discount->start_date = $request['start_date'];
            $discount->end_date = $request['end_date'];
            $discount->is_active = 1;
            $discount->save();

            $discount->discount_types()->delete();

            $dis_types = ['category', 'product', 'zone'];
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

        Toastr::success(CAMPAIGN_UPDATE_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $campaign = $this->productcampaign->where('id', $id)->first();

        if (isset($campaign)){
            file_remover('productcampaign/', $campaign['thumbnail']);
            file_remover('productcampaign/', $campaign['cover_image']);
            $this->productdiscount->where('id', $campaign['discount_id'])->delete();
            $this->productdiscountType->where('discount_id', $campaign['discount_id'])->delete();
            $this->productcampaign->where('id', $id)->delete();
        }

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
        $campaign = $this->productcampaign->where('id', $id)->first();
        $this->productcampaign->where('id', $id)->update(['is_active' => !$campaign->is_active]);
        $this->productdiscount->where('id', $campaign->discount_id)->update(['is_active' => !$campaign->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->productcampaign->with(['discount', 'discount.category_types', 'discount.service_types', 'discount.zone_types'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('campaign_name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($request->has('discount_type') && $request['discount_type'] != 'all', function ($query) use ($request) {
                return $query->whereHas('discount',function ($query) use ($request) {
                    $query->where(['discount_type' => $request['discount_type']]);
                });
            })->latest()->get();

        return (new FastExcel($items))->download(time().'-file.xlsx');
    }
}
