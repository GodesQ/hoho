<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelReservationResource;
use App\Models\HotelReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelReservationController extends Controller
{
    public function index(Request $request) {

    }

    public function  userHotelReservation(Request $request) {
        $user = Auth::user();
        return $user;
    }

    public function show(Request $request, $hotel_reservation_id) {
        $hotel_reservation = HotelReservation::findOrFail($hotel_reservation_id);

        return HotelReservationResource::make($hotel_reservation);
    }
}
