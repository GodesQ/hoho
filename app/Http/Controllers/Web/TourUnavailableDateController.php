<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TourUnavailableDate;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TourUnavailableDateController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = TourUnavailableDate::get();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("unavailable_date", function ($row) {
                        return date_format(new \DateTime($row->unavailable_date), 'F d, Y');
                    })
                    ->addColumn("actions", function ($row) {
                        return '<div class="dropdown">
                                    <a href="/admin/unavailable_dates/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="javascript:void(0);" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></a>
                                </div>';
                    })
                    ->make(true);
        }

        return view('admin-page.unavailable_dates.list-unavailable-date');
    }
}
