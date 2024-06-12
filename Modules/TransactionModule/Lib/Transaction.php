<?php

use Illuminate\Support\Facades\DB;
use Modules\BookingModule\Entities\BookingDetailsAmount;
use Modules\ProviderManagement\Entities\Provider;
use Modules\TransactionModule\Entities\Account;
use Modules\TransactionModule\Entities\Transaction;
use Modules\UserManagement\Entities\User;


//*** place booking ***
if (!function_exists('place_booking_transaction')) {
    function place_booking_transaction($booking)
    {
        if ($booking['payment_method'] != 'cash_after_service') {
            $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;
            DB::transaction(function () use ($booking, $admin_user_id) {
                //Admin account update
                $account = Account::where('user_id', $admin_user_id)->first();
                $account->balance_pending += $booking['total_booking_amount'];
                $account->save();

                //Admin transaction
                Transaction::create([
                    'ref_trx_id' => null,
                    'booking_id' => $booking['id'],
                    'trx_type' => TRX_TYPE['booking_amount'],
                    'debit' => 0,
                    'credit' => $booking['total_booking_amount'],
                    'balance' => $account->balance_pending,
                    'from_user_id' => $booking->customer_id,
                    'to_user_id' => $admin_user_id,
                    'from_user_account' => null,
                    'to_user_account' => ACCOUNT_STATES[0]['value']
                ]);
            });
        }
    }
}

//*** place product order ***
if (!function_exists('place_product_transaction')) {
    function place_product_transaction($booking)
    {
        if ($booking['payment_method'] != 'cash_after_service') {
//            dd($booking);
            $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;
            $provider_user_id = get_user_id($booking['provider_id'], PROVIDER_USER_TYPES[0]);
            DB::transaction(function () use ($booking, $admin_user_id, $provider_user_id) {

                //Transition Commission
                $admin_commission = (($booking['total_discount_amount'] * $booking['total_qty']) - ($booking['total_cost_amount'] * $booking['total_qty'])) + $booking['total_shipping_charge'];

                $provider_commission = ($booking['total_cost_amount'] * $booking['total_qty']) + $booking['total_delivery_charge'];


                //Admin account update
                $account = Account::where('user_id', $admin_user_id)->first();
                $account->received_balance += $admin_commission;
                $account->save();

                //Admin transaction
                $primary_transaction = Transaction::create([
                    'ref_trx_id' => null,
                    'booking_id' => $booking['id'],
                    'trx_type' => TRX_TYPE['received_commission'],
                    'debit' => $provider_commission,
                    'credit' => $booking['total_booking_amount'],
                    'balance' => $admin_commission,
                    'from_user_id' => $booking->customer_id,
                    'to_user_id' => $admin_user_id,
                    'from_user_account' => null,
                    'to_user_account' => ACCOUNT_STATES[1]['value'],
                    'tran_type' => 'product'
                ]);

                //Provider account update
                $account = Account::where('user_id', $provider_user_id)->first();
                $account->received_balance += $provider_commission;
                $account->save();

                //Provider transaction
                Transaction::create([
                    'ref_trx_id' => $primary_transaction['id'],
                    'booking_id' => $booking['id'],
                    'trx_type' => TRX_TYPE['received_commission'],
                    'debit' => $admin_commission,
                    'credit' => $booking['total_booking_amount'],
                    'balance' => $provider_commission,
                    'from_user_id' => $booking->customer_id,
                    'to_user_id' => $provider_user_id,
                    'from_user_account' => null,
                    'to_user_account' => ACCOUNT_STATES[1]['value'],
                    'tran_type' => 'product'
                ]);
            });
        }
    }
}

//*** after complete booking ***
if (!function_exists('digital_payment_booking_transaction')) {
    function digital_payment_booking_transaction($booking)
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
        $admin_commission -= $promotional_cost_by_admin;

        //total booking amount (without commission)
        $booking_amount_without_commission = $booking['total_booking_amount'] - $admin_commission;

        //user ids (from/to)
        $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;
        $provider_user_id = get_user_id($booking['provider_id'], PROVIDER_USER_TYPES[0]);

        DB::transaction(function () use ($booking, $admin_user_id, $provider_user_id, $admin_commission, $booking_amount_without_commission, $promotional_cost_by_admin, $promotional_cost_by_provider) {

            $account = Account::where('user_id', $admin_user_id)->first();
            $account->balance_pending -= $booking['total_booking_amount'];
            $account->save();

            //Admin transaction (-pending)
            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['pending_amount'],
                'debit' => $booking['total_booking_amount'],
                'credit' => 0,
                'balance' => $account->balance_pending,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[0]['value'],
                'to_user_account' => null
            ]);

            //Provider transactions (+receivable)
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->account_receivable += $booking_amount_without_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['receivable_amount'],
                'debit' => 0,
                'credit' => $booking_amount_without_commission,
                'balance' => $account->account_receivable,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[3]['value']
            ]);

            //Admin transactions for commission (+received)
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->received_balance += $admin_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['received_commission'],
                'debit' => 0,
                'credit' => $admin_commission,
                'balance' => $account->received_balance,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[1]['value'],
                'to_user_account' => null
            ]);

            //Admin transactions (+payable)
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->account_payable += $booking_amount_without_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['payable_amount'],
                'debit' => 0,
                'credit' => $booking_amount_without_commission,
                'balance' => $account->account_payable,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[2]['value'],
                'to_user_account' => null
            ]);

            //expense
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->total_expense += $promotional_cost_by_admin;
            $account->save();

            $account = Account::where('user_id', $provider_user_id)->first();
            $account->total_expense += $promotional_cost_by_provider;
            $account->save();
        });
    }
}

if (!function_exists('digital_payment_booking_transaction_without_item')) {
    function digital_payment_booking_transaction_without_item($booking)
    {
        $booking_calculation = $booking['total_booking_amount'] -($booking['total_booking_amount'] - $booking['total_booking_unpaid']);
        $service_cost = $booking_calculation - $booking['total_tax_amount'] + $booking['total_discount_amount'] + $booking['total_campaign_discount_amount'] + $booking['total_coupon_discount_amount'];

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
        $admin_commission -= $promotional_cost_by_admin;

        //total booking amount (without commission)
        $booking_amount_without_commission = $booking_calculation - $admin_commission;

        //user ids (from/to)
        $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;
        $provider_user_id = get_user_id($booking['provider_id'], PROVIDER_USER_TYPES[0]);

        DB::transaction(function () use ($booking, $admin_user_id, $provider_user_id, $admin_commission, $booking_amount_without_commission, $promotional_cost_by_admin, $promotional_cost_by_provider,$booking_calculation,$commission_percentage) {

            $account = Account::where('user_id', $admin_user_id)->first();
            $account->balance_pending -= $booking_calculation;
            $account->save();

            //Admin transaction (-pending)
            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['pending_amount'],
                'debit' => $booking_calculation,
                'credit' => 0,
                'balance' => $account->balance_pending,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[0]['value'],
                'to_user_account' => null
            ]);

            //Provider transactions (+receivable)
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->account_receivable += $booking_amount_without_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['receivable_amount'],
                'debit' => 0,
                'credit' => $booking_amount_without_commission,
                'balance' => $account->account_receivable,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[3]['value']
            ]);

            //Admin transactions for commission (+received)
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->received_balance += $admin_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['received_commission'],
                'debit' => 0,
                'credit' => $admin_commission,
                'balance' => $account->received_balance,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[1]['value'],
                'to_user_account' => null
            ]);

            //Admin transactions (+payable)
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->account_payable += $booking_amount_without_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['payable_amount'],
                'debit' => 0,
                'credit' => $booking_amount_without_commission,
                'balance' => $account->account_payable,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[2]['value'],
                'to_user_account' => null
            ]);

            //expense
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->total_expense += $promotional_cost_by_admin;
            $account->save();

            $account = Account::where('user_id', $provider_user_id)->first();
            $account->total_expense += $promotional_cost_by_provider;
            $account->save();

            //NEW CODE FOR IF USER PAY ANY CASH AMOUNT TO PROVIDER/TECHNICIAN :: 23-11-2023 :: SAHIL PATEL
            $cash_amount_paid = ($booking['total_booking_amount'] - $booking['total_booking_unpaid']);
            if ($cash_amount_paid != 0.000){

                $service_cost = $cash_amount_paid - $booking['total_tax_amount'] + $booking['total_discount_amount'] + $booking['total_campaign_discount_amount'] + $booking['total_coupon_discount_amount'];

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
                $admin_commission = ($provider_receivable_total_booking_amount*$commission_percentage)/100;

                //admin promotional cost will be deducted from admin commission
                $admin_commission -= $promotional_cost_by_admin;

                //total booking amount (without commission)
                $booking_amount_without_commission = $cash_amount_paid - $admin_commission;

                //Provider transactions
                $account = Account::where('user_id', $provider_user_id)->first();
                $account->received_balance += $booking_amount_without_commission;
                $account->save();

                $primary_transaction = Transaction::create([
                    'ref_trx_id' => null,
                    'booking_id' => $booking['id'],
                    'trx_type' => TRX_TYPE['received_amount'],
                    'debit' => 0,
                    'credit' => $booking_amount_without_commission,
                    'balance' => $account->received_balance,
                    'from_user_id' => $provider_user_id,
                    'to_user_id' => $provider_user_id,
                    'from_user_account' => null,
                    'to_user_account' => ACCOUNT_STATES[1]['value']
                ]);

                //Provider transactions (for commission)
                $account = Account::where('user_id', $provider_user_id)->first();
                $account->account_payable += $admin_commission;
                $account->save();

                Transaction::create([
                    'ref_trx_id' => $primary_transaction['id'],
                    'booking_id' => $booking['id'],
                    'trx_type' => TRX_TYPE['payable_commission'],
                    'debit' => 0,
                    'credit' => $admin_commission,
                    'balance' => $account->account_payable,
                    'from_user_id' => $provider_user_id,
                    'to_user_id' => $provider_user_id,
                    'from_user_account' => ACCOUNT_STATES[2]['value'],
                    'to_user_account' => null
                ]);

                //Provider transactions (for commission)
                $account = Account::where('user_id', $admin_user_id)->first();
                $account->account_receivable += $admin_commission;
                $account->save();

                Transaction::create([
                    'ref_trx_id' => $primary_transaction['id'],
                    'booking_id' => $booking['id'],
                    'trx_type' => TRX_TYPE['receivable_commission'],
                    'debit' => 0,
                    'credit' => $admin_commission,
                    'balance' => $account->account_receivable,
                    'from_user_id' => $provider_user_id,
                    'to_user_id' => $admin_user_id,
                    'from_user_account' => ACCOUNT_STATES[3]['value'],
                    'to_user_account' => null
                ]);

                //expense (admin)
                $account = Account::where('user_id', $admin_user_id)->first();
                $account->total_expense += $promotional_cost_by_admin;
                $account->save();

//                $account = Account::where('user_id', $admin_user_id)->first();
//                $account->received_balance += $admin_commission;
//                $account->save();

                //expense (provider)
                $account = Account::where('user_id', $provider_user_id)->first();
                $account->total_expense += $promotional_cost_by_provider;
                $account->save();
            }
            //END CODE :: 23-11-2023 :: SAHIL PATEL
        });

    }
}

