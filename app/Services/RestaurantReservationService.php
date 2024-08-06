<?php

namespace App\Services;
use App\Models\RestaurantReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RestaurantReservationService {
    public function __construct() {
    
    }

    public function create(Request $request, $data) {
        $reservation = RestaurantReservation::create(array_merge($data, [
            'approved_date' => $request->status == 'approved' ? Carbon::now() : null,
        ]));

        return $reservation;
    }

    public function update(Request $request, $id, $data) {
        $reservation = RestaurantReservation::findOrFail($id);
        
        $reservation->update(array_merge($data, [
            'approved_date' => $request->status === 'approved' ? Carbon::now() : null,
        ]));

        return $reservation;
    }

    public function generateDataTable(Request $request, $restaurant_reservations) {
        return DataTables::of($restaurant_reservations)
            ->addIndexColumn()
            ->addColumn('reserved_user_id', function ($row) {
                return $row->reserved_user->email ?? 'No user found';
            })
            ->addColumn('merchant_id', function ($row) {
                return $row->merchant->name ?? null;
            })
            ->addColumn('status', function ($row) {
                if ($row->status === 'pending') {
                    return '<div class="badge bg-label-warning">Pending</div>';
                }

                if ($row->status === 'approved') {
                    return '<div class="badge bg-label-success">Approved</div>';
                }

                if ($row->status === 'declined') {
                    return '<div class="badge bg-label-danger">Declined</div>';
                }
            })
            ->addColumn('actions', function ($row) {
                $output = '<div class="dropdown">';
                    
                $output .= '<a href="'. route('admin.restaurant_reservations.edit', $row->id) .'" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>';

                if($row->status != 'approved') {
                    $output .= '<button type="button" id="' . $row->id . '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>';
                }
                
                $output .= '</div>';

                return $output;
            })
            ->rawColumns(['status','actions'])
            ->make(true);
    }
}