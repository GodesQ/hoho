<?php

namespace App\Http\Controllers\Api\v2;

use App\Enum\TourTypeEnum;
use App\Enum\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TourReservation\StoreRequest;
use App\Http\Requests\TourReservation\v2\BookUnregisteredMultipleReservationsRequest;
use App\Http\Requests\TourReservation\v2\BookUnregisteredSingleReservationsRequest;
use App\Http\Resources\TourReservationResource;
use App\Mail\TourProviderBookingNotification;
use App\Models\LayoverTourReservationDetail;
use App\Models\PromoCode;
use App\Models\Referral;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\TourReservationCustomerDetail;
use App\Models\TourReservationInsurance;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AqwireService;
use App\Services\TestServices\BookingService;
use App\Services\MailService;
use App\Services\Responses\ExceptionHandlerService;
use App\Services\TourReservationService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TourReservationController extends Controller
{
    protected $bookingService;
    private $aqwireService;
    public function __construct(BookingService $bookingService, AqwireService $aqwireService)
    {
        $this->bookingService = $bookingService;
        $this->aqwireService = $aqwireService;
    }

    public function userTourReservations(Request $request, $user_id)
    {
        $reservations = TourReservation::where('reserved_user_id', $user_id)->with('tour', 'feedback')->get();
        return TourReservationResource::collection($reservations);
    }

    public function show(Request $request, $tour_reservation_id)
    {
        return TourReservationResource::make(TourReservation::findOrFail($tour_reservation_id));
    }

    public function userTourReservationDates(Request $request, $user_id)
    {
        $tour_reservations = TourReservation::select('id', 'start_date', 'end_date')
            ->where('reserved_user_id', $user_id)
            ->with('tour', 'feedback')
            ->get();

        $disabledDates = [];

        foreach ($tour_reservations as $reservation)
        {
            if ($reservation->start_date && $reservation->end_date)
            {
                $startDate = $reservation->start_date;
                $endDate = $reservation->end_date;

                $datesInRange = \Carbon\CarbonPeriod::create($startDate, $endDate);

                foreach ($datesInRange as $date)
                {
                    $disabledDates[] = $date ? $date->format('Y-m-d') : null;
                }
            }
        }

        return [
            'data' => $disabledDates,
        ];
    }

    public function store(StoreRequest $request)
    {
        try
        {
            $result = $this->bookingService->handleRegisteredMultipleReservations($request);

            if ($result['status'] == "paying")
            {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservations has been proccessed.",
                    "payment_link" => $result['payment_response']['paymentUrl'],
                    "reservations" => $result['tour_reservations'],
                    "transaction" => $result['transaction'],
                ]);
            }

            return response()->json([
                "status" => $result['status'],
                "message" => "Tour Reservations has been proccessed. Please wait for approval.",
                "transaction" => $result['transaction'],
                "reservations" => $result['tour_reservations'],
            ]);

        } catch (Exception $exception)
        {
            $message = in_array($exception->getCode(), [400, 401, 403, 404, 405, 422]) ? $exception->getMessage() : null;
            $exceptionHandler = new ExceptionHandlerService;
            return $exceptionHandler->handler($exception, $message, $request);
        }
        // return $tourReservationService->storeRegisteredUserReservation($request);
    }

    public function storeGuestReservations(BookUnregisteredMultipleReservationsRequest $request)
    {
        try
        {
            $result = $this->bookingService->handleUnregisteredMultipleReservations($request);

            if (! isset($result['status']))
                throw new Exception("An error occurred while processing the request. The result status could not be found.", 400);

            if ($result['status'] === "success")
            {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                    "reservations" => $result["tour_reservations"],
                ]);
            }

            if ($result['status'] === "paying")
            {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed.",
                    "payment_link" => $result['payment_response']['paymentUrl'],
                    "reservations" => $result["tour_reservations"],
                ]);
            }

        } catch (Exception $exception)
        {
            $message = in_array($exception->getCode(), [400, 401, 403, 404, 405, 422]) ? $exception->getMessage() : null;
            $exceptionHandler = new ExceptionHandlerService;
            return $exceptionHandler->handler($exception, $message, $request);
        }
    }

    public function storeGuestSingleReservation(BookUnregisteredSingleReservationsRequest $request)
    {

        try
        {
            $result = $this->bookingService->handleUnRegisteredSingleReservation($request);

            if (! isset($result['status']))
                throw new Exception("An error occurred while processing the request. The result status could not be found.", 400);

            if ($result['status'] === "success")
            {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                    "reservation" => $result["reservation"]->load('tour', 'reservation_insurance'),
                ]);
            }

            if ($result['status'] === "paying")
            {
                return response()->json([
                    "status" => $result['status'],
                    "message" => "Tour Reservation has been proccessed.",
                    "reservation" => $result["reservation"],
                    "payment_link" => $result['payment_response']['paymentUrl'],
                ]);
            }

        } catch (Exception $exception)
        {
            $message = in_array($exception->getCode(), [400, 401, 403, 404, 405, 422]) ? $exception->getMessage() : null;
            $exceptionHandler = new ExceptionHandlerService;
            return $exceptionHandler->handler($exception, $message, $request);
        }
    }

    private function notifyTourProviderOfBooking($reservation, $transaction, $passenger = null)
    {
        $tour = Tour::where('id', $reservation->tour_id)->first();
        $reserved_passenger = $passenger ? $passenger->firstname . ' ' . $passenger->lastname : '';

        $details = [
            'tour_provider_name' => $tour->tour_provider->merchant->name ?? '',
            'reserved_passenger' => $reserved_passenger,
            'trip_date' => $reservation->start_date,
            'tour_name' => $tour->name
        ];


        if ($tour?->tour_provider?->contact_email)
        {
            $recipientEmail = config('app.env') === 'production' ? $tour->tour_provider->contact_email : config('mail.test_receiver');
            $ccRecipientEmail = config('app.env') === 'production' ? 'philippinehoho@tourism.gov.ph' : config('mail.test_receiver');
            Mail::to($recipientEmail)->cc($ccRecipientEmail)->send(new TourProviderBookingNotification($details));
        }
    }

    private function getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount)
    {
        # NOTE: The amount for each booking has already been set.
        # This function is for additional charges, which the discounted amount calculated from all of the bookings for the transaction.

        return ($subAmount - $totalOfDiscount) + $totalOfAdditionalCharges;
    }
}
