<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelTaxPassengerResource;
use App\Http\Resources\TravelTaxPaymentResource;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use Illuminate\Http\Request;

class TravelTaxController extends Controller
{
    public function index(Request $request) {
        $travel_taxes = TravelTaxPayment::all();
        return TravelTaxPaymentResource::collection($travel_taxes);
    }

    public function show(Request $request, $travel_tax_id) {
        $travel_tax = TravelTaxPayment::where('id', $travel_tax_id)->first();
        return TravelTaxPaymentResource::make($travel_tax);
    }

    public function travelTaxPassenger(Request $request, $travel_tax_id, $passenger_id) {
        $passenger = TravelTaxPassenger::where('id', $passenger_id)
                        ->where('payment_id', $travel_tax_id)
                        ->firstOrFail();

        return TravelTaxPassengerResource::make($passenger);
    }
}
