<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tour;

use DataTables;
class TourController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = Tour::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/users/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.tours.list-tour');
    }

    public function create(Request $request) {
        return view('admin-page.tours.create-tour');
    }

    public function store(Request $request) {
        dd($request->all());
    }

    public function edit(Request $request) {
        return view('admin-page.tours.edit-tour');
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
