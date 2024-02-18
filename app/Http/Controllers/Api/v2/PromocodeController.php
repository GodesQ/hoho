<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromocodeResource;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromocodeController extends Controller
{
    public function index(Request $request) {
        $promocodes = PromoCode::where('name', 'SENIOR/PWD/STUDENT')->get();
        return PromocodeResource::collection($promocodes);
    }

    public function verify(Request $request) {
        $promocode = PromoCode::where('code', $request->code)->firstOrFail();

        return response([
            'promocode_exist' => $promocode ? true : false,
            'data' => PromocodeResource::make($promocode),
        ]);
    }
}
