<?php

namespace Modules\CategoryManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CategoryManagement\Entities\Category;
use Modules\ServiceManagement\Entities\Variation;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;


class CategoryController extends Controller
{

    private $category, $zone, $variation;

    public function __construct(Category $category, Zone $zone, Variation $variation)
    {
        $this->category = $category;
        $this->zone = $zone;
        $this->variation = $variation;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request): View|Factory|Application
    {
        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

//        $categories = $this->category->withCount(['children', 'zones'])
//            ->when($request->has('search'), function ($query) use ($request) {
//                $keys = explode(' ', $request['search']);
//                foreach ($keys as $key) {
//                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
//                }
//            })
//            ->when($status != 'all', function ($query) use ($status) {
//                $query->ofStatus($status == 'active' ? 1 : 0);
//            })
//            ->ofType('main')
//            ->latest()->paginate(pagination_limit())->appends($query_param);

        $categories = $this->category->selectRaw("group_id,parent_id,GROUP_CONCAT(image order By lang_id) as image,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id")
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
//        DB::enableQueryLog();
//        $children = $this->category->withCount(['children', 'zones'])->get();
        $children = DB::table("categories")
            ->select('categories.*', DB::raw("(select count(*) from `categories` as `laravel_reserved_0` where `categories`.`id` = `laravel_reserved_0`.`parent_id`) as `children_count`"), DB::raw("(select count(*) from `zones` inner join `category_zone` on `zones`.`id` = `category_zone`.`zone_id` where `categories`.`id` = `category_zone`.`category_id`) as `zones_count`"))
            ->groupBy('categories.group_id')
            ->get();
//        dd(DB::getQueryLog());
        $zones = $this->zone->where('is_active', 1)->get();

        $languages = DB::table('language_master')->get();

        $category_data = Category::orderBy('group_id', 'desc')->first();
        if (!empty($category_data)) {
            $category_grp_id = $category_data->group_id;
        } else {
            $category_grp_id = 0;
        }

        return view('categorymanagement::admin.create', compact('categories', 'zones', 'languages', 'category_grp_id', 'children', 'search', 'status'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:categories',
            'zone_ids' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);

        //English Code
        $category = new Category();
        $category->group_id = ($request->group_id) + 1;
        $category->name = $request->name;
        $category->image = file_uploader('category/', 'png', $request->file('image'));
        $category->parent_id = 0;
        $category->position = 1;
        $category->description = !empty($request->description) ? $request->description : null;
        $category->lang_id = $request->eng_lang_id;
        $category->save();
        $category->zones()->sync($request->zone_ids);

        //Arabic Code
        $category1 = new Category();
        $category1->group_id = ($request->group_id) + 1;
        $category1->name = $request->arabic_name;
        $category1->image = file_uploader('category/', 'png', $request->file('image'));
        $category1->parent_id = 0;
        $category1->position = 1;
        $category1->description = !empty($request->arabic_description) ? $request->arabic_description : null;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->save();
        $category1->zones()->sync($request->arabic_zone_ids);
//        dd($category1);

        Toastr::success(CATEGORY_STORE_200['message']);
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $id
     * @return View|Factory|Application|RedirectResponse
     */
    public function edit(string $id, int $group_id): View|Factory|Application|RedirectResponse
    {
//        $category = $this->category->with(['zones'])->ofType('main')->where('group_id', $group_id)->orderBy('lang_id','asc')->first();

//        $category = Category::selectRaw("group_id,parent_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(description order By lang_id) as description,image")
//            ->where('group_id', $group_id)
//            ->groupBy('group_id', 'parent_id', 'is_active')
//            ->get();

        $category = Category::selectRaw("group_id,parent_id,is_active,name,id,  lang_id,description,image")
            ->where('group_id', $group_id)
            ->orderBy('lang_id','asc')
            ->get();

        $category_id_eng = explode(',', $category[0]->id);
        $category_id_arabic = explode(',', $category[1]->id);

//        $category_id_eng = explode(',', $category[0]->id)[0];
//        $category_id_arabic = explode(',', $category[0]->id)[1];

        $selected_zones = $this->category->with(['zones'])->ofType('main')->where('id', $category_id_eng)->first();
        $selected_zones_arabic = $this->category->with(['zones'])->ofType('main')->where('id', $category_id_arabic)->first();
//dd(DB::getQueryLog());
//dd($selected_zones);
        $languages = DB::table('language_master')->get();
        if (isset($category)) {
            $zones = $this->zone->where('is_active', 1)->where('lang_id', 1)->get();
            $arabic_zones = $this->zone->where('is_active', 1)->where('lang_id', 2)->get();
            return view('categorymanagement::admin.edit', compact('category', 'zones', 'arabic_zones', 'languages', 'selected_zones', 'selected_zones_arabic'));
        }

        Toastr::error(DEFAULT_204['message']);
        return back();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $request->validate([
//            'name' => 'required|unique:categories,name,' . $id,
            'name' => 'required',
            'zone_ids' => 'required|array',
        ]);

//        $category = $this->category->ofType('main')->where('id', $id)->first();
        $category = $this->category->ofType('main')->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();
        $category1 = $this->category->ofType('main')->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!$category) {
            return response()->json(response_formatter(CATEGORY_204), 204);
        }
//        $category->name = $request->name;
//        if ($request->has('image')) {
//            $category->image = file_uploader('category/', 'png', $request->file('image'), $category->image);
//        }
//        $category->parent_id = 0;
//        $category->position = 1;
//        $category->description = null;
//        $category->save();

//        $category->zones()->sync($request->zone_ids);

        //English Code
        $category->name = $request->name;

        if ($request->has('image')) {
            $category->image = file_uploader('category/', 'png', $request->file('image'), $category->image);
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
            $category1->image = file_uploader('category/', 'png', $request->file('image'), $category1->image);
        }
        $category1->parent_id = 0;
        $category1->position = 1;
        $category1->description = !empty($request->arabic_description) ? $request->arabic_description : null;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->save();
        $category1->zones()->sync($request->arabic_zone_ids);

        Toastr::success(CATEGORY_UPDATE_200['message']);
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
        $category = $this->category->ofType('main')->where('group_id', $id)->first();
        if (isset($category)) {
            file_remover('category/', $category->image);
            $category->zones()->sync([]);
            Category::where('group_id', $id)->delete();

//            $category->delete();
            Toastr::success(CATEGORY_DESTROY_200['message']);
            return back();
        }
        Toastr::success(CATEGORY_204['message']);
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
        $category = $this->category->where('group_id', $id)->first();
        $this->category->where('group_id', $id)->update(['is_active' => !$category->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function childes(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:all,active,inactive',
            'id' => 'required|uuid'
        ]);

        $childes = $this->category->when($request['status'] != 'all', function ($query) use ($request) {
            return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
        })->ofType('sub')->with(['zones'])->where('parent_id', $request['id'])->orderBY('name', 'asc')->paginate(pagination_limit());

        return response()->json(response_formatter(DEFAULT_200, $childes), 200);
    }

    /**
     * Display a listing of the resource.
     * @param $id
     * @return JsonResponse
     */
    public function ajax_childes(Request $request, $id): JsonResponse
    {
        $categories = $this->category->where('lang_id',1)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->category->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['invoice_item' => 0])->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('categorymanagement::admin.partials._childes-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);
    }

    public function ajax_childes_arabic(Request $request, $id): JsonResponse
    {
//        $categories = $this->category->where('lang_id',2)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
//
//        return response()->json([
//            'template' => view('categorymanagement::admin.partials._arabic-childes-selector', compact('categories'))->render(),
//        ], 200);

        $categories = $this->category->where('lang_id',2)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->category->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['invoice_item' => 0])->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('categorymanagement::admin.partials._arabic-childes-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);

    }


    public function ajax_childes_multiple(Request $request, $id): JsonResponse
    {
        $categories = $this->category->where('lang_id',1)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->category->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['invoice_item' => 0])->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('categorymanagement::admin.partials._childes-multiple-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);
    }

    public function ajax_childes_arabic_multiple(Request $request, $id): JsonResponse
    {
//        $categories = $this->category->where('lang_id',2)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
//
//        return response()->json([
//            'template' => view('categorymanagement::admin.partials._arabic-childes-selector', compact('categories'))->render(),
//        ], 200);

        $categories = $this->category->where('lang_id',2)->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $category = $this->category->where('id', $id)->with(['zones'])->first();
        $zones = $category->zones;

        session()->put('category_wise_zones', $zones);

        $variants = $this->variation->where(['invoice_item' => 0])->where(['service_id' => $request['service_id']])->get();

        return response()->json([
            'template' => view('categorymanagement::admin.partials._arabic-multiple-childes-selector', compact('categories'))->render(),
            'template_for_zone' => view('servicemanagement::admin.partials._category-wise-zone', compact('zones'))->render(),
            'template_for_variant' => view('servicemanagement::admin.partials._variant-data', compact('zones'))->render(),
            'template_for_update_variant' => view('servicemanagement::admin.partials._update-variant-data', compact('zones', 'variants'))->render()
        ], 200);

    }

    /**
     * Display a listing of the resource.
     * @param $id
     * @return JsonResponse
     */
    public function ajax_childes_only(Request $request, $id): JsonResponse
    {
        $categories = $this->category->ofStatus(1)->ofType('sub')->where('parent_id', $id)->orderBY('name', 'asc')->get();
        $sub_category_id = $request->sub_category_id ?? null;

        return response()->json([
            'template' => view('categorymanagement::admin.partials._childes-selector', compact('categories', 'sub_category_id'))->render()
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

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
//        $items = $this->category->withCount(['children', 'zones'])
//            ->when($request->has('search'), function ($query) use ($request) {
//                $keys = explode(' ', $request['search']);
//                foreach ($keys as $key) {
//                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
//                }
//            })
//            ->ofType('main')
//            ->latest()->latest()->get();

        $items = $this->category->selectRaw("group_id,parent_id,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                }
            })
            ->groupBy('group_id', 'parent_id', 'is_active')
            ->ofType('main')
            ->latest()->latest()->get();

        $children = DB::table("categories")
            ->select('categories.*', DB::raw("(select count(*) from `categories` as `laravel_reserved_0` where `categories`.`id` = `laravel_reserved_0`.`parent_id`) as `children_count`"), DB::raw("(select count(*) from `zones` inner join `category_zone` on `zones`.`id` = `category_zone`.`zone_id` where `categories`.`id` = `category_zone`.`category_id`) as `zones_count`"))
            ->groupBy('categories.group_id')
            ->get();

//        if (!empty($items)) {
//            $list = [];
//            foreach ($items as $res) {
////                $subcategory = '';
////                $zone = '';
////                foreach ($children as $key => $child) {
////                    if ($child->id == (explode(',', $res->id)[0])) {
////                        $subcategory .= $child->children_count;
////                        $zone .= $child->zones_count;
////                    }
////                }
//                $list .= collect([
//                    ['category_name (english)' => 'Jane',
//                        'category_name (arabic)' => 'Jane',
//                        'sub_category_count' => '1',
//                        'zone_count' => '2',
//                        'status' => '1'
//                        ],
//                ]);
//            }
//        }
//        $list2 = collect([
//            [ 'id' => 1, 'name' => 'Jane' ],
//            [ 'id' => 2, 'name' => 'John' ],
//        ]);

        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }
}
