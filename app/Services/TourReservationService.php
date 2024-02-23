<?php

namespace App\Services;

use App\Models\TourReservation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TourReservationService
{   
    public function storeRegisteredUserReservation(Request $request) {
        
    }

    public function storeAnonymousUserReservation(Request $request) {

    }

    public function RetrieveAllTourReservationsList($request)
    {   
        $current_user = Auth::guard('admin')->user();

        $data = TourReservation::with('user', 'tour')
            ->whereHas('user')
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
                })->orWhereHas('tour', function ($tourQuery) use ($searchQuery) {
                    $tourQuery->where('name', 'LIKE', $searchQuery . '%');
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
                return $query->where('tour_provider_id', $admin->merchant->tour_provider_info->id);
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
                if($row->user) {
                    return view('components.user-contact', ['user' => $row->user]);
                }

                return '-';
            })
            ->editColumn('tour', function ($row) {
                if($row->tour) {
                    return view('components.tour', ['tour' => $row->tour]);
                }
            })
            ->addColumn('status', function ($row) {
                if($row->status == 'approved') {
                    return '<div class="badge bg-label-success">Approved</div>';
                } else if($row->status == 'pending') {
                    return '<div class="badge bg-label-warning">Pending</div>';
                } else if($row->status == 'cancelled') {
                    return '<div class="badge bg-label-warning">Cancelled</div>';
                }
            })
            ->addColumn('actions', function ($row) {
                $output = '<div class="dropdown">
                    <a href="/admin/tour_reservations/edit/' . $row->id . '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>';
            
                $output .= $row->status === 'pending' && optional($row->transaction)->payment_status != 'success' ? '<button type="button" id="'.$row->id.'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>' : '';
            
                $output .= '</div>';
                return $output;
            })            
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }


}