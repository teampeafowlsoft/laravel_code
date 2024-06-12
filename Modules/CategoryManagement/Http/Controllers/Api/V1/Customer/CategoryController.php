<?php

namespace Modules\CategoryManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\CategoryManagement\Entities\Category;
use Modules\ZoneManagement\Entities\Zone;

class CategoryController extends Controller
{

    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
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

//        $categories = $this->category->with(['zones'])->ofStatus(1)->ofType('main')
//            ->where('lang_id', $lang_id)
//            ->latest()
//            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        $categories = $this->category->with(['zones'])->ofStatus(1)->ofType('main')->withoutGlobalScopes()
            ->where('lang_id', $lang_id)
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $categories), 200);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
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
        $catId = '';
        $lang_id = $request->header('X-localization')=='' ? "1" : (($request->header('X-localization')=='en')?"1":"2");

        if($lang_id == 1){
            $catId = $request['id'];
        }else{
            $childes = $this->category->where('id',$request->id)->withoutGlobalScopes()->first();
            if(!empty($childes)){
                $catDetail = $this->category->where('group_id',$childes->group_id)->where('lang_id',2)->withoutGlobalScopes()->first();
                $catId = $catDetail->id;
            }
        }
        $childes = $this->category
            ->ofStatus(1)
            ->ofType('sub')
            ->withCount('services')
                ->with(['services' => function ($query) {
                    $query->where('is_active', 1)->groupBy('group_id');
                }])
//            ->whereHas('parent', function ($query) {
//                $query->ofStatus(1);
//            })
            ->withoutGlobalScopes()
            ->where('parent_id', $catId)
            ->orderBY('name', 'asc')
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $childes), 200);
    }

}
