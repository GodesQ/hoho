<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantReservation\StoreRequest;
use App\Http\Resources\RestaurantReservationResource;
use App\Models\RestaurantReservation;
use Illuminate\Http\Request;

class RestaurantReservationController extends Controller
{
    public function store(StoreRequest $request) {
        $data = $request->validated();
        $restaurant_reservation = RestaurantReservation::create(array_merge($data, ['status' => 'pending']));

        return response([
            'status' => TRUE,
            'message'=> 'Submission successful! Your request for availability is now under review. Please await the next notification.',
            'restaurant_reservation' => $restaurant_reservation,
        ], 201);
    }

    public function show(Request $request, $id) {
        $restaurantReservation = RestaurantReservation::where('id', $id)->with('merchant')->firstOrFail();

        return response([
            'status' => TRUE,
            'mesage' => 'Reservation Found.',
            'restaurant_reservations' => RestaurantReservationResource::make($restaurantReservation),
        ]);
    }

    public function getMerchantRestaurantReservations(Request $request, $merchant_id) {
        $reservations = RestaurantReservation::where('merchant_id', $merchant_id)->with('merchant')->get();

        return response([
            'status' => TRUE,
            'restaurant_reservations' => $reservations,
        ]);
    }

    public function getUserRestaurantReservations(Request $request, $user_id) {
        $reservations = RestaurantReservation::where('reserved_user_id', $user_id)->with('merchant')->get();
        
        return response([
            'status' => TRUE,
            'restaurant_reservations' => RestaurantReservationResource::collection($reservations),
        ]);
    }
}
