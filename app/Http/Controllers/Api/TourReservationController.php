<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourReservation\StoreRequest;
use App\Models\Role;
use App\Models\TourUnavailableDate;
use ErrorException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\TourReservation;
use App\Models\Cart;
use App\Models\User;
use App\Models\ReservationCodeScanLog;
use App\Models\ReservationUserCode;

use App\Services\BookingService;

use Carbon\Carbon;

class TourReservationController extends Controller
{
    protected $bookingService;
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function getUserTodayReservation(Request $request)
    {
        $user = Auth::user();
        $today_date = date('Y-m-d');

        $tour_reservation = TourReservation::where('reserved_user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', $today_date)
            ->with('tour', 'tour.transport')
            ->first();

        // dd($tour_reservation);
        if ($tour_reservation) {
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

    public function getAllUserFutureDateReservations(Request $request)
    {
        $user = Auth::user();
        // Get disabled dates from Tour Reservation
        $tourReservations = TourReservation::where('reserved_user_id', $user->id)->where('status', 'approved')->get();
        $disabledDates = [];


        foreach ($tourReservations as $reservation) {
            if ($reservation->start_date && $reservation->end_date) {
                $startDate = $reservation->start_date;
                $endDate = $reservation->end_date;

                $datesInRange = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($datesInRange as $date) {
                    $disabledDates[] = $date ? $date->format('Y-m-d') : null;
                }
            }
        }

        $cartDates = Cart::where('user_id', $user->id)->pluck('trip_date')->toArray();

        $disabledDates = array_merge($disabledDates, $cartDates);

        $unavailableDates = TourUnavailableDate::pluck('unavailable_date')->toArray();

        $disabledDates = array_merge($disabledDates, $unavailableDates);

        return response([
            'status' => TRUE,
            'disabled_dates' => $disabledDates
        ]);
    }

    public function storeTourReservation(Request $request)
    {
        try {
            // dd($request->all());
            $result = $this->bookingService->processBookingReservation($request);

            if (! isset($result['status']))
                throw new Exception("An error occurred while processing the request. The result status could not be found.", 400);

            if ($result['status'] === "success") {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                ]);
            }

            if ($result['status'] === "paying") {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                    "payment_link" => $result['payment_response']['paymentUrl'],
                ]);
            }

        } catch (Exception $exception) {
            $exception_code = $exception->getCode() == 0 ? 500 : $exception->getCode();

            return response()->json([
                'status' => 'failed',
                'message' => $exception->getMessage(),
            ], $exception_code);
        }
    }

    public function storeMultipleTourReservation(StoreRequest $request)
    {
        try {
            $result = $this->bookingService->processMultipleBookingReservation($request);

            if (! isset($result['status']))
                throw new Exception("An error occurred while processing the request. The result status could not be found.", 400);

            if ($result['status'] === "success") {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                ]);
            }

            if ($result['status'] === "paying") {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                    "payment_link" => $result['payment_response']['paymentUrl'],
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                "status" => 'failed',
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function getDIYTicketPassReservations(Request $request)
    {
        $user = Auth::user();
        $reservations = TourReservation::latest('created_at')
            ->where('status', 'approved')
            ->where('type', 'DIY')
            ->where('reserved_user_id', $user->id)
            ->with('reservation_codes')
            ->get();

        return response($reservations);
    }

    public function getDIYTicketPassReservation(Request $request)
    {
        $reservation_code = ReservationUserCode::where('id', $request->id)->first();

        if (! $reservation_code) {
            return response([
                'status' => FALSE,
                'message' => 'Reservation Not Found',
                'reservation_code' => null
            ], 404);
        }

        return response([
            'status' => TRUE,
            'message' => 'Reservation Found',
            'reservation_code' => $reservation_code
        ]);
    }

    public function getUserReservations(Request $request)
    {
        $user = Auth::user();

        $reservations = TourReservation::latest('created_at')
            ->where('reserved_user_id', $user->id)
            ->with([
                'tour',
                'feedback' => function ($query) use ($user) {
                    $query->where('customer_id', $user->id); // Limit to retrieve only one feedback
                }
            ])
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->setAppends([]); // Exclude the "attractions" attribute for this instance
            $reservation->tour->setAppends([]);
        }

        return response([
            'status' => TRUE,
            'reservations' => $reservations,
        ]);
    }

    // public function verifyReservationCode(Request $request)
    // {
    //     $today = date('Y-m-d');
    //     $user = Auth::user();

    //     if ($user->role != Role::BUS_OPERATOR) {
    //         return response([
    //             'status' => FALSE,
    //             'message' => 'The current authenticated user is not a bus operator.'
    //         ]);
    //     }

    //     $tour_reservation = TourReservation::where('id', $request->reservation_id)->with('tour.transport')->first();
    //     if (!$tour_reservation) {
    //         return response([
    //             'status' => FALSE,
    //             'message' => 'Failed! No Tour Reservation Found',
    //             'tour_reservation' => $tour_reservation
    //         ]);
    //     }

    //     $qrcode = $tour_reservation->reservation_codes()->where('code', $request->code)->first();

    //     if (!$qrcode) {
    //         return response([
    //             'status' => FALSE,
    //             'message' => 'Failed! Invalid QR Code',
    //         ]);
    //     }

    //     // return response($this->getDatesInRange($tour_reservation->start_date, $tour_reservation->end_date));

    //     if ($qrcode->start_datetime) {
    //         $startDatetime = \DateTime::createFromFormat('Y-m-d H:i:s', $qrcode->start_datetime);

    //         if ($startDatetime && $startDatetime->format('Y-m-d') != $today) {
    //             return response([
    //                 'status' => FALSE,
    //                 'message' => 'Failed! You already use this QR Code in other date.',
    //             ]);
    //         }
    //     }

    //     if ($qrcode->status == 'hop_on') {
    //         $status = 'hop_off';
    //         $user->transport()->update([
    //             'available_seats' => $user->transport->available_seats + 1,
    //         ]);
    //     } else {
    //         $status = 'hop_on';
    //         $user->transport()->update([
    //             'available_seats' => $user->transport->available_seats - 1,
    //         ]);
    //     }

    //     $qrcode->update([
    //         'scan_count' => $qrcode->scan_count + 1,
    //         'start_datetime' => $qrcode->start_datetime ? $qrcode->start_datetime : Carbon::now(),
    //         'end_datetime' => $qrcode->end_datetime ? $qrcode->end_datetime : Carbon::now()->addDay(),
    //         'status' => $status
    //     ]);

    //     ReservationCodeScanLog::create([
    //         'reservation_code_id' => $qrcode->id,
    //         'scan_datetime' => Carbon::now(),
    //         'scan_type' => $status
    //     ]);

    //     return response([
    //         'status' => TRUE,
    //         'message' => $status == 'hop_on' ? 'Success! You can now ride the HOHO bus.' : 'Thank you for riding with us! Have a great day!',
    //     ]);
    // }


    public function scanReservationCode(Request $request)
    {
        $today = date('Y-m-d');
        $user = Auth::user();

        if ($user->role != Role::BUS_OPERATOR) {
            return response([
                'status' => FALSE,
                'message' => 'The current authenticated user is not a bus operator.'
            ], 400);
        }

        // $tour_reservation = TourReservation::where('id', $request->reservation_id)->with('tour.transport')->first();
        // if (!$tour_reservation) {
        //     return response([
        //         'status' => FALSE,
        //         'message' => 'Failed! No Tour Reservation Found',
        //         'tour_reservation' => $tour_reservation
        //     ], 404);
        // }

        $qrcode = ReservationUserCode::where('code', $request->code)->first();

        // $qrcode = $tour_reservation->reservation_codes()->where('code', $request->code)->first();

        if (! $qrcode) {
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
                ], 400);
            }
        }

        if ($qrcode->status == 'hop_on') {
            $status = 'hop_off';
            $user->transport()->update([
                'available_seats' => $user->transport->available_seats + 1,
            ]);
        } else {
            $status = 'hop_on';
            $user->transport()->update([
                'available_seats' => $user->transport->available_seats - 1,
            ]);
        }

        $qrcode->update([
            'scan_count' => $qrcode->scan_count + 1,
            'start_datetime' => $qrcode->start_datetime ? $qrcode->start_datetime : Carbon::now(),
            'end_datetime' => $qrcode->end_datetime ? $qrcode->end_datetime : Carbon::now()->addDay(),
            'status' => $status,
            'current_hub' => $request->current_hub,
            'current_attraction' => $request->current_attraction,
        ]);

        ReservationCodeScanLog::create([
            'reservation_code_id' => $qrcode->id,
            'scan_datetime' => Carbon::now(),
            'scan_type' => $status,
            'hub_type_id' => $request->current_hub,
            'attraction_id' => $request->current_attraction,
        ]);

        return response([
            'status' => TRUE,
            'message' => $status == 'hop_on' ? 'Success! You can now ride the HOHO bus.' : 'Thank you for riding with us! Have a great day!',
        ]);
    }

    # HELPERS
    public function getDatesInRange($start_date, $end_date)
    {
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