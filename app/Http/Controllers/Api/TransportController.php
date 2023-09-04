<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transport;
use App\Events\BusLocationEvent;

class TransportController extends Controller
{

    public function getTransports(Request $request) {
        $transports = Transport::get();
        return response($transports);
    }

    public function getTransport(Request $request) {
        $transport = Transport::select('id', 'route', 'capacity', 'tour_assigned_id', 'tour_assignment_ids', 'latitude', 'longitude', 'name', 'current_location', 'next_location', 'previous_location')->where('id', $request->id)->with('assigned_tour')->firstOr(function () {
            return response([
                'status' => FALSE,
                'transport' => null
            ], 404);
        });
        return response([
            'status' => TRUE,
            'transport' => $transport
        ]);
    }

    public function updateLocation(Request $request) {
        $transport = Transport::where('id', $request->id)->first();

        $update = $transport->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        $user_id = Auth::user()->id;

        $coordinates = [
            'latitude' => $transport->latitude,
            'longitude' => $transport->longitude
        ];

        $event = event(new BusLocationEvent($user_id, $coordinates, $transport->id));
        // dd($event);
        return response([
            'status' => true,
            'message' => 'Updated Successfully'
        ]);
    }

    public function updateTracking(Request $request) {

    }
}