if (!function_exists('cash_after_service_booking_transaction')) {
    function cash_after_service_booking_transaction($booking)
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
        $admin_commission -= $promotional_cost_by_admin;

        //total booking amount (without commission)
        $booking_amount_without_commission = $booking['total_booking_amount'] - $admin_commission;

        //user ids (from/to)
        $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;
        $provider_user_id = get_user_id($booking['provider_id'], PROVIDER_USER_TYPES[0]);

        DB::transaction(function () use ($booking, $admin_user_id, $provider_user_id, $admin_commission, $booking_amount_without_commission, $promotional_cost_by_admin, $promotional_cost_by_provider) {

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->received_balance += $booking_amount_without_commission;
            $account->save();

            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['received_amount'],
                'debit' => 0,
                'credit' => $booking_amount_without_commission,
                'balance' => $account->received_balance,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[1]['value']
            ]);

            //Provider transactions (for commission)
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->account_payable += $admin_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['payable_commission'],
                'debit' => 0,
                'credit' => $admin_commission,
                'balance' => $account->account_payable,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[2]['value'],
                'to_user_account' => null
            ]);

            //Provider transactions (for commission)
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->account_receivable += $admin_commission;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => $booking['id'],
                'trx_type' => TRX_TYPE['receivable_commission'],
                'debit' => 0,
                'credit' => $admin_commission,
                'balance' => $account->account_receivable,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[3]['value'],
                'to_user_account' => null
            ]);

            //expense (admin)
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->total_expense += $promotional_cost_by_admin;
            $account->save();

            //expense (provider)
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->total_expense += $promotional_cost_by_provider;
            $account->save();
        });
    }
}


