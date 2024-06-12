<?php

namespace Modules\ZoneManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ZoneManagement\Entities\Zone;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Rap2hpoutre\FastExcel\FastExcel;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Modules\CountryManagement\Entities\Country;

class ZoneController extends Controller
{
    private Zone $zone;
    protected Country $country;

    public function __construct(Zone $zone, Country $country)
    {
        $this->zone = $zone;
        $this->country = $country;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request): View|Factory|Application
    {
        if (!session()->has('location')) {
            $data = Location::get($request->ip());
            $location = [
                'lat' => $data ? $data->latitude : '23.757989',
                'lng' => $data ? $data->longitude : '90.360587'
            ];
            session()->put('location', $location);
        }
        $search = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
//        $zones = $this->zone->withCount(['providers', 'categories'])
//            ->when($request->has('search'), function ($query) use ($request) {
//                $keys = explode(' ', $request['search']);
//                foreach ($keys as $key) {
//                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
//                }
//            })
//            ->when($request->has('country_id'), function ($query) use ($request) {
//                return $query->where('country_id', $request->country_id);
//            })
//            ->latest()->paginate(pagination_limit())->appends($query_param);

        $zones = $this->zone
            ->selectRaw("zones.group_id,GROUP_CONCAT(zones.country_id order By lang_id) as country_id,zones.coordinates,zones.is_active,GROUP_CONCAT(zones.name order By lang_id) as name,GROUP_CONCAT(zones.id order By lang_id) as id,GROUP_CONCAT(zones.lang_id) as lang_id")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?',array("%$key%"));
                }
            })
            ->groupBy('group_id', 'coordinates', 'is_active')
            ->when($request->has('country_id'), function ($query) use ($request) {
                return $query->where('country_id', $request->country_id);
            })
            ->paginate(pagination_limit())->appends($query_param);

        $countries = $this->country->where('lang_id',1)->where('is_active', 1)->get();
        $arabic_countries = $this->country->where('lang_id',2)->where('is_active', 1)->get();

        $languages = DB::table('language_master')->get();
//        DB::enableQueryLog();

//        $providers = $this->zone->withCount(['providers', 'categories'])->get();
        $providers = DB::table("zones")
            ->select('zones.*',DB::raw("(SELECT COUNT(*) from `providers` where `zones`.`id` = `providers`.`zone_id`) as `providers_count`"),DB::raw("(SELECT COUNT(*) from `categories` inner join `category_zone` on `categories`.`id` = `category_zone`.`category_id` where `zones`.`id` = `category_zone`.`zone_id`) as `categories_count`"))
                ->groupBy('zones.group_id')
                ->get();
//        dd(DB::getQueryLog());
        $zone_data = Zone::orderBy('group_id', 'desc')->first();

        if (!empty($zone_data)) {
            $zone_grp_id = $zone_data->group_id;
        } else {
            $zone_grp_id = 0;
        }

        return view('zonemanagement::admin.create', compact('zones', 'countries','arabic_countries', 'providers', 'zone_grp_id', 'languages', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $zone = $this->zone;
        $request->validate([
            'name' => 'required|unique:zones|max:191',
//            'arabic_name' => 'required|unique:zones|max:191',
//            'coordinates' => 'required',
            'shipping_charge' => 'required|numeric|max:10'
        ]);

        $value = !empty($request->coordinates) ? $request->coordinates : '';
        $polygon = [];
        if(!empty($value)) {
            foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
                if ($index == 0) {
                    $lastcord = explode(',', $single_array);
                }
                $coords = explode(',', $single_array);
                $polygon[] = new Point($coords[0], $coords[1]);
            }
            $polygon[] = new Point($lastcord[0], $lastcord[1]);
        }

        $zone_data = Zone::orderBy('group_id', 'desc')->first();

//        if (isset($request->eng_lang_id) && !empty($request->eng_lang_id)) {
        DB::transaction(function () use ($polygon, $request) {
            if (!empty($zone_data)) {
                $zone_grp_id = $zone_data->group_id;
            } else {
                $zone_grp_id = 0;
            }
//                $zone = $this->zone;
            $zone = new Zone;
            $zone->group_id = ($request->group_id) + 1;
            $zone->country_id = $request->country_id;
            $zone->name = $request->name;
            $zone->shipping_charge = $request->shipping_charge;
            $zone->lang_id = $request->eng_lang_id;
            $zone->coordinates = !empty($request->coordinates) ? new Polygon([new LineString($polygon)]) : null;
            $zone->save();
//                dd($zone);

            $zone1 = new Zone;
            $zone1->group_id = ($request->group_id) + 1;
            $zone1->country_id = $request->arabic_country_id;
            $zone1->name = $request->arabic_name;
            $zone1->shipping_charge = $request->shipping_charge_arabic;
            $zone1->lang_id = $request->arabic_lang_id;
            $zone1->coordinates = !empty($request->coordinates) ? new Polygon([new LineString($polygon)]) : null;
            $zone1->save();
        });
//        }

        Toastr::success(ZONE_STORE_200['message']);

        return back();
    }

    /**
     * Show the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $zone = $this->zone->where('id', $id)->first();
        if (isset($zone)) {
            return response()->json(response_formatter(DEFAULT_200, $zone), 200);
        }
        return response()->json(response_formatter(DEFAULT_204, $zone), 204);
    }

    public function edit(string $id, int $group_id): View|Factory|Application|RedirectResponse
    {
        $zone = Zone::selectRaw("group_id,GROUP_CONCAT(country_id order By lang_id) as country_id,coordinates,is_active,ST_AsText(ST_Centroid(`coordinates`)) as center,GROUP_CONCAT(name order By lang_id) as name,GROUP_CONCAT(id) as id,GROUP_CONCAT(lang_id) as lang_id,shipping_charge")
            ->where('group_id', $group_id)
            ->groupBy('group_id', 'coordinates', 'is_active')
            ->get();

        $languages = DB::table('language_master')->get();

        if (isset($zone)) {
            $countries = $this->country->where('lang_id',1)->where('is_active', 1)->get();
            $arabic_countries = $this->country->where('lang_id',2)->where('is_active', 1)->get();


            $current_zone = ($zone[0]->center != null) ? format_coordinates($zone[0]->coordinates) : '';

            $center_lat = ($zone[0]->center != null) ? trim(explode(' ', $zone[0]->center)[1], 'POINT()') : '';
            $center_lng = ($zone[0]->center != null) ? trim(explode(' ', $zone[0]->center)[0], 'POINT()') : '';

            return view('zonemanagement::admin.edit', compact('zone', 'current_zone', 'center_lat', 'center_lng', 'countries', 'arabic_countries', 'languages'));
        }

        Toastr::error(DEFAULT_204['message']);
        return back();
    }

    public function get_active_zones($id): JsonResponse
    {
//        $all_zones = Zone::where('id', '<>', $id)->where('is_active', 1)->get();
        $all_zones = Zone::where('group_id', '<>', $id)->where('is_active', 1)->get();
        $all_zone_data = [];

        foreach ($all_zones as $item) {
            $data = [];
            foreach ($item->coordinates as $coordinate) {
                $data[] = (object)['lat' => $coordinate->lat, 'lng' => $coordinate->lng];
            }
            $all_zone_data[] = $data;
        }
        return response()->json($all_zone_data, 200);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function status_update(Request $request, $id): JsonResponse
    {
        $zone = $this->zone->where('id', $id)->first();
        $this->zone->where('id', $id)->update(['is_active' => !$zone->is_active]);

        return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:191',
//            'arabic_name' => 'required|max:191',
//            'coordinates' => 'required',
            'shipping_charge' => 'required|numeric|max:10'
        ]);

        $value = !empty($request->coordinates) ? $request->coordinates : '';
        $polygon = [];
        if(!empty($value)) {
            foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
                if ($index == 0) {
                    $lastcord = explode(',', $single_array);
                }
                $coords = explode(',', $single_array);
                $polygon[] = new Point($coords[0], $coords[1]);
            }
            $polygon[] = new Point($lastcord[0], $lastcord[1]);
        }
        $zone = $this->zone->where('group_id', $id)->where('lang_id', $request->eng_lang_id)->first();
        $zone1 = $this->zone->where('group_id', $id)->where('lang_id', $request->arabic_lang_id)->first();

        if (!isset($zone) || !isset($zone1)) {
            Toastr::success(ZONE_404['message']);
            return back();
        }

        $zone->country_id = $request->country_id;
        $zone->name = $request->name;
        $zone->shipping_charge = $request->shipping_charge;
        $zone->lang_id = $request->eng_lang_id;
        $zone->coordinates = !empty($request->coordinates) ? new Polygon([new LineString($polygon)]) : null;
        $zone->save();

        $zone1->country_id = $request->arabic_country_id;
        $zone1->name = $request->arabic_name;
        $zone1->shipping_charge = $request->shipping_charge_arabic;
        $zone1->lang_id = $request->arabic_lang_id;
        $zone1->coordinates = !empty($request->coordinates) ? new Polygon([new LineString($polygon)]) : null;
        $zone1->save();

        Toastr::success(ZONE_UPDATE_200['message']);
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
        $this->zone->where('group_id', $id)->delete();
        Toastr::success(ZONE_DESTROY_200['message']);
        return back();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $items = $this->zone
            ->selectRaw("GROUP_CONCAT(zones.id) as id,zones.group_id,GROUP_CONCAT(zones.name order By lang_id) as name,zones.country_id,zones.coordinates,GROUP_CONCAT(zones.lang_id) as lang_id,zones.is_active")
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?',array("%$key%"));
                }
            })
            ->groupBy('group_id', 'country_id', 'coordinates', 'is_active')
            ->when($request->has('country_id'), function ($query) use ($request) {
                return $query->where('country_id', $request->country_id);
            })->latest()->get();

//        $items = $this->zone->withCount(['providers', 'categories'])
//            ->when($request->has('search'), function ($query) use ($request) {
//                $keys = explode(' ', $request['search']);
//                foreach ($keys as $key) {
//                    $query->orWhere('name', 'LIKE', '%' . $key . '%');
//                }
//            })
//            ->latest()->get();
//        $list = collect([
//            [ 'id' => 1, 'name' => 'Jane' ],
//            [ 'id' => 2, 'name' => 'John' ],
//        ]);
        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }

}
