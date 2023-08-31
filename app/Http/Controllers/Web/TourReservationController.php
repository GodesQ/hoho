<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use GuzzleHttp\Client;
use App\Services\BookingService;

use App\Models\TourReservation;
use App\Models\User;
use App\Models\Tour;
use App\Models\Transaction;

use DataTables;
use Carbon\Carbon;

class TourReservationController extends Controller
{
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function list(Request $request) {
        if($request->ajax()) {
            $data = TourReservation::latest()->with('user', 'tour');
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('reserved_user', function($row) {
                        return $row->user->email;
                    })
                    ->addColumn('type', function($row) {
                        return $row->tour->type;
                    })
                    ->addColumn('tour', function($row) {
                        return $row->tour->name;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/tour_reservations/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
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
        // dd($request->all());
        return $this->bookingService->createBooking($request);
    }

    public function edit(Request $request) {
        $reservation = TourReservation::where('id', $request->id)->with('user', 'tour', 'transaction')->firstOrFail();
        return view('admin-page.tour_reservations.edit-tour-reservation', compact('reservation'));
    }

    public function update(Request $request) {
        $reservation = TourReservation::where('id', $request->id)->first();

        $update_reservation = $reservation->update([
            'status' => $request->status
        ]);

        if($update_reservation) return back()->withSuccess('Reservation updated successfully');
    }

    public function destroy(Request $request) {

    }
}
