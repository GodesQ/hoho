<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\TourReservation;
use Illuminate\Http\Request;

class TourReservationController extends Controller
{
    public function userTourReservations(Request $request, $user_id) {
        return TourReservation::where('reserved_user_id', $user_id)->with('tour', 'feedback')->get();
    }

    public function userTourReservationDates(Request $request, $user_id) {
        $tour_reservations = TourReservation::select('id', 'start_date', 'end_date')
                ->where('reserved_user_id', $user_id)
                ->with('tour', 'feedback')
                ->get();

        $disabledDates = [];

        foreach ($tour_reservations as $reservation) {
            if($reservation->start_date && $reservation->end_date) {
                $startDate = $reservation->start_date;
                $endDate = $reservation->end_date;

                $datesInRange = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($datesInRange as $date) {
                    $disabledDates[] = $date ? $date->format('Y-m-d') : null;
                }
            }
        }

        return [
            'data' => $disabledDates,
        ];
    }
}
