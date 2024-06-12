<?php

namespace Modules\BookingModule\Http\Controllers\Api\V1\Provider;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingScheduleHistory;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\ProviderManagement\Entities\SubscribedService;
use Rap2hpoutre\FastExcel\FastExcel;

class BookingController extends Controller
{

    private Booking $booking;
    private BookingStatusHistory $booking_status_history;
    private BookingScheduleHistory $booking_schedule_history;
    private $subscribed_sub_categories;

    public function __construct(Booking $booking, BookingStatusHistory $booking_status_history, BookingScheduleHistory $booking_schedule_history, SubscribedService $subscribedService)
    {
        $this->booking = $booking;
        $this->booking_status_history = $booking_status_history;
        $this->booking_schedule_history = $booking_schedule_history;
        try {
            $this->subscribed_sub_categories = $subscribedService->where(['provider_id' => auth('api')->user()->provider->id])
                ->where(['is_subscribed' => 1])->pluck('sub_category_id')->toArray();
        } catch (\Exception $exception) {
            $this->subscribed_sub_categories = $subscribedService->pluck('sub_category_id')->toArray();
        }
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
            'booking_status' => 'required|in:' . implode(',', array_column(BOOKING_STATUSES, 'key')) . ',all',
            'zone_ids' => 'array',
            'from_date' => 'date',
            'to_date' => 'date',
            'sub_category_ids' => 'array',
            'sub_category_ids.*' => 'uuid',
            'category_ids' => 'array',
            'category_ids.*' => 'uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        if ($request['booking_status'] != 'pending'){
            $bookings = $this->booking
                ->with(['customer'])
                //->with(['status_histories'])
                ->with(['status_histories'=> function($q) {
                    $q->where('booking_status', '!=',"decline");
                }])
                ->when($request->has('string'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $keys = explode(' ', base64_decode($request['string']));
                        foreach ($keys as $key) {
                            $query->orWhere('bookings.readable_id', 'LIKE', '%' . $key . '%');
                        }
                    });
                })
                ->when(!in_array($request['booking_status'], ['pending', 'all']), function ($query) use ($request) {
                    $query->ofBookingStatus($request['booking_status'])->where('bookings.provider_id', 'LIKE', '%'.$request->user()->provider->id.'%');
                })
                ->when($request['booking_status'] == 'all', function ($query) use ($request) {
                    $query->where('bookings.provider_selected_ids', 'LIKE', '%'.$request->user()->provider->id.'%');
                })
                ->when($request['booking_status'] == 'pending', function ($query) use ($request) {
                    $query->ofBookingStatus($request['booking_status'])->where('bookings.provider_selected_ids',  'LIKE', '%'.$request->user()->provider->id.'%');
                })
                ->when($request->has('zone_ids'), function ($query) use ($request) {
                    $query->whereIn('bookings.zone_id', $request['zone_ids']);
                })
                ->when($request->has('from_date') && $request->has('to_date'), function ($query) use ($request) {
                    $query->whereBetween('bookings.created_at', [$request['from_date'], $request['to_date']]);
                })
                ->when($request->has('sub_category_ids'), function ($query) use ($request) {
                    $query->whereIn('bookings.sub_category_id', [$request['sub_category_ids']]);
                })
                ->when($request->has('category_ids'), function ($query) use ($request) {
                    $query->whereIn('bookings.category_id', [$request['category_ids']]);
                })
                ->latest()
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
        }
        else {
            $bookings = $this->booking
                ->select('bookings.*')
                ->with(['customer'])
                ->where('bookings.booking_status', $request['booking_status'])
                ->where('bookings.provider_selected_ids', 'LIKE', '%' . $request->user()->provider->id . '%')
                ->whereRaw("NOT EXISTS (
                        SELECT * FROM (
                            SELECT * FROM booking_status_histories
                            WHERE booking_status_histories.changed_by = (
                                SELECT user_id FROM providers
                                WHERE providers.id LIKE '" . $request->user()->provider->id . "'
                            )
                            AND booking_status_histories.booking_status = 'decline'
                        ) as t1 WHERE t1.booking_id = bookings.id
                    )")
                ->latest('bookings.created_at')
                ->paginate($request['limit'], ['*'], 'offset', $request['offset'])->withPath('');
        }

        return response()->json(response_formatter(DEFAULT_200, $bookings), 200);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function download(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_status' => 'required|in:all,' . implode(',', array_column(BOOKING_STATUSES, 'key')),
            'zone_ids' => 'array',
            'from_date' => 'date',
            'to_date' => 'date',
            'sub_category_ids' => 'array',
            'sub_category_ids.*' => 'uuid',
            'category_ids' => 'array',
            'category_ids.*' => 'uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $bookings = $this->booking->where('provider_id', $request->user()->id)
            ->when($request['booking_status'] != 'all', function ($query) use ($request) {
                $query->ofBookingStatus($request['booking_status']);
            })->when($request->has('zone_ids'), function ($query) use ($request) {
                $query->whereIn('zone_id', $request['zone_ids']);
            })->when($request->has('from_date') && $request->has('to_date'), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request['from_date'], $request['to_date']]);
            })->when($request->has('sub_category_ids'), function ($query) use ($request) {
                $query->whereIn('sub_category_id', [$request['sub_category_ids']]);
            })->when($request->has('category_ids'), function ($query) use ($request) {
                $query->whereIn('category_id', [$request['category_ids']]);
            })->latest()->get();

        if (!Storage::disk('public')->exists('/download')) {
            Storage::disk('public')->makeDirectory('/download');
        }
        return response()->json(response_formatter(DEFAULT_200, ['download_link' => (new FastExcel($bookings))->export('storage/app/public/download/bookings-' . date('Y-m-d') . '-' . rand(1000, 99999) . '.xlsx')]), 200);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
