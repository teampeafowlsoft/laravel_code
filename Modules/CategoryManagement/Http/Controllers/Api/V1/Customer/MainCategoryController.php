<?php

namespace Modules\CategoryManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CategoryManagement\Entities\Maincategories;
use Modules\ProductManagement\Entities\Product;
use Modules\ServiceManagement\Entities\Service;
use function PHPUnit\Framework\isNull;

class MainCategoryController extends Controller
{
    private $main_category;

    private $service;

    private $product;

    public function __construct(Maincategories $main_category,Service $service,Product $product)
    {
        $this->main_category = $main_category;
        $this->product = $product;
        $this->service = $service;
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

        $main_category = $this->main_category
            ->where('maincategories.lang_id', $lang_id)
            ->where('maincategories.is_active', '1')
//            ->latest()
            ->orderBy('group_id','DESC')
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
        $cnt = 0;
        foreach ($main_category as $mc){
            //GET SERVICE DETAILS:
            $service_id = $mc->service_id;
            $service_details = $this->service
                ->select(DB::raw('COUNT(services.id) as service_cnt'),DB::raw('SUM(services.avg_rating) as service_rating'))
                ->where('category_id', $service_id)
                ->where('is_active', '1')
                ->withoutGlobalScopes()
                ->first();
            $main_category[$cnt]['service_cnt'] = $service_details->service_cnt;
            $main_category[$cnt]['service_rating'] = ($service_details->service_rating != null)?$service_details->service_rating:0;
            //GET PRODUCT DETAILS:
            $product_id = $mc->product_id;
            $product_details = $this->product
                ->select(DB::raw('COUNT(products.id) as product_cnt'),DB::raw('SUM(products.avg_rating) as product_rating'),'vendor')
                // New Code 07-12-23 Pc1
                ->with(['provider'])
                ->whereHas('provider',function ($query){
                    $query->where('is_active',1);
                })
                // Close New Code
                ->where('category_id', $product_id)
                ->where('is_active', '1')
                ->withoutGlobalScopes()
                ->first();
            $main_category[$cnt]['product_cnt'] = $product_details->product_cnt;
            $main_category[$cnt]['product_rating'] = ($product_details->product_rating != null)?$product_details->product_rating:0;
            //UPDATE COUNT FOR NEXT RECORD
            $cnt++;
        }

        return response()->json(response_formatter(DEFAULT_200, $main_category), 200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('categorymanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('categorymanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('categorymanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
