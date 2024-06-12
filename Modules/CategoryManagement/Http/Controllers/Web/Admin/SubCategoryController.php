<?php

namespace Modules\CategoryManagement\Http\Controllers\Web\Admin;

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
use Illuminate\Support\Facades\Validator;
use Modules\CategoryManagement\Entities\Category;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubCategoryController extends Controller
{

    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return Renderable
     */
    public function create(Request $request): Renderable
    {
        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $sub_categories = $this->category->selectRaw("group_id,position,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(parent_id order By lang_id) as parent_id,image")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
//                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orHavingRaw('name LIKE ?', array("%$key%"));
                    }
//                });
            })
            ->groupBy('group_id', 'position', 'is_active')
            ->when($status != 'all', function ($query) use ($request) {
                return $query->ofStatus(($request['status'] == 'active') ? 1 : 0);
            })
            ->ofType('sub')
            ->latest()->paginate(pagination_limit())->appends($query_param);
//DB::enableQueryLog();
        $main_categories = $this->category->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
        $arabic_main_categories = $this->category->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);
//dd(DB::getQueryLog());
        $services = DB::table("categories")
            ->select('categories.*', DB::raw("(select count(*) from `services` where `categories`.`id` = `services`.`sub_category_id`) as `services_count`"))
            ->groupBy('categories.group_id')
            ->get();

        $category_data = Category::orderBy('group_id', 'desc')->first();
        if (!empty($category_data)) {
            $category_grp_id = $category_data->group_id;
        } else {
            $category_grp_id = 0;
        }

        $languages = DB::table('language_master')->get();

        return view('categorymanagement::admin.sub-category.create', compact('sub_categories', 'main_categories','arabic_main_categories' ,'category_grp_id','status', 'search', 'languages','services'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
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

//        $category = $this->category;
//        $category->name = $request->name;
//        $category->image = file_uploader('category/', 'png', $request->file('image'));
//        $category->parent_id = $request['parent_id'];
//        $category->position = 2;
//        $category->description = $request['short_description'];
//        $category->save();

        $category = new Category();
        $category->group_id = ($request->group_id) + 1;
        $category->name = $request->name;
        $category->image = file_uploader('category/', 'png', $request->file('image'));
        $category->parent_id = $request['parent_id'];
        $category->position = 2;
        $category->description = !empty($request['short_description']) ? $request['short_description'] : null;
        $category->lang_id = $request->eng_lang_id;
        $category->save();

        $category1 = new Category();
        $category1->group_id = ($request->group_id) + 1;
        $category1->name = $request->arabic_name;
        $category1->image = file_uploader('category/', 'png', $request->file('image'));
        $category1->parent_id = $request['arabic_parent_id'];
        $category1->position = 2;
        $category1->description = !empty($request['arabic_short_description']) ? $request['arabic_short_description'] : null;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->save();

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
     * @param string $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit(string $id,int $group_id): View|Factory|RedirectResponse|Application
    {
//        $sub_category = $this->category->selectRaw("group_id,position,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id order By lang_id) as lang_id,GROUP_CONCAT(parent_id order By lang_id) as parent_id,GROUP_CONCAT(description order By lang_id) as description,GROUP_CONCAT(image order By lang_id) as image")
//            ->groupBy('group_id', 'position', 'is_active')
//            ->ofType('sub')
//            ->where('group_id', $group_id)->first();

        $sub_category = $this->category->selectRaw("group_id,position,is_active,name,id,lang_id,parent_id,description,image")
            ->ofType('sub')
            ->where('group_id', $group_id)
            ->orderBy('lang_id','asc')
            ->get();

        $languages = DB::table('language_master')->get();
        if (isset($sub_category)) {
            $main_categories = $this->category->ofType('main')->where('lang_id', 1)->orderBy('name')->get(['id', 'name']);
            $arabic_main_categories = $this->category->ofType('main')->where('lang_id', 2)->orderBy('name')->get(['id', 'name']);
            return view('categorymanagement::admin.sub-category.edit', compact('sub_category', 'main_categories', 'arabic_main_categories', 'languages'));
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
            'parent_id' => 'required|uuid',
            'short_description' => 'required',
        ]);

        $category = $this->category->ofType('sub')->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();

        $category1 = $this->category->ofType('sub')->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!$category) {
            return response()->json(response_formatter(CATEGORY_204), 204);
        }
        $category->name = $request->name;
        if ($request->has('image')) {
            $category->image = file_uploader('category/', 'png', $request->file('image'), $category->image);
        }
        $category->parent_id = $request['parent_id'];
        $category->position = 2;
        $category->lang_id = $request->eng_lang_id;
        $category->description = $request['short_description'];
        $category->save();

        $category1->name = $request->arabic_name;
        if ($request->has('image')) {
            $category1->image = file_uploader('category/', 'png', $request->file('image'), $category1->image);
        }
        $category1->parent_id = $request['arabic_parent_id'];
        $category1->position = 2;
        $category1->lang_id = $request->arabic_lang_id;
        $category1->description = $request['arabic_short_description'];
        $category1->save();

        Toastr::success(CATEGORY_UPDATE_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function destroy(Request $request,$id): RedirectResponse
    {
        $category = $this->category->ofType('sub')->where('group_id', $id)->first();

        if ($category) {
            file_remover('category/', $category->image);
            Category::where('group_id', $id)->delete();

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
        $category = $this->category->ofType('sub')->where('group_id', $id)->first();
        $this->category->where('group_id', $id)->update(['is_active' => !$category->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->category->withCount('services')->with(['parent'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->orWhere('name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->ofType('sub')->latest()->get();

        return (new FastExcel($items))->download(time().'-file.xlsx');
    }

    public function lang_translate(Request $request): JsonResponse
    {
        $lang_id = $request->lang_id;
        $category['data'] = Category::orderby("name","asc")
            ->select('*')
            ->ofType('main')
            ->where('lang_id',$lang_id)
            ->get();

        return response()->json($category);
    }
}
