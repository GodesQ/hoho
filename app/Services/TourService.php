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
                ->addColumn('tour_image', function ($row) {
                    if($row->featured_image) {
                            $path = '../../../assets/img/tours/' . $row->id . '/' . $row->featured_image;
                            return '<img src="' .$path. '" width="50" height="50" style="object-fit: cover; border-radius: 50px;" />';
                    } else {
                        $path = '../../../assets/img/' . 'default-image.jpg';
                        return '<img src="' .$path. '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
                    }
                })
                ->editColumn('name', function ($row) {
                    return view('components.tour', ['tour' => $row]);
                })
                ->editColumn('price', function ($row) {
                    return '₱ ' . number_format($row->price, 2);
                })
                ->addColumn('actions', function ($row) {
                    $output = '<div class="dropdown">';

                    $output .= "<a href='/admin/tours/edit/{$row->id}' class='btn btn-outline-primary btn-sm'><i class='bx bx-edit-alt me-1'></i></a>";

                    // For Main DIY Tour
                    if($row->id != 63) {
                        $output .= '<a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>';
                    }

                    $output .= '</div>';

                    return $output;
                })
                ->addColumn('status', function($row) {
                    
                    if($row->status) {
                        return '<div class="badge bg-label-success">Active</div>';
                    } else {
                        return '<div class="badge bg-label-warning">InActive</div>';
                    }
                })
                ->filter(function ($query) use ($request) {
                    $search = $request->search;
                    $type = $request->type;
                    $status = $request->status;

                    if($search) {
                        $query->where('name','LIKE', "%{$search}%");
                    }

                    if($type) {
                        $query->where("type", $type);
                    }

                    if($status) {
                        $query->where("status", $status);
                    }

                })
                ->rawColumns(['actions', 'status', 'tour_image'])
                ->make(true);
    }

    public function RetrieveTourProviderToursList(Request $request) {
        $admin = Auth::guard('admin')->user();

        $data = Tour::where('tour_provider_id', $admin->merchant->tour_provider_info->id)->latest('id')->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('name', function ($row) {
            return view('components.tour', ['tour' => $row]);
        })
        ->addColumn('tour_image', function ($row) {
            if($row->featured_image) {
                    $path = '../../../assets/img/tours/' . $row->id . '/' . $row->featured_image;
                    return '<img src="' .$path. '" width="50" height="50" style="object-fit: cover; border-radius: 50px;" />';
            } else {
                $path = '../../../assets/img/' . 'default-image.jpg';
                return '<img src="' .$path. '" width="50" height="50" style="border-radius: 50%; object-fit: cover;" />';
            }
        })
        ->editColumn('price', function ($row) {
            return '₱ ' . number_format($row->price, 2);
        })
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
        ->rawColumns(['actions', 'status', 'tour_image'])
        ->make(true);
    }
}
