<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelReservation\StoreRequest;
use App\Http\Requests\HotelReservation\UpdateRequest;
use App\Mail\HotelReservationConfirmation;
use App\Models\Admin;
use App\Models\HotelReservation;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AqwireService;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class HotelReservationController extends Controller
{   
    private $aqwireService;
    public function __construct(AqwireService $aqwireService) {
        $this->aqwireService = $aqwireService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::guard('admin')->user();
            $hotel_reservations = HotelReservation::when(in_array($user->role, ['merchant_hotel_admin', 'merchant_hotel_employee']), function ($query) use($user) {
                $query->whereHas('room', function ($q) use ($user) {
                    $q->where('merchant_id', $user->merchant_id);
                });
            })
            ->with('reserved_user', 'room');


            return DataTables::of($hotel_reservations)
                ->addIndexColumn()
                ->addColumn('reserved_user_id', function ($row) {
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
                ->addColumn('status', function ($row) {
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

        return view("admin-page.hotel_reservations.list-hotel-reservation");
    }

    public function create(Request $request)
    {   
        $user = Auth::guard('admin')->user();   
        $merchant_hotels = Merchant::where('type', 'Hotel')
                            ->when(in_array($user->role, ['merchant_hotel_admin', 'merchant_hotel_employee']), function ($query) use($user) {
                                return $query->where('id', $user->merchant_id);
                            })
                            ->get();
                            
        return view('admin-page.hotel_reservations.create-hotel-reservation', compact('merchant_hotels'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $reservation = HotelReservation::create(array_merge($data, [
            'approved_date' => $request->status == 'approved' ? date('Y-m-d') : null,
        ]));

        if($reservation) {
            $details = [
                'hotel_name' => $reservation->room->merchant->name,
                'room_name' => $reservation->room->room_name,
                'reserved_customer' => ($reservation->reserved_user->lastname) . ', ' . ($reservation->reserved_user->firstname),
                'reservation_date' => $reservation->reservation_date,
                'reservation_time' => $reservation->reservation_time,
                'reservation_link' => route('admin.login') . '?redirectTo=' . route('admin.hotel_reservations.edit', $reservation->id),
            ];

            $hotel_admin = Admin::where('merchant_id', $reservation->room->merchant->id)->first();
            Mail::to('jamesgarnfil15@gmail.com')->send(new HotelReservationConfirmation($details));

        }

        return redirect()->route('admin.hotel_reservations.edit', $reservation->id)->with('success', 'Hotel reservation added successfully.');
    }

    public function edit(Request $request, $id)
    {   
        $user = Auth::guard('admin')->user();   
        $merchant_hotels = Merchant::where('type', 'Hotel')
                            ->when(in_array($user->role, ['merchant_hotel_admin', 'merchant_hotel_employee']), function ($query) use($user) {
                                return $query->where('id', $user->merchant_id);
                            })
                            ->get();
                            
        $reservation = HotelReservation::where('id', $id)->with('reserved_user', 'room')->firstOrFail();

        return view('admin-page.hotel_reservations.edit-hotel-reservation', compact('reservation', 'merchant_hotels'));
    }

    public function update(UpdateRequest $request, $id)
    {   
        try {
            $data = $request->validated();

            $reservation = HotelReservation::where('id', $id)->firstOrFail();

            $reservation->update(array_merge($data, [
                'approved_date' => $request->status == 'approved' ? Carbon::now() : null,
            ]));

            if($request->status == 'approved' && $reservation->payment_status == 'unpaid') {
                $reference_no = $this->generateReferenceNo();
                
                $transaction = Transaction::create([
                    'reference_no' => $reference_no,
                    'transaction_by_id' => $request->customer_id,
                    'sub_amount' => $reservation->room->price,
                    'total_additional_charges' => 0,
                    'total_discount' => 0,
                    'transaction_type' => 'order',
                    'payment_amount' => $reservation->room->price,
                    'additional_charges' => null,
                    'payment_status' => 'pending',
                    'resolution_status' => 'pending',
                    'aqwire_paymentMethodCode' => null,
                    'order_date' => $request->order_date,
                    'transaction_date' => Carbon::now(),
                ]);

                $reservation->update([
                    'reference_number' => $transaction->reference_no,
                ]);

                $payment_request_model = $this->aqwireService->createRequestModel($transaction, $reservation->reserved_user);
                $payment_response = $this->aqwireService->pay($payment_request_model);

                $transaction->update([
                    'aqwire_transactionId' => $payment_response['data']['transactionId'] ?? null,
                    'payment_url' => $payment_response['paymentUrl'] ?? null,
                    'payment_status' => Str::lower($payment_response['data']['status'] ?? ''),
                    'payment_details' => json_encode($payment_response),
                ]);

                return redirect($payment_response['paymentUrl'] ?? '');
            }

            return back()->withSuccess('Hotel reservation updated successfully');
        } catch (ErrorException $e) {
            return back()->with('fail', $e->getMessage());
        }

    }

    public function destroy(Request $request, $id)
    {
        $reservation = HotelReservation::findOrFail($id);

        $reservation->delete();

        return response([
            'status' => TRUE,
            'message' => 'Hotel Reservation deleted successfully'
        ]);
    }

    private function generateReferenceNo()
    {
        return date('Ym') . '-' . 'OHR' . rand(100000, 10000000);
    }
}