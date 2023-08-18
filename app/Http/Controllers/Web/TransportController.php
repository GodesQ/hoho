<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transport;

use DataTables;

class TransportController extends Controller
{
    public function list(Request $request) {

        if($request->ajax()) {
            $data = Transport::latest();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('transport_provider', function() {
                        return null;
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/admin/transports/edit/' .$row->id. '">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['actions', 'transport_provider'])
                    ->make(true);
        }

        return view('admin-page.transports.list-transport');
    }

    public function create(Request $request) {
        return view('admin-page.transports.create-transport');
    }

    public function store(Request $request) {

    }

    public function edit(Request $request) {
        return view('admin-page.transports.edit-transport');
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
