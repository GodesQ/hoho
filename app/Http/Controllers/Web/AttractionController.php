<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attraction;
use DataTables;

class AttractionController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = Attraction::latest('id');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    return '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="/admin/attractions/edit/' .$row->id. '">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </a>
                                </div>
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

        return view('admin-page.attractions.list-attraction');
    }

    public function create(Request $request) {
        return view('admin-page.attractions.create-attraction');
    }

    public function store(Request $request) {

    }

    public function edit(Request $request) {
        $attraction = Attraction::findOrFail($request->id);
        return view('admin-page.attractions.edit-attraction', compact('attraction'));
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
