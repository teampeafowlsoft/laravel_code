<?php

namespace Modules\AttributeManagement\Http\Controllers\Web\Admin;

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
use Modules\AttributeManagement\Entities\Attribute;

class AttributeManagementController extends Controller
{
    private Attribute $attribute;
    public function __construct(Attribute $attribute)
    {
        $this->attribute = $attribute;
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    public function index(Request $request): View|Factory|Application
    {
        $request->validate([
            'status' => 'in:active,inactive,all'
        ]);

        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $attributes = $this->attribute->selectRaw("group_id,is_active,GROUP_CONCAT(attribute_name order By lang_id) as attribute_name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(display_name order By lang_id) as display_name,attribute_field_type_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('attribute_name LIKE ?',array("%$key%"));
                }
            })
            ->groupBy('group_id','attribute_field_type_id', 'is_active')
            ->when($status != 'all', function ($query) use ($status) {
                $query->ofStatus($status == 'active' ? 1 : 0);
            })
            ->paginate(pagination_limit())->appends($query_param);

        return view('attributemanagement::admin.list', compact('search', 'status','attributes'));
    }

    public function create(Request $request): View|Factory|Application
    {
        $languages = DB::table('language_master')->get();
        $attribute_data = Attribute::orderBy('group_id','desc')->first();

        if (!empty($attribute_data)) {
            $attribute_grp_id = $attribute_data->group_id;
        } else {
            $attribute_grp_id = 0;
        }
        return view('attributemanagement::admin.create',compact('languages','attribute_grp_id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'attribute_name' => 'required',
            'display_name' => 'required'
        ]);

        //English Fields
        $attribute = new Attribute();
        $attribute->group_id = ($request->group_id) + 1;
        $attribute->attribute_name = $request->attribute_name;
        $attribute->display_name = $request->display_name;
        $attribute->attribute_field_type_id = $request->attribute_field_type_id;
        $attribute->lang_id = $request->eng_lang_id;
        $attribute->save();

        //Arabic Fields
        $attribute1 = new Attribute();
        $attribute1->group_id = ($request->group_id) + 1;
        $attribute1->attribute_name = $request->arabic_attribute_name;
        $attribute1->display_name = $request->arabic_display_name;
        $attribute1->attribute_field_type_id = $request->arabic_attribute_field_type_id;
        $attribute1->lang_id = $request->arabic_lang_id;
        $attribute1->save();

        Toastr::success(ATTRIBUTE_STORE_200['message']);
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('attributemanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(string $id, int $group_id): View|Factory|RedirectResponse|Application
    {
        $languages = DB::table('language_master')->get();
        $attribute_data = Attribute::orderBy('group_id', 'desc')->first();

        if (!empty($attribute_data)) {
            $attribute_grp_id = $attribute_data->group_id;
        } else {
            $attribute_grp_id = 0;
        }

        $attributes = $this->attribute->selectRaw("group_id,is_active,GROUP_CONCAT(attribute_name order By lang_id) as attribute_name,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(display_name order By lang_id) as display_name,GROUP_CONCAT(attribute_field_type_id order By lang_id) as attribute_field_type_id")
            ->where('group_id', $group_id)
            ->groupBy('group_id', 'is_active')
            ->get();

        return view('attributemanagement::admin.edit',compact('languages','attribute_grp_id','attributes'));
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
            'attribute_name' => 'required',
            'display_name' => 'required'
        ]);

        $attribute = $this->attribute->where('group_id', $id)->where('lang_id', 1)->first();
        $attribute1 = $this->attribute->where('group_id', $id)->where('lang_id', 2)->first();
//        dd($attribute1);

        if (!isset($attribute)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        //English Fields
//        $attribute->group_id = $request->group_id;

        $attribute->attribute_name = $request->attribute_name;
        $attribute->display_name = $request->display_name;
        $attribute->attribute_field_type_id = $request->attribute_field_type_id;
        $attribute->lang_id = $request->eng_lang_id;

        $attribute->save();

        //Arabic Fields
//        $attribute1->group_id = $request->group_id;
        $attribute1->attribute_name = $request->arabic_attribute_name;
        $attribute1->display_name = $request->arabic_display_name;
        $attribute1->attribute_field_type_id = $request->arabic_attribute_field_type_id;
        $attribute1->lang_id = $request->arabic_lang_id;
        $attribute1->save();

        Toastr::success(DEFAULT_UPDATE_200['message']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,$id): RedirectResponse
    {
        $attribute = $this->attribute->where('group_id', $id)->first();
        if (isset($attribute)) {
            Attribute::where('group_id', $id)->delete();
            Toastr::success(DEFAULT_DELETE_200['message']);
            return back();
        }
        Toastr::success(DEFAULT_204['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $attribute = $this->attribute->where('group_id', $id)->first();
        $this->attribute->where('group_id', $id)->update(['is_active' => !$attribute->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }
}
