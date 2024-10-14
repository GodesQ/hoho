<?php
use App\Models\AppSetting;
use App\Models\Role;
use App\Models\TravelTaxPayment;

if (! function_exists('merchant_roles')) {

    function merchant_roles()
    {
        return [
            Role::MERCHANT_HOTEL_ADMIN,
            Role::MERCHANT_RESTAURANT_ADMIN,
            Role::MERCHANT_STORE_ADMIN,
            Role::TOUR_OPERATOR_ADMIN
        ];
    }
}

if (! function_exists('maintenanceMode')) {

    function maintenanceMode()
    {
        $setting = AppSetting::where('code', 'hoho_mobile')->first();

        return $setting->maintenance_mode;
    }

}

if (! function_exists('getConvenienceFee')) {
    function getConvenienceFee()
    {
        return [
            'amount' => 0.05,
            'type' => 'percentage'
        ];
    }
}

if (! function_exists('generateBookingReferenceNumber')) {
    function generateBookingReferenceNumber()
    {
        return date('Ym') . '-' . 'OT' . rand(100000, 10000000);
    }
}

if (! function_exists('generateOrderReferenceNumber')) {
    function generateOrderReferenceNumber()
    {
        return date('Ym') . '-' . 'OR' . rand(100000, 10000000);
    }
}

if (! function_exists('generateHotelReservationReferenceNumber')) {
    function generateHotelReservationReferenceNumber()
    {
        return date('Ym') . '-' . 'OHR' . rand(100000, 10000000);
    }
}

if (! function_exists('generateTravelTaxReferenceNumber')) {
    function generateTravelTaxReferenceNumber()
    {
        return date('Ym') . '-' . 'OTRX' . rand(100000, 10000000);
    }
}

if (! function_exists('generateArNumber')) {
    function generateARNumber()
    {
        // Fetch the latest Travel Tax Payment entry
        $latestPayment = TravelTaxPayment::orderBy('created_at', 'desc')->first();

        // Default starting AR number if no record exists
        $defaultAR = 'HOHO-0000000001';

        if (! $latestPayment) {
            // If there's no previous payment, return the default AR number
            return $defaultAR;
        }

        // Get the latest AR number
        $latestAR = $latestPayment->ar_number; // Assuming the column is named 'ar_number'

        // Extract the numeric part from the latest AR number
        $latestNumber = intval(substr($latestAR, 5)); // "0000000000" part starts from index 5

        // Increment the number
        $newNumber = $latestNumber + 1;

        // Pad the new number with leading zeros to keep it 10 digits
        $newNumberPadded = str_pad($newNumber, 10, '0', STR_PAD_LEFT);

        // Return the new AR number in the format HOHO-0000000000
        return 'HOHO-' . $newNumberPadded;
    }
}

if (! function_exists('getDevelopersEmail')) {
    function getDevelopersEmail()
    {
        return [
            "joebenmirana09@gmail.com",
            "joeben@godesq.com",
            "james@godesq.com",
            "jamesgarnfil4@gmail.com",
            "jamesgarnfil15@gmail.com",
            "joecristian.jamis@godesq.com",
            "jamisjoecristian@gmail.com"
        ];
    }
}