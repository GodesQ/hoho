<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantReservation\StoreRequest;
use App\Http\Requests\RestaurantReservation\UpdateRequest;
use App\Models\Merchant;
use App\Models\RestaurantReservation;
use Carbon\Carbon;
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
                        return $row->reserved_user->email ?? null;
                    })
                    ->addColumn('merchant_id', function ($row) {
                        return $row->merchant->name ?? null;
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
                        $output = '<div class="dropdown">';
                            
                        $output .= '<a href="/admin/restaurant-reservations/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>';
    
                        if($row->status != 'approved') {
                            $output .= '<button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>';
                        }
                        
                        $output .= '</div>';
    
                        return $output;
                    })
                    ->rawColumns(['status','actions'])
                    ->make(true);
        }

        return view('admin-page.restaurant_reservations.list-restaurant-reservation');
    }

    public function create(Request $request) {
        $merchants = Merchant::where('type', 'Restaurant')->get();
        // dd($merchants);
        return view('admin-page.restaurant_reservations.create-restaurant-reservation', compact('merchants'));
    }

    public function store(StoreRequest $request) {
        $data = $request->validated();

        $reservation = RestaurantReservation::create(array_merge($data, [
            'approved_date' => $request->status == 'approved' ? Carbon::now() : null,
        ]));

        return redirect()->route('admin.restaurant_reservations.edit', $reservation->id)->with('success','Restaurant reservation added successfully.');
    }

    public function edit(Request $request, $id) {
        $reservation = RestaurantReservation::findOrFail($id);
        $merchants = Merchant::where('type', 'Restaurant')->get();
        return view('admin-page.restaurant_reservations.edit-restaurant-reservation', compact('reservation', 'merchants'));
    }

    public function update(UpdateRequest $request, $id) {
        $reservation = RestaurantReservation::findOrFail($id);
        $data = $request->validated();

        $reservation->update(array_merge($data, [
            'approved_date' => $request->status == 'approved' ? Carbon::now() : null,
        ]));

        return back()->with('success','Restaurant reservation updated successfully.');
    }

    public function destroy(Request $request, $id) {
        $reservation = RestaurantReservation::findOrFail($id);

        $reservation->delete();

        return response([
            'status' => TRUE,
            'message' => 'Restaurant Reservation deleted successfully'
        ]);
    }
}
