<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TourReservationCustomerDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Services\BookingService;
use App\Services\TourReservationService;

use App\Models\User;
use App\Models\TourReservation;
use App\Models\Tour;
use App\Models\ReservationUserCode;
use App\Models\TicketPass;

use DB;

class TourReservationController extends Controller
{   
    protected $tourReservationService;
    protected $bookingService;
    
    public function __construct(BookingService $bookingService, TourReservationService $tourReservationService)
    {
        $this->bookingService = $bookingService;
        $this->tourReservationService = $tourReservationService;
    }

    public function list(Request $request) {
        if($request->ajax()) {
            $admin = Auth::guard('admin')->user();
            
            if(in_array($admin->role, ['tour_operator_admin', 'tour_operator_employee'])) {
                return $this->tourReservationService->retrieveTourProviderReservationsList($request);
            } 
            
            return $this->tourReservationService->retrieveAllTourReservationsList($request);
        }

        return view('admin-page.tour_reservations.list-tour-reservation');
    }

    public function create(Request $request) {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        $guided_tours = Tour::where('type', 'Guided Tour')->get();
        $tours = Tour::get();
        $ticket_passes = TicketPass::get();
        
        return view('admin-page.tour_reservations.test-create-tour-reservation', compact('diy_tours', 'guided_tours', 'tours', 'ticket_passes'));
    }

    public function store(Request $request) {
        $user = User::where('id', $request->reserved_user_id)->first();

        if(!$user->firstname || !$user->lastname) {
            return back()->with('fail', 'Please complete your name before continue to checkout');
        }

        if(!$user->contact_no) {
            return back()->with('fail', 'Please provide a contact number to continue');
        }

        return $this->bookingService->createBookReservation($request);
    }

    public function edit(Request $request) {
        $reservation = TourReservation::where('id', $request->id)->with('user', 'tour', 'transaction', 'reservation_codes', 'customer_details')->firstOrFail();
        $ticket_passes = TicketPass::get();
        return view('admin-page.tour_reservations.edit-tour-reservation', compact('reservation', 'ticket_passes'));
    }

    public function update(Request $request) {
        try {
            DB::beginTransaction();

            $reservation = TourReservation::where('id', $request->id)->with('user', 'customer_details', 'transaction')->first();

            $trip_date = Carbon::parse($request->trip_date); 

            $reservation->update([
                'start_date' => $trip_date->format('Y-m-d'),
                'end_date' => $request->type == 'Guided' ? $trip_date->addDays(1) : $this->bookingService->getDateOfDIYPass($request->ticket_pass, $trip_date),
                'status' => $request->status
            ]);

            // If the status is approved, process the payment of tour reservation and send the payment request to user. 
            if($request->status === 'approved') {
                $this->tourReservationService->handlePaymentForApprovedReservation($reservation);
            }

            DB::commit();

            return back()->withSuccess('Reservation updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('fail', $e->getMessage());
        }
    }

    public function destroy(Request $request) {
        $tour_reservation = TourReservation::with('transaction')->find($request->id);
    
        if(!$tour_reservation) {
            return response([
                'status' => false,
                'message' => 'Reservation Not Found'
            ]);
        }
    
        $reservationTransactionId = $tour_reservation->order_transaction_id;
        $reservations_by_transaction = TourReservation::where('order_transaction_id', $reservationTransactionId)->get();
    
        if($reservations_by_transaction->count() <= 1 && $tour_reservation->transaction) {
            $tour_reservation->transaction->delete();
        }
    
        $tour_reservation->delete();
    
        return response([
            'status'=> true,
            'message' => 'Reservation Deleted Successfully'
        ]);
    }

    public function get_tour_reservation_codes(Request $request) {
        $tour_reservation_codes = ReservationUserCode::where('reservation_id', $request->reservation_id)->get();

        return response([
            'status' => TRUE,
            'reservation_codes' => $tour_reservation_codes
        ]);
    }

    public function syncCustomerDetails() {
        $tour_reservations = TourReservation::with('user')->whereHas('user')->get();

        foreach ($tour_reservations as $key => $tour_reservation) {
            TourReservationCustomerDetail::updateOrCreate([
                'tour_reservation_id' => $tour_reservation->id,
                'firstname' => $tour_reservation->user->lastname,
                'lastname' => $tour_reservation->user->lastname,
                'email' => $tour_reservation->user->email,
                'contact_no' => optional($tour_reservation->user)->countryCode . optional($tour_reservation->user)->contact_no,
            ]);
        }

        echo "Success";
    }
}
