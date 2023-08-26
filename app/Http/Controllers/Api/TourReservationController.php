<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\TourReservation;

class TourReservationController extends Controller
{
    public function getUserTodayReservation(Request $request) {
        $user = Auth::user();
        $today_date = date('Y-m-d');
        $tour_reservation = TourReservation::where('reserved_user_id', $user->id)
                            ->where('start_date', $today_date)
                            ->orWhere('end_date', $today_date)
                            ->first();
        
        if($tour_reservation) {
            return response([
                'status' => TRUE,
                'message' => 'You have a tour reservation today',
                'tour_reservation' => $tour_reservation
            ], 200);
        }

        return response([
            'status' => FALSE,
            'message' => "You don't have a reservation for today"
        ]);
    }
}
