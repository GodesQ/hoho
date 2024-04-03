<?php
use App\Models\AppSetting;

if (!function_exists('merchant_roles')) {

    function merchant_roles() {
        return [
            'merchant_restaurant_admin',
            'merchant_hotel_admin',
            'merchant_store_admin',
            'tour_operator_admin'
        ];
    }
}

if(!function_exists('maintenanceMode')) {

    function maintenanceMode() {
        $setting = AppSetting::where('code', 'hoho_mobile')->first();

        return $setting->maintenance_mode;
    }

}