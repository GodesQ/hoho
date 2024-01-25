<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RestaurantReservation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RestaurantReservationController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()) {
            $data = RestaurantReservation::query();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('reserved_user_id', function ($row) {
                        return $row->reserved_user->email;
                    })
                    ->addColumn('merchant_id', function ($row) {
                        return $row->merchant->name;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                        <a href="/admin/restaurant-reservations/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                        <button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
                                    </div>';
                    })
                    ->make(true);
        }

        return view('admin-page.restaurant_reservations.list-restaurant-reservation');
    }

    public function create(Request $request) {

    }

    public function store(Request $request) {
    
    }

    public function edit(Request $request, $id) {
    
    }

    public function update(Request $request, $id) {
    
    }

    public function destroy(Request $request, $id) {
    
    }
}
