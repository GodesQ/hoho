<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Tour;
use App\Models\Attraction;
use App\Models\MerchantTourProvider;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class TourService
{
    public function RetrieveAllToursList(Request $request) {
        $data = Tour::latest('id');
        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <a href="/admin/tours/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                            </div>';
                })
                ->addColumn('status', function($row) {
                    if($row->status) {
                        return '<div class="badge bg-label-success">Active</div>';
                    } else {
                        return '<div class="badge bg-label-warning">InActive</div>';

                    }
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
    }

    public function RetrieveTourProviderToursList(Request $request) {
        $admin = Auth::guard('admin')->user();

        $data = Tour::where('tour_provider_id', $admin->merchant_data_id)->latest('id')->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('actions', function ($row) {
            return '<div class="dropdown">
                        <a href="/admin/tours/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                        <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                    </div>';
        })
        ->addColumn('status', function($row) {
            if($row->status) {
                return '<div class="badge bg-label-success">Active</div>';
            } else {
                return '<div class="badge bg-label-warning">InActive</div>';
            }
        })
        ->rawColumns(['actions', 'status'])
        ->make(true);
    }
}