//*** (admin) collect cash from provider ***
if (!function_exists('collect_cash_transaction')) {
    function collect_cash_transaction($provider_id, $collect_amount) {
        $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;
        $provider_user_id = get_user_id($provider_id, PROVIDER_USER_TYPES[0]);

        DB::transaction(function () use ($collect_amount, $admin_user_id, $provider_user_id) {

            $account = Account::where('user_id', $provider_user_id)->first();
            $account->account_payable -= $collect_amount;
            $account->save();

            //Provider transactions
            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => null,
                'trx_type' => TRX_TYPE['paid_commission'],
                'debit' => $collect_amount,
                'credit' => 0,
                'balance' => $account->account_payable,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[2]['value'],
                'to_user_account' => null
            ]);

            //Admin transactions
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->received_balance += $collect_amount;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => null,
                'trx_type' => TRX_TYPE['received_commission'],
                'debit' => 0,
                'credit' => $collect_amount,
                'balance' => $account->received_balance,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[1]['value']
            ]);

            //admin transactions
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->account_receivable -= $collect_amount;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => null,
                'trx_type' => TRX_TYPE['receivable_commission'],
                'debit' => $collect_amount,
                'credit' => 0,
                'balance' => $account->account_receivable,
                'from_user_id' => $admin_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[3]['value'],
                'to_user_account' => null
            ]);
        });
    }
}


//*** (provider) withdraw from admin ***
if (!function_exists('withdraw_request_transaction')) {
    function withdraw_request_transaction($provider_user_id, $withdrawal_amount) {

        DB::transaction(function () use ($withdrawal_amount, $provider_user_id) {

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->account_receivable -= $withdrawal_amount;
            $account->save();

            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => null,
                'trx_type' => TRX_TYPE['withdrawable_amount'],
                'debit' => $withdrawal_amount,
                'credit' => 0,
                'balance' => $account->account_receivable,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[3]['value'],
                'to_user_account' => null
            ]);

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->balance_pending += $withdrawal_amount;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => null,
                'trx_type' => TRX_TYPE['pending_amount'],
                'debit' => 0,
                'credit' => $withdrawal_amount,
                'balance' => $account->balance_pending,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[0]['value']
            ]);
        });
    }
}

if (!function_exists('withdraw_request_accept_transaction')) {
    function withdraw_request_accept_transaction($provider_user_id, $withdrawal_amount) {
        $admin_user_id = User::where('user_type', ADMIN_USER_TYPES[0])->first()->id;

        DB::transaction(function () use ($admin_user_id, $withdrawal_amount, $provider_user_id) {

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->balance_pending -= $withdrawal_amount;
            $account->save();

            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => null,
                'trx_type' => TRX_TYPE['pending_amount'],
                'debit' => $withdrawal_amount,
                'credit' => 0,
                'balance' => $account->balance_pending,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[0]['value'],
                'to_user_account' => null
            ]);

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->total_withdrawn += $withdrawal_amount;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => null,
                'trx_type' => TRX_TYPE['received_amount'],
                'debit' => 0,
                'credit' => $withdrawal_amount,
                'balance' => $account->total_withdrawn,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => ACCOUNT_STATES[4]['value'],
                'to_user_account' => null
            ]);

            //Admin transactions
            $account = Account::where('user_id', $admin_user_id)->first();
            $account->account_payable -= $withdrawal_amount;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => null,
                'trx_type' => TRX_TYPE['paid_amount'],
                'debit' => $withdrawal_amount,
                'credit' => 0,
                'balance' => $account->account_payable,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $admin_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[2]['value']
            ]);
        });
    }
}

if (!function_exists('withdraw_request_deny_transaction')) {
    function withdraw_request_deny_transaction($provider_user_id, $withdrawal_amount) {

        DB::transaction(function () use ($withdrawal_amount, $provider_user_id) {

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->account_receivable += $withdrawal_amount;
            $account->save();

            $primary_transaction = Transaction::create([
                'ref_trx_id' => null,
                'booking_id' => null,
                'trx_type' => TRX_TYPE['withdrawable_amount'],
                'debit' => 0,
                'credit' => $withdrawal_amount,
                'balance' => $account->account_receivable,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => ACCOUNT_STATES[3]['value'],
                'to_user_account' => null
            ]);

            //Provider transactions
            $account = Account::where('user_id', $provider_user_id)->first();
            $account->balance_pending -= $withdrawal_amount;
            $account->save();

            Transaction::create([
                'ref_trx_id' => $primary_transaction['id'],
                'booking_id' => null,
                'trx_type' => TRX_TYPE['pending_amount'],
                'debit' => $withdrawal_amount,
                'credit' => 0,
                'balance' => $account->balance_pending,
                'from_user_id' => $provider_user_id,
                'to_user_id' => $provider_user_id,
                'from_user_account' => null,
                'to_user_account' => ACCOUNT_STATES[0]['value']
            ]);
        });
    }
}
