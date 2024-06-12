<?php

namespace Modules\PromotionManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\PromotionManagement\Entities\ProductBanner;

class ProductBannerController extends Controller
{
    private ProductBanner $productbanner;

    public function __construct(ProductBanner $productbanner)
    {
        $this->productbanner = $productbanner;
    }
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
        $banners = $this->productbanner->with(['product', 'category'])->ofStatus(1)->withoutGlobalScopes()
            ->with(['product' => function ($query) use ($lang_id)  {
                $query->where('is_active', 1)->where('lang_id',$lang_id)->withoutGlobalScopes();
            }])
            ->with(['category' => function ($query) use ($lang_id) {
                $query->where('is_active', 1)->where('lang_id',$lang_id)->withoutGlobalScopes();
            }])
            ->where('lang_id',$lang_id)
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        foreach ($banners as $key=>$item) {
            if ($item->resource_type == 'product' && is_null($item->product)) {
                unset($banners[$key]);
            }
            if ($item->resource_type == 'category' && is_null($item->category)) {
                unset($banners[$key]);
            }
        }
        return response()->json(response_formatter(DEFAULT_200, $banners), 200);

    }
}
