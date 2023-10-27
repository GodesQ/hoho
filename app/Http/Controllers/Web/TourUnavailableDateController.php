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
                    ->rawColumns(['actions','unavailable_date'])
                    ->make(true);
        }

        return view('admin-page.unavailable_dates.list-unavailable-date');
    }

    public function create(Request $request) {
        return view('admin-page.unavailable_dates.create-unavailable-date');
    }

    public function store(Request $request) {
        $tourUnavailableDate = TourUnavailableDate::create($request->all());

        if($tourUnavailableDate) {
            return redirect()->route('admin.unavailable_dates.edit', $tourUnavailableDate->id)->withSuccess('Unavailable Date Added Successfully');
        }
    }

    public function edit(Request $request) {
        $tourUnavailableDate = TourUnavailableDate::findOrFail($request->id);
        return view('admin-page.unavailable_dates.edit-unavailable-date', compact('tourUnavailableDate'));
    }

    public function update(Request $request) {
        $tourUnavailableDate = TourUnavailableDate::findOrFail($request->id);
        $tourUnavailableDate->update($request->all());

        if($tourUnavailableDate) {
            return back()->withSuccess('Unavailable Date Update Successfully');    
        }
    }
}
