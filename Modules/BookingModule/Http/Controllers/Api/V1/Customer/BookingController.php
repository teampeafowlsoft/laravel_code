<?php

namespace Modules\BookingModule\Http\Controllers\Api\V1\Customer;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingDetail;
use Modules\BookingModule\Entities\BookingDetailsAmount;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\BookingModule\Http\Traits\BookingTrait;
use Modules\ServiceManagement\Entities\Variation;

class BookingController extends Controller
{
    use BookingTrait;
    private Booking $booking;
    private BookingDetail $bookingdetail;
    private BookingDetailsAmount $bookingdetailamt;
    private BookingStatusHistory $booking_status_history;
    private Variation $variation;


    public function __construct(Booking $booking, BookingStatusHistory $booking_status_history, Variation $variation, BookingDetailsAmount $bookingdetailamt, BookingDetail $bookingdetail)
    {
        $this->booking = $booking;
        $this->booking_status_history = $booking_status_history;
        $this->bookingdetailamt = $bookingdetailamt;
        $this->bookingdetail = $bookingdetail;
        $this->variation = $variation;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function place_request(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:' . implode(',', array_column(PAYMENT_METHODS, 'key')),
            'zone_id' => 'required|uuid',
            'service_schedule' => 'required|date',
            'service_address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $this->place_booking_request($request->user()->id, $request, 'cash-payment');

        return response()->json(response_formatter(DEFAULT_200), 200);
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000',
            'booking_status' => 'required|in:all,' . implode(',', array_column(BOOKING_STATUSES, 'key')),
            'string' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
//        $cid = ($request->user()->id);
//        echo "customer_id : " . $cid;
        $bookings = $this->booking->with(['customer'])
            ->where(['customer_id' => $request->user()->id])
            ->when($request->has('string'), function ($query) use ($request) {
                $keys = explode(' ', base64_decode($request['string']));
                foreach ($keys as $key) {
                    $query->orWhere('id', 'LIKE', '%' . $key . '%');
                }
            })
            ->when($request['booking_status'] != 'all', function ($query) use ($request) {
                return $query->ofBookingStatus($request['booking_status']);
            })
            ->latest()
            ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');

        return response()->json(response_formatter(DEFAULT_200, $bookings), 200);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $booking = $this->booking->where(['customer_id' => $request->user()->id])->with([
            'detail.service', 'schedule_histories.user', 'status_histories.user', 'service_address', 'customer', 'provider', 'zone', 'serviceman.user'
        ])->where(['id' => $id])->first();
        if (isset($booking)) {
            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param string $booking_id
     * @return JsonResponse
     */
    public function status_update(Request $request, string $booking_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_status' => 'required|in:canceled',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking->where('id', $booking_id)->where('customer_id', $request->user()->id)->first();


        if (isset($booking)) {
            $booking->booking_status = $request['booking_status'];

            $booking_status_history = $this->booking_status_history;
            $booking_status_history->booking_id = $booking_id;
            $booking_status_history->changed_by = $request->user()->id;
            $booking_status_history->booking_status = $request['booking_status'];


            DB::transaction(function () use ($booking_status_history, $booking) {
                $booking->save();
                $booking_status_history->save();
            });

            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'service_name' => 'required',
            'variant' => 'required',
            'service_cost' => 'required',
            'quantity' => 'required',
//            'discount_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking->where('id', $request['booking_id'])->first();
        $booking_data = $this->bookingdetail->where('booking_id', $request['booking_id'])->first();

        $service_variation = $this->variation
            ->where('service_id', $booking_data->service_id)
            ->where('invoice_item', 0)
            ->first();

        if (isset($booking_data)) {
            //Insert Code in booking_details table
            $total_cost = ($request['service_cost'] * $request['quantity']) - $request['discount_amount'];
            $detail = new BookingDetail();
            $detail->booking_id = $request['booking_id'];
            $detail->service_id = $booking_data->service_id;
            $detail->service_name = $request['service_name'];
            $detail->variant_key = $request['variant'];
            $detail->quantity = $request['quantity'];
            $detail->service_cost = $request['service_cost'];
            $detail->discount_amount = (!empty($request['discount_amount']))?$request['discount_amount']:0.000;
            $detail->campaign_discount_amount = 0.000;
            $detail->overall_coupon_discount_amount = 0.000;
            $detail->tax_amount = 0.000;
            $detail->total_cost = $total_cost;
            $detail->is_paid = 0;
            $detail->payment_method = 'cash';
            $detail->transaction_id = 'cash-payment';
            $detail->save();

            $last_inserted_detail_id = $detail->id;

            //Insert Code in variations table
            $variation = new Variation();
            $variation->variant = $request['variant'];
            $variation->variant_key = $request['variant'];
            $variation->zone_id = $booking->zone_id;
            $variation->price = $request['service_cost'];
            $variation->service_id = $booking_data->service_id;
            $variation->group_id = !empty($service_variation) ? $service_variation->group_id : 0;
            $variation->booking_detail_id = $last_inserted_detail_id;
            $variation->invoice_item = 1;
            $variation->save();

            //Update Code in booking_details_amounts table
            $booking_detail_amt = $this->bookingdetailamt->where('booking_id', $request['booking_id'])->first();

            if (!isset($booking_detail_amt)) {
                return response()->json(response_formatter(DEFAULT_204), 200);
            }

            $booking_detail_amt->service_unit_cost = ($booking_detail_amt->service_unit_cost) + $total_cost;
            $booking_detail_amt->save();

            //Update Code in bookings table
            if (!isset($booking)) {
                return response()->json(response_formatter(DEFAULT_204), 200);
            }
            $booking->total_booking_amount = ($booking->total_booking_amount) + $total_cost;
            //$booking->total_booking_unpaid = ($booking->total_booking_unpaid) + $total_cost;
            $booking->total_discount_amount = ($booking->total_discount_amount) + (!empty($request['discount_amount']))?$request['discount_amount']:0.000;
            $booking->save();

            return response()->json(response_formatter(DEFAULT_STORE_200), 200);
        }
        return response()->json(response_formatter(DEFAULT_404), 200);
    }

    //Remove Item
    public function remove_item2(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'detail_id' => 'required',
            'service_id' => 'required',
            'variation_id' => 'required',
            'service_cost' => 'required',
            'discount_cost' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }
        //Delete Record from variations table
        Variation::where('id', $request->variation_id)->delete();

        //Delete Record from variations table
        BookingDetail::where('id', $request->detail_id)->delete();

        $total_cost = ($request->service_cost * $request->qty) - $request->discount_cost;

        //Update Record from bookings & booking_details_amounts tables
        $booking = $this->booking->where('id', $request->booking_id)->first();
        $booking_detail_amt = $this->bookingdetailamt->where('booking_id',$request->booking_id)->first();

        if (isset($booking)) {
            $booking->total_booking_amount = ($booking->total_booking_amount) - $total_cost;
            $booking->total_discount_amount = ($booking->total_discount_amount) - $request->discount_cost;

            $booking_detail_amt->service_unit_cost = ($booking_detail_amt->service_unit_cost) - $total_cost;

//            if ($booking->isDirty('total_booking_amount')) {
                $booking->save();
                $booking_detail_amt->save();
                return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
//            }
//            return response()->json(response_formatter(NO_CHANGES_FOUND), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }

    public function remove_item(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'detail_id' => 'required',
            'service_id' => 'required',
//            'variation_id' => 'required',
//            'service_cost' => 'required',
//            'discount_cost' => 'required',
//            'qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking_detail_data = BookingDetail::where('id', $request->detail_id)->first();
        $service_cost = $booking_detail_data->service_cost;
        $discount_cost = $booking_detail_data->discount_amount;
        $quantity = $booking_detail_data->quantity;
        $total_cost = $booking_detail_data->total_cost;

        //Update Record from bookings & booking_details_amounts tables
        $booking = $this->booking->where('id', $request->booking_id)->first();
        $booking_detail_amt = $this->bookingdetailamt->where('booking_id',$request->booking_id)->first();

        if (isset($booking)) {
            $booking->total_booking_amount = ($booking->total_booking_amount) - $total_cost;
            //$booking->total_booking_unpaid = ($booking->total_booking_unpaid) - $total_cost;
            $booking->total_discount_amount = ($booking->total_discount_amount) - $discount_cost;

            $booking_detail_amt->service_unit_cost = ($booking_detail_amt->service_unit_cost) - $total_cost;

//            if ($booking->isDirty('total_booking_amount')) {
            $booking->save();
            $booking_detail_amt->save();

            //Delete Record from variations table
            Variation::where('booking_detail_id', $request->detail_id)->delete();

            //Delete Record from variations table
            BookingDetail::where('id', $request->detail_id)->delete();

            return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
//            }
//            return response()->json(response_formatter(NO_CHANGES_FOUND), 200);
        }

        return response()->json(response_formatter(DEFAULT_204), 200);
    }

}
