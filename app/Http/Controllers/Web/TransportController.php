<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transport;
use App\Models\Tour;
use App\Models\Admin;

use App\Events\BusLocationEvent;
use Yajra\DataTables\DataTables;


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
                                    <a href="/admin/transports/edit/' .$row->id. '" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button type="button" id="'.$row->id.'" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
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
        $tours = Tour::get();
        return view('admin-page.transports.edit-transport', compact('transport', 'operators', 'tours'));
    }

    public function updateLocation(Request $request) {
        $transport = Transport::where('id', $request->id)->first();

        $update = $transport->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        $user_id = null;

        $coordinates = [
            'latitude' => $transport->latitude,
            'longitude' => $transport->longitude
        ];

        event(new BusLocationEvent($user_id, $coordinates, $transport->id));

        // dd($event);
        return response([
            'status' => true,
            'message' => 'Updated Successfully'
        ]);
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {
        $transports = Transport::findOrFail($request->id);

        $upload_image = public_path('assets/img/transports/') . $transports->id . '/' . $transports->featured_image;

        if($upload_image) {
             @unlink($upload_image);
        }

        $remove = $transports->delete();

        if($remove) {
            return response([
                'status' => true,
                'message' => 'Tour Deleted Successfully'
            ]);
        }
    }
}
