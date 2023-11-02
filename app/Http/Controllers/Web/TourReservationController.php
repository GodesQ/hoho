<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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

use App\Mail\BookingConfirmationMail;

use Yajra\DataTables\DataTables;
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
                return $this->tourReservationService->RetrieveTourProviderReservationsList($request);
            } 
            
            return $this->tourReservationService->RetrieveAllTourReservationsList($request);
        }

        return view('admin-page.tour_reservations.list-tour-reservation');
    }

    public function create(Request $request) {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        $guided_tours = Tour::where('type', 'Guided Tour')->get();
        $tours = Tour::get();
        $ticket_passes = TicketPass::get();
        
        return view('admin-page.tour_reservations.create-tour-reservation', compact('diy_tours', 'guided_tours', 'tours', 'ticket_passes'));
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
        $reservation = TourReservation::where('id', $request->id)->with('user', 'tour', 'transaction')->firstOrFail();
        $ticket_passes = TicketPass::get();
        return view('admin-page.tour_reservations.edit-tour-reservation', compact('reservation', 'ticket_passes'));
    }

    public function update(Request $request) {
        $reservation = TourReservation::where('id', $request->id)->with('user')->first();

        $update_reservation = $reservation->update([
            'status' => $request->status
        ]);

        if($request->status == 'approved') {
            $number_of_pass = $reservation->number_of_pass;
            $this->generateReservationCode($number_of_pass, $reservation);

            if($reservation->user) {
                $what = $reservation->type == 'DIY' ? (
                                $reservation->ticket_pass . " x " . $reservation->number_of_pass . " pax " . "(Valid for 24 hours from first tap)"
                            )
                            : (
                                "1 Guided Tour " . '"' . $reservation->tour->name . '"' . ' x ' . $reservation->number_of_pass . ' pax'
                            );

                $trip_date = new \DateTime($reservation->start_date);
                $when = $trip_date->format('l, F j, Y');

                $details = [
                    'name' => $reservation->user->firstname . ' ' . $reservation->user->lastname,
                    'what' => $what,
                    'when' => $when,
                    'where' => 'Robinson’s Manila',
                    'type' => $reservation->type,
                    'tour_name' => optional($reservation->tour)->name
                ];

                Mail::to(optional($reservation->user)->email)->send(new BookingConfirmationMail($details));
            }

        }

        if($update_reservation) return back()->withSuccess('Reservation updated successfully');
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

    private function generateReservationCode($number_of_pass, $reservation) {
        // Generate the random letter part
        // Assuming you have str_random function available
        $random_letters = strtoupper(Str::random(5));

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
            }
        }
    }
}
