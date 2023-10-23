<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $user_id = null;

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

    public function updateNextLocation(Request $request) {
        $transport = Transport::where('id', $request->id)->first();

        if(!$transport) {
            return response([
                'status' => FALSE,
                'message' => 'Transport Not Foud'
            ]);
        }

        $next_location = [
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        $update_next_location = $transport->update([
            'next_location' => json_encode($next_location)
        ]);

        if($update_next_location) {
            return response([
                'status' => TRUE,
                'message' => 'Next location updated successfully'
            ]);
        }
    }

    public function updateCurrentLocation(Request $request) {
        $transport = Transport::where('id', $request->id)->first();

        if(!$transport) {
            return response([
                'status' => FALSE,
                'message' => 'Transport Not Foud'
            ]);
        }

        $current_location = [
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        $update_current_location = $transport->update([
            'current_location' => json_encode($current_location)
        ]);

        if($update_current_location) {
            return response([
                'status' => TRUE,
                'message' => 'Current location updated successfully'
            ]);
        }
    }

    public function updateTracking(Request $request) {

    }
}
