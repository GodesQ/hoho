<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\TourReservation;
use App\Services\BookingService;

use Carbon\Carbon;
use Image;
use thiagoalessio\TesseractOCR\TesseractOCR;

class TourReservationController extends Controller
{
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function getUserTodayReservation(Request $request) {
        $user = Auth::user();
        $today_date = date('Y-m-d');
        // return response($user);
        $tour_reservation = TourReservation::where('reserved_user_id', $user->id)
                            ->where('start_date', $today_date)
                            ->orWhere('end_date', $today_date)
                            ->with('tour', 'tour.transport')
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

    public function getAllUserFutureDateReservations(Request $request) {
        dd($request->all());
        $user = Auth::user();
        dd($user);
        $today = date('Y-m-d');
        $tour_reservations = TourReservation::select('start_date', 'end_date')
                                            ->where('reserved_user_id', $user->id)
                                            ->where('start_date', '>=', $today)
                                            ->get();

        return response($tour_reservations);
    }

    public function storeTourReservation(Request $request) {

        // $image = Image::make($imagePath);
        // dd($image);

        // $text = (new TesseractOCR($imagePath))->run();

        return $this->bookingService->createMultipleBooking($request);
    }

    // public function storeMultipleTourReservation(Request $request) {
    //     return $this->bookingService->createMultipleBooking($request);
    // }

    public function getDIYTicketPassReservations(Request $request) {
        $user = Auth::user();
        $reservations = TourReservation::latest('created_at')->where('status', 'approved')->where('type', 'DIY')->where('reserved_user_id', $user->id)->with('reservation_codes')->get();

        return response($reservations);
    }

    public function getUserReservations(Request $request) {
        $user = Auth::user();
        $reservations = TourReservation::latest('created_at')->where('reserved_user_id', $user->id)->with('tour')->get();
        foreach ($reservations as $reservation) {
            $reservation->setAppends([]); // Exclude the "attractions" attribute for this instance
        }

        return response([
            'status' => TRUE,
            'reservations' => $reservations,
        ]);
    }

    public function verifyReservationCodes(Request $request) {
        $today = date('Y-m-d');
        $tour_reservation = TourReservation::where('id', $request->reservation_id)->first();

        if(!$tour_reservation) {
            return response([
                'status' => FALSE,
                'message' => 'Failed! No Tour Reservation Found',
                'tour_reservation' => $tour_reservation
            ]);
        }

        $qrcode = $tour_reservation->reservation_codes()->where('code', $request->code)->first();

        if(!$qrcode) {
            return response([
                'status' => FALSE,
                'message' => 'Failed! Invalid QR Code',
            ]);
        }

        // return response($this->getDatesInRange($tour_reservation->start_date, $tour_reservation->end_date));

        if($tour_reservation->start_date != $today) {
            return response([
                'status' => FALSE,
                'message' => 'Failed! Your tour reservation is not for today.',
            ]);
        }

        $qrcode->update([
            'scan_count' => $qrcode->scan_count + 1,
            'start_datetime' => Carbon::now(),
            'end_datetime' => Carbon::now()->addDay()
        ]);

        return response([
            'status' => TRUE,
            'message' => 'Success! You can now ride the HOHO bus.',
        ]);
    }

    # HELPERS
    public function getDatesInRange($start_date, $end_date) {
        $start_date = new \DateTime($start_date);
        $end_date = new \DateTime($end_date);

        $dates = array();

        while ($start_date <= $end_date) {
            $dates[] = $start_date->format('Y-m-d');
            $start_date->add(new \DateInterval('P1D'));
        }

        return $dates;
    }
}
