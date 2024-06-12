<?php
namespace Modules\BookingModule\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\BookingModule\Entities\Booking;
use Modules\BookingModule\Entities\BookingDetail;
use Modules\BookingModule\Entities\BookingDetailsAmount;
use Modules\BookingModule\Entities\BookingScheduleHistory;
use Modules\BookingModule\Entities\BookingStatusHistory;
use Modules\BookingModule\Events\BookingRequested;
use Modules\CartModule\Entities\Cart;
use Modules\CartModule\Entities\CartServiceImages;
use Modules\ProviderManagement\Entities\Provider;
use Modules\ServiceManagement\Entities\Service;
use Modules\BusinessSettingsModule\Entities\BusinessSettings;

trait BookingTrait
{
    protected function place_booking_request($user_id, $request, $transaction_id)
    {
        $cart_data = Cart::where(['customer_id' => $user_id])->get();

        if ($cart_data->count() == 0) {
            return [
                'flag' => 'failed',
                'message' => 'no data found'
            ];
        }

        $booking_ids = [];
        foreach ($cart_data->pluck('sub_category_id')->unique() as $sub_category) {

            $booking = new Booking();

            DB::transaction(function () use ($sub_category, $booking, $transaction_id, $request, $cart_data, $user_id) {
                $cart_data = $cart_data->where('sub_category_id', $sub_category);

                //bookings
                $booking->customer_id = $user_id;
                $booking->category_id = $cart_data->first()->category_id;
                $booking->sub_category_id = $sub_category;
                $booking->provider_selected_ids = $cart_data->first()->provider_selected_ids;
                $booking->zone_id = config('zone_id') == null ? $request['zone_id'] : config('zone_id');
                $booking->booking_status = 'pending';
                $booking->is_paid = ($request->has('payment_method') && $request['payment_method'] == 'cash_after_service') ? 0 : 1;
                $booking->payment_method = $request['payment_method'];
                $booking->transaction_id = ($request->has('payment_method') && $request['payment_method'] == 'cash_after_service') ? 'cash-payment' : $transaction_id;
                $booking->total_booking_amount = $cart_data->sum('total_cost');
                $booking->total_booking_unpaid = $cart_data->sum('total_cost');
                $booking->total_tax_amount = $cart_data->sum('tax_amount');
                $booking->total_discount_amount = $cart_data->sum('discount_amount');
                $booking->total_campaign_discount_amount = $cart_data->sum('campaign_discount');
                $booking->total_coupon_discount_amount = $cart_data->sum('coupon_discount');
                $booking->service_schedule = $request->service_schedule ?? now()->addHours(5);
                $booking->service_address_id = $request->service_address_id ?? '';
                $booking->save();

                foreach ($cart_data->all() as $datum) {
                    $serviceName = DB::table('services')->where('id',$datum['service_id'])->first();
                    //booking_details
                    $detail = new BookingDetail();
                    $detail->booking_id = $booking->id;
                    $detail->service_id = $datum['service_id'];
//                    $detail->service_name = Service::find($datum['service_id'])->name ?? 'service-not-found';
                    $detail->service_name = (!empty($serviceName))? $serviceName->name : 'service-not-found';
                    $detail->variant_key = $datum['variant_key'];
                    $detail->quantity = $datum['quantity'];
                    $detail->service_cost = $datum['service_cost'];
                    $detail->discount_amount = $datum['discount_amount'];
                    $detail->campaign_discount_amount = $datum['campaign_discount'];
                    $detail->overall_coupon_discount_amount = $datum['coupon_discount'];
                    $detail->tax_amount = $datum['tax_amount'];
                    $detail->total_cost = $datum['total_cost'];
                    $detail->is_paid = ($request->has('payment_method') && $request['payment_method'] == 'cash_after_service') ? 0 : 1;
                    $detail->payment_method = $request['payment_method'];
                    $detail->transaction_id = ($request->has('payment_method') && $request['payment_method'] == 'cash_after_service') ? 'cash-payment' : $transaction_id;
                    $detail->save();

                    //booking_details_amount
                    $booking_details_amount = new BookingDetailsAmount();
                    $booking_details_amount->booking_details_id = $detail->id;
                    $booking_details_amount->booking_id = $booking->id;
                    $booking_details_amount->service_unit_cost = $datum['service_cost'];
                    $booking_details_amount->service_quantity = $datum['quantity'];
                    $booking_details_amount->service_tax = $datum['tax_amount'];
                    $booking_details_amount->discount_by_admin = $this->calculate_discount_cost($datum['discount_amount'])['admin'];
                    $booking_details_amount->discount_by_provider = $this->calculate_discount_cost($datum['discount_amount'])['provider'];
                    $booking_details_amount->campaign_discount_by_admin = $this->calculate_campaign_cost($datum['campaign_discount'])['admin'];
                    $booking_details_amount->campaign_discount_by_provider = $this->calculate_campaign_cost($datum['campaign_discount'])['provider'];
                    $booking_details_amount->coupon_discount_by_admin = $this->calculate_coupon_cost($datum['coupon_discount'])['admin'];
                    $booking_details_amount->coupon_discount_by_provider = $this->calculate_coupon_cost($datum['coupon_discount'])['provider'];
                    //admin commission will update after complete the service
                    $booking_details_amount->save();
                }

                //booking_schedule_histories
                $schedule = new BookingScheduleHistory();
                $schedule->booking_id = $booking->id;
                $schedule->changed_by = $user_id;
                $schedule->schedule = $request->service_schedule ?? now()->addHours(5);
                $schedule->save();

                //booking_status_histories
                $status_history = new BookingStatusHistory();
                $status_history->changed_by = $booking->id;
                $status_history->booking_id = $user_id;
                $status_history->booking_status = 'pending';
                $status_history->save();
            });
            $booking_ids[] = $booking->id;
        }

        //  UPDATE BOOKING ID & CART CLEAN STATUS
        foreach ($cart_data as $cart) {
            $cartID = $cart->id;
//            $cartDetail = CartServiceImages::where('cart_id', $cartID)->first();
            //UPDATE cart_service_images TABLE
            DB::table('cart_service_images') ->where('cart_id', $cartID)->update( ['booking_id' => $booking->id, 'cart_clean_status' => 1]);

            //UPDATE cart_service_pdf TABLE
            DB::table('cart_service_pdfs') ->where('cart_id', $cartID)->update( ['booking_id' => $booking->id, 'cart_clean_status' => 1]);

            //UPDATE cart_service_videos TABLE
            DB::table('cart_service_videos') ->where('cart_id', $cartID)->update( ['booking_id' => $booking->id, 'cart_clean_status' => 1]);
        }

        cart_clean($user_id);
        event(new BookingRequested($booking));

        return [
            'flag' => 'success',
            'booking_id' => $booking_ids
        ];
    }


