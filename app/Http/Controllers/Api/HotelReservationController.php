<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelReservation\StoreRequest;
use App\Models\HotelReservation;
use Illuminate\Http\Request;

class HotelReservationController extends Controller
{
    public function store(StoreRequest $request) {
        $data = $request->validated();
        $hotelReservation = HotelReservation::create(array_merge($data, ['status' => 'pending']));

        return response([
            'status' => TRUE,
            'message'=> 'Submission successful! Your request for availability is now under review. Please await the next notification.',
        ], 201);
    }

    public function show(Request $request, $id) {
        $hotelReservation = HotelReservation::where('id', $id)->with('merchant')->first();
        
        if(!$hotelReservation) {
            return response([
                'status' => FALSE,
                'message' => 'No Reservation Found.',
            ], 404);
        }

        return response([
            'status' => TRUE,
            'mesage' => 'Reservation Found.',
            'hotel_reservation' => $hotelReservation
        ]);
    }
}