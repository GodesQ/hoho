<?php

namespace App\Services;
use Illuminate\Http\Request;

class TravelTaxService {
    public function __construct() {
    
    }

    public function storeTraveTax(Request $request) {
        
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