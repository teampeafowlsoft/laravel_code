<?php

namespace Modules\CountryManagement\Http\Controllers\Web\Admin;

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
use Modules\CountryManagement\Entities\Country;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CountryManagementController extends Controller
{
    private Country $country;
    public function __construct(Country $country)
    {
        $this->country = $country;
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

//        $country = $this->country
//            ->when($request->has('search'), function ($query) use ($request) {
//                $keys = explode(' ', $request['search']);
//                foreach ($keys as $key) {
//                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
//                }
//            })
//            ->when($status != 'all', function ($query) use ($status) {
//                $query->ofStatus($status == 'active' ? 1 : 0);
//            })
//            ->latest()->paginate(pagination_limit())->appends($query_param);
        $country = $this->country->selectRaw("group_id,country_type,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(country_code order By lang_id) as country_code,GROUP_CONCAT(currency_name order By lang_id) as currency_name,GROUP_CONCAT(currency_code order By lang_id) as currency_code,GROUP_CONCAT(country_flag order By lang_id) as country_flag")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?',array("%$key%"));
                }
            })
            ->groupBy('group_id', 'country_type', 'is_active')
            ->when($status != 'all', function ($query) use ($status) {
                $query->ofStatus($status == 'active' ? 1 : 0);
            })
//            ->ofType('main')
            ->latest()->paginate(pagination_limit())->appends($query_param);
//        dd($country);

        $languages = DB::table('language_master')->get();
        $country_data = Country::orderBy('group_id', 'desc')->first();
        if (!empty($country_data)) {
            $country_grp_id = $country_data->group_id;
        } else {
            $country_grp_id = 0;
        }
        return view('countrymanagement::admin.create', compact('country','search', 'status', 'languages','country_grp_id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_type' => 'required',
            'name' => 'required',
            'country_code' => 'required',
            'currency_name' => 'required',
            'currency_code' => 'required',
            'country_flag' => 'required|image|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        $image_name = file_uploader('country/', 'png', $request->file('country_flag'));

        //English Code
        $country = new Country();
//        $country = $this->country;
        $country->group_id = ($request->group_id) + 1;
        $country->country_type = $request['country_type'];
        $country->name = $request['name'];
        $country->country_code = $request['country_code'];
        $country->currency_name = $request['currency_name'];
        $country->currency_code = $request['currency_code'];
        $country->country_flag = $image_name;
        $country->lang_id = $request->eng_lang_id;
        $country->is_active = 1;
        $country->save();

        //Arabic Code
        $country1 = new Country();
//        $country = $this->country;
        $country1->group_id = ($request->group_id) + 1;
        $country1->country_type = $request['arabic_country_type'];
        $country1->name = $request['arabic_name'];
        $country1->country_code = $request['arabic_country_code'];
        $country1->currency_name = $request['arabic_currency_name'];
        $country1->currency_code = $request['arabic_currency_code'];
        $country1->country_flag = $image_name;
        $country1->lang_id = $request->arabic_lang_id;
        $country1->is_active = 1;
        $country1->save();

        Toastr::success(COUNTRY_CREATE_200['message']);
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('countrymanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(string $id, int $group_id): View|Factory|Application|RedirectResponse
    {
//        $country = $this->country->where('id', $id)->first();

        $country = Country::selectRaw("group_id,country_type,is_active,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,GROUP_CONCAT(country_code order By lang_id) as country_code,GROUP_CONCAT(currency_name order By lang_id) as currency_name,GROUP_CONCAT(currency_code order By lang_id) as currency_code,GROUP_CONCAT(country_flag order By lang_id) as country_flag")
            ->where('group_id', $group_id)
            ->groupBy('group_id', 'country_type', 'is_active')
            ->get();

        $languages = DB::table('language_master')->get();

        if (isset($country)) {
            return view('countrymanagement::admin.edit', compact('country','languages'));
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
            'country_type' => 'required',
            'name' => 'required',
            'country_code' => 'required',
            'currency_name' => 'required',
            'currency_code' => 'required',
        ]);

//        $country = $this->country->where('id', $id)->first();
        $country = $this->country->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();
        $country1 = $this->country->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!$country) {
            return response()->json(response_formatter(COUNTRY_204), 204);
        }
//        $country->country_type = $request->country_type;
//        $country->name = $request->name;
//        if ($request->has('country_flag')) {
//            $country->country_flag = file_uploader('country/', 'png', $request->file('country_flag'), $country->country_flag);
//        }
//        $country->country_code = $request->country_code;
//        $country->currency_name = $request->currency_name;
//        $country->currency_code = $request->currency_code;
//        $country->save();

        //English Code
//        $country = $this->country;
        if ($request->has('country_flag')) {
            $country->country_flag = file_uploader('country/', 'png', $request->file('country_flag'), $country->country_flag);
        }
        $country->country_type = $request['country_type'];
        $country->name = $request['name'];
        $country->country_code = $request['country_code'];
        $country->currency_name = $request['currency_name'];
        $country->currency_code = $request['currency_code'];
        $country->lang_id = $request->eng_lang_id;
        $country->is_active = 1;
        $country->save();

        //Arabic Code
//        $country = $this->country;
        $country1->country_type = $request['arabic_country_type'];
        $country1->name = $request['arabic_name'];
        $country1->country_code = $request['arabic_country_code'];
        $country1->currency_name = $request['arabic_currency_name'];
        $country1->currency_code = $request['arabic_currency_code'];
        if ($request->has('country_flag')) {
            $country1->country_flag = file_uploader('country/', 'png', $request->file('country_flag'), $country1->country_flag);
        }
        $country1->lang_id = $request->arabic_lang_id;
        $country1->is_active = 1;

        $country1->save();

        Toastr::success(COUNTRY_UPDATE_200['message']);
        return back();
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $country = $this->country->where('group_id', $id)->first();
        if (isset($country)) {
            file_remover('category/', $country->country_flag);
            Country::where('group_id', $id)->delete();

//            $country->delete();
            Toastr::success(COUNTRY_DESTROY_200['message']);
            return back();
        }
        Toastr::success(COUNTRY_204['message']);
        return back();
    }

    public function status_update(Request $request, $id): JsonResponse
    {
        $country = $this->country->where('group_id', $id)->first();
        $this->country->where('group_id', $id)->update(['is_active' => !$country->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->country
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
                }
            })
            ->latest()->latest()->get();
        return (new FastExcel($items))->download(time().'-file.xlsx');
    }
}
