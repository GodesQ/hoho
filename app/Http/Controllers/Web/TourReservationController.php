<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TourReservation;
use App\Models\User;
use App\Models\Tour;

use DataTables;

class TourReservationController extends Controller
{
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
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/tour_reservations/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
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

    }

    public function edit(Request $request) {

    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
