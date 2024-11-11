<?php

namespace App\Services;

use App\Enum\TourTypeEnum;
use App\Enum\TransactionTypeEnum;
use App\Mail\PaymentRequestMail;
use App\Mail\TourProviderBookingNotification;
use App\Models\Referral;
use App\Models\ReservationUserCode;
use App\Models\Role;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\TourReservationCustomerDetail;
use App\Models\TourReservationInsurance;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;
use App\Mail\BookingConfirmationMail;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class TourReservationService
{
    protected $aqwireService;
    protected $mailService;
    protected $bookingService;

    public function __construct(AqwireService $aqwireService, MailService $mailService, BookingService $bookingService)
    {
        $this->aqwireService = $aqwireService;
        $this->mailService = $mailService;
        $this->bookingService = $bookingService;
    }

    public function storeAnonymousUserReservations(Request $request, MailService $mailService)
    {
        try {
            DB::beginTransaction();

            if (! $request->firstname || ! $request->lastname || ! $request->contact_no)
                throw new Exception("The first name, last name and contact number must be filled in correctly in your profile to continue.");

            $phone_number = $request->contact_no;

            if (! preg_match('/^\+\d{10,12}$/', $phone_number)) {
                throw new Exception("The contact number must be a valid E.164 format.");
            }

            $subAmount = 0;
            $totalOfDiscount = 0;

            $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

            if (! is_array($items)) {
                throw new Exception("Items is not a valid JSON type.");
            }

            $subAmount = array_sum(array_column($items, 'amount'));
            $totalOfDiscount = array_reduce($items, function ($carry, $item) {
                return $carry + (($item['amount'] ?? 0) - ($item['discounted_amount'] ?? $item['amount']));
            }, 0);

            $additional_charges = processAdditionalCharges($subAmount);

            $totalAmount = ($subAmount - $totalOfDiscount) + $additional_charges['total'];

            $transaction = $this->storeTransaction($request, $totalAmount, $additional_charges, $subAmount, $totalOfDiscount, $additional_charges['total']);

            $reservation_items = array_map(fn ($item) => $this->storeReservation($request, $transaction, $item), $items);

            $status = "success";
            $payment_response = null;

            $user = $this->createUser($request);

            $first_item_tour = Tour::where('id', $items[0]['tour_id'])->first();

            if ($first_item_tour->type === TourTypeEnum::DIY_TOUR || $first_item_tour === "DIY Tour") {
                $request_model = $this->aqwireService->createRequestModel($transaction, $user);
                $payment_response = $this->aqwireService->pay($request_model);

                $this->updateTransactionAfterPayment($transaction, $payment_response);

                $payment_request_details = [
                    'transaction_by' => $request->firstname . ' ' . $request->lastname,
                    'reference_no' => $transaction->reference_no,
                    'total_additional_charges' => $transaction->total_additional_charges,
                    'sub_amount' => $transaction->sub_amount,
                    'total_amount' => $transaction->payment_amount,
                    'payment_url' => $payment_response['paymentUrl'] ?? null,
                    'payment_expiration' => $payment_response['data']['expiresAt'] ?? null,
                ];

                Mail::to($request->email)->send(new PaymentRequestMail($payment_request_details));
                $status = "paying";

            } else {
                $this->sendMultipleBookingNotification($items, $transaction, $request);
            }

            DB::commit();

            return [
                "status" => $status,
                "payment_link" => $payment_response['paymentUrl'] ?? null,
                "reservations" => $reservation_items,
                "transaction" => $transaction,
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $reservation = TourReservation::where('id', $request->id)->with('user', 'customer_details', 'transaction')->first();

            $trip_date = Carbon::parse($request->trip_date);

            $reservation->update([
                'start_date' => $trip_date->format('Y-m-d'),
                'end_date' => $request->type === 'Guided' ? $trip_date->addDays(1) : $this->bookingService->getDateOfDIYPass($request->ticket_pass, $trip_date),
                'status' => $request->status
            ]);

            // If the status is approved, process the payment of tour reservation and send the payment request to user. 
            if ($request->status === 'approved') {
                if ($reservation->transaction->aqwire_paymentMethodCode === 'cash') {
                    $this->generateAndSendReservationCode($reservation->number_of_pass, $reservation);
                } else {
                    if (! $reservation->transaction->payment_url) {
                        $this->handlePaymentForApprovedReservation($reservation);
                    }
                }
            }

            DB::commit();

            return $reservation;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**** HELPER FUNCTIONS ****/

    private function createUser($request)
    {
        $user = new User();
        $user->firstname = $request->firstname;
        $user->middlename = $request->middlename;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_number;

        return $user;
    }

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


    private function storeReservation($request, $transaction, $item = [])
    {
        try {
            DB::beginTransaction();

            $user = User::find($request->reserved_user_id);

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

            // Add the Reservation Insurance
            TourReservationInsurance::create([
                'insurance_id' => rand(1000000, 100000000),
                'reservation_id' => $reservation->id,
                'type_of_plan' => $type_of_plan,
                'total_insurance_amount' => $total_insurance_amount,
                'number_of_pax' => $reservation->number_of_pass,
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

            $user_contact_number = $user ? $user->countryCode . $user->contact_no : preg_replace('/\D/', '', ($request->contact_no ?? $request->contact_number));

            // Store customer details of tour reservation in database
            TourReservationCustomerDetail::create([
                'tour_reservation_id' => $reservation->id,
                'firstname' => $user ? $user->firstname : $request->firstname,
                'lastname' => $user ? $user->lastname : $request->lastname,
                'email' => $user ? $user->email : ($request->email ?? $request->email_address),
                'contact_no' => $user_contact_number,
                'address' => $request->address ?? null,
            ]);

            DB::commit();
            return $reservation;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handlePaymentForApprovedReservation($reservation)
    {
        if (! $reservation->transaction->payment_url) {
            $payment_request_model = $this->aqwireService->createRequestModel($reservation->transaction, $reservation->user);

            $payment_response = $this->aqwireService->pay($payment_request_model);

            $this->updateTransactionAfterPayment($reservation->transaction, $payment_response);
            $this->mailService->sendPaymentRequestMail($reservation->transaction, $payment_response['paymentUrl'], $payment_response['data']['expiresAt']);
        }
    }

    public function generateAndSendReservationCode($number_of_pax, $reservation)
    {
        try {
            $reservations_codes = $this->generateReservationCode($number_of_pax, $reservation);

            if ($reservation->customer_details) {
                $what = $reservation->type == 'DIY' ? (
                    $reservation->ticket_pass . " x " . $reservation->number_of_pass . " pax " . "(Valid for 24 hours from first tap)"
                )
                    : (
                        "1 Guided Tour " . '"' . $reservation->tour->name . '"' . ' x ' . $reservation->number_of_pass . ' pax'
                    );

                $trip_date = Carbon::parse($reservation->start_date);
                $when = $trip_date->format('l, F j, Y');

                $details = [
                    'name' => $reservation->customer_details->firstname . ' ' . $reservation->customer_details->lastname,
                    'what' => $what,
                    'when' => $when,
                    'where' => 'Robinson’s Manila',
                    'type' => $reservation->type,
                    'tour_name' => optional($reservation->tour)->name
                ];

                $pdf = null;

                if ($reservation->type == 'DIY Tour' || $reservation->type == 'DIY') {
                    $qrCodes = [];
                    foreach ($reservations_codes as $code) {
                        $value = $code . "&" . $reservation->id;
                        $qrCodes[] = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($value));
                    }
                    $pdf = PDF::loadView('pdf.qrcodes', ['qrCodes' => $qrCodes]);
                }

                Mail::to(optional($reservation->customer_details)->email)->send(new BookingConfirmationMail($details, $pdf));

                return $reservations_codes;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function retrieveAllTourReservationsList($request)
    {
        $current_user = Auth::guard('admin')->user();

        $data = TourReservation::with('user', 'tour')
            // ->whereHas('user')
            ->when(! in_array($current_user->role, [Role::SUPER_ADMIN, Role::ADMIN]), function ($query) use ($current_user) {
                $query->where('created_by', $current_user->id);
            })
            ->when(! empty($request->get('search')), function ($query) use ($request) {
                $searchQuery = $request->get('search');
                $query->whereHas('user', function ($userQuery) use ($searchQuery) {
                    $userQuery->where('email', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('firstname', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('lastname', 'LIKE', "%{$searchQuery}%")
                        ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'LIKE', "%{$searchQuery}%");
                })->orWhereHas('tour', function ($tourQuery) use ($searchQuery) {
                    $tourQuery->where('name', 'LIKE', $searchQuery . '%');
                });
            })
            ->when(! empty($request->get('status')), function ($query) use ($request) {
                $statusQuery = $request->get('status');
                $query->where('status', $statusQuery);
            })
            ->when(! empty($request->get('type')), function ($query) use ($request) {
                $typeQuery = $request->get('type');
                $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                    $tourQuery->where('type', $typeQuery);
                });
            })
            ->when(! empty($request->get('trip_date')), function ($query) use ($request) {
                $tripDateQuery = $request->get('trip_date');
                $query->where('start_date', $tripDateQuery);
            });

        return $this->_generateDataTable($data, $request);
    }

    public function retrieveTourProviderReservationsList(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = TourReservation::with('user', 'tour', 'transaction')
            ->whereHas('tour', function ($query) use ($admin) {
                return $query->where('tour_provider_id', $admin->merchant->tour_provider_info->id);
            })
            ->when(! empty($request->get('search')), function ($query) use ($request) {
                $searchQuery = $request->get('search');
                $query->whereHas('user', function ($userQuery) use ($searchQuery) {
                    $userQuery->where('email', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('firstname', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('lastname', 'LIKE', "%{$searchQuery}%")
                        ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'LIKE', "%{$searchQuery}%");
                });
            })
            ->when(! empty($request->get('status')), function ($query) use ($request) {
                $statusQuery = $request->get('status');
                $query->where('status', $statusQuery);
            })
            ->when(! empty($request->get('type')), function ($query) use ($request) {
                $typeQuery = $request->get('type');
                $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                    $tourQuery->where('type', $typeQuery);
                });
            })
            ->when(! empty($request->get('trip_date')), function ($query) use ($request) {
                $tripDateQuery = $request->get('trip_date');
                $query->where('start_date', $tripDateQuery);
            });

        return $this->_generateDataTable($data, $request);
    }

    private function _generateDataTable($data, $request)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('start_date', function ($row) {
                return Carbon::parse($row->start_date)->format('M d, Y');
            })
            ->addColumn('reserved_user', function ($row) {
                if ($row->customer_details) {
                    return view('components.user-contact', ['user' => $row->customer_details]);
                }

                return '-';
            })
            ->editColumn('tour', function ($row) {
                if ($row->tour) {
                    return view('components.tour', ['tour' => $row->tour]);
                }
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 'approved') {
                    return '<div class="badge bg-label-success">Approved</div>';
                } else if ($row->status == 'pending') {
                    return '<div class="badge bg-label-warning">Pending</div>';
                } else if ($row->status == 'cancelled') {
                    return '<div class="badge bg-label-warning">Cancelled</div>';
                }
            })
            ->addColumn('transaction_status', function ($row) {
                if ($row->transaction->payment_status == 'success') {
                    return '<div class="badge bg-label-success">Paid</div>';
                } else if ($row->transaction->payment_status == 'pending') {
                    return '<div class="badge bg-label-warning">Unpaid</div>';
                } else if ($row->transaction->payment_status == 'cancelled') {
                    return '<div class="badge bg-label-warning">Cancelled</div>';
                } else if ($row->transaction->payment_status == 'inc') {
                    return '<div class="badge bg-label-warning">Incompleted</div>';
                }
            })
            ->addColumn('actions', function ($row) {
                $output = '<div class="dropdown">
                    <a href="' . route('admin.tour_reservations.edit', $row->id) . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a> ';

                $output .= $row->status === 'pending' && optional($row->transaction)->payment_status != 'success' ? '<button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>' : '';

                $output .= '</div>';
                return $output;
            })
            ->rawColumns(['actions', 'status', 'transaction_status'])
            ->make(true);
    }

    private function sendMultipleBookingNotification($items, $transaction, $request)
    {
        foreach ($items as $key => $item) {
            $tour = Tour::where('id', $item['tour_id'])->first();

            if (! $tour) {
                throw new Exception("No Tour Found in Item " . ($key + 1));
            }

            if ($tour->tour_provider) {
                $details = [
                    'tour_provider_name' => $tour->tour_provider->merchant->name,
                    'reserved_passenger' => $request->firstname . ' ' . $request->lastname,
                    'trip_date' => $item['trip_date'],
                    'tour_name' => $tour->name
                ];

                if ($tour?->tour_provider?->contact_email) {
                    $recipientEmail = config('app.env') === 'production' ? $tour->tour_provider->contact_email : config('mail.test_receiver');
                    Mail::to($recipientEmail)->send(new TourProviderBookingNotification($details));
                }
            }
        }
    }

    private function generateReservationCode($number_of_pass, $reservation)
    {
        // Generate the random letter part
        // Assuming you have str_random function available
        $random_letters = strtoupper(Str::random(5));
        $reservation_codes = [];

        for ($i = 1; $i <= $number_of_pass; $i++) {
            // Generate the pass number with leading zeros (e.g., -001)
            $pass_number = str_pad($i, 3, '0', STR_PAD_LEFT);

            // Concatenate the parts to create the code
            $code = "GRP{$random_letters}{$reservation->id}-{$pass_number}";

            $reservation_codes_exist = ReservationUserCode::where('reservation_id', $reservation->id)->count();

            if ($reservation_codes_exist < $number_of_pass) {
                $create_code = ReservationUserCode::create([
                    'reservation_id' => $reservation->id,
                    'code' => $code
                ]);

                array_push($reservation_codes, $create_code->code);
            }
        }

        return $reservation_codes;
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

    # HELPERS
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

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OT' . rand(100000, 10000000);
    }

    private function generateAdditionalCharges()
    {
        $charges = [
            'Convenience Fee' => 99,
            // 'Travel Pass' => 50,
        ];

        return $charges;
    }

    private function getTotalAmountOfBooking($subAmount, $totalOfAdditionalCharges, $totalOfDiscount)
    {
        # NOTE: The amount for each booking has already been set.
        # This function is for additional charges, which the discounted amount calculated from all of the bookings for this transaction.

        return ($subAmount - $totalOfDiscount) + $totalOfAdditionalCharges;
    }

    private function getTotalOfAdditionalCharges($number_of_pax, $additional_charges)
    {
        $convenience_fee = $additional_charges['Convenience Fee'] * $number_of_pax;
        // $travel_pass = $additional_charges['Travel Pass'] * $number_of_pax;
        $travel_pass = ($additional_charges['Travel Pass'] ?? 0) * $number_of_pax;

        return $convenience_fee + $travel_pass;
    }

    private function getDateOfDIYPass($ticket_pass, $trip_date)
    {
        if ($ticket_pass == '1 Day Pass') {
            $date = Carbon::parse($trip_date)->addDays(1);
        } else if ($ticket_pass == '2 Day Pass') {
            $date = Carbon::parse($trip_date)->addDays(2);
        } else if ($ticket_pass == '3 Day Pass') {
            $date = Carbon::parse($trip_date)->addDays(3);
        } else {
            $date = Carbon::parse($trip_date)->addDays(1);
        }

        return $date;
    }

}