<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Referral;

class ReferralController extends Controller
{
    public function verifyReferralCode(Request $request) {
        $referral = Referral::select('id', 'referral_code', 'referral_name')
                    ->where('referral_code', $request->referral_code)
                    ->first();

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
