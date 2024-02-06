<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantReservation\StoreRequest;
use App\Models\RestaurantReservation;
use Illuminate\Http\Request;

class RestaurantReservationController extends Controller
{
    public function store(StoreRequest $request) {
        $data = $request->validated();
        RestaurantReservation::create(array_merge($data, ['status' => 'pending']));

        return response([
            'status' => TRUE,
            'message'=> 'Submission successful! Your request for availability is now under review. Please await the next notification.',
        ], 201);
    }

    public function show(Request $request, $id) {
        $restaurantReservation = RestaurantReservation::where('id', $id)->with('merchant')->first();
        
        if(!$restaurantReservation) {
            return response([
                'status' => FALSE,
                'message' => 'No Reservation Found.',
            ], 404);
        }

        return response([
            'status' => TRUE,
            'mesage' => 'Reservation Found.',
            'restaurant_reservation' => $restaurantReservation
        ]);
    }
}
