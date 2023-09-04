<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function verifyReferralCode(Request $request) {
        $referral = Referral::where('referral_code', $request->rreferral_code)->first();

        if(!$referral) {
            return response([
                'status' => FALSE,
                'message' => 'Referral Code Not Found',
                'referral' => null
            ]);
        }

        return response([
            'status' => TRUE,
            'message' => 'Referral Code Found',
            'referral' => $referral
        ]);
    }
}
