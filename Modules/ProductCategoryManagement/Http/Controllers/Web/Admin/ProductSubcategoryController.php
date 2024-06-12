<?php

namespace Modules\ProductCategoryManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductSubcategoryController extends Controller
{
    private $productcategory;

    public function __construct(Productcategory $productcategory)
    {
        $this->productcategory = $productcategory;
    }

    public function create(Request $request): Renderable
    {
        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $sub_categories = $this->productcategory->selectRaw("group_id,position,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(parent_id order By lang_id) as parent_id,image")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orHavingRaw('name LIKE ?', array("%$key%"));
                    }
                });
            })
            ->groupBy('group_id', 'position', 'is_active')
            ->when($status != 'all', function ($query) use ($request) {
                return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
            })
            ->ofType('sub')
            ->latest()->paginate(pagination_limit())->appends($query_param);
//dd($sub_categories);
        $main_categories = $this->productcategory->ofType('main')->where('lang_id',1)->orderBy('name')->get(['id', 'name']);

        $arabic_main_categories = $this->productcategory->ofType('main')->where('lang_id',2)->orderBy('name')->get(['id', 'name']);
//dd(DB::getQueryLog());
        $services = DB::table("productcategories")
            ->select('productcategories.*', DB::raw("(select count(*) from `product_subcategory` where `productcategories`.`id` = `product_subcategory`.`subcategory_id`) as `services_count`"))
            ->groupBy('productcategories.group_id')
            ->get();

        $category_data = Productcategory::orderBy('group_id', 'desc')->first();
        if (!empty($category_data)) {
            $category_grp_id = $category_data->group_id;
        } else {
            $category_grp_id = 0;
        }

        $languages = DB::table('language_master')->get();

        return view('productcategorymanagement::admin.sub-category.create', compact('sub_categories', 'main_categories', 'arabic_main_categories', 'category_grp_id', 'status', 'search', 'languages', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
//            'name' => 'required|unique:categories',
            'name' => 'required',
            'parent_id' => 'required|uuid',
            'short_description' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);

        $category = new Productcategory();
        $category->group_id = ($request->group_id) + 1;
        $category->name = $request->name;
        $category->image = file_uploader('productcategory/', 'png', $request->file('image'));
        $category->parent_id = $request['parent_id'];
        $category->position = 2;
        $category->description = !empty($request['short_description']) ? $request['short_description'] : null;
        $category->lang_id = $request->eng_lang_id;
        $category->save();

        $category1 = new Productcategory();
        $category1->group_id = ($request->group_id) + 1;
        $category1->name = $request->arabic_name;
        $category1->image = file_uploader('productcategory/', 'png', $request->file('image'));
        $category1->parent_id = $request['arabic_parent_id'];
        $category1->position = 2;
        $category1->description = !empty($request['arabic_short_description']) ? $request['arabic_short_description'] : null;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->save();

        Toastr::success(PRODUCT_CATEGORY_STORE_200['message']);
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('productcategorymanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(string $id, int $group_id): View|Factory|RedirectResponse|Application
    {
//        DB::enableQueryLog();
//        $sub_category = $this->productcategory->selectRaw("group_id,position,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id order By lang_id) as lang_id,GROUP_CONCAT(parent_id order By lang_id) as parent_id,GROUP_CONCAT(description order By lang_id) as description,image")
//            ->groupBy('group_id', 'position', 'is_active')
//            ->ofType('sub')
//            ->where('group_id', $group_id)->first();

        $sub_category = $this->productcategory->selectRaw("group_id,position,is_active,name,id,lang_id,parent_id,description,image")
            ->ofType('sub')
            ->where('group_id', $group_id)
            ->orderBy('lang_id', 'asc')
            ->get();

        $languages = DB::table('language_master')->get();
        if (isset($sub_category)) {
            $main_categories = $this->productcategory->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
            $arabic_main_categories = $this->productcategory->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);
            return view('productcategorymanagement::admin.sub-category.edit', compact('sub_category', 'main_categories', 'arabic_main_categories', 'languages'));
        }
        Toastr::error(DEFAULT_204['message']);
        return back();
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $request->validate([
//            'name' => 'required|unique:categories,name,' . $id,
            'name' => 'required',
            'parent_id' => 'required|uuid',
            'short_description' => 'required',
        ]);

        $category = $this->productcategory->ofType('sub')->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();

        $category1 = $this->productcategory->ofType('sub')->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!$category) {
            return response()->json(response_formatter(PRODUCT_CATEGORY_204), 204);
        }
        $category->name = $request->name;
        if ($request->has('image')) {
            $category->image = file_uploader('productcategory/', 'png', $request->file('image'), $category->image);
        }
        $category->parent_id = $request['parent_id'];
        $category->position = 2;
        $category->lang_id = $request->eng_lang_id;
        $category->description = $request['short_description'];
        $category->save();

        $category1->name = $request->arabic_name;
        if ($request->has('image')) {
            $category1->image = file_uploader('productcategory/', 'png', $request->file('image'), $category1->image);
        }
        $category1->parent_id = $request['arabic_parent_id'];
        $category1->position = 2;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->description = $request['arabic_short_description'];
        $category1->save();

        Toastr::success(PRODUCT_CATEGORY_UPDATE_200['message']);
        return back();
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $category = $this->productcategory->ofType('sub')->where('group_id', $id)->first();

        if ($category) {
            $products = DB::table("product_subcategory")
                ->where('subcategory_id', $category->id)
                ->get();

            if (count($products) == 0) {
                file_remover('productcategory/', $category->image);
                Productcategory::ofType('sub')->where('group_id', $id)->delete();

                Toastr::success(PRODUCT_CATEGORY_DESTROY_200['message']);
                return back();
            }
            Toastr::success(PRODUCT_SUBCATEGORY_204['message']);
            return back();
        }
        Toastr::success(PRODUCT_SUBCATEGORY_204['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $category = $this->productcategory->ofType('sub')->where('group_id', $id)->first();
        $this->productcategory->where('group_id', $id)->update(['is_active' => !$category->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->productcategory->withCount('services')->with(['parent'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->ofType('sub')->latest()->get();

        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }

    public function lang_translate(Request $request): JsonResponse
    {
        $lang_id = $request->lang_id;
        $category['data'] = Productcategory::orderby("name", "asc")
            ->select('*')
            ->ofType('main')
            ->where('lang_id', $lang_id)
            ->get();

        return response()->json($category);
    }

}
