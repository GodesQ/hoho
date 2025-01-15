<?php

namespace App\Services\TestServices;

use App\Enum\TourTypeEnum;
use App\Enum\TransactionTypeEnum;
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
use App\Services\MailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingService
{

    private $aqwireService;
    public function __construct(AqwireService $aqwireService)
    {
        $this->aqwireService = $aqwireService;
    }

    public function handleUnregisteredMultipleReservations($request)
    {
        try
        {
            DB::beginTransaction();

            $user = new User();
            $user->firstname = $request->firstname;
            $user->middlename = $request->middlename;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->mobile_number = preg_replace('/\D/', '', $request->contact_no);

            // The items will be the list of tour reservations made by the customers/tourists.
            $items = $request->items;

            $sub_amount = array_sum(array_column($items, 'amount')) ?? 0;
            $total_discount = array_reduce($items, function ($carry, $item) {
                return $carry + (($item['amount'] ?? 0) - ($item['discounted_amount'] ?? $item['amount']));
            }, 0);

            $additional_charges = processAdditionalCharges($sub_amount);
            $total_amount = ($sub_amount - $total_discount) + ($additional_charges['total'] ?? 0);

            $transaction = $this->storeTransaction($request, $total_amount, $additional_charges['list'], $sub_amount, $total_discount, $additional_charges['total']);

            $reservation_items = array_map(fn ($item) => $this->storeReservation($request, $transaction, $user, $item), $items);

            $status = "success";
            $payment_response = null;

            $this->sendMultipleBookingNotification($items, $transaction, $request);

            $first_item_tour = Tour::where('id', $items[0]['tour_id'])->first();
            if ($first_item_tour->type === TourTypeEnum::DIY_TOUR || $first_item_tour->type === "DIY Tour")
            {
                $request_model = $this->aqwireService->createRequestModel($transaction, $user);
                $payment_response = $this->aqwireService->pay($request_model);
                $this->updateTransactionAfterPayment($transaction, $payment_response);
                $mailService = new MailService();
                $mailService->sendPaymentRequestMail($transaction, $payment_response['paymentUrl'], $payment_response['data']['expiresAt'], $user);
                $status = "paying";
            }

            DB::commit();

            return [
                'status' => $status,
                'transaction' => $transaction,
                'tour_reservations' => $reservation_items,
                'payment_response' => $payment_response,
            ];


        } catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
    }

    public function handleRegisteredMultipleReservations($request)
    {
        try
        {
            DB::beginTransaction();

            $user = User::where('id', $request->reserved_user_id)->first();

            // The items will be the list of tour reservations made by the customers/tourists.
            $items = $request->items;

            $sub_amount = array_sum(array_column($items, 'amount')) ?? 0;
            $total_discount = array_reduce($items, function ($carry, $item) {
                return $carry + (($item['amount'] ?? 0) - ($item['discounted_amount'] ?? $item['amount']));
            }, 0);

            $additional_charges = processAdditionalCharges($sub_amount);
            $total_amount = ($sub_amount - $total_discount) + ($additional_charges['total'] ?? 0);

            $transaction = $this->storeTransaction($request, $total_amount, $additional_charges['list'], $sub_amount, $total_discount, $additional_charges['total']);

            $reservation_items = array_map(fn ($item) => $this->storeReservation($request, $transaction, $user, $item), $items);

            $status = "success";
            $payment_response = null;

            $this->sendMultipleBookingNotification($items, $transaction, $request);

            $first_item_tour = Tour::where('id', $items[0]['tour_id'])->first();
            if ($first_item_tour->type === TourTypeEnum::DIY_TOUR || $first_item_tour->type === "DIY Tour")
            {
                $request_model = $this->aqwireService->createRequestModel($transaction, $user);
                $payment_response = $this->aqwireService->pay($request_model);
                $this->updateTransactionAfterPayment($transaction, $payment_response);
                $mailService = new MailService();
                $mailService->sendPaymentRequestMail($transaction, $payment_response['paymentUrl'], $payment_response['data']['expiresAt'], $user);
                $status = "paying";
            }

            DB::commit();

            return [
                'status' => $status,
                'transaction' => $transaction,
                'tour_reservations' => $reservation_items,
                'payment_response' => $payment_response,
            ];

        } catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;

        }
    }

    public function handleUnRegisteredSingleReservation($request)
    {
        try
        {
            DB::beginTransaction();

            $user = new User();
            $user->firstname = $request->firstname;
            $user->middlename = $request->middlename;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->mobile_number = $request->contact_no;

            $sub_amount = intval($request->amount) ?? 0;
            $total_discount = 0;

            if ($request->promo_code != null || $request->promo_code != "")
            {
                $total_discount = intval($request->amount) - intval($request->discounted_amount);
            }

            if ($request->promo_code === "COMPLIHOHO")
            {
                $total_discount = $request->amount;
            }

            $additional_charges = processAdditionalCharges($sub_amount);
            $total_amount = ($sub_amount - $total_discount) + ($additional_charges['total'] ?? 0);

            $transaction = $this->storeTransaction($request, $total_amount, $additional_charges['list'], $sub_amount, $total_discount, $additional_charges['total']);

            $reservation = $this->storeReservation($request, $transaction, $user);


            $status = "success";
            $payment_response = null;

            // Check if the tour type is DIY and the payment method is not cash.
            if ($request->payment_method != "cash" && ($request->type == "DIY" || $request->type == "DIY Tour"))
            {
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

            DB::commit();

            return [
                "status" => $status,
                "reservation" => $reservation,
                "payment_response" => $payment_response,
            ];

        } catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }
    }

    public function handleRegisteredSingleReservation($request)
    {
        try
        {
            DB::beginTransaction();

            $user = User::where('id', $request->reserved_user_id)->first();
            $sub_amount = intval($request->amount) ?? 0;
            $total_discount = 0;

            $promocode = PromoCode::where('code', $request->promo_code)->first();

            if ($request->promo_code != null || $request->promo_code != "")
            {
                $total_discount = intval($request->amount) - intval($request->discounted_amount);
            }

            if ($request->promo_code === "COMPLIHOHO")
            {
                $total_discount = $request->amount;
            }

            // Get additional charges
            $additional_charges = processAdditionalCharges($sub_amount);
            $total_amount = ($sub_amount - $total_discount) + ($additional_charges['total'] ?? 0);

            // Store transaction in database
            $transaction = $this->storeTransaction($request, $total_amount, $additional_charges['list'], $sub_amount, $total_discount, $additional_charges['total']);

            // Store tour reservation and the guest details
            $reservation = $this->storeReservation($request, $transaction, $user);

            $status = "success";
            $payment_response = null;

            // Check if the tour type is DIY and the payment method is not cash.
            if ($request->payment_method != "cash" && ($request->type == "DIY" || $request->type == "DIY Tour"))
            {
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

            DB::commit();

            return [
                "status" => $status,
                "reservation" => $reservation,
                "payment_response" => $payment_response,
            ];
        } catch (Exception $exception)
        {
            DB::rollBack();
            dd($exception);
            throw $exception;
        }
    }

    /*************************************************************** HELPERS *****************************************************/

    private function storeTransaction($request, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $totalOfAdditionalCharges)
    {
        $reference_no = generateBookingReferenceNumber();

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

    /**
     * Summary of storeReservation
     * @param mixed $request
     * @param mixed $transaction
     * @param mixed $user
     * @param mixed $item
     * @return TourReservation|\Illuminate\Database\Eloquent\Model
     */
    private function storeReservation($request, $transaction, $user, $item = [])
    {
        try
        {
            DB::beginTransaction();

            // Get the start and end date of the booking
            $trip_date = empty($item) ? $request->trip_date : $item['trip_date'];
            $trip_start_date = Carbon::parse($trip_date);
            $trip_end_date = $request->type == 'Guided' ? $trip_start_date->addDays(1) : getDateOfDIYPass($request->ticket_pass, $trip_start_date);

            // Set reservation details
            $tour_id = empty($item) ? $request->tour_id : $item['tour_id'];
            $tour_type = empty($item) ? $request->type : $item['type'];
            $number_of_pax = empty($item) ? $request->number_of_pass : $item['number_of_pass'];
            $ticket_pass = empty($item) ? $request->ticket_pass : $item['ticket_pass'];

            // Insurance
            $has_insurance = $item['has_insurance'] ?? $request->has_insurance ?? false;
            $type_of_plan = $item['type_of_plan'] ?? $request->type_of_plan ?? 1;
            $total_insurance_amount = $item['total_insurance_amount'] ?? $request->total_insurance_amount ?? 0.00;

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
                'has_insurance' => 1,
                'end_date' => $trip_end_date,
                'status' => 'pending',
                'number_of_pass' => $number_of_pax,
                'ticket_pass' => $tour_type === 'DIY' || $tour_type === 'DIY Tour' ? ($ticket_pass ?? '1 Day Pass') : null,
                'promo_code' => $request->promo_code,
                'discount_amount' => $transaction->sub_amount - $transaction->discount,
                'created_by' => $request->reserved_user_id,
                'created_user_type' => Auth::guard('admin')->user() ? Auth::guard('admin')->user()->role : 'guest'
            ]);

            $reservation->setAppends([]);

            $this->storeTourInsurance($reservation, $type_of_plan, $total_insurance_amount);

            // Check if the request has a file of requirements and if it's valid
            if ($request->hasFile('requirement') && $request->file('requirement')->isValid())
            {
                $this->saveAndUploadRequirement($request, $reservation);
            }

            $this->checkAndStoreReferralCode($reservation, $request->referral_code);

            $user_email = $user->email_address ?? $user->email;
            $user_mobile_number = "+".($user->mobile_number ?? $user->countryCode.$user->contact_no);

            // Store customer details of tour reservation in database
            TourReservationCustomerDetail::create([
                'tour_reservation_id' => $reservation->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user_email,
                'contact_no' => $user_mobile_number,
                'address' => null,
            ]);

            if ($tour_type === TourTypeEnum::TRANSIT_TOUR)
            {
                $transit_details = [
                    'arrival_datetime' => $item['arrival_datetime'] ?? $request->arrival_datetime,
                    'departure_datetime' => $item['departure_datetime'] ?? $request->departure_datetime,
                    'flight_to' => $item['flight_to'] ?? $request->flight_to,
                    'flight_from' => $item['flight_from'] ?? $request->flight_from,
                    'passport_number' => $item['passport_number'] ?? $request->passport_number,
                    'special_instruction' => $item['special_instruction'] ?? $request->special_instruction,
                ];

                $this->storeTransitTourDetails($reservation, $transit_details);
            }

            if (! in_array($user->email, getDevelopersEmail()))
            {
                // Notify the tour provider via email
                $this->notifyTourProviderOfBooking($reservation, $transaction, $user);
            }

            DB::commit();
            return $reservation;
        } catch (Exception $e)
        {
            DB::rollBack();
            throw $e;
        }
    }

    private function storeTransitTourDetails($reservation, $transit_details)
    {
        $layover_user_details = LayoverTourReservationDetail::create([
            'reservation_id' => $reservation->id,
            'arrival_datetime' => $transit_details['arrival_datetime'],
            'flight_to' => $transit_details['flight_to'],
            'departure_datetime' => $transit_details['departure_datetime'],
            'flight_from' => $transit_details['flight_from'],
            'passport_number' => $transit_details['passport_number'],
            'special_instruction' => $transit_details['special_instruction']
        ]);

        return $layover_user_details;
    }

    private function checkAndStoreReferralCode($reservation, $referral_code)
    {
        // Check if the referral code is valid and existing in the referral list
        $referral = Referral::where('referral_code', $referral_code)->first();
        if ($referral)
        {
            $reservation->update([
                'referral_merchant_id' => $referral->merchant_id,
                'referral_code' => $referral->referral_code,
            ]);
        }
    }

    private function storeTourInsurance($reservation, $type_of_plan, $total_insurance_amount)
    {
        // Add the Reservation Insurance
        TourReservationInsurance::create(attributes: [
            'insurance_id' => rand(1000000, 100000000),
            'reservation_id' => $reservation->id,
            'type_of_plan' => $type_of_plan,
            'total_insurance_amount' => $total_insurance_amount,
            'number_of_pax' => $reservation->number_of_pass,
        ]);
    }

    private function notifyTourProviderOfBooking($reservation, $transaction, $user)
    {
        $tour = Tour::where('id', $reservation->tour_id)->first();

        $details = [
            'tour_provider_name' => $tour->tour_provider->merchant->name ?? '',
            'reserved_passenger' => $user->firstname.' '.$user->lastname,
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

    public function updateTransactionAfterPayment($transaction, $payment_response)
    {
        $update_transaction = $transaction->update([
            'aqwire_transactionId' => $payment_response['data']['transactionId'],
            'payment_url' => $payment_response['paymentUrl'],
            'payment_status' => Str::lower($payment_response['data']['status']),
            'payment_details' => json_encode($payment_response),
        ]);

        return $update_transaction;
    }

    private function sendMultipleBookingNotification($items, $transaction, $request)
    {
        foreach ($items as $key => $item)
        {
            $tour = Tour::where('id', $item['tour_id'])->first();

            if (! $tour)
            {
                throw new Exception("No Tour Found in Item ".($key + 1));
            }

            if ($tour->tour_provider)
            {
                $details = [
                    'tour_provider_name' => $tour->tour_provider->merchant->name,
                    'reserved_passenger' => $request->firstname.' '.$request->lastname,
                    'trip_date' => $item['trip_date'],
                    'tour_name' => $tour->name
                ];

                if ($tour?->tour_provider?->contact_email)
                {
                    $recipientEmail = config('app.env') === 'production' ? $tour->tour_provider->contact_email : config('mail.test_receiver');
                    Mail::to($recipientEmail)->send(new TourProviderBookingNotification($details));
                }
            }
        }
    }

    private function saveAndUploadRequirement($request, $reservation)
    {
        $file = $request->file('requirement');
        $file_name = Str::random(7).'-'.time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path().'/assets/img/tour_reservations/requirements/'.$reservation->id, $file_name);

        $reservation->update([
            'requirement_file_path' => $file_name
        ]);
    }
}