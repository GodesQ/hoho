<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelReservation\StoreRequest;
use App\Models\HotelReservation;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HotelReservationController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $hotel_reservations = HotelReservation::with('reserved_user', 'room')->get();
            return DataTables::of($hotel_reservations)
                ->addIndexColumn()
                ->addColumn('reserved_user_id', function ($row) {
                    return $row->reserved_user->email;
                })
                ->editColumn('room_id', function ($row) {
                    return $row->room->room_name . ' ( ' . $row->room->merchant->name . ' ) ';
                })
                ->editColumn('number_of_pax', function ($row) {
                    return $row->number_of_pax . ' Pax';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'pending') {
                        return '<div class="badge bg-warning">Pending</div>';
                    }

                    if ($row->status == 'approved') {
                        return '<div class="badge bg-success">Approved</div>';
                    }

                    if ($row->status == 'declined') {
                        return '<div class="badge bg-success">Declined</div>';
                    }

                })
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                    <a href="/admin/hotel-reservations/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                </div>';
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view("admin-page.hotel_reservations.list-hotel-reservation");
    }

    public function create(Request $request)
    {
        $merchant_hotels = Merchant::where('type', 'Hotel')->get();
        return view('admin-page.hotel_reservations.create-hotel-reservation', compact('merchant_hotels'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $reservation = HotelReservation::create(array_merge($data, [
            'approved_date' => $request->status == 'approved' ? date('Y-m-d') : null,
        ]));

        return redirect()->route('admin.hotel_reservations.edit', $reservation->id)->with('success', 'Hotel reservation added successfully.');
    }

    public function edit(Request $request, $id)
    {   
        $merchant_hotels = Merchant::where('type', 'Hotel')->get();
        $reservation = HotelReservation::where('id', $id)->with('reserved_user', 'room')->firstOrFail();

        return view('admin-page.hotel_reservations.edit-hotel-reservation', compact('reservation', 'merchant_hotels'));
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy(Request $request, $id)
    {

    }
}
