<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Interest;
use DataTables;

class InterestController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Interest::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/admin/merchants/stores/edit/' .$row->id. '">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
        }

        return view('admin-page.interests.list-interest');
    }

    public function create(Request $request) {
        return view('admin-page.interests.create-interest');
    }

    public function store(Request $request) {

    }

    public function edit(Request $request) {
        return view('admin-page.interests.edit-interest');
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
