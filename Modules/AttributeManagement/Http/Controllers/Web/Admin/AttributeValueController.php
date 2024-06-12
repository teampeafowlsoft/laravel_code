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
use Modules\AttributeManagement\Entities\Attributevalue;

class AttributeValueController extends Controller
{
    private $attribute, $attributeValue;
    public function __construct(Attribute $attribute, Attributevalue $attributeValue)
    {
        $this->attribute = $attribute;
        $this->attributeValue = $attributeValue;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request): View|Factory|Application
    {
        $segment = request()->segments();
        $last_segment = end($segment);

        $attribute = $this->attribute->where('lang_id',1)->where('group_id',$last_segment)->get(['attribute_name','id'])[0];
        $attribute_name = $attribute->attribute_name;
        $attribute_id = $attribute->id;

        $request->validate([
            'status' => 'in:active,inactive,all'
        ]);

        $search = $request->has('search') ? $request['search'] : '';
        $status = $request->has('status') ? $request['status'] : 'all';
        $query_param = ['search' => $search, 'status' => $status];

        $attribute_values = $this->attributeValue->selectRaw("group_id,is_active,GROUP_CONCAT(attribute_value order By lang_id) as attribute_value,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(attribute_id order By lang_id) as attribute_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('attribute_value LIKE ?',array("%$key%"));
                }
            })
            ->where('attribute_group_id',$last_segment)
            ->groupBy('group_id', 'is_active')
            ->when($status != 'all', function ($query) use ($status) {
                $query->ofStatus($status == 'active' ? 1 : 0);
            })
            ->paginate(pagination_limit())->appends($query_param);

        return view('attributemanagement::admin.attribute-value.list', compact('search', 'status','attribute_name','attribute_values','last_segment'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request): View|Factory|Application
    {
        $segment = request()->segments();
        $last_segment = end($segment);

        $languages = DB::table('language_master')->get();
        $attribute_data = Attributevalue::orderBy('group_id','desc')->first();

        $attribute = $this->attribute->where('lang_id',1)->where('group_id',$last_segment)->get(['id'])[0];
        $attribute_id = $attribute->id;

        $attribute_arabic = $this->attribute->where('lang_id',2)->where('group_id',$last_segment)->get(['id'])[0];
        $arabic_attribute_id = $attribute_arabic->id;

        if (!empty($attribute_data)) {
            $attribute_grp_id = $attribute_data->group_id;
        } else {
            $attribute_grp_id = 0;
        }
        return view('attributemanagement::admin.attribute-value.create',compact('languages','attribute_grp_id','attribute_id','arabic_attribute_id','last_segment'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'attribute_value' => 'required',
        ]);

        //English Fields
        $attribute = new Attributevalue();
        $attribute->group_id = ($request->group_id) + 1;
        $attribute->attribute_value = $request->attribute_value;
        $attribute->attribute_id = $request->attribute_id;
        $attribute->attribute_group_id = $request->attribute_group_id;
        $attribute->lang_id = $request->eng_lang_id;
        $attribute->save();


        //Arabic Fields
        $attribute1 = new Attributevalue();
        $attribute1->group_id = ($request->group_id) + 1;
        $attribute1->attribute_value = $request->arabic_attribute_value;
        $attribute1->attribute_id = $request->arabic_attribute_id;
        $attribute1->attribute_group_id = $request->attribute_group_id;
        $attribute1->lang_id = $request->arabic_lang_id;
        $attribute1->save();

        Toastr::success(ATTRIBUTE_VALUE_STORE_200['message']);
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

        $attributes = $this->attributeValue->selectRaw("group_id,is_active,GROUP_CONCAT(attribute_id order By lang_id) as attribute_id,GROUP_CONCAT(id order By lang_id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(attribute_value order By lang_id) as attribute_value")
            ->where('group_id', $group_id)
            ->groupBy('group_id', 'is_active')
            ->get();

        return view('attributemanagement::admin.attribute-value.edit',compact('languages','attribute_grp_id','attributes'));    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'attribute_value' => 'required',
        ]);

        $attribute = $this->attributeValue->where('group_id', $id)->where('lang_id', 1)->first();
        $attribute1 = $this->attributeValue->where('group_id', $id)->where('lang_id', 2)->first();

        if (!isset($attribute)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        //English Fields
        $attribute->attribute_value = $request->attribute_value;
        $attribute->lang_id = $request->eng_lang_id;
        $attribute->save();

        //Arabic Fields
        $attribute1->attribute_value = $request->arabic_attribute_value;
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
        $attribute = $this->attributeValue->where('group_id', $id)->first();
        if (isset($attribute)) {
            Attributevalue::where('group_id', $id)->delete();
            Toastr::success(DEFAULT_DELETE_200['message']);
            return back();
        }
        Toastr::success(DEFAULT_204['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $attribute = $this->attributeValue->where('group_id', $id)->first();
        $this->attributeValue->where('group_id', $id)->update(['is_active' => !$attribute->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }
}
