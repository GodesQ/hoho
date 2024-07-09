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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function getUserTravelTaxPayments(Request $request) {
        $user = Auth::user();
        $payments = TravelTaxPayment::where('user_id', $user->id)->latest()->get();
        
        return response([
            'status' => TRUE,
            'travel_tax_payments' => $payments
        ]);
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
