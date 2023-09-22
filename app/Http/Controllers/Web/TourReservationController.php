<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use GuzzleHttp\Client;
use App\Services\BookingService;

use App\Enum\ReservationStatusEnum;

use App\Models\TourReservation;
use App\Models\User;
use App\Models\Tour;
use App\Models\Transaction;
use App\Models\ReservationUserCode;

use App\Mail\BookingConfirmationMail;

use DataTables;
use DB;
use Carbon\Carbon;

class TourReservationController extends Controller
{
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function list(Request $request) {
        if($request->ajax()) {
            $data = TourReservation::with('user', 'tour')->latest()
                                    ->when(!empty($request->get('search')), function ($query) use ($request) {
                                        $searchQuery = $request->get('search');
                                        $query->whereHas('user', function ($userQuery) use ($searchQuery) {
                                                $userQuery->where('email', 'LIKE', '%' . $searchQuery . '%')
                                                ->orWhere('firstname', 'LIKE', '%' . $searchQuery . '%')
                                                ->orWhere('lastname', 'LIKE', '%' . $searchQuery . '%')
                                                ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'LIKE', '%' . $searchQuery . '%');
                                        });
                                    })
                                    ->when(!empty($request->get('status')), function($query) use ($request) {
                                        $statusQuery = $request->get('status');
                                        $query->where('status', $statusQuery);
                                    })
                                    ->when(!empty($request->get('type')), function($query) use ($request) {
                                        $typeQuery = $request->get('type');
                                        $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                                            $tourQuery->where('type', $typeQuery);
                                        });
                                    })
                                    ->when(!empty($request->get('trip_date')), function($query) use ($request) {
                                        $tripDateQuery = $request->get('trip_date');
                                        $query->where('start_date', $tripDateQuery);
                                    });

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('reserved_user', function($row) {
                        return optional($row->user)->email ? optional($row->user)->email : 'Deleted User';
                    })
                    ->addColumn('type', function($row) {
                        return optional($row->tour)->type;
                    })
                    ->addColumn('tour', function($row) {
                        return optional($row->tour)->name;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/tour_reservations/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button type="button" disabled class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.tour_reservations.list-tour-reservation');
    }

    public function create(Request $request) {
        $diy_tours = Tour::where('type', 'DIY Tour')->get();
        $guided_tours = Tour::where('type', 'Guided Tour')->limit(50)->get();
        $tours = Tour::get();
        return view('admin-page.tour_reservations.create-tour-reservation', compact('diy_tours', 'guided_tours', 'tours'));
    }

    public function store(Request $request) {
        return $this->bookingService->createBooking($request);
    }

    public function edit(Request $request) {
        $reservation = TourReservation::where('id', $request->id)->with('user', 'tour', 'transaction')->firstOrFail();
        return view('admin-page.tour_reservations.edit-tour-reservation', compact('reservation'));
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

                $trip_date = new \DateTime($reservation->trip_date);
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
