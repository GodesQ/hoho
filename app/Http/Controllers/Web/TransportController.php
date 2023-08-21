<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transport;
use App\Models\Tour;
use App\Models\Admin;

use App\Events\BusLocationEvent;
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
        $transports = Transport::all();
        return view('admin-page.transports.list-transport', compact('transports'));
    }

    public function create(Request $request) {
        $operators = Admin::where('role', 'bus_operator')->get();
        $tours = Tour::get();
        return view('admin-page.transports.create-transport', compact('operators', 'tours'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'tour_assignment_ids');
        $transport = Transport::create(array_merge($data, [
            'tour_assignment_ids' => json_encode($request->tour_assignment_ids)
        ]));

        if($transport) return redirect()->route('admin.transports.edit', $transport->id)->withSuccess('Transport created successfully');
    }

    public function edit(Request $request) {
        $operators = Admin::where('role', 'bus_operator')->get();
        $transport = Transport::where('id', $request->id)->firstOrFail();
        dd($transport->tour_assignment_ids);
        $tours = Tour::get();
        return view('admin-page.transports.edit-transport', compact('transport', 'operators', 'tours'));
    }

    public function updateLocation(Request $request) {
        $transport = Transport::where('id', $request->id)->first();

        $update = $transport->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        $user_id = auth('admin')->user()->id;

        $coordinates = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        $event = event(new BusLocationEvent($user_id, $coordinates));
        // dd($event);
        return response([
            'status' => true,
            'message' => 'Updated Successfully'
        ]);
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {

    }
}
