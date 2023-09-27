<?php

namespace App\Services;

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

use Yajra\DataTables\DataTables;
use DB;
use Carbon\Carbon;

class TourReservationService
{
    public function getAllTourReservationsList($request)
    {
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
    }

    public function getTourProviderReservationsList($request)
    {
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
    }

    private function generateDataTable($data, $request) {
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

    

            
}