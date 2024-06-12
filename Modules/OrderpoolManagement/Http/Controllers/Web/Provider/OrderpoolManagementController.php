<?php

namespace Modules\OrderpoolManagement\Http\Controllers\Web\Provider;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingScheduleHistory;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\CategoryManagement\Entities\Category;
use Modules\OrderpoolManagement\Entities\Order;
use Modules\ProductCategoryManagement\Entities\Productcategory;
use Modules\ProductManagement\Entities\Product;
use Modules\ProductManagement\Entities\ProductCartBooking;
use Modules\ProductManagement\Entities\ProductCartBookingScheduleHistory;
use Modules\ProductManagement\Entities\ProductCartBookingStatusHistory;
use Modules\ProviderManagement\Entities\Provider;
use Modules\ProviderManagement\Entities\SubscribedService;
use Modules\UserManagement\Entities\Serviceman;
use Modules\ZoneManagement\Entities\Zone;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderpoolManagementController extends Controller
{
    private Order $order;
    private Booking $booking;
    private BookingStatusHistory $booking_status_history;
    private BookingScheduleHistory $booking_schedule_history;
    private ProductCartBooking $productcartbooking;
    private ProductCartBookingScheduleHistory $productcartbookingschedulehistory;
    private ProductCartBookingStatusHistory $productcartbookingstatushistory;
    private $subscribed_sub_categories;
    private Category $category;
    private Zone $zone;
    private Serviceman $serviceman;
    private Provider $provider;
    private Productcategory $productcategory;
    private Product $product;

    public function __construct(Order $order,Productcategory $productcategory, Booking $booking, BookingStatusHistory $booking_status_history, BookingScheduleHistory $booking_schedule_history, SubscribedService $subscribedService, Category $category, Zone $zone, Serviceman $serviceman, Provider $provider, ProductCartBooking $productcartbooking, ProductCartBookingScheduleHistory $productcartbookingschedulehistory, ProductCartBookingStatusHistory $productcartbookingstatushistory, Product $product)
    {
        $this->order = $order;
        $this->productcategory = $productcategory;
        $this->booking = $booking;
        $this->booking_status_history = $booking_status_history;
        $this->booking_schedule_history = $booking_schedule_history;
        $this->product = $product;
        $this->productcartbooking = $productcartbooking;
        $this->productcartbookingschedulehistory = $productcartbookingschedulehistory;
        $this->productcartbookingstatushistory = $productcartbookingstatushistory;
        $this->category = $category;
        $this->zone = $zone;
        $this->serviceman = $serviceman;
        $this->provider = $provider;
        $this->subscribedService = $subscribedService;

        try {
            $this->subscribed_sub_categories = $subscribedService->where(['is_subscribed' => 1])->pluck('sub_category_id')->toArray();
        } catch (\Exception $exception) {
            $this->subscribed_sub_categories = $subscribedService->pluck('sub_category_id')->toArray();
        }
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $request->validate([
            'order_status' => 'in:' . implode(',', array_column(ORDER_STATUSES, 'key')) . ',all',
        ]);
        $request['order_status'] = $request['order_status'] ?? 'received';

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

        if ($request->has('order_status')) {
            $order_status = $request['order_status'];
            $query_param['order_status'] = $order_status;
        } else {
            $query_param['order_status'] = 'received';
        }

        $bookings = $this->productcartbooking->with(['customer'])
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $keys = explode(' ', $request['search']);
                    foreach ($keys as $key) {
                        $query->orWhere('readable_id', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->when($order_status != 'all', function ($query) use ($order_status) {
                $query->ofBookingStatus($order_status);
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
            ->where('provider_id', 'LIKE', '%'.$request->user()->provider->id.'%')
            ->latest()->paginate(pagination_limit())->appends($query_param);

        //for filter
        $zones = $this->zone->select('id', 'name')->get();
        $categories = $this->productcategory->select('id', 'parent_id', 'name')->where('position', 1)->get();
        $sub_categories = $this->productcategory->select('id', 'parent_id', 'name')->where('position', 2)->get();

        return view('orderpoolmanagement::provider.orderpool.list', compact('bookings', 'zones', 'categories', 'sub_categories', 'query_param','order_status'));
    }

//    public function check_booking($id)
//    {
//        $this->productcartbooking->where('id', $id)->whereIn('sub_category_id', $this->subscribed_sub_categories)
//            ->where('is_checked', 0)->update(['is_checked' => 1]); //update the unseen bookings
//    }

    public function details($id, Request $request)
    {
        Validator::make($request->all(), [
            'web_page' => 'required|in:details,status',
        ]);

        $web_page = $request->has('web_page') ? $request['web_page'] : 'details';
        $booking = $this->productcartbooking->with(['detail.service', 'detail.variation', 'customer', 'provider', 'service_address', 'serviceman', 'service_address', 'status_histories.user'])->find($id);

        if ($booking['booking_status'] != 'processed' && $booking['provider_id'] != $request->user()->provider->id) {
            Toastr::error(ACCESS_DENIED['message']);
            return redirect(route('provider.orderpool.list'));
        }

        if ($request->web_page == 'details') {
            $servicemen = $this->serviceman->with(['user'])
                ->whereHas('user', function($q){
                    $q->ofStatus(1);
                })
                ->where('provider_id', $this->provider->where('user_id', $request->user()->id)->first()->id)
                ->latest()
                ->get();

            return view('orderpoolmanagement::provider.orderpool.details', compact('booking', 'servicemen', 'web_page'));

        } elseif ($request->web_page == 'status') {
            return view('orderpoolmanagement::provider.orderpool.status', compact('booking', 'web_page'));
        }
    }

    public function status_update($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'booking_status' => 'required|in:' . implode(',', array_column(ORDER_STATUSES, 'key')),
        ]);

        $booking = $this->productcartbooking->where('id', $booking_id)->where(function ($query) use ($request) {
            return $query->where('provider_id', $request->user()->provider->id)->orWhereNull('provider_id');
        })->first();

        if (isset($booking)) {
            $booking->booking_status = $request['booking_status'];
            $booking->provider_id = $request->user()->provider->id;
            $booking->service_schedule = date('Y-m-d H:i:s');

            $booking_status_history = $this->productcartbookingstatushistory;
            $booking_status_history->product_cart_booking_id = $booking_id;
            $booking_status_history->changed_by = $request->user()->id;
            $booking_status_history->booking_status = $request['booking_status'];

            if ($booking->isDirty('booking_status')) {
                DB::transaction(function () use ($booking_status_history, $booking) {
                    $booking->save();
                    $booking_status_history->save();
                });

//                self::check_booking($booking->id);

                return response()->json(DEFAULT_STATUS_UPDATE_200, 200);
            }
            return response()->json(NO_CHANGES_FOUND, 200);
        }
        return response()->json(DEFAULT_204, 200);
    }

    public function payment_update($booking_id, Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $booking = $this->productcartbooking->where('id', $booking_id)->where(function ($query) use ($request) {
            return $query->where('provider_id', $request->user()->provider->id)->orWhereNull('provider_id');
        })->first();

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

    public function download(Request $request): string|StreamedResponse
    {
        $request->validate([
            'order_status' => 'in:' . implode(',', array_column(ORDER_STATUSES, 'key')) . ',all',
        ]);
        $request['order_status'] = $request['order_status'] ?? 'received';

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

        if ($request->has('sku')) {
            $sku = $request['sku'];
            $query_param['sku'] = $sku;
        } else {
            $query_param['sku'] = null;
        }

        if ($request->has('search')) {
            $search = $request['search'];
            $query_param['search'] = $search;
        }

        if ($request->has('order_status')) {
            $order_status = $request['order_status'];
            $query_param['order_status'] = $order_status;
        } else {
            $query_param['order_status'] = 'received';
        }

        $items = DB::table('order_items')->selectRaw("order_items.*,products.image,products.name,products.sku,products.category_id,products.vendor,attributes.attribute_name,attributevalues.attribute_value,users.first_name as first_name,users.id as uid")

            ->leftJoin('product_variant', 'order_items.product_variant_id', '=', 'product_variant.id')
            ->leftJoin('products', 'products.id', '=', 'product_variant.product_id')
            ->leftJoin('users', 'users.id', '=', 'order_items.user_id')
            ->leftJoin('attributes', 'attributes.id', '=', 'product_variant.packate_measurement_attribute_id')
            ->leftJoin('attributevalues', 'attributevalues.id', '=', 'product_variant.packate_measurement_attribute_value')
            ->where('order_items.status', $order_status)
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orHavingRaw('name LIKE ?', array("%$key%"));
                }
            })
            ->when($query_param['start_date'] != null && $query_param['end_date'] != null, function ($query) use ($request) {
                if ($request['start_date'] == $request['end_date']) {
                    $query->whereDate('order_items.created_at', $request['start_date']);
                } else {
                    $query->whereBetween('order_items.created_at', [$request['start_date'], $request['end_date']]);
                }
            })
            ->when($request->has('sku'), function ($query) use ($request) {
                $query->where('sku', $request['sku']);
            })
            ->when($request->has('sub_category_ids'), function ($query) use ($request) {
                $query->whereIn('sub_category_id', $request['sub_category_ids']);
            })->when($request->has('category_ids'), function ($query) use ($request) {
                $query->whereIn('category_id', $request['category_ids']);
            })
            ->latest()->get();

        return (new FastExcel($items))->download(time() . '-file.xlsx');
    }

    public function invoice($id, Request $request): Renderable
    {
        $booking = $this->productcartbooking->with(['detail.service', 'detail.variation', 'customer', 'provider', 'service_address', 'serviceman', 'service_address', 'status_histories.user'])->find($id);
        return view('orderpoolmanagement::provider.orderpool.invoice', compact('booking'));
    }
}
