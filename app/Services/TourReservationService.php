<?php

namespace App\Services;

use App\Models\TourReservation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TourReservationService
{
    public function RetrieveAllTourReservationsList(Request $request)
    {   
        $current_user = Auth::guard('admin')->user();

        $data = TourReservation::with('user', 'tour')
            ->when(!in_array($current_user->role, ['super_admin', 'admin']), function($query) use ($current_user) {
                $query->where('created_by', $current_user->id);
            })
            ->when(!empty($request->get('search')), function ($query) use ($request) {
                $searchQuery = $request->get('search');
                $query->whereHas('user', function ($userQuery) use ($searchQuery) {
                    $userQuery->where('email', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('firstname', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'LIKE', '%' . $searchQuery . '%');
                });
            })
            ->when(!empty($request->get('status')), function ($query) use ($request) {
                $statusQuery = $request->get('status');
                $query->where('status', $statusQuery);
            })
            ->when(!empty($request->get('type')), function ($query) use ($request) {
                $typeQuery = $request->get('type');
                $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                    $tourQuery->where('type', $typeQuery);
                });
            })
            ->when(!empty($request->get('trip_date')), function ($query) use ($request) {
                $tripDateQuery = $request->get('trip_date');
                $query->where('start_date', $tripDateQuery);
            });

            return $this->_generateDataTable($data, $request);
    }

    public function RetrieveTourProviderReservationsList(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = TourReservation::with('user', 'tour', 'transaction')
            ->whereHas('tour', function ($query) use ($admin) {
                return $query->where('tour_provider_id', $admin->merchant_data_id);
            })
            ->when(!empty($request->get('search')), function ($query) use ($request) {
                $searchQuery = $request->get('search');
                $query->whereHas('user', function ($userQuery) use ($searchQuery) {
                    $userQuery->where('email', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('firstname', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhere(DB::raw("concat(firstname, ' ', lastname)"), 'LIKE', '%' . $searchQuery . '%');
                });
            })
            ->when(!empty($request->get('status')), function ($query) use ($request) {
                $statusQuery = $request->get('status');
                $query->where('status', $statusQuery);
            })
            ->when(!empty($request->get('type')), function ($query) use ($request) {
                $typeQuery = $request->get('type');
                $query->whereHas('tour', function ($tourQuery) use ($typeQuery) {
                    $tourQuery->where('type', $typeQuery);
                });
            })
            ->when(!empty($request->get('trip_date')), function ($query) use ($request) {
                $tripDateQuery = $request->get('trip_date');
                $query->where('start_date', $tripDateQuery);
            });

            return $this->_generateDataTable($data, $request);
    }

    #HELPER FUNCTIONS
 
    private function _generateDataTable($data, $request)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('reserved_user', function ($row) {
                return optional($row->user)->email ? optional($row->user)->email : 'Deleted User';
            })
            ->addColumn('type', function ($row) {
                return optional($row->tour)->type ?? 'Deleted Tour';
            })
            ->addColumn('tour', function ($row) {
                return optional($row->tour)->name ?? "Deleted Tour";
            })
            ->addColumn('actions', function ($row) {
                $output = '<div class="dropdown">
                    <a href="/admin/tour_reservations/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>';
            
                $output .= $row->status === 'pending' && optional($row->transaction)->payment_status != 'success' ? '<button type="button" id="'.$row->id.'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>' : '';
            
                $output .= '</div>';
                return $output;
            })            
            ->rawColumns(['actions'])
            ->make(true);
    }




}