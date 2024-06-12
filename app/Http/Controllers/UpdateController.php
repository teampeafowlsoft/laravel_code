<?php

namespace App\Http\Controllers;

use App\Traits\ActivationClass;
use App\Traits\UnloadedHelpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\BusinessSettingsModule\Entities\BusinessSettings;

class UpdateController extends Controller
{
    use UnloadedHelpers;
    use ActivationClass;

    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        $this->setEnvironmentValue('SOFTWARE_ID', 'NDAyMjQ3NzI=');
        $this->setEnvironmentValue('BUYER_USERNAME', $request['username']);
        $this->setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        $this->setEnvironmentValue('SOFTWARE_VERSION', '1.2');
        $this->setEnvironmentValue('APP_ENV', 'live');
        $this->setEnvironmentValue('APP_URL', url('/'));

        Artisan::call('migrate', ['--force' => true]);
        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

        Artisan::call('module:enable');

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');

        //new keys for business settings
        //withdraw amount
        if (BusinessSettings::where(['key_name' => 'minimum_withdraw_amount', 'settings_type'=> 'business_information'])->first() == false) {
            BusinessSettings::updateOrCreate(['key_name' => 'minimum_withdraw_amount', 'settings_type'=> 'business_information'], [
                'live_values' => 0,
                'test_values' => 0
            ]);
        }
        if (BusinessSettings::where(['key_name' => 'maximum_withdraw_amount', 'settings_type'=> 'business_information'])->first() == false) {
            BusinessSettings::updateOrCreate(['key_name' => 'maximum_withdraw_amount', 'settings_type'=> 'business_information'], [
                'live_values' => 0,
                'test_values' => 0,
            ]);
        }

        //promotional cost setup
        if (BusinessSettings::where(['key_name' => 'discount_cost_bearer', 'settings_type'=> 'promotional_setup'])->first() == false) {
            BusinessSettings::updateOrCreate(['key_name' => 'discount_cost_bearer', 'settings_type'=> 'promotional_setup'], [
                'live_values' => ["bearer" => "provider", "admin_percentage" => 0 , "provider_percentage" => 100, "type" => "discount"],
                'test_values' => ["bearer" => "provider", "admin_percentage" => 0 , "provider_percentage" => 100, "type" => "coupon"]
            ]);
        }
        if (BusinessSettings::where(['key_name' => 'coupon_cost_bearer', 'settings_type'=> 'promotional_setup'])->first() == false) {
            BusinessSettings::updateOrCreate(['key_name' => 'coupon_cost_bearer', 'settings_type'=> 'promotional_setup'], [
                'live_values' => ["bearer" => "provider", "admin_percentage" => 0 , "provider_percentage" => 100, "type" => "coupon"],
                'test_values' => ["bearer" => "provider", "admin_percentage" => 0 , "provider_percentage" => 100, "type" => "coupon"]
            ]);
        }
        if (BusinessSettings::where(['key_name' => 'campaign_cost_bearer', 'settings_type'=> 'promotional_setup'])->first() == false) {
            BusinessSettings::updateOrCreate(['key_name' => 'campaign_cost_bearer', 'settings_type'=> 'promotional_setup'], [
                'live_values' => ["bearer" => "provider", "admin_percentage" => 0 , "provider_percentage" => 100, "type" => "campaign"],
                'test_values' => ["bearer" => "provider", "admin_percentage" => 0 , "provider_percentage" => 100, "type" => "campaign"]
            ]);
        }
        //end

        return redirect(env('APP_URL'));
    }
}
