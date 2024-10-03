<?php

namespace App\Services;

use App\Enum\TourTypeEnum;
use App\Enum\TransactionTypeEnum;
use App\Models\LayoverTourReservationDetail;
use App\Models\PromoCode;
use App\Models\Referral;
use App\Models\TourReservationCustomerDetail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\TourProviderBookingNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\TourReservation;
use App\Models\Tour;

use DB;

class BookingService
{
    protected $mailService;
    protected $aqwireService;

    public function __construct(MailService $mailService, AqwireService $aqwireService)
    {
        $this->mailService = $mailService;
        $this->aqwireService = $aqwireService;
    }

    /**
     * Process and create single tour reservation.
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return array
     */
    public function processBookingReservation(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = User::where('id', $request->reserved_user_id)->first();

            if (! $user)
                throw new Exception("User Not Found.", 404);

            if (! $user->firstname || ! $user->lastname)
                throw new Exception("Please complete your name before continue to checkout", 422);

            $phone_number = "+{$user->countryCode}{$user->contact_no}";
            if (! preg_match('/^\+\d{10,12}$/', $phone_number))
                throw new Exception("The contact number must be a valid E.164 format.", 422);

            $sub_amount = intval($request->amount) ?? 0;
            $total_of_discount = 0;

            $promocode = PromoCode::where('code', $request->promo_code)->first();

            if ($request->promo_code != null || $request->promo_code != "") {
                $total_of_discount = intval($request->amount) - intval($request->discounted_amount);
            }

            if ($request->promo_code === "COMPLIHOHO") {
                $total_of_discount = $request->amount;
            }

            // Get additional charges
            $additional_charges = $this->processAdditionalCharges($sub_amount);

            $total_amount = $this->getTotalAmountOfBooking($sub_amount, $additional_charges['total'], $total_of_discount);

            // Store transaction in database
            $transaction = $this->storeTransaction($request, $total_amount, $additional_charges['list'], $sub_amount, $total_of_discount, $additional_charges['total']);

            // Store tour reservation and the guest details
            $reservation = $this->storeReservation($request, $transaction);

            $status = "success";
            $payment_response = null;

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

            // Notify the tour provider based on the reservation of the guest
            $this->notifyTourProviderOfBooking($reservation, $transaction);

            DB::commit();

            return [
                "status" => $status,
                "reservation" => $reservation,
                "payment_response" => $payment_response,
            ];

            // return $reservation;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Process and apply multiple tour reservations.
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return array [$transaction, $tour_reservation]
     */
    public function processMultipleBookingReservation(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = User::where('id', $request->reserved_user_id)->first();

            if (! $user->firstname || ! $user->lastname || ! $user->contact_no)
                throw new Exception("The first name, last name and contact number must be filled in correctly in your profile to continue.");

            $phone_number = "+{$user->countryCode}{$user->contact_no}";

            if (! preg_match('/^\+\d{10,12}$/', $phone_number)) {
                throw new Exception("The contact number must be a valid E.164 format.");
            }

            // The items will be the list of tour reservations made by the customers/tourists.
            $items = $request->items;

            if (is_string($items) && is_array(json_decode($items, true))) {
                $items = json_decode($items, true); // Set the 2nd parameter to true to get the associative array result.
            }

            if (! is_array($items))
                throw new Exception("Invalid type of items.");

            $sub_amount = 0;
            $total_discount = 0;
            $total_insurance_amount = 0;

            foreach ($items as $item) {
                $discounted_amount = array_key_exists('discounted_amount', $item) ? intval($item['discounted_amount']) : intval($item['amount']);
                $sub_amount += intval($item['amount']) ?? 0;
                $total_discount += intval($item['amount']) - $discounted_amount;
                $total_insurance_amount += intval($item['total_insurance_amount']) ?? 0;
            }

            // Get additional charges
            $additional_charges = $this->processAdditionalCharges($sub_amount);

            // Compute the total amount of booking 
            $total_amount = $this->getTotalAmountOfBooking($sub_amount, $additional_charges['total'], $total_discount);

            // Add the insurance amount in total amoi
            $total_amount += $total_insurance_amount;

            // Store a transaction in database
            $transaction = $this->storeTransaction($request, $total_amount, $additional_charges['list'], $sub_amount, $total_discount, $additional_charges['total']);

            $tour_reservations = [];

            foreach ($items as $item) {
                // Store tour reservation and customer details
                $reservation = $this->storeReservation($request, $transaction, $item);

                // Check if the tour type is transit, and if it is, then store transit tour details
                if ($item['type'] === TourTypeEnum::TRANSIT_TOUR) {
                    $this->storeTransitTourDetails($reservation, $item);
                }

                // Remove the append attributes in reservation model
                $reservation->setAppends([]);

                // Add new reservation to $tour_reservations variable
                array_push($tour_reservations, $reservation->load('tour'));

                // Notify the tour provider via email
                $this->notifyTourProviderOfBooking($reservation, $transaction);
            }

            DB::commit();

            return [
                'transaction' => $transaction,
                'tour_reservations' => $tour_reservations,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function storeTransaction($request, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges)
    {
        $reference_no = $this->generateReferenceNo();

        $transaction = Transaction::create([
            'reference_no' => $reference_no,
            'transaction_by_id' => $request->reserved_user_id,
            'sub_amount' => $subAmount ?? $totalAmount,
            'total_additional_charges' => $totalOfAdditionalCharges ?? 0,
            'total_discount' => $totalOfDiscount ?? 0,
            'transaction_type' => TransactionTypeEnum::BOOK_TOUR,
            'payment_amount' => $totalAmount,
            'additional_charges' => json_encode($additional_charges),
            'payment_status' => $request->payment_method == "cash" ? 'success' : 'pending',
            'resolution_status' => 'pending',
            'aqwire_paymentMethodCode' => $request->payment_method == "cash" ? "cash" : null,
            'order_date' => Carbon::now(),
            'transaction_date' => Carbon::now(),
        ]);

        return $transaction;
    }

    private function storeReservation($request, $transaction, $item = [])
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->reserved_user_id);

            // Get the start and end date of the booking
            $trip_date = empty($item) ? $request->trip_date : $item['trip_date'];
            $trip_start_date = Carbon::parse($trip_date);
            $trip_end_date = $request->type == 'Guided' ? $trip_start_date->addDays(1) : $this->getDateOfDIYPass($request->ticket_pass, $trip_start_date);

            // Set reservation details
            $tour_id = empty($item) ? $request->tour_id : $item['tour_id'];
            $tour_type = empty($item) ? $request->type : $item['type'];
            $number_of_pax = empty($item) ? $request->number_of_pass : $item['number_of_pass'];
            $ticket_pass = empty($item) ? $request->ticket_pass : $item['ticket_pass'];

            // Insurance
            $has_insurance = isset($item['has_insurance']) ? $item['has_insurance'] : false;
            $type_of_plan = isset($item['type_of_plan']) ? $item['type_of_plan'] : null;
            $total_insurance_amount = isset($item['total_insurance_amount']) ? $item['total_insurance_amount'] : 0.00;

            // Store tour reservation in database
            $reservation = TourReservation::create([
                'tour_id' => $tour_type === 'DIY' || $tour_type === 'DIY Tour' ? 63 : $tour_id, // Set the tour id to 63 when the tour type is DIY (For Main DIY: Tour)
                'type' => $tour_type,
                'total_additional_charges' => $transaction->total_additional_charges,
                'discount' => $transaction->total_discount,
                'sub_amount' => $transaction->sub_amount,
                'amount' => $transaction->payment_amount,
                'reserved_user_id' => $request->reserved_user_id,
                'passenger_ids' => $request->has('passenger_ids') ? json_encode($request->passenger_ids) : json_encode([$request->reserved_user_id]),
                'reference_code' => $transaction->reference_no,
                'order_transaction_id' => $transaction->id,
                'start_date' => $trip_start_date,
                'has_insurance' => $has_insurance,
                'type_of_plan' => $type_of_plan,
                'total_insurance_amount' => $total_insurance_amount,
                'insurance_id' => $has_insurance ? rand(1000000, 100000000) : null,
                'end_date' => $trip_end_date,
                'status' => 'pending',
                'number_of_pass' => $number_of_pax,
                'ticket_pass' => $tour_type === 'DIY' || $tour_type === 'DIY Tour' ? ($ticket_pass ?? '1 Day Pass') : null,
                'promo_code' => $request->promo_code,
                'discount_amount' => $transaction->sub_amount - $transaction->discount,
                'created_by' => $request->reserved_user_id,
                'created_user_type' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->role : 'guest'
            ]);

            // Check if the request has a file of requirements and if it's valid
            if ($request->hasFile('requirement') && $request->file('requirement')->isValid()) {
                $file = $request->file('requirement');
                $file_name = Str::random(7) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . '/assets/img/tour_reservations/requirements/' . $reservation->id, $file_name);

                $reservation->update([
                    'requirement_file_path' => $file_name
                ]);
            }

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
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'contact_no' => '+' . $user->countryCode . $user->contact_no,
                'address' => null,
            ]);

            DB::commit();
            return $reservation;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function storeTransitTourDetails($reservation, $item)
    {
        $layover_user_details = LayoverTourReservationDetail::create([
            'reservation_id' => $reservation->id,
            'arrival_datetime' => $item['arrival_datetime'],
            'flight_to' => $item['flight_to'],
            'departure_datetime' => $item['departure_datetime'],
            'flight_from' => $item['flight_from'],
            'passport_number' => $item['passport_number'],
            'special_instruction' => $item['special_instruction']
        ]);

        return $layover_user_details;
    }

    private function notifyTourProviderOfBooking($reservation, $transaction)
    {
        $tour = Tour::where('id', $reservation->tour_id)->first();

        $details = [
            'tour_provider_name' => $tour->tour_provider->merchant->name ?? '',
            'reserved_passenger' => $transaction->user->firstname . ' ' . $transaction->user->lastname,
            'trip_date' => $reservation->start_date,
            'tour_name' => $tour->name
        ];

        if ($tour?->tour_provider?->contact_email) {
            $recipientEmail = config('app.env') === 'production' ? $tour->tour_provider->contact_email : config('mail.test_receiver');
            $ccRecipientEmail = config('app.env') === 'production' ? 'philippinehoho@tourism.gov.ph' : config('mail.test_receiver');
            Mail::to($recipientEmail)->cc($ccRecipientEmail)->send(new TourProviderBookingNotification($details));
        }
    }

    # HELPERS

    public function processAdditionalCharges(float|int $sub_amount, array $additional = [])
    {
        $additional_charges = [];

        $convenience_fee = getConvenienceFee();

        $total_of_additional_charges = $convenience_fee['type'] === 'percentage' ? $sub_amount * $convenience_fee['amount'] : $sub_amount + $convenience_fee['amount'];

        array_push($additional_charges, ['convenience_fee' => $convenience_fee]);

        return [
            'list' => $additional_charges,
            'total' => $total_of_additional_charges,
        ];
    }

    private function getHMACSignatureHash($text, $secret_key)
    {
        $key = $secret_key;
        $message = $text;

        $hex = hash_hmac('sha256', $message, $key);
        $bin = hex2bin($hex);

        return base64_encode($bin);
    }

    private function getLiveHMACSignatureHash($text, $key)
    {
        $keyBytes = utf8_encode($key);
        $textBytes = utf8_encode($text);

        $hashBytes = hash_hmac('sha256', $textBytes, $keyBytes, true);

        $base64Hash = base64_encode($hashBytes);
        $base64Hash = str_replace(['+', '/'], ['-', '_'], $base64Hash);

        return $base64Hash;
    }

    private function getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount)
    {
        # NOTE: The amount for each booking has already been set.
        # This function is for additional charges, which the discounted amount calculated from all of the bookings for this transaction.

        return ($subAmount - $totalOfDiscount) + $totalOfAdditionalCharges;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OT' . rand(100000, 10000000);
    }

    public function getDateOfDIYPass($ticket_pass, $trip_date)
    {
        if ($ticket_pass == '1 Day Pass') {
            $date = $trip_date->addDays(1);
        } else if ($ticket_pass == '2 Day Pass') {
            $date = $trip_date->addDays(2);
        } else if ($ticket_pass == '3 Day Pass') {
            $date = $trip_date->addDays(3);
        } else {
            $date = $trip_date->addDays(1);
        }

        return $date;
    }

}