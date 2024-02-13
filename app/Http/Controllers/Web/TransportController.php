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
                                    <a href="'. route('admin.transports.edit', $row->id) .'" class="btn btn-outline-primary btn-sm"><i class="bx bx-edit-alt me-1"></i></a>
                                    <button type="button" id="' .$row->id. '" class="btn btn-outline-danger remove-btn btn-sm"><i class="bx bx-trash me-1"></i></button>
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
        $tours = Tour::whereNull('transport_id')->get();
        return view('admin-page.transports.create-transport', compact('operators', 'tours'));
    }

    public function store(Request $request) {
        $data = $request->except('_token', 'tour_assignment_ids');

        $transport = Transport::create(array_merge($data, [
            'tour_assignment_ids' => json_encode($request->tour_assignment_ids)
        ]));

        if(is_array($request->tour_assignment_ids) && count($request->tour_assignment_ids) > 0) {
            Tour::whereIn('id', $request->tour_assignment_ids)->update([
                'transport_id' => $transport->id
            ]);
        }

        if($transport) return redirect()->route('admin.transports.edit', $transport->id)->withSuccess('Transport created successfully');
    }

    public function edit(Request $request) {
        $operators = Admin::where('role', 'bus_operator')->get();
        $transport = Transport::where('id', $request->id)->firstOrFail();
        $tours = Tour::where('transport_id', $transport->id)->orWhereNull('transport_id')->get();

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
        $data = $request->except('_token', 'tour_assignment_ids');
        $transport = Transport::where('id', $request->id)->firstOrFail();
    
        $transport_tour_ids = $transport->tour_assignment_ids ?? [];
    
        Tour::whereIn('id', $transport_tour_ids)->update([
            'transport_id' => null
        ]);
    
        $updatedTourAssignments = null;
        if ($request->has('tour_assignment_ids') && is_array($request->tour_assignment_ids)) {
            $updatedTourAssignments = json_encode($request->tour_assignment_ids);
            Tour::whereIn('id', $request->tour_assignment_ids)->update([
                'transport_id' => $transport->id
            ]);
        }
    
        $update = $transport->update(array_merge($data, [
            'tour_assignment_ids' => $updatedTourAssignments
        ]));
    
        if ($update) {
            return back()->with('success', 'Transport Updated Successfully');
        }
    }
    
    public function destroy(Request $request) {
        $transport = Transport::findOrFail($request->id);

        $transport_tour_ids = $transport->tour_assignment_ids ?? [];
    
        Tour::whereIn('id', $transport_tour_ids)->update([
            'transport_id' => null
        ]);

        $remove = $transport->delete();

        if($remove) {
            return response([
                'status' => true,
                'message' => 'Transport Deleted Successfully'
            ]);
        }
    }

    public function getTransportAttractions(Request $request, $id) {
        $transport = Transport::where('id', $id)->with('assigned_tour')->first();
        return $transport;
    }
}
