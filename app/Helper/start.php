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

if (! function_exists("processAdditionalCharges")) {
    function processAdditionalCharges(float|int $sub_amount, array $additional = [])
    {
        $additional_charges = [];

        $convenience_fee = getConvenienceFee();

        $total_of_additional_charges = $convenience_fee['type'] === 'percentage' ? $sub_amount * $convenience_fee['amount'] : $sub_amount + $convenience_fee['amount'];

        array_push($additional_charges, ['convenience_fee' => $convenience_fee]);

        return [
            'list' => $additional_charges,
            'total' => $total_of_additional_charges,
        ];
    }
}

if (! function_exists('generateRandomUuid')) {
    function generateRandomUuid()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4 (random)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant (RFC 4122)

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return $uuid;
    }
}

if (! function_exists('getDateOfDIYPass')) {
    function getDateOfDIYPass($ticket_pass, $trip_date)
    {
        if ($ticket_pass == '1 Day Pass') {
            $date = $trip_date->addDays(1);
        } else if ($ticket_pass == '2 Day Pass') {
            $date = $trip_date->addDays(2);
        } else if ($ticket_pass == '3 Day Pass') {
            $date = $trip_date->addDays(3);
        } else {
            $date = $trip_date->addDays(1);
        }

        return $date;
    }
}