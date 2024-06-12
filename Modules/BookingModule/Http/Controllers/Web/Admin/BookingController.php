<?php

namespace Modules\BookingModule\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingDetailsAmount;
use Modules\BookingModule\Entities\BookingDetail;
use Modules\BookingModule\Entities\BookingScheduleHistory;
use Illuminate\Http\RedirectResponse;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\CategoryManagement\Entities\Category;
use Modules\ProviderManagement\Entities\Provider;
use Modules\ProviderManagement\Entities\SubscribedService;
use Modules\UserManagement\Entities\Serviceman;
use Modules\ServiceManagement\Entities\Service;
use Modules\ServiceManagement\Entities\Variation;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{

    private Booking $booking;
    private BookingDetailsAmount $bookingdetailamt;
    private BookingStatusHistory $booking_status_history;
    private BookingScheduleHistory $booking_schedule_history;
    private $subscribed_sub_categories;
    private Category $category;
    private Zone $zone;
    private Serviceman $serviceman;
    private Provider $provider;
    private Service $service;
    private Variation $variation;

    public function __construct(Booking $booking, BookingStatusHistory $booking_status_history, BookingScheduleHistory $booking_schedule_history, SubscribedService $subscribedService, Category $category, Zone $zone, Serviceman $serviceman, Provider $provider, Service $service, Variation $variation, BookingDetailsAmount $bookingdetailamt)
    {
        $this->booking = $booking;
        $this->booking_status_history = $booking_status_history;
        $this->booking_schedule_history = $booking_schedule_history;
        $this->bookingdetailamt = $bookingdetailamt;
        $this->category = $category;
        $this->zone = $zone;
        $this->serviceman = $serviceman;
        $this->provider = $provider;
        $this->service = $service;
        $this->variation = $variation;
        $this->subscribedService = $subscribedService;
        try {
            $this->subscribed_sub_categories = $subscribedService->where(['is_subscribed' => 1])->pluck('sub_category_id')->toArray();
        } catch (\Exception $exception) {
            $this->subscribed_sub_categories = $subscribedService->pluck('sub_category_id')->toArray();
        }
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request)
    {
        $request->validate([
            'booking_status' => 'in:' . implode(',', array_column(BOOKING_STATUSES, 'key')) . ',all',
        ]);
        $request['booking_status'] = $request['booking_status'] ?? 'pending';

        $query_param = [];

        if ($request->has('zone_ids')) {
            $zone_ids = $request['zone_ids'];
            $query_param['zone_ids'] = $zone_ids;
        }

        if ($request->has('category_ids')) {
            $category_ids = $request['category_ids'];
            $query_param['category_ids'] = $category_ids;
        }

        if ($request->has('sub_category_ids')) {
            $sub_category_ids = $request['sub_category_ids'];
            $query_param['sub_category_ids'] = $sub_category_ids;
        }

        if ($request->has('start_date')) {
            $start_date = $request['start_date'];
            $query_param['start_date'] = $start_date;
        } else {
            $query_param['start_date'] = null;
        }

        if ($request->has('end_date')) {
            $end_date = $request['end_date'];
            $query_param['end_date'] = $end_date;
        } else {
            $query_param['end_date'] = null;
        }

        if ($request->has('search')) {
            $search = $request['search'];
            $query_param['search'] = $search;
        }

        if ($request->has('booking_status')) {
            $booking_status = $request['booking_status'];
            $query_param['booking_status'] = $booking_status;
        } else {
            $query_param['booking_status'] = 'pending';
        }

        $bookings = $this->booking->with(['customer'])
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->orWhere('readable_id', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($booking_status != 'all', function ($query) use ($booking_status) {
                $query->ofBookingStatus($booking_status);
            })
            ->when($request->has('zone_ids'), function ($query) use ($request) {
                $query->whereIn('zone_id', $request['zone_ids']);
            })->when($query_param['start_date'] != null && $query_param['end_date'] != null, function ($query) use ($request) {
                if ($request['start_date'] == $request['end_date']) {
                    $query->whereDate('created_at', Carbon::parse($request['start_date'])->startOfDay());
                } else {
                    $query->whereBetween('created_at', [Carbon::parse($request['start_date'])->startOfDay(), Carbon::parse($request['end_date'])->endOfDay()]);
                }
            })->when($request->has('sub_category_ids'), function ($query) use ($request) {
                $query->whereIn('sub_category_id', $request['sub_category_ids']);
            })->when($request->has('category_ids'), function ($query) use ($request) {
                $query->whereIn('category_id', $request['category_ids']);
            })
            ->latest()->paginate(pagination_limit())->appends($query_param);

        //for filter
        $zones = $this->zone->select('id', 'name')->get();
        $categories = $this->category->select('id', 'parent_id', 'name')->where('position', 1)->get();
        $sub_categories = $this->category->select('id', 'parent_id', 'name')->where('position', 2)->get();

        return view('bookingmodule::admin.booking.list', compact('bookings', 'zones', 'categories', 'sub_categories', 'query_param','booking_status'));
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Renderable
     */
    public function check_booking()
    {
        $this->booking->where('is_checked', 0)->update(['is_checked' => 1]); //update the unseen bookings
    }

    /**
     * Display a listing of the resource.
     * @param $id
     * @param Request $request
     * @return Renderable
     */
    public function details($id, Request $request)
    {
        Validator::make($request->all(), [
            'web_page' => 'required|in:details,status,images',
        ]);
        $web_page = $request->has('web_page') ? $request['web_page'] : 'business_setup';

        if ($request->web_page == 'details') {

            $booking = $this->booking->with(['detail.service', 'detail.variation', 'customer', 'provider', 'service_address', 'serviceman', 'service_address', 'status_histories.user','cart_images','cart_videos','cart_pdf'])->find($id);

            $servicemen = $this->serviceman->with(['user'])
                ->where('provider_id', $booking->provider_id)
                ->whereHas('user', function ($query) {
                    $query->ofStatus(1);
                })
                ->latest()
                ->get();

            $service_list = $this->service->where('lang_id', 1)->where('is_active', 1)->get();
// New Code
            $booking_sub_category_id = '';
            $catgory_find = $this->category->where('id',$booking->sub_category_id)->first();

            if($catgory_find->lang_id == 1){
                $booking_sub_category_id = $booking->sub_category_id;
            }else{
                $catgory_Arb = $this->category->where('group_id',$catgory_find->group_id)->where(['lang_id'=>1,'position'=>2])->first();
                $booking_sub_category_id = $catgory_Arb->id;
            }
// Close New Code
            $booking_subcategory_id = $this->subscribedService->where('sub_category_id', $booking_sub_category_id)->get();
//            $this->subscribed_sub_categories = $subscribedService->where(['sub_category_id' => $booking->sub_category_id])->pluck('sub_category_id')->toArray();
//            $booking_subcategory_id = $this->subscribed_sub_categories;

            $providers = $this->provider->where('is_active', 1)->get();
//            echo "<pre>";
//            print_r($booking->toArray());
//            die();
//dd($booking->cart_videos);
            return view('bookingmodule::admin.booking.details', compact('booking', 'servicemen', 'web_page','providers','booking_subcategory_id','service_list'));

        }
        elseif ($request->web_page == 'status') {
            $booking = $this->booking->with(['detail.service', 'customer', 'provider', 'service_address', 'serviceman.user', 'service_address', 'status_histories.user'])->find($id);
            return view('bookingmodule::admin.booking.status', compact('booking', 'web_page'));
        }
        elseif ($request->web_page == 'images') {
            $booking = $this->booking->with(['detail.service', 'detail.variation', 'customer', 'provider', 'service_address', 'serviceman', 'service_address', 'status_histories.user','cart_images','cart_videos','cart_pdf'])->find($id);

            return view('bookingmodule::admin.booking.images', compact('booking', 'web_page'));
        }elseif ($request->web_page == 'request'){
            $booking = $this->booking->with(['detail.service', 'detail.variation', 'customer', 'provider', 'service_address', 'serviceman', 'service_address', 'status_histories.user','cart_images','cart_videos','cart_pdf'])->find($id);

            $providerIds = explode(',', $booking->provider_selected_ids);
            $providers = $this->provider->whereIn('id', $providerIds)->where('is_active', 1)->get();

            return view('bookingmodule::admin.booking.request',compact('booking','providers','web_page'));
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'service_name' => 'required',
            'variant' => 'required',
            'service_cost' => 'required',
            'quantity' => 'required',
            'discount_amount' => 'required',
        ]);

        $service_variation = $this->variation->where('service_id', $request['service_id'])->where('invoice_item', 0)->get();

        //Insert Code in booking_details table
        $detail = new BookingDetail();
        $detail->booking_id = $request['booking_id'];
        $detail->service_id = $request['service_id'];
        $detail->service_name = $request['service_name'];
        $detail->variant_key = $request['variant'];
        $detail->quantity = $request['quantity'];
        $detail->service_cost = $request['service_cost'];
        $detail->discount_amount = $request['discount_amount'];
        $detail->campaign_discount_amount = 0.000;
        $detail->overall_coupon_discount_amount = 0.000;
        $detail->tax_amount = 0.000;
        $detail->total_cost = $request['total_cost'];
        $detail->save();

        $last_inserted_detail_id = $detail->id;

        //Insert Code in variations table
        $variation = new Variation();
        $variation->variant = $request['variant'];
        $variation->variant_key = $request['variant'];
        $variation->zone_id = $request['zone_id'];
        $variation->price = $request['service_cost'];
        $variation->service_id = $request['service_id'];
        $variation->group_id = $service_variation[0]->group_id;
        $variation->booking_detail_id = $last_inserted_detail_id;
        $variation->invoice_item = 1;
        $variation->save();

//        $variation_format = [];
//            foreach ($zones as $zone) {
//                $variation_format[] = [
//                    'variant' => $item['variant'],
//                    'variant_key' => $item['variant_key'],
//                    'zone_id' => $zone->id,
//                    'price' => $request[$item['variant_key'] . '_' . $zone->id . '_price'] ?? 0,
//                    'service_id' => $service->id,
//                    'group_id' => $service->group_id,
//                ];
//            }



        //Update Code in booking_details_amounts table
        $booking_detail_amt = $this->bookingdetailamt->where('booking_id', $request['booking_id'])->first();

        if (!isset($booking_detail_amt)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }

        $booking_detail_amt->service_unit_cost = ($booking_detail_amt->service_unit_cost) + $request['total_cost'];
        $booking_detail_amt->save();

        //Update Code in bookings table
        $booking = $this->booking->where('id', $request['booking_id'])->first();
        if (!isset($booking)) {
            return response()->json(response_formatter(DEFAULT_204), 200);
        }
        $booking->total_booking_amount = ($booking->total_booking_amount) + $request['total_cost'];
        $booking->total_discount_amount = ($booking->total_discount_amount) + $request['discount_amount'];
        $booking->save();

        Toastr::success(CATEGORY_STORE_200['message']);
        return back();
    }


    /**
     * Display a listing of the resource.
     * @param $booking_id
     * @param Request $request
     * @return JsonResponse
     */
    public function status_update($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'booking_status' => 'required|in:' . implode(',', array_column(BOOKING_STATUSES, 'key')),
        ]);

        $booking = $this->booking->where('id', $booking_id)->first();

        if (isset($booking)) {
            $booking->booking_status = $request['booking_status'];

            $booking_status_history = $this->booking_status_history;
            $booking_status_history->booking_id = $booking_id;
            $booking_status_history->changed_by = $request->user()->id;
            $booking_status_history->booking_status = $request['booking_status'];

            if ($booking->isDirty('booking_status')) {
                DB::transaction(function () use ($booking_status_history, $booking) {
                    $booking->save();
                    $booking_status_history->save();
                });

                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
            }
            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }

    public function status_provider_update($booking_id, Request $request): JsonResponse
    {

        Validator::make($request->all(), [
            'booking_status' => 'required|in:' . implode(',', array_column(BOOKING_STATUSES, 'key')),
        ]);

        $booking = $this->booking->where('id', $booking_id)->first();
        if (isset($booking)) {

            //      Change Booking Decline
            $providerUserId = $this->provider->where('id', $request['provider_id'])->first();
            if(isset($providerUserId)){
                $providerId =  json_encode($providerUserId->user_id);
                $bookingId =  json_encode($booking_id);
                $providerId = trim($providerId, '"');
                $bookingId = trim($bookingId, '"');

                $bookingDeclineStatus = $this->booking_status_history->where('booking_id', $bookingId)->where('changed_by',$providerId)->where('booking_status','decline')->first();

                if(isset($bookingDeclineStatus)){
                    BookingStatusHistory::where('booking_id', $bookingId)->where('changed_by',$providerId)->where('booking_status','decline')->delete();
                }
            }
            // Change Booking Decline Close
            $booking->booking_status = $request['booking_status'];
//            $booking->provider_id = $request['provider_id'];
            $booking->provider_id = null;
            $booking->provider_selected_ids = $request['provider_selected_ids'];


            $booking_status_history = $this->booking_status_history;
            $booking_status_history->booking_id = $booking_id;
            $booking_status_history->changed_by = $request->user()->id;
            $booking_status_history->booking_status = $request['booking_status'];

//            if ($booking->isDirty('booking_status')) {
                DB::transaction(function () use ($booking_status_history, $booking) {
                    $booking->save();
                    $booking_status_history->save();
                });
                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
//            }
//            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }


    /**
     * Display a listing of the resource.
     * @param $booking_id
     * @param Request $request
     * @return JsonResponse
     */
    public function payment_update($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $booking = $this->booking->where('id', $booking_id)->first();

        if (isset($booking)) {
            $booking->is_paid = $request->payment_status == 'paid' ? 1 : 0;

            if ($booking->isDirty('is_paid')) {
                $booking->save();
                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
            }
            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }

    /**
     * Display a listing of the resource.
     * @param $booking_id
     * @param Request $request
     * @return JsonResponse
     */
    public function schedule_upadte($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'service_schedule' => 'required',
        ]);

        $booking = $this->booking->where('id', $booking_id)->first();

        if (isset($booking)) {
            $booking->service_schedule = Carbon::parse($request->service_schedule)->toDateTimeString();

            //history
            $booking_schedule_history = $this->booking_schedule_history;
            $booking_schedule_history->booking_id = $booking_id;
            $booking_schedule_history->changed_by = $request->user()->id;
            $booking_schedule_history->schedule = $request['service_schedule'];

            if ($booking->isDirty('service_schedule')) {
                $booking->save();
                $booking_schedule_history->save();
                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
            }
            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }

    /**
     * Display a listing of the resource.
     * @param $booking_id
     * @param Request $request
     * @return JsonResponse
     */
    public function serviceman_update($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'serviceman_id' => 'required|uuid',
        ]);

        $booking = $this->booking->where('id', $booking_id)->first();

        if (isset($booking)) {
            $booking->serviceman_id = $request->serviceman_id;

            if ($booking->isDirty('serviceman_id')) {
                $booking->save();
                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
            }
            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return string|StreamedResponse
     */
    public function download(Request $request): string|StreamedResponse
    {
        $request->validate([
            'booking_status' => 'in:' . implode(',', array_column(BOOKING_STATUSES, 'key')) . ',all',
        ]);
        $request['booking_status'] = $request['booking_status'] ?? 'pending';

        $query_param = [];

        if ($request->has('zone_ids')) {
            $zone_ids = $request['zone_ids'];
            $query_param['zone_ids'] = $zone_ids;
        }

        if ($request->has('category_ids')) {
            $category_ids = $request['category_ids'];
            $query_param['category_ids'] = $category_ids;
        }

        if ($request->has('sub_category_ids')) {
            $sub_category_ids = $request['sub_category_ids'];
            $query_param['sub_category_ids'] = $sub_category_ids;
        }

        if ($request->has('start_date')) {
            $start_date = $request['start_date'];
            $query_param['start_date'] = $start_date;
        } else {
            $query_param['start_date'] = null;
        }

        if ($request->has('end_date')) {
            $end_date = $request['end_date'];
            $query_param['end_date'] = $end_date;
        } else {
            $query_param['end_date'] = null;
        }

        if ($request->has('search')) {
            $search = $request['search'];
            $query_param['search'] = $search;
        }

        if ($request->has('booking_status')) {
            $booking_status = $request['booking_status'];
            $query_param['booking_status'] = $booking_status;
        } else {
            $query_param['booking_status'] = 'pending';
        }

        $items = $this->booking->with(['customer'])
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->orWhere('readable_id', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($booking_status != 'all', function ($query) use ($booking_status) {
                $query->ofBookingStatus($booking_status);
            })
            ->when($request->has('zone_ids'), function ($query) use ($request) {
                $query->whereIn('zone_id', $request['zone_ids']);
            })->when($query_param['start_date'] != null && $query_param['end_date'] != null, function ($query) use ($request) {
                $query->whereBetween('created_at', [$request['start_date'], $request['end_date']]);
            })->when($request->has('sub_category_ids'), function ($query) use ($request) {
                $query->whereIn('sub_category_id', $request['sub_category_ids']);
            })->when($request->has('category_ids'), function ($query) use ($request) {
                $query->whereIn('category_id', $request['category_ids']);
            })
            ->latest()->get();


        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }


    /**
     * Display a listing of the resource.
     * @param $id
     * @param Request $request
     * @return Renderable
     */
    public function invoice($id, Request $request): Renderable
    {
        $booking = $this->booking->with(['detail.service', 'customer', 'provider', 'service_address', 'serviceman', 'service_address', 'status_histories.user'])->find($id);
        return view('bookingmodule::admin.booking.invoice', compact('booking'));
    }

    //Remove Item
    public function remove_item($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'detail_id' => 'required',
            'service_id' => 'required',
            'variation_id' => 'required',
            'service_cost' => 'required',
            'discount_cost' => 'required',
            'qty' => 'required',
        ]);

        //Delete Record from variations table
        Variation::where('id', $request->variation_id)->delete();

        //Delete Record from variations table
        BookingDetail::where('id', $request->detail_id)->delete();

        $total_cost = ($request->service_cost * $request->qty) - $request->discount_cost;

        //Update Record from bookings & booking_details_amounts tables
        $booking = $this->booking->where('id', $booking_id)->first();
        $booking_detail_amt = $this->bookingdetailamt->where('booking_id', $booking_id)->first();

        if (isset($booking)) {
            $booking->total_booking_amount = ($booking->total_booking_amount) - $total_cost;
            $booking->total_discount_amount = ($booking->total_discount_amount) - $request->discount_cost;

            $booking_detail_amt->service_unit_cost = ($booking_detail_amt->service_unit_cost) - $total_cost;

            if ($booking->isDirty('total_booking_amount')) {
                $booking->save();
                $booking_detail_amt->save();
                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
            }
            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }
}
