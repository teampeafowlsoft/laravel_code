<?php

namespace Modules\ProductCategoryManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
//use Illuminate\Contracts\Support\Renderable;
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
use Modules\ServiceManagement\Entities\Variation;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductCategoryManagementController extends Controller
{
    private $productcategory, $zone, $variation;

    public function __construct(Productcategory $productcategory, Zone $zone, Variation $variation)
    {
        $this->productcategory = $productcategory;
        $this->zone = $zone;
        $this->variation = $variation;
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request): View|Factory|Application
    {
        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $categories = $this->productcategory->selectRaw("group_id,parent_id,GROUP_CONCAT(image order By lang_id) as image,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                }
            })
            ->groupBy('group_id', 'parent_id', 'is_active')
            ->when($status != 'all', function ($query) use ($status) {
                $query->ofStatus($status == 'active' ? 1 : 0);
            })
            ->ofType('main')
            ->latest()->paginate(pagination_limit())->appends($query_param);

        $children = DB::table("productcategories")
            ->select('productcategories.*', DB::raw("(select count(*) from `productcategories` as `laravel_reserved_0` where `productcategories`.`id` = `laravel_reserved_0`.`parent_id`) as `children_count`"), DB::raw("(select count(*) from `zones` inner join `product_category_zone` on `zones`.`id` = `product_category_zone`.`zone_id` where `productcategories`.`id` = `product_category_zone`.`productcategory_id`) as `zones_count`"))
            ->groupBy('productcategories.group_id')
            ->get();
//        dd(DB::getQueryLog());
        $zones = $this->zone->where('is_active', 1)->where('lang_id', 1)->get();
        $arabic_zones = $this->zone->where('is_active', 1)->where('lang_id', 2)->get();
        $languages = DB::table('language_master')->get();

        $category_data = Productcategory::orderBy('group_id', 'desc')->first();
        if (!empty($category_data)) {
            $category_grp_id = $category_data->group_id;
        } else {
            $category_grp_id = 0;
        }
//
//        $categories = $this->productcategory->with(['zones'])->ofStatus(1)->ofType('main')
//            ->where('lang_id', 2)
//            ->get();
//        dd($categories);

        return view('productcategorymanagement::admin.create', compact('categories', 'zones', 'arabic_zones', 'languages', 'category_grp_id', 'children', 'search', 'status'));
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
//            'zone_ids' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);

        //English Code
        $productcategory = new Productcategory();
        $productcategory->group_id = ($request->group_id) + 1;
        $productcategory->name = $request->name;
        $productcategory->image = file_uploader('productcategory/', 'png', $request->file('image'));
        $productcategory->parent_id = 0;
        $productcategory->position = 1;
        $productcategory->description = !empty($request->description) ? $request->description : null;
        $productcategory->lang_id = $request->eng_lang_id;
        $productcategory->save();
        $productcategory->zones()->sync($request->zone_ids);

        //Arabic Code
        $productcategory1 = new Productcategory();
        $productcategory1->group_id = ($request->group_id) + 1;
        $productcategory1->name = $request->arabic_name;
        $productcategory1->image = file_uploader('productcategory/', 'png', $request->file('image'));
        $productcategory1->parent_id = 0;
        $productcategory1->position = 1;
        $productcategory1->description = !empty($request->arabic_description) ? $request->arabic_description : null;
        $productcategory1->lang_id = $request->arabic_lang_id;
        $productcategory1->save();
        $productcategory1->zones()->sync($request->arabic_zone_ids);

        Toastr::success(PRODUCT_CATEGORY_STORE_200['message']);
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(string $id, int $group_id): View|Factory|Application|RedirectResponse
    {
//        $category = Productcategory::selectRaw("group_id,parent_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(description order By lang_id) as description,image")
//            ->where('group_id', $group_id)
//            ->groupBy('group_id', 'parent_id', 'is_active')
//            ->get();
        $category = Productcategory::selectRaw("group_id,parent_id,is_active,name,id,lang_id,description,image")
            ->where('group_id', $group_id)
            ->orderBy('lang_id','asc')
            ->get();
        $category_id_eng = $category[0]->id;
        $category_id_arabic = $category[1]->id;

//        $category_id_eng = explode(',', $category[0]->id)[0];
//        $category_id_arabic = explode(',', $category[0]->id)[1];

        $selected_zones = $this->productcategory->with(['zones'])->ofType('main')->where('id', $category_id_eng)->first();

        $selected_zones_arabic = $this->productcategory->with(['zones'])->ofType('main')->where('id', $category_id_arabic)->first();

        $languages = DB::table('language_master')->get();
        if (isset($category)) {
            $zones = $this->zone->where('is_active', 1)->where('lang_id', 1)->get();

            $arabic_zones = $this->zone->where('is_active', 1)->where('lang_id', 2)->get();
            return view('productcategorymanagement::admin.edit', compact('category', 'zones', 'arabic_zones', 'languages', 'selected_zones', 'selected_zones_arabic'));
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
            'name' => 'required',
//            'zone_ids' => 'required|array',
        ]);

        $category = $this->productcategory->ofType('main')->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();
        $category1 = $this->productcategory->ofType('main')->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!$category) {
            return response()->json(response_formatter(PRODUCT_CATEGORY_204), 204);
        }

        //English Code
        $category->name = $request->name;

        if ($request->has('image')) {
            $category->image = file_uploader('productcategory/', 'png', $request->file('image'), $category->image);
        }
        $category->parent_id = 0;
        $category->position = 1;
        $category->description = !empty($request->description) ? $request->description : null;

        $category->lang_id = $request->eng_lang_id;
        $category->save();
        $category->zones()->sync($request->zone_ids);

        //Arabic Code
        $category1->name = $request->arabic_name;
        if ($request->has('image')) {
            $category1->image = file_uploader('productcategory/', 'png', $request->file('image'), $category1->image);
        }
        $category1->parent_id = 0;
        $category1->position = 1;
        $category1->description = !empty($request->arabic_description) ? $request->arabic_description : null;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->save();
        $category1->zones()->sync($request->arabic_zone_ids);

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
        $category = $this->productcategory->ofType('main')->where('group_id', $id)->first();
        if (isset($category)) {
            file_remover('productcategory/', $category->image);
            $category->zones()->sync([]);
            Productcategory::where('group_id', $id)->delete();

            Toastr::success(PRODUCT_CATEGORY_DESTROY_200['message']);
            return back();
        }
        Toastr::success(PRODUCT_CATEGORY_204['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $category = $this->productcategory->where('group_id', $id)->first();
        $this->productcategory->where('group_id', $id)->update(['is_active' => !$category->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    public function childes(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:all,active,inactive',
            'id' => 'required|uuid'
        ]);

        $childes = $this->productcategory->when($request['status'] != 'all', function ($query) use ($request) {
            return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
        })->ofType('sub')->with(['zones'])->where('parent_id', $request['id'])->orderBY('name', 'asc')->paginate(pagination_limit());

        return response()->json(response_formatter(DEFAULT_200, $childes), 200);
    }

    public function ajax_childes(Request $request, $id): JsonResponse
    {
        $categories = $this->productcategory->where('lang_id',1)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->productcategory->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('productcategorymanagement::admin.partials._childes-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);
    }

    public function ajax_childes_arabic(Request $request, $id): JsonResponse
    {
        $categories = $this->productcategory->where('lang_id',2)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->productcategory->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('productcategorymanagement::admin.partials._arabic-childes-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);

    }

    public function ajax_childes_multiple(Request $request, $id): JsonResponse
    {
        $categories = $this->productcategory->where('lang_id',1)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->productcategory->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['service_id' => $request['service_id']])->get();
        return response()->json([
            'template' => view('productcategorymanagement::admin.partials._childes-multiple-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);
    }

    public function ajax_childes_arabic_multiple(Request $request, $id): JsonResponse
    {
        $categories = $this->productcategory->where('lang_id',2)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->productcategory->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('productcategorymanagement::admin.partials._arabic-multiple-childes-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);

    }

    public function ajax_childes_only(Request $request, $id): JsonResponse
    {
        $categories = $this->productcategory->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $sub_category_id = $request->sub_category_id ?? null;

        return response()->json([
            'template' => view('productcategorymanagement::admin.partials._childes-selector', compact('categories', 'sub_category_id'))->render()
        ], 200);
    }

    public function lang_translate(Request $request): JsonResponse
    {
        $lang_id = $request->lang_id;
        $zone['data'] = Zone::orderby("name", "asc")
            ->select('*')
            ->where('lang_id', $lang_id)
            ->get();
        return response()->json($zone);
    }

    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->productcategory->selectRaw("group_id,parent_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                }
            })
            ->groupBy('group_id', 'parent_id', 'is_active')
            ->ofType('main')
            ->latest()->latest()->get();

        $children = DB::table("productcategories")
            ->select('productcategories.*', DB::raw("(select count(*) from `productcategories` as `laravel_reserved_0` where `productcategories`.`id` = `laravel_reserved_0`.`parent_id`) as `children_count`"), DB::raw("(select count(*) from `zones` inner join `category_zone` on `zones`.`id` = `category_zone`.`zone_id` where `productcategories`.`id` = `category_zone`.`category_id`) as `zones_count`"))
            ->groupBy('productcategories.group_id')
            ->get();

        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }

    public function zone_select(Request $request): JsonResponse
    {
        $zone = [];
        $arabic_zone_data = [];
        $eng_zone_id = $request['eng_zone_id'];
        foreach($eng_zone_id as $zone_id) {
            $zone[] = Zone::orderby("name", "asc")
            ->select('group_id')
            ->where('id', $zone_id)
            ->get();
        }
        foreach($zone as $arabic_zone) {
//            DB::enableQueryLog();
            $arabic_zone_data[] = Zone::select('*')
                ->where('group_id', $arabic_zone[0]->group_id)
                ->where('lang_id', 2)
                ->get();

        }
        return response()->json($arabic_zone_data);


    }
}
