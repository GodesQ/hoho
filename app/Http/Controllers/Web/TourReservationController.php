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

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $admin = Auth::guard('admin')->user();

            if (in_array($admin->role, ['tour_operator_admin', 'tour_operator_employee'])) {
                return $this->tourReservationService->retrieveTourProviderReservationsList($request);
            }

            return $this->tourReservationService->retrieveAllTourReservationsList($request);
        }

        return view('admin-page.tour_reservations.list-tour-reservation');
    }

    public function create(Request $request)
    {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        $guided_tours = Tour::where('type', 'Guided Tour')->get();
        $tours = Tour::get();
        $ticket_passes = TicketPass::get();

        return view('admin-page.tour_reservations.create-tour-reservation', compact('diy_tours', 'guided_tours', 'tours', 'ticket_passes'));
    }

    public function store(Request $request)
    {
        try {
            $result = $this->bookingService->processBookingReservation($request);

            if (! isset($result['status']))
                throw new Exception("An error occurred while processing the request. The result status could not be found.", 400);

            if ($result['status'] === "success") {
                return redirect()->route('admin.tour_reservations.edit', $result['reservation']->id)->withSuccess('Book Tour Successfully');
            }

            if ($result['status'] === "paying") {
                return redirect($result['payment_response']['paymentUrl']);
            }

        } catch (Exception $e) {
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $reservation = TourReservation::where('id', $request->id)->with('user', 'tour', 'transaction', 'reservation_codes', 'customer_details')->firstOrFail();
        $ticket_passes = TicketPass::get();

        return view('admin-page.tour_reservations.edit-tour-reservation', compact('reservation', 'ticket_passes'));
    }

    public function update(Request $request)
    {
        try {
            $this->tourReservationService->update($request);
            return back()->withSuccess('Reservation updated successfully.');
        } catch (Exception $exception) {
            return back()->with('fail', $exception->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $tour_reservation = TourReservation::with('transaction')->find($request->id);

            if (! $tour_reservation)
                throw new Exception('Tour Reservation Not Found.', 404);

            $reservation_transaction_id = $tour_reservation->order_transaction_id;
            $reservations_by_transaction_count = TourReservation::where('order_transaction_id', $reservation_transaction_id)->count();

            // if($reservations_by_transaction_count <= 1 && $tour_reservation->transaction) {
            //     $tour_reservation->transaction->delete();
            // }

            $tour_reservation->delete();

            DB::commit();

            return response([
                'status' => true,
                'message' => 'Reservation Deleted Successfully'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], $exception->getCode() ?? 400);
        }
    }

    public function get_tour_reservation_codes(Request $request)
    {
        $tour_reservation_codes = ReservationUserCode::where('reservation_id', $request->reservation_id)->get();

        return response([
            'status' => TRUE,
            'reservation_codes' => $tour_reservation_codes
        ]);
    }

    public function syncCustomerDetails()
    {
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
