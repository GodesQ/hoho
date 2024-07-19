<?php
use App\Models\AppSetting;
use App\Models\Role;

if (!function_exists('merchant_roles')) {

    function merchant_roles() {
        return [
            Role::MERCHANT_HOTEL_ADMIN,
            Role::MERCHANT_RESTAURANT_ADMIN,
            Role::MERCHANT_STORE_ADMIN,
            Role::TOUR_OPERATOR_ADMIN
        ];
    }
}

if(!function_exists('maintenanceMode')) {

    function maintenanceMode() {
        $setting = AppSetting::where('code', 'hoho_mobile')->first();

        return $setting->maintenance_mode;
    }

}

if(!function_exists('getConvenienceFee')) {
    
    function getConvenienceFee() {
        return 99;
    }
}