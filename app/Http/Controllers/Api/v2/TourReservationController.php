<?php

namespace App\Http\Controllers\Api\v2;

use App\Enum\TourTypeEnum;
use App\Enum\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TourReservation\SingleGuestReservationRequest;
use App\Http\Requests\TourReservation\StoreRequest;
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
use App\Services\BookingService;
use App\Services\MailService;
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

        foreach ($tour_reservations as $reservation) {
            if ($reservation->start_date && $reservation->end_date) {
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

    public function store(StoreRequest $request, TourReservationService $tourReservationService)
    {
        try {
            $result = $this->bookingService->processMultipleBookingReservation($request);

            return response()->json([
                'status' => true,
                'message' => 'Tour reservation added successfully.',
                'transaction' => $result['transaction'],
                'tour_reservations' => $result['tour_reservations'],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }
        // return $tourReservationService->storeRegisteredUserReservation($request);
    }

    public function storeGuestReservation(Request $request, TourReservationService $tourReservationService)
    {
        $mailService = new MailService;
        return $tourReservationService->storeAnonymousUserReservation($request, $mailService);
    }

    public function storeGuestSingleReservation(SingleGuestReservationRequest $request)
    {
        try {
            DB::beginTransaction();

            $sub_amount = intval($request->amount) ?? 0;
            $total_of_discount = 0;

            $promocode = PromoCode::where('code', $request->promo_code)->first();

            if ($request->promo_code != null || $request->promo_code != "") {
                $total_of_discount = intval($request->amount) - intval($request->discounted_amount);
            }

            if ($request->promo_code === "COMPLIHOHO") {
                $total_of_discount = $request->amount;
            }

            $additional_charges = processAdditionalCharges($sub_amount);

            $total_amount = $this->getTotalAmountOfBooking($sub_amount, $additional_charges['total'], $total_of_discount);

            $reference_no = generateBookingReferenceNumber();


            $transaction = Transaction::create([
                'reference_no' => $reference_no,
                'transaction_by_id' => $request->reserved_user_id,
                'sub_amount' => $subAmount ?? $total_amount,
                'total_additional_charges' => $additional_charges['total'] ?? 0,
                'total_discount' => $totalOfDiscount ?? 0,
                'transaction_type' => TransactionTypeEnum::BOOK_TOUR,
                'payment_amount' => $total_amount,
                'additional_charges' => json_encode($additional_charges['list']),
                'payment_status' => $request->payment_method == "cash" ? 'success' : 'pending',
                'resolution_status' => 'pending',
                'aqwire_paymentMethodCode' => $request->payment_method == "cash" ? "cash" : null,
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
            ]);

            $trip_start_date = Carbon::parse($request->trip_date);
            $trip_end_date = $request->type != 'DIY' || $request->type != 'DIY Tour' ? $trip_start_date->addDays(1) : $this->getDateOfDIYPass($request->ticket_pass, $trip_start_date);

            $tour_type = $request->type ?? $request->tour_type;

            $reservation = TourReservation::create([
                'tour_id' => $tour_type === 'DIY' || $tour_type === 'DIY Tour' ? 63 : $request->tour_id, // Set the tour id to 63 when the tour type is DIY (For Main DIY: Tour)
                'type' => $tour_type,
                'total_additional_charges' => $transaction->total_additional_charges,
                'discount' => $transaction->total_discount,
                'sub_amount' => $transaction->sub_amount,
                'amount' => $transaction->payment_amount,
                'reserved_user_id' => $request->reserved_user_id,
                'passenger_ids' => json_encode([]),
                'reference_code' => $transaction->reference_no,
                'order_transaction_id' => $transaction->id,
                'start_date' => $trip_start_date,
                'has_insurance' => 1,
                'end_date' => $trip_end_date,
                'status' => 'pending',
                'number_of_pass' => $request->number_of_pax,
                'ticket_pass' => $tour_type === 'DIY' || $tour_type === 'DIY Tour' ? ($request->ticket_pass ?? '1 Day Pass') : null,
                'promo_code' => $request->promo_code,
                'discount_amount' => $transaction->sub_amount - $transaction->discount,
                'created_by' => $request->reserved_user_id,
                'created_user_type' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->role : 'guest'
            ]);

            // Add the Reservation Insurance
            TourReservationInsurance::create([
                'insurance_id' => rand(1000000, 100000000),
                'reservation_id' => $reservation->id,
                'type_of_plan' => 1,
                'total_insurance_amount' => 0,
                'number_of_pax' => $reservation->number_of_pass,
            ]);

            // Check if the referral code is valid and existing in the referral list
            $referral = Referral::where('referral_code', $request->referral_code)->first();
            if ($referral) {
                $reservation->update([
                    'referral_merchant_id' => $referral->merchant_id,
                    'referral_code' => $referral->referral_code,
                ]);
            }

            // Store customer details of tour reservation in database
            TourReservationCustomerDetail::create([
                'tour_reservation_id' => $reservation->id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'contact_no' => '+' . $request->contact_no,
                'address' => null,
            ]);

            if ($tour_type == TourTypeEnum::TRANSIT_TOUR) {
                LayoverTourReservationDetail::create([
                    'reservation_id' => $reservation->id,
                    'arrival_datetime' => Carbon::parse($request->arrival_datetime)->format('Y-m-d H:i:s'),
                    'flight_to' => $request->flight_to,
                    'departure_datetime' => Carbon::parse($request->departure_datetime)->format('Y-m-d H:i:s'),
                    'flight_from' => $request->flight_from,
                    'passport_number' => $request->passport_number,
                    'special_instruction' => $request->special_instruction
                ]);
            }

            $status = "success";
            $payment_response = null;

            $user = new User();
            $user->firstname = $request->firstname;
            $user->middlename = $request->middlename;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->mobile_number = $request->mobile_number;

            // Check if the tour type is DIY and the payment method is not cash.
            if ($request->payment_method != "cash" && ($request->type == "DIY" || $request->type == "DIY Tour")) {
                $request_payment_model = $this->aqwireService->createRequestModel($transaction, $user);
                $payment_response = $this->aqwireService->pay($request_payment_model);

                $transaction->update([
                    'aqwire_transactionId' => $payment_response['data']['transactionId'],
                    'payment_url' => $payment_response['paymentUrl'],
                    'payment_status' => Str::lower($payment_response['data']['status']),
                    'payment_details' => json_encode($payment_response),
                ]);

                $status = "paying";
            }

            if (! in_array($user->email, getDevelopersEmail())) {
                // Notify the tour provider based on the reservation of the guest
                $this->notifyTourProviderOfBooking($reservation, $transaction, $user);
            }

            DB::commit();

            if ($status == "paying") {
                return response()->json([
                    "status" => $status,
                    "message" => "Tour Reservation has been proccessed.",
                    "reservation" => $reservation,
                    "payment_link" => $payment_response['paymentUrl'],
                ]);
            }

            return response()->json([
                "status" => $status,
                "message" => "Tour Reservation has been proccessed. Please wait for approval.",
                "reservation" => $reservation->load('tour', 'reservation_insurance'),
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'stauts' => 'failed',
                'error' => $exception,
                'message' => $exception->getMessage()
            ], 400);
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


        if ($tour?->tour_provider?->contact_email) {
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
