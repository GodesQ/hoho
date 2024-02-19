<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request) {

    }

    public function verify(Request $request) {
        $referral = Referral::where('referral_code', $request->referral_code)->firstOrFail();

        return response([
            'referral_exist' => $referral ? true : false,
            'data' => ReferralResource::make($referral),
        ]);
    }
}