//    public function show(Request $request, string $id)
//    {
////        $booking = $this->booking->with([
////            'detail.service', 'schedule_histories.user', 'status_histories.user', 'service_address', 'customer', 'provider', 'zone', 'serviceman.user'
////        ])->where(function ($query) use ($request) {
////            return $query->where('provider_id', $request->user()->provider->id)->orWhereNull('provider_id');
////        })->where(['id' => $id])->first();
////
////
////        if (isset($booking)) {
////            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
////        }
////        return response()->json(response_formatter(DEFAULT_204), 200);
//
//        $booking = $this->booking->with([
//            'detail.service',
//            'schedule_histories.user',
//            'status_histories.user',
//            'service_address',
//            'customer',
//            'provider',
//            'zone',
//            'serviceman.user'
//        ])
//            ->where(function ($query) use ($request) {
//                return $query->where('provider_id', $request->user()->provider->id)
//                    ->orWhereNull('provider_id');
//            })
//            ->where(['id' => $id])
//            ->whereDoesntHave('status_histories', function ($query) {
//                $query->where('booking_status', 'decline');
//            })
//            ->first();
//
//        if (isset($booking)) {
//            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
//        }
//        return response()->json(response_formatter(DEFAULT_204), 200);
//
//    }

    public function show(Request $request, string $id)
    {
//        $bookings = $this->booking
//            ->leftJoin('booking_details','bookings.id','booking_details.booking_id')
//            ->leftJoin('services','booking_details.service_id','services.id')
//            ->leftJoin('booking_schedule_histories','bookings.id','booking_schedule_histories.booking_id')
//            ->leftJoin('users as schedule_users','booking_schedule_histories.user_id','schedule_users.id')
//            ->leftJoin('booking_status_histories','bookings.id','booking_status_histories.booking_id')
//            ->leftJoin('users as status_users','booking_status_histories.user_id','status_users.id')
//            ->leftJoin('service_addresses','bookings.service_address_id','status_users.id')
//            ->leftJoin('customers','bookings.customer_id','customers.id')
//            ->leftJoin('providers','bookings.provider_id','providers.id')
//            ->leftJoin('zones','bookings.zone_id','zones.id')
//            ->leftJoin('servicemen','bookings.serviceman_id','servicemen.id')
//            ->leftJoin('users AS servicemen_users','servicemen.user_id','servicemen_users.id')
//            ->where('bookings.provider_id', $request->user()->provider->id)
//            ->whereNull('bookings.provider_id')
//            ->get()
//            ->take(1);

/*        $booking = $this->booking
            ->with(['detail.service', 'schedule_histories.user','status_histories_list.user','service_address', 'customer', 'provider', 'zone', 'serviceman.user'])
            ->where(function ($query) use ($request) {
                return $query->where('provider_id', $request->user()->provider->id)->orWhereNull('provider_id');
            })
            ->where(['id' => $id])->first();*/


        $booking = $this->booking
            ->with(['detail.service', 'schedule_histories.user','status_histories_list.user','service_address', 'customer', 'provider', 'zone', 'serviceman.user',
                'status_histories_list' => function ($query) use ($request) {
                    $query->where('changed_by', $request->user()->id);
                    $query->where('booking_id', $request->id);
                    $query->where('booking_status', 'decline');
                },
                ])
            ->where(function ($query) use ($request) {
                return $query->where('provider_id', $request->user()->provider->id)->orWhereNull('provider_id');
            })
            ->where(['id' => $id])

//            ->whereExists(function ($query) {
//                $query->select('*')
//                    ->from('booking_status_histories')
//                    ->where('changed_by', 'fa36bcf4-32fe-4a69-a4c5-14df1bde4ddd')
//                    ->where('booking_id', '1494f146-8112-4018-ab24-dba5afab295e')
//                    ->where('booking_status', 'decline');
//                    })
            ->first();



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
    public function request_accept(Request $request, string $booking_id): JsonResponse
    {
        $booking = $this->booking->where('id', $booking_id)->whereNull('provider_id')->first();
        if (isset($booking)) {
            $booking->provider_id = $request->user()->provider->id;
            $booking->booking_status = 'accepted';

            $booking_status_history = $this->booking_status_history;
            $booking_status_history->booking_id = $booking_id;
            $booking_status_history->changed_by = $request->user()->id;
            $booking_status_history->booking_status = 'accepted';

            $booking_status = $this->booking_status_history->where('booking_id', $booking_id)->where('booking_status','!=','accepted');
            if (isset($booking_status)){
                DB::transaction(function () use ($booking_id, $booking_status_history, $booking) {
                    $booking->save();
                    $booking_status_history->save();
                });
            } else {
                return response()->json(response_formatter(DEFAULT_400), 400);
            }
            return response()->json(response_formatter(DEFAULT_STATUS_UPDATE_200), 200);
        }
        return response()->json(response_formatter(DEFAULT_400), 400);
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
            'booking_status' => 'required|in:' . implode(',', array_column(BOOKING_STATUSES, 'key')),
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking->where('id', $booking_id)->where(function ($query) use ($request) {
            return $query->where('provider_id', $request->user()->provider->id)->orWhereNull('provider_id');
        })->first();

        if (isset($booking)) {
            if ($request['booking_status'] != "decline"){
                $booking->booking_status = $request['booking_status'];
                $booking_status_history = $this->booking_status_history;
                $booking_status_history->booking_id = $booking_id;
                $booking_status_history->changed_by = $request->user()->id;
                $booking_status_history->booking_status = $request['booking_status'];

                DB::transaction(function () use ($booking_status_history, $booking) {
                    $booking->save();
                    $booking_status_history->save();
                });
            } else {
                $booking_status_history = $this->booking_status_history;
                $booking_status_history->booking_id = $booking_id;
                $booking_status_history->changed_by = $request->user()->id;
                $booking_status_history->booking_status = $request['booking_status'];

                DB::transaction(function () use ($booking_status_history, $booking) {
                    $booking_status_history->save();
                });
            }
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
    public function assign_serviceman(Request $request, string $booking_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'serviceman_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking->where('id', $booking_id)->where('provider_id', $request->user()->provider->id)->first();
        if (isset($booking)) {
            $booking->serviceman_id = $request['serviceman_id'];
            $booking->save();
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
    public function schedule_update(Request $request, string $booking_id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'schedule' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $booking = $this->booking->where('id', $booking_id)->where(function ($query) use ($request) {
            return $query->where('provider_id', $request->user()->provider->id);
        })->first();

        if (isset($booking)) {
            $booking->service_schedule = $request['schedule'];

            $booking_schedule_history = $this->booking_schedule_history;
            $booking_schedule_history->booking_id = $booking_id;
            $booking_schedule_history->changed_by = $request->user()->id;
            $booking_schedule_history->schedule = $request['schedule'];

            DB::transaction(function () use ($booking_schedule_history, $booking) {
                $booking->save();
                $booking_schedule_history->save();
            });

            return response()->json(response_formatter(DEFAULT_200, $booking), 200);
        }
        return response()->json(response_formatter(DEFAULT_204), 200);
    }
}
