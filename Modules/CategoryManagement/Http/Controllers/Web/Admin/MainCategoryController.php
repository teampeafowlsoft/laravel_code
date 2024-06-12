<?php

namespace Modules\CategoryManagement\Http\Controllers\Web\Admin;

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
use Modules\CategoryManagement\Entities\Maincategories;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ServiceManagement\Entities\Variation;
use Modules\ZoneManagement\Entities\Zone;

class MainCategoryController extends Controller
{
    private $main_category, $service_category, $product_category, $zone;

    public function __construct(Maincategories $main_category,Category $service_category, Productcategory $product_category, Zone $zone)
    {
        $this->main_category = $main_category;
        $this->service_category = $service_category;
        $this->product_category = $product_category;
        $this->zone = $zone;
    }

    public function create(Request $request):View|Factory|Application
    {

        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $maincategories = $this->main_category
            ->selectRaw("group_id,GROUP_CONCAT(image order By lang_id) as image,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(service_id order By lang_id) as service_id,GROUP_CONCAT(product_id order By lang_id) as product_id,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,color")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                }
            })
            //->join('categories', 'categories.id', '=', 'maincategories.service_id')
            ->groupBy('group_id', 'is_active')
            ->when($status != 'all', function ($query) use ($status) {
                $query->ofStatus($status == 'active' ? 1 : 0);
            })->paginate(pagination_limit())->appends($query_param);

        $service_categories = $this->service_category->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
        $service_categories_arabic = $this->service_category->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);

        $product_categories = $this->product_category->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
        $product_categories_arabic = $this->product_category->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);

        $zones = $this->zone->where('is_active', 1)->get();

        $languages = DB::table('language_master')->get();

        $main_category_data = Maincategories::orderBy('group_id', 'desc')->first();
        if (!empty($main_category_data)) {
            $category_grp_id = $main_category_data->group_id;
        } else {
            $category_grp_id = 0;
        }
        $category_db = $this->service_category;
        $product_db = $this->product_category;
        return view('categorymanagement::admin.main-category.create', compact('maincategories', 'zones', 'languages', 'category_grp_id', 'service_categories', 'service_categories_arabic', 'product_categories', 'product_categories_arabic', 'search', 'status','category_db','product_db'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:categories',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);

        //English Code
        $main_category = new Maincategories();
        $main_category->group_id = ($request->group_id) + 1;
        $main_category->name = $request->name;
        $main_category->service_id = $request->eng_service_category_id;
        $main_category->product_id = $request->eng_product_category_id;
        $main_category->first_button_text = $request->button_name_1;
        $main_category->second_button_text = $request->button_name_2;
        $main_category->image = file_uploader('main_category/', 'png', $request->file('image'));
        $main_category->position = 0;
        $main_category->is_active = 1;
        $main_category->lang_id = $request->eng_lang_id;
        $main_category->color = $request->color_code;
        $main_category->save();

        //Arabic Code
        $main_category = new Maincategories();
        $main_category->group_id = ($request->group_id) + 1;
        $main_category->name = $request->name_arabic;
        $main_category->service_id = $request->eng_service_category_id_arabic;
        $main_category->product_id = $request->eng_product_category_id_arabic;
        $main_category->first_button_text = $request->button_name_1_arabic;
        $main_category->second_button_text = $request->button_name_2_arabic;
        $main_category->image = file_uploader('main_category/', 'png', $request->file('image'));
        $main_category->position = 0;
        $main_category->is_active = 1;
        $main_category->lang_id = $request->arabic_lang_id;
        $main_category->color = $request->color_code;
        $main_category->save();

        Toastr::success(CATEGORY_STORE_200['message']);
        return back();
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
    public function edit(string $id, int $group_id): View|Factory|Application|RedirectResponse
    {
        $maincategory = Maincategories::selectRaw("group_id,service_id,first_button_text,second_button_text,product_id,is_active,name,id,lang_id,color,image")
            ->where('group_id', $group_id)
            ->orderBy('lang_id','asc')
            ->get();

        $maincategory_id_eng = explode(',', $maincategory[0]->id);
        $maincategory_id_arabic = explode(',', $maincategory[1]->id);

        $selected_service_categories = explode(',', $maincategory[0]->service_id);
        $selected_product_categories = explode(',', $maincategory[0]->product_id);

        $selected_service_categories_arabic = explode(',', $maincategory[1]->service_id);
        $selected_product_categories_arabic = explode(',', $maincategory[1]->product_id);
        //Select all service categories
        //English
        $service_categories = $this->service_category->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
        //Arabic
        $product_categories = $this->product_category->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
        //Select all product categories
        //English
        $service_categories_arabic = $this->service_category->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);
        //Arabic
        $product_categories_arabic = $this->product_category->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);

        $languages = DB::table('language_master')->get();
        if (isset($maincategory)) {
            return view('categorymanagement::admin.main-category.edit', compact('maincategory', 'maincategory_id_eng', 'maincategory_id_arabic', 'languages', 'service_categories', 'service_categories_arabic', 'product_categories', 'product_categories_arabic', 'selected_service_categories', 'selected_product_categories', 'selected_service_categories_arabic', 'selected_product_categories_arabic'));
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
            'name_arabic' => 'required',
            'eng_service_category_id' => 'required',
            'eng_product_category_id' => 'required',
            'button_name_1' => 'required',
            'button_name_2' => 'required',
            'eng_service_category_id_arabic' => 'required',
            'eng_product_category_id_arabic' => 'required',
            'button_name_1_arabic' => 'required',
            'button_name_2_arabic' => 'required',
        ]);

        $main_category_get = $this->main_category->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();
        $main_category_arabic = $this->main_category->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!$main_category_get) {
            return response()->json(response_formatter(CATEGORY_204), 204);
        }

        //English Code
        $main_category_get->name = $request->name;
        $main_category_get->service_id = $request->eng_service_category_id;
        $main_category_get->product_id = $request->eng_product_category_id;
        $main_category_get->first_button_text = $request->button_name_1;
        $main_category_get->second_button_text = $request->button_name_2;
        if ($request->has('image')) {
            $main_category_get->image = file_uploader('main_category/', 'png', $request->file('image'), $main_category_get->image);
        }
        $main_category_get->color = $request->color_code;
        $main_category_get->lang_id = $request->eng_lang_id;
        $main_category_get->save();

        if (!$main_category_arabic) {
            return response()->json(response_formatter(CATEGORY_204), 204);
        }

        //Arabic Code
        $main_category_arabic->name = $request->name_arabic;
        $main_category_arabic->service_id = $request->eng_service_category_id_arabic;
        $main_category_arabic->product_id = $request->eng_product_category_id_arabic;
        $main_category_arabic->first_button_text = $request->button_name_1_arabic;
        $main_category_arabic->second_button_text = $request->button_name_2_arabic;
        if ($request->has('image')) {
            $main_category_arabic->image = file_uploader('main_category/', 'png', $request->file('image'), $main_category_arabic->image);
        }
        $main_category_arabic->color = $request->color_code;
        $main_category_arabic->lang_id = $request->arabic_lang_id;
        $main_category_arabic->save();

        Toastr::success(CATEGORY_UPDATE_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $maincategory = $this->main_category->where('group_id', $id)->first();
        if (isset($maincategory)) {
            file_remover('main_category/', $maincategory->image);
            Maincategories::where('group_id', $id)->delete();
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
        $maincategory = $this->main_category->where('group_id', $id)->first();
        $this->main_category->where('group_id', $id)->update(['is_active' => !$maincategory->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }
}
