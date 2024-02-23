<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationCodeResource;
use App\Models\ReservationUserCode;
use Illuminate\Http\Request;

class ReservationCodeController extends Controller
{
    public function index(Request $request) {

    }

    public function show(Request $request, $reservation_code_id) {
        $reservation_code = ReservationUserCode::findOrFail($request->reservation_code_id);
        return ReservationCodeResource::make($reservation_code);
    }

    public function getTourReservationCodes(Request $request, $reservation_id) {
        $reservation_codes = ReservationUserCode::where('reservation_id', $reservation_id)->get();
        return ReservationCodeResource::collection($reservation_codes);
    }
}
