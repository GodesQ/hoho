<?php 
namespace App\Services;
use App\Enum\TransactionTypeEnum;
use App\Mail\HotelReservationApproved;
use App\Mail\HotelReservationConfirmation;
use App\Models\Admin;
use App\Models\HotelReservation;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Enum\LoggerActionEnum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class HotelReservationService {

    private $aqwireService;
    
    public function __construct(AqwireService $aqwireService) {
        $this->aqwireService = $aqwireService;
    }

    public function create(Request $request, $data) {
        try {
            DB::beginTransaction();

            $number_of_pax = $request->adult_quantity + $request->children_quantity;

            $reservation = HotelReservation::create(array_merge($data, [
                'number_of_pax' => $number_of_pax,
                'approved_date' => $request->status == 'approved' ? Carbon::now() : null,
            ]));

            $details = [
                'hotel_name' => $reservation->room->merchant->name,
                'room_name' => $reservation->room->room_name,
                'reserved_customer' => ($reservation->reserved_user->lastname) . ', ' . ($reservation->reserved_user->firstname),
                'checkin_date' => $reservation->checkin_date,
                'checkout_date' => $reservation->checkout_date,
                'reservation_link' => route('admin.login') . '?redirectTo=' . route('admin.hotel_reservations.edit', $reservation->id),
            ];

            // Store this transaction in system log.
            LoggerService::log(LoggerActionEnum::CREATE, HotelReservation::class, ['added' => $request->all()]);

            $hotel_admin = Admin::where('merchant_id', $reservation->room->merchant->id)->first();
            $receiver = config('app.env') === 'production' ? $hotel_admin->email : config('mail.test_receiver');
            Mail::to($receiver)->send(new HotelReservationConfirmation($details));

            DB::commit();
            return $reservation;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($request, $id, $data) {
        try {
            DB::beginTransaction();

            $reservation = HotelReservation::where('id', $id)->firstOrFail();
            $number_of_pax = $request->adult_quantity + $request->children_quantity;

            if(!$reservation->transaction_id && $reservation->payment_status === 'unpaid' && $request->status === 'approved' ) {
                $reference_no = $this->generateReferenceNo();
                $checkin_date = Carbon::parse($request->checkin_date);
                $checkout_date = Carbon::parse($request->checkout_date);
                
                $total_days = $checkin_date->diffInDays($checkout_date);

                $additional_charges = getConvenienceFee();

                $total_amount_of_all_days = $reservation->room->price * $total_days; // The price is multiplied by the number of stayed days.
                $total_amount = $this->computeTotalAmount($total_amount_of_all_days, $additional_charges);

                $transaction = Transaction::create([
                    'reference_no' => $reference_no,
                    'transaction_by_id' => $reservation->reserved_user->id,
                    'sub_amount' => $reservation->room->price,
                    'total_additional_charges' => $additional_charges,
                    'additional_charges' => json_encode(['Convenience Fee' => 99]),
                    'transaction_type' => TransactionTypeEnum::HOTEL_RESERVATION,
                    'payment_amount' => $total_amount,
                    'order_date' => Carbon::now(),
                    'transaction_date' => Carbon::now(),
                ]);

                $reservation->update([
                    'reference_number' => $transaction->reference_no,
                    'transaction_id' => $transaction->id,
                ]);

                $payment_request_model = $this->aqwireService->createRequestModel($transaction, $reservation->reserved_user);

                $payment_response = $this->aqwireService->pay($payment_request_model);

                $transaction->update([
                    'aqwire_transactionId' => $payment_response['data']['transactionId'] ?? null,
                    'payment_url' => $payment_response['paymentUrl'] ?? null,
                    'payment_status' => Str::lower($payment_response['data']['status'] ?? ''),
                    'payment_details' => json_encode($payment_response),
                ]);

                LoggerService::log(LoggerActionEnum::CREATE, Transaction::class, ['added' => $transaction->toArray()]);

                $details = [
                    'reserved_customer' => $reservation->reserved_user->firstname . ' ' . $reservation->reserved_user->lastname,
                    'room_name' => $reservation->room->room_name,
                    'merchant_name' => $reservation->room->merchant->name,
                    'checkin_date' => $request->checkin_date,
                    'checkout_date' => $request->checkout_date,
                    'payment_link' => $payment_response['paymentUrl'] ?? '',
                    'expiration_date' => $payment_response['data']['expiresAt'] ?? null,
                ];

                $receiver = config('app.env') === "production" ? $reservation->reserved_user->email : config('mail.test_receiver');
                Mail::to($receiver)->send(new HotelReservationApproved($details));
            }

            $reservation->update(array_merge($data, [
                'number_of_pax' => $number_of_pax,
                'approved_date' => $request->status == 'approved' ? Carbon::now() : null,
            ]));

            LoggerService::log(LoggerActionEnum::UPDATE, HotelReservation::class, ['changes' => $reservation->getChanges()]);

            DB::commit();

            return $reservation;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generateDataTable(Request $request, $hotel_reservations) {
        return DataTables::of($hotel_reservations)
                ->addIndexColumn()
                ->editColumn('reserved_user_id', function ($row) {
                    if($row->reserved_user) {
                        return view('components.user-contact', ['user' => $row->reserved_user]);
                    }
    
                    return 'Deleted User';
                })
                ->editColumn('room_id', function ($row) {
                    if($row->room) {
                        return view('components.hotel-room', ['reservation' => $row]);
                    }
                })
                ->editColumn('number_of_pax', function ($row) {
                    return $row->number_of_pax . ' Pax';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'pending') {
                        return '<div class="badge bg-label-warning">Pending</div>';
                    }

                    if ($row->status == 'approved') {
                        return '<div class="badge bg-label-success">Approved</div>';
                    }

                    if ($row->status == 'declined') {
                        return '<div class="badge bg-label-danger">Declined</div>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $output = '<div class="dropdown">';
                        
                    $output .= '<a href="'. route('admin.hotel_reservations.edit', $row->id) .'" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a> ';

                    if($row->status != 'approved') {
                        $output .= '<button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>';
                    }
                    
                    $output .= '</div>';

                    return $output;
                })
                ->rawColumns(['actions', 'status', 'room_id'])
                ->make(true);
    }

    private function computeTotalAmount($amount, $additional_charges) {
        return $amount + $additional_charges;
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OHR' . rand(100000, 10000000);
    }
}