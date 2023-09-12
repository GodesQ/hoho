<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function getPromoCodes(Request $request) {
        $promocodes = PromoCode::select('id', 'name', 'code', 'description', 'is_need_requirement')->where('is_need_requirement', 1)->get();

        return response([
            'status' => TRUE,
            'promo_codes' => $promocodes
        ]);
    }

    public function checkValidPromoCode(Request $request) {
        $promocode = PromoCode::where('code', $request->code)->exists();

        return response([
            'is_promocode_exist' => $promocode
        ]); 
    }
}
