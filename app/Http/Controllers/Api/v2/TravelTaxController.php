<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelTax\PaymentRequest;
use App\Http\Resources\TravelTaxPassengerResource;
use App\Http\Resources\TravelTaxPaymentResource;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use App\Services\TravelTaxService;
use Error;
use ErrorException;
use Illuminate\Http\Request;

class TravelTaxController extends Controller
{
    public $travelTaxService;

    public function __construct(TravelTaxService $travelTaxService)
    {
        $this->travelTaxService = $travelTaxService;
    }

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

    public function store(PaymentRequest $request) {
        try {
            $travelTax = $this->travelTaxService->createTravelTax($request);

            return response([
                'status' => 'paying',
                'url' => $travelTax['url']
            ], 201);

        } catch (ErrorException $e) {
            return response([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Error $e) {
            return response([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
