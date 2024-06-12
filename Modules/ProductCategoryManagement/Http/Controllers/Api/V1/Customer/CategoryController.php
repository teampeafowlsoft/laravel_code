<?php

namespace Modules\ProductCategoryManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Product;
use Modules\ZoneManagement\Entities\Zone;

class CategoryController extends Controller
{
    private $productcategory;
    private $product;

    public function __construct(Productcategory $productcategory,Product $product)
    {
        $this->productcategory = $productcategory;
        $this->product = $product;
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
//            'lang_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

//        $lang_id = $request->header('Content-Language', app('translator')->getLocale());

        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");
//        echo 'lang:' . $lang_id;
//with(['zones'])->
        //->ofType('main')
            $categories = $this->productcategory->ofStatus(1)->ofType('main')->withoutGlobalScopes()
                ->where('lang_id', $lang_id)
                ->latest()
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');


        return response()->json(response_formatter(DEFAULT_200, $categories), 200);
    }

    public function childes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
//        $lang_id = $request->header('Content-Language', app('translator')->getLocale());
        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $childes = $this->productcategory->ofStatus(1)->ofType('sub')->withoutGlobalScopes()
            ->withCount(['totalproducts' => function ($query) {
                $query->join('products', function ($join) {
                    $join->on('products.id', '=', 'product_subcategory.product_id')
                        ->where('products.is_active', '=', 1);
                });
                $query->join('providers', function($join){
                    $join->on('providers.id', '=', 'products.vendor')
                        ->where('providers.is_active', '=', 1);
                });
            }])
            ->with(['totalproducts' => function ($query) {
                $query->groupBy('product_subcategory.group_id');
            }])
            ->with(['productvariants'])
//            ->with(['productvariants' => function ($query) {
//                $query->groupBy('product_subcategory.group_id');
//            }])
//            ->with(['variations'])
            ->whereHas('parent', function ($query) {
                $query->ofStatus(1);
            })
            ->where('parent_id', $request['id'])
            ->where('lang_id', $lang_id)
            ->orderBY('name', 'asc')
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

//        $childes['variations_app_format'] = self::variations_app_format($childes);

        return response()->json(response_formatter(DEFAULT_200, $childes), 200);
    }

    private function variations_app_format($service): array
    {
        $formatting = [];
        $filtered = $service['variations'];
//        $formatting['zone_id'] = Config::get('zone_id');
//        $formatting['default_price'] = $filtered->first() ? $filtered->first()->price : 0;
        foreach ((array) $filtered as $data) {
            $formatting['zone_wise_variations'][] = [
                'price' => $data['price']
            ];
        }
        return $formatting;
    }

    public function childes_products(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
//        $lang_id = $request->header('Content-Language', app('translator')->getLocale());
        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        $childes = $this->productcategory->ofStatus(1)->ofType('sub')->withoutGlobalScopes()
            ->with(['totalproducts' => function ($query) use ($request) {
                $query->where('product_subcategory.subcategory_id',$request['id'])
                    ->groupBy('product_subcategory.group_id');
            }])
//            ->whereHas('parent', function ($query) {
//                $query->where('parent_id','!=',0);
//            })
//            ->where('parent_id', $request['id'])
            ->where('lang_id', $lang_id)
            ->orderBY('name', 'asc')
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $childes), 200);
    }

}
