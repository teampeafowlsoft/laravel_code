<?php

namespace Modules\ProductManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Modules\PromotionManagement\Entities\ProductBanner;
use Modules\ProductManagement\Entities\Productcampaign;
use Modules\ProductManagement\Entities\ProductdiscountType;

class ProductCampaignController extends Controller
{
    private Productcampaign $productcampaign;
    private ProductdiscountType $productdiscountType;

    public function __construct(Productcampaign $productcampaign, ProductdiscountType $productdiscountType)
    {
        $this->productcampaign = $productcampaign;
        $this->productdiscountType = $productdiscountType;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
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

        $campaigns = $this->productcampaign->with(['discount'])->ofStatus(1)->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $campaigns), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function campaign_items(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required|uuid',
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $campaign = $this->productcampaign
            ->whereHas('discount', function ($query) {
                $query->where('promotion_type', 'campaign')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('is_active', 1);
            })
            ->whereHas('discount.discount_types', function ($query) {
                $query->where(['discount_type' => 'zone', 'type_wise_id' => config('zone_id')]);
            })
            ->where('id', $request['campaign_id'])
            ->first();

        if (isset($campaign)){
            $items = $this->productdiscountType->where(['discount_id' => $campaign->discount->id])
                ->with(['category' => function ($query) {
                    $query->where('is_active', 1);
                }])
                ->with(['product' => function ($query) {
                    $query->where('is_active', 1)->with(['variations']);
                }])
                ->with(['discount'])
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
            return response()->json(response_formatter(DEFAULT_200, $items), 200);
        }

        return response()->json(response_formatter(DEFAULT_404), 200);
    }
}