    /**
     * @param float $discount_amount
     * @return array
     */
    private function calculate_discount_cost(float $discount_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'discount_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if ($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($discount_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($discount_amount * $data['provider_percentage'])/100;
        }
        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    /**
     * @param float $campaign_amount
     * @return array
     */
    private function calculate_campaign_cost(float $campaign_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'campaign_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($campaign_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($campaign_amount * $data['provider_percentage'])/100;
        }

        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    /**
     * @param float $coupon_amount
     * @return array
     */
    private function calculate_coupon_cost(float $coupon_amount): array
    {
        $data = BusinessSettings::where('settings_type', 'promotional_setup')->where('key_name', 'coupon_cost_bearer')->first();
        if (!isset($data)) return [];
        $data = $data->live_values;

        if($data['admin_percentage'] == 0) {
            $admin_percentage = 0;
        } else {
            $admin_percentage = ($coupon_amount * $data['admin_percentage'])/100;
        }

        if ($data['provider_percentage'] == 0) {
            $provider_percentage = 0;
        } else {
            $provider_percentage = ($coupon_amount * $data['provider_percentage'])/100;
        }

        return [
            'admin' => $admin_percentage,
            'provider' => $provider_percentage
        ];
    }

    /**
     * @param $booking_id
     * @param float $booking_amount
     * @param $provider_id
     * @return void
     */
    private function update_admin_commission($booking, float $booking_amount, $provider_id)
    {
        $service_cost = $booking['total_booking_amount'] - $booking['total_tax_amount'] + $booking['total_discount_amount'] + $booking['total_campaign_discount_amount'] + $booking['total_coupon_discount_amount'];

        //cost bearing (promotional)
        $booking_details_amounts = BookingDetailsAmount::where('booking_id', $booking->id)->get();
        $promotional_cost_by_admin = 0;
        $promotional_cost_by_provider = 0;
        foreach($booking_details_amounts as $booking_details_amount) {
            $promotional_cost_by_admin += $booking_details_amount['discount_by_admin'] + $booking_details_amount['coupon_discount_by_admin'] + $booking_details_amount['campaign_discount_by_admin'];
            $promotional_cost_by_provider += $booking_details_amount['discount_by_provider'] + $booking_details_amount['coupon_discount_by_provider'] + $booking_details_amount['campaign_discount_by_provider'];
        }

        //total booking amount (for provider)
        $provider_receivable_total_booking_amount = $service_cost - $promotional_cost_by_provider;

        //admin commission
        $provider = Provider::find($booking['provider_id']);
        $commission_percentage = $provider->commission_status == 1 ? $provider->commission_percentage : (business_config('default_commission', 'business_information'))->live_values;
        $admin_commission = ($provider_receivable_total_booking_amount*$commission_percentage)/100;

        //admin promotional cost will be deducted from admin commission
        $admin_commission_without_cost = $admin_commission - $promotional_cost_by_admin;

        //total booking amount (without commission)
        $booking_amount_without_commission = $booking['total_booking_amount'] - $admin_commission_without_cost;

        $booking_amount_detail_amount = BookingDetailsAmount::where('booking_id', $booking->id)->first();
        $booking_amount_detail_amount->admin_commission = $admin_commission;
        $booking_amount_detail_amount->provider_earning = $booking_amount_without_commission;
        $booking_amount_detail_amount->save();
    }



}
