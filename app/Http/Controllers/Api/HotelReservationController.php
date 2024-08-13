<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelReservation\StoreRequest;
use App\Mail\HotelReservationConfirmation;
use App\Models\Admin;
use App\Models\HotelReservation;
use App\Models\HotelReservationChildren;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HotelReservationController extends Controller
{   
    public function userHotelReservations(Request $request) {
        
    }

    public function store(StoreRequest $request) {
        $data = $request->validated();

        $reservation = HotelReservation::create(array_merge($data, ['status' => 'pending']));

        if($request->has('children_age') && is_array($request->children_age) && $reservation->children_quantity > 0) {
            for ($i=0; $i < $reservation->children_quantity; $i++) { 
                HotelReservationChildren::create([
                    'reservation_id' => $reservation->id,
                    'age' => $request->children_age[$i], 
                ]);
            }
        }

        if($reservation) {
            $details = [
                'hotel_name' => $reservation->room->merchant->name,
                'room_name' => $reservation->room->room_name,
                'reserved_customer' => ($reservation->reserved_user->lastname) . ', ' . ($reservation->reserved_user->firstname),
                'checkin_date' => $reservation->checkin_date,
                'checkout_date' => $reservation->checkout_date,
                'reservation_link' => route('admin.login') . '?redirectTo=' . route('admin.hotel_reservations.edit', $reservation->id),
            ];

            $hotel_admin = Admin::where('merchant_id', $reservation->room->merchant->id)->first();

            $receiver = config('app.env') === "production" ? $hotel_admin->email : config('mail.test_receiver');
            Mail::to($receiver)->send(new HotelReservationConfirmation($details));
        }

        return response([
            'status' => TRUE,
            'message'=> 'Submission successful! Your request for availability is now under review. Please await the next notification.',
        ], 201);
    }

    public function show(Request $request, $id) {
        $hotelReservation = HotelReservation::where('id', $id)->with('age', 'room')->first();
        
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

    public function getMerchantHotelReservations(Request $request, $merchant_id) {
        $hotelReservations = HotelReservation::where('merchant_id', $merchant_id)
                            ->whereHas('room', function ($q) use ($merchant_id) {
                                $q->where('merchant_id', $merchant_id);
                            })
                            ->with('room')
                            ->get();

        return response([
            'status' => TRUE,
            'message' => 'Hotel Reservations Found',
            'hotel_reservations' => $hotelReservations
        ]);
    }

    public function getUserHotelReservations(Request $request, $user_id) {
        $hotelReservations = HotelReservation::where('reserved_user_id', $user_id)->with('age','room')->get();
        
        return response([
            'status' => TRUE,
            'message' => 'Hotel Reservations Found',
            'hotel_reservations' => $hotelReservations
        ]);
    }
}
