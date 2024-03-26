<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationCodeResource;
use App\Models\ReservationUserCode;
use App\Models\TourReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationCodeController extends Controller
{
    public function index(Request $request) {

    }

    public function show(Request $request, $reservation_code_id) {
        $reservation_code = ReservationUserCode::findOrFail($request->reservation_code_id);
        return ReservationCodeResource::make($reservation_code);
    }

    public function getTourReservationCodesByReservation(Request $request, $reservation_id) {
        $reservation_codes = ReservationUserCode::where('reservation_id', $reservation_id)->get();
        return ReservationCodeResource::collection($reservation_codes);
    }

    public function verify(Request $request) {
        $today = date('Y-m-d');
        $tour_reservation = TourReservation::where('id', $request->reservation_id)->with('tour.transport')->first();
        
        if (!$tour_reservation) {
            return response([
                'status' => FALSE,
                'message' => 'Failed! No Tour Reservation Found',
            ], 400);
        }

        $qrcode = $tour_reservation->reservation_codes()->where('code', $request->code)->first();

        if (!$qrcode) {
            return response([
                'status' => FALSE,
                'message' => 'Failed! Invalid QR Code',
            ], 400);
        }

        // return response($this->getDatesInRange($tour_reservation->start_date, $tour_reservation->end_date));

        if ($qrcode->start_datetime) {
            $startDatetime = \DateTime::createFromFormat('Y-m-d H:i:s', $qrcode->start_datetime);

            if ($startDatetime && $startDatetime->format('Y-m-d') != $today) {
                return response([
                    'status' => FALSE,
                    'message' => 'Failed! You already use this QR Code in other date.',
                ]);
            }
        }

        if($qrcode->status == 'hop_on') {
            $status = 'hop_off';
            $tour_reservation->tour->transport->update([
                'available_seats' => $tour_reservation->tour->transport->available_seats + 1,
            ]);
        } else {
            $status = 'hop_on';
            $tour_reservation->tour->transport->update([
                'available_seats' => $tour_reservation->tour->transport->available_seats - 1,
            ]);
        }

        $qrcode->update([
            'scan_count' => $qrcode->scan_count + 1,
            'start_datetime' => $qrcode->start_datetime ? $qrcode->start_datetime : Carbon::now(),
            'end_datetime' => $qrcode->end_datetime ? $qrcode->end_datetime : Carbon::now()->addDay(),
            'status' => $status
        ]);

        // ReservationCodeScanLog::create([
        //     'reservation_code_id' => $qrcode->id,
        //     'scan_datetime' => Carbon::now(),
        //     'scan_type' => $status
        // ]);

        return response([
            'status' => TRUE,
            'message' => $status == 'hop_on' ? 'Success! You can now ride the HOHO bus.' : 'Thank you for riding with us! Have a great day!',
        ]);
    }
}
