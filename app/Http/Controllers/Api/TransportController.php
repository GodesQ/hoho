<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transport;

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
        $transport = Transport::where('id', $request->id)->firstOr(function () {
            return response([
                'status' => FALSE,
                'message' => 'Not Found'
            ], 404);
        });

        $update_transport = $transport->update([
            'next_location' => $request->next_location
        ]);

        if($update_transport) {
            return response([
                'status' => TRUE,
                'message' => 'The next location updated successfully'
            ]);
        }
    }
}
