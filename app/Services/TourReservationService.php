<?php

namespace App\Services;

use App\Mail\PaymentRequestMail;
use App\Mail\TourProviderBookingNotification;
use App\Models\ReservationUserCode;
use App\Models\Role;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\TourReservationCustomerDetail;
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

    public function __construct(AqwireService $aqwireService, MailService $mailService, BookingService $bookingService) {
        $this->aqwireService = $aqwireService;
        $this->mailService = $mailService;
        $this->bookingService = $bookingService;
    }

    public function storeRegisteredUserReservation(Request $request)
    {
        try {
            $user = User::findOrFail($request->reserved_user_id);

            if(!$user->firstname || !$user->lastname || !$user->contact_no) {
                throw new Exception("The first name, last name and contact number must be filled in completely in your profile to continue.");
            }

            $referenceNumber = $this->generateReferenceNo();
            $additionalCharges = $this->generateAdditionalCharges();
            $subAmount = 0;
            $totalOfDiscount = 0;
            $totalOfAdditionalCharges = 0;

            $items = [];

            if (is_string($request->items) && is_array(json_decode($request->items, true))) {
                $items = json_decode($request->items, true);
            }

            if(!is_array($items)) {
                throw new Exception("Items is not a valid JSON type.");
            }

            if(count($items) === 0) {
                throw new Exception("Items is empty. Please populate to continue.");
            }

            foreach ($items as $key => $item) {
                $subAmount += intval($item['amount']) ?? 0;
                $totalOfDiscount += (intval($item['amount'] ?? 0) - (intval($item['discounted_amount'] ?? 0) ?? intval($item['amount'])));
                $totalOfAdditionalCharges += $this->getTotalOfAdditionalCharges(($item['number_of_pax'] ?? 0), $additionalCharges);
            }

            # Calculate Total Amount ((sub amount - total of discount) + total of additional charges)              
            $totalAmount = ($subAmount - $totalOfDiscount) + $totalOfAdditionalCharges;

            # Store Transaction in Database
            $transaction = Transaction::create([
                'reference_no' => $referenceNumber,
                'transaction_by_id' => $user->id,
                'sub_amount' => $subAmount ?? $totalAmount,
                'total_additional_charges' => $totalOfAdditionalCharges ?? 0,
                'total_discount' => $totalOfDiscount ?? 0,
                'transaction_type' => 'book_tour',
                'payment_amount' => $totalAmount,
                'additional_charges' => json_encode($additionalCharges),
                'aqwire_paymentMethodCode' => $request->payment_method ?? null,
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
            ]);

            foreach ($items as $key => $item) {
                $reservation = TourReservation::create([
                    'tour_id' => $item['tour_id'],
                    'type' => $item['type'],
                    'total_additional_charges' => $totalOfAdditionalCharges,
                    'discount' => $totalOfDiscount,
                    'sub_amount' => $subAmount,
                    'amount' => $totalAmount,
                    'reserved_user_id' => $request->reserved_user_id,
                    'passenger_ids' => null,
                    'reference_code' => $transaction->reference_no,
                    'order_transaction_id' => $transaction->id,
                    'start_date' => $item['trip_date'],
                    'end_date' => $item['type'] == 'Guided' || $item['type'] == 'Guided Tour' ? Carbon::parse($item['trip_date'])->addDays(1) : $this->getDateOfDIYPass($item['ticket_pass'], $item['trip_date']),
                    'number_of_pass' => $item['number_of_pax'],
                    'ticket_pass' => $item['type'] == 'DIY' ? $item['ticket_pass'] : null,
                    'promo_code' => $request->promo_code,
                    'requirement_file_path' => null,
                    'discount_amount' => $subAmount - $totalOfDiscount,
                    'created_user_type' => 'guest'
                ]);

                TourReservationCustomerDetail::create([
                    'tour_reservation_id' => $reservation->id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'contact_no' => '+' . $user->countryCode . $user->contact_no,
                    'address' => null,
                ]);
            }

            # Create Request Model for Payment Gateway
            $requestModel = [
                'uniqueId' => $transaction->reference_no,
                'currency' => 'PHP',
                'paymentType' => 'DTP',
                'amount' => $transaction->payment_amount,
                'customer' => [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'email' => $user->email,
                    'mobile' => '+' . $user->countryCode . preg_replace('/[^0-9]/', '', $user->contact_no),
                ],
                'project' => [
                    'name' => 'Philippines Hop-On Hop-Off Checkout Reservation',
                    'unitNumber' => '00000',
                    'category' => 'Checkout'
                ],
                'redirectUrl' => [
                    'success' => env('AQWIRE_TEST_SUCCESS_URL') . $transaction->id,
                    'cancel' => env('AQWIRE_TEST_CANCEL_URL') . $transaction->id,
                    'callback' => env('AQWIRE_TEST_CALLBACK_URL') . $transaction->id
                ],
                'note' => 'Checkout for Tour Reservation',
                'metadata' => [
                    'Convenience Fee' => '99.00' . ' ' . 'Per Pax',
                ]
            ];

            # Generate URL Endpoint and Auth Token for Payment Gateway
            if (config('app.env') === 'production') {
                $url_create = 'https://payments.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            } else {
                $url_create = 'https://payments-sandbox.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Qw-Merchant-Id' => config('services.aqwire.merchant_code'),
                'Authorization' => 'Bearer ' . $authToken,
            ])->post($url_create, $requestModel);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 400) {
                $content = json_decode($response->getBody()->getContents());
                throw new Exception($content->message . ' in Aqwire Payment Gateway.');
            }

            $responseData = json_decode($response->getBody(), true);

            $transaction->update([
                'aqwire_transactionId' => $responseData['data']['transactionId'] ?? null,
                'payment_url' => $responseData['paymentUrl'] ?? null,
                'payment_status' => Str::lower($responseData['data']['status'] ?? ''),
                'payment_details' => json_encode($responseData),
                'additional_charges' => json_encode($additionalCharges)
            ]);

            $this->sendMultipleBookingNotification($items, $transaction, $request);

            $payment_request_details = [
                'transaction_by' => $user->firstname . ' ' . $user->lastname,
                'reference_no' => $transaction->reference_no,
                'total_additional_charges' => $transaction->total_additional_charges,
                'sub_amount' => $transaction->sub_amount,
                'total_amount' => $transaction->payment_amount,
                'payment_url' => $responseData['paymentUrl'],
                'payment_expiration' => $responseData['data']['expiresAt'] ?? null,
            ];

            Mail::to($user->email)->send(new PaymentRequestMail($payment_request_details));

            return response([
                'status' => 'paying',
                'url' => $responseData['paymentUrl']
            ], 201);

        } catch (HttpException $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);

        } catch (\ErrorException $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);

        } catch (Exception $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);

        } catch (\Error $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function storeAnonymousUserReservation(Request $request, MailService $mailService)
    {
        try {

            if(!$request->firstname || !$request->lastname || !$request->contact_no) {
                throw new Exception("The first name, last name and contact number must be filled in completely in your profile to continue.");
            }

            $referenceNumber = $this->generateReferenceNo();
            $additionalCharges = $this->generateAdditionalCharges();
            $subAmount = 0;
            $totalOfDiscount = 0;
            $totalOfAdditionalCharges = 0;

            $items = [];

            if (is_string($request->items) && is_array(json_decode($request->items, true))) {
                $items = json_decode($request->items, true);
            }

            if(!is_array($items)) {
                throw new Exception("Items is not a valid JSON type.");
            }

            foreach ($items as $key => $item) {
                $subAmount += intval($item['amount']) ?? 0;
                $totalOfDiscount += (intval($item['amount'] ?? 0) - (intval($item['discounted_amount'] ?? 0) ?? intval($item['amount'])));
                $totalOfAdditionalCharges += $this->getTotalOfAdditionalCharges(($item['number_of_pax'] ?? 0), $additionalCharges);
            }

            # Calculate Total Amount ((sub amount - total of discount) + total of additional charges)              
            $totalAmount = ($subAmount - $totalOfDiscount) + $totalOfAdditionalCharges;

            # Store Transaction in Database
            $transaction = Transaction::create([
                'reference_no' => $referenceNumber,
                'sub_amount' => $subAmount ?? $totalAmount,
                'total_additional_charges' => $totalOfAdditionalCharges ?? 0,
                'total_discount' => $totalOfDiscount ?? 0,
                'transaction_type' => 'book_tour',
                'payment_amount' => $totalAmount,
                'additional_charges' => json_encode($additionalCharges),
                'aqwire_paymentMethodCode' => $request->payment_method ?? null,
                'order_date' => Carbon::now(),
                'transaction_date' => Carbon::now(),
            ]);

            # Store Multiple Reservation Items in Database
            foreach ($items as $key => $item) {
                $reservation = TourReservation::create([
                    'tour_id' => $item['tour_id'],
                    'type' => $item['type'],
                    'total_additional_charges' => $totalOfAdditionalCharges,
                    'discount' => $totalOfDiscount,
                    'sub_amount' => $subAmount,
                    'amount' => $totalAmount,
                    'reserved_user_id' => null,
                    'passenger_ids' => null,
                    'reference_code' => $transaction->reference_no,
                    'order_transaction_id' => $transaction->id,
                    'start_date' => $item['trip_date'],
                    'end_date' => $item['type'] == 'Guided' || $item['type'] == 'Guided Tour' ? Carbon::parse($item['trip_date'])->addDays(1) : $this->getDateOfDIYPass($item['ticket_pass'], $item['trip_date']),
                    'number_of_pass' => $item['number_of_pax'],
                    'ticket_pass' => $item['type'] == 'DIY' ? $item['ticket_pass'] : null,
                    'promo_code' => $request->promo_code,
                    'requirement_file_path' => null,
                    'discount_amount' => $subAmount - $totalOfDiscount,
                    'created_user_type' => 'guest'
                ]);

                TourReservationCustomerDetail::create([
                    'tour_reservation_id' => $reservation->id,
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'contact_no' => preg_replace('/[^0-9]/', '', $request->contact_no),
                    'address' => $request->address,
                ]);
            }

            # Create Request Model for Payment Gateway
            $requestModel = [
                'uniqueId' => $transaction->reference_no,
                'currency' => 'PHP',
                'paymentType' => 'DTP',
                'amount' => $transaction->payment_amount,
                'customer' => [
                    'name' => $request->firstname . ' ' . $request->lastname,
                    'email' => $request->email,
                    'mobile' => $request->contact_no,
                ],
                'project' => [
                    'name' => 'Philippines Hop-On Hop-Off Checkout Reservation',
                    'unitNumber' => '00000',
                    'category' => 'Checkout'
                ],
                'redirectUrl' => [
                    'success' => env('AQWIRE_TEST_SUCCESS_URL') . $transaction->id,
                    'cancel' => env('AQWIRE_TEST_CANCEL_URL') . $transaction->id,
                    'callback' => env('AQWIRE_TEST_CALLBACK_URL') . $transaction->id
                ],
                'note' => 'Checkout for Tour Reservation',
                'metadata' => [
                    'Convenience Fee' => '99.00' . ' ' . 'Per Pax',
                ]
            ];

            # Generate URL Endpoint and Auth Token for Payment Gateway
            if (config('app.env') === 'production') {
                $url_create = 'https://payments.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getLiveHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            } else {
                $url_create = 'https://payments-sandbox.aqwire.io/api/v3/transactions/create';
                $authToken = $this->getHMACSignatureHash(config('services.aqwire.merchant_code') . ':' . config('services.aqwire.client_id'), config('services.aqwire.secret_key'));
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Qw-Merchant-Id' => config('services.aqwire.merchant_code'),
                'Authorization' => 'Bearer ' . $authToken,
            ])->post($url_create, $requestModel);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 400) {
                $content = json_decode($response->getBody()->getContents());
                throw new Exception($content->message . ' in Aqwire Payment Gateway.');
            }

            $responseData = json_decode($response->getBody(), true);

            $transaction->update([
                'aqwire_transactionId' => $responseData['data']['transactionId'] ?? null,
                'payment_url' => $responseData['paymentUrl'] ?? null,
                'payment_status' => Str::lower($responseData['data']['status'] ?? ''),
                'payment_details' => json_encode($responseData),
                'additional_charges' => json_encode($additionalCharges)
            ]);

            $this->sendMultipleBookingNotification($items, $transaction, $request);

            $payment_request_details = [
                'transaction_by' => $request->firstname . ' ' . $request->lastname,
                'reference_no' => $transaction->reference_no,
                'total_additional_charges' => $transaction->total_additional_charges,
                'sub_amount' => $transaction->sub_amount,
                'total_amount' => $transaction->payment_amount,
                'payment_url' => $responseData['paymentUrl'],
                'payment_expiration' => $responseData['data']['expiresAt'] ?? null,
            ];

            Mail::to($request->email)->send(new PaymentRequestMail($payment_request_details));

            return response([
                'status' => 'paying',
                'url' => $responseData['paymentUrl']
            ], 201);

        } catch (HttpException $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);

        } catch (\ErrorException $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);

        } catch (Exception $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);

        } catch (\Error $e) {
            return response([
                'status' => 'failed',
                'message' => 'Transaction Failed to Submit',
                'error' => $e->getMessage()
            ], 400);
        }

    }

    public function update(Request $request) {
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
            if($request->status === 'approved') {
                $this->handlePaymentForApprovedReservation($reservation);
            }

            DB::commit();

            return $reservation;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function handlePaymentForApprovedReservation($reservation) {
        $payment_request_model = $this->aqwireService->createRequestModel($reservation->transaction, $reservation->user);

        $payment_response = $this->aqwireService->pay($payment_request_model);

        $this->updateTransactionAfterPayment($reservation->transaction, $payment_response);

        $this->mailService->sendPaymentRequestMail($reservation->transaction, $payment_response['paymentUrl'], $payment_response['data']['expiresAt']);
    }

    public function generateAndSendReservationCode($number_of_pax, $reservation) {
        try {
            $reservations_codes = $this->generateReservationCode($number_of_pax, $reservation);

            if($reservation->customer_details) {
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
                
                if($reservation->type == 'DIY Tour' || $reservation->type == 'DIY') {
                    $qrCodes = [];
                    foreach ($reservations_codes as $code) {
                        $qrCodes[] = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($code));
                    }
                    $pdf = PDF::loadView('pdf.qrcodes', ['qrCodes' => $qrCodes]);
                }

                Mail::to(optional($reservation->customer_details)->email)->send(new BookingConfirmationMail($details, $pdf));
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
            ->when(!in_array($current_user->role, [Role::SUPER_ADMIN, Role::ADMIN]), function ($query) use ($current_user) {
                $query->where('created_by', $current_user->id);
            })
            ->when(!empty($request->get('search')), function ($query) use ($request) {
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
            ->when(!empty($request->get('status')), function ($query) use ($request) {
                $statusQuery = $request->get('status');
                $query->where('status', $statusQuery);
            })
            ->when(!empty($request->get('type')), function ($query) use ($request) {
                $typeQuery = $request->get('type');
                $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                    $tourQuery->where('type', $typeQuery);
                });
            })
            ->when(!empty($request->get('trip_date')), function ($query) use ($request) {
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
            ->when(!empty($request->get('search')), function ($query) use ($request) {
                $searchQuery = $request->get('search');
                $query->whereHas('user', function ($userQuery) use ($searchQuery) {
                    $userQuery->where('email', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('firstname', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('lastname', 'LIKE', "%{$searchQuery}%")
                        ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'LIKE', "%{$searchQuery}%");
                });
            })
            ->when(!empty($request->get('status')), function ($query) use ($request) {
                $statusQuery = $request->get('status');
                $query->where('status', $statusQuery);
            })
            ->when(!empty($request->get('type')), function ($query) use ($request) {
                $typeQuery = $request->get('type');
                $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                    $tourQuery->where('type', $typeQuery);
                });
            })
            ->when(!empty($request->get('trip_date')), function ($query) use ($request) {
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
                    <a href="'. route('admin.tour_reservations.edit', $row->id) .'" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a> ';

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

            if(!$tour) {
                throw new Exception("No Tour Found in Item " . ($key + 1));   
            }

            if($tour->tour_provider) {
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

    private function generateReservationCode($number_of_pass, $reservation) {
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

            if($reservation_codes_exist < $number_of_pass) {
                $create_code = ReservationUserCode::create([
                    'reservation_id' => $reservation->id,
                    'code' => $code
                ]);

                array_push($reservation_codes, $create_code->code);
            }
        }

        return $reservation_codes;
    }

    private function updateTransactionAfterPayment($transaction, $payment_response)
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