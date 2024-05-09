<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TravelTaxService;
use Enum\TransactionTypeEnum;
use App\Http\Requests\TravelTax\PaymentRequest;
use App\Models\Transaction;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use App\Services\AqwireService;
use Carbon\Carbon;
use ErrorException;

class TravelTaxController extends Controller
{   
    public $travelTaxService;

    public function __construct(TravelTaxService $travelTaxService)
    {
        $this->travelTaxService = $travelTaxService;
    }

    public function store(PaymentRequest $request)
    {
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
        }
    }

    private function computeTotalAmount($amount, $processing_fee, $discount)
    {
        return ($amount + $processing_fee) - $discount;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OTRX' . rand(100000, 10000000);
    }

    private function generateTransactionNumber()
    {
        return 'TN' . date('Ym') . rand(100000, 10000000);
    }
}
