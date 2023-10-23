<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourBadge;
use App\Models\TourReservation;
use App\Models\UserTourBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourBadgeController extends Controller
{   
    public function getAllTourBadges(Request $request) {
        $tour_badges = TourBadge::select('id', 'tour_id', 'badge_name', 'badge_code', 'badge_img', 'location', 'latitude', 'longitude')->get();
        return response()->json($tour_badges);
    }

    public function getUserTourBadges(Request $request) {
        $user = Auth::user();
        $user_badges = UserTourBadge::where('user_id', $user->id)->with('tour_badge')->get();

        return response()->json([
            'status' => 'success',
            'user_badges' => $user_badges
        ]);
    }

    public function checkBadge(Request $request) {
        $user = Auth::user();
        $today = date("Y-m-d");

        $tour_reservation = TourReservation::where('reserved_user_id', $user->id)->where('start_date', $today)->where('status', 'approved')->first();
        // check if there's a tour reservation
        if (!$tour_reservation) return response()->json(['status' => FALSE, 'message'=> 'No Reservation Found Today'], 400);
        
        // check if the tour badge is existing base on the badge code
        $tour_badge = TourBadge::where('badge_code', $request->badge_code)->with('tour')->first();
        if (!$tour_badge) return response()->json(['status'=> FALSE, 'message'=> 'Invalid Badge Code'], 400);

        // check if the tour badge is same with the tour reservation of user
        if($tour_reservation->tour_id != $tour_badge->tour_id) return response()->json(['status'=> FALSE, 'message'=> 'Tour is not the same with the tour reservation you booked today.'],400);

        // check if the distance is atmost 200m away to scan
        $distance = $this->calculateDistance($tour_badge->latitude, $tour_badge->longitude, $request->latitude, $request->longitude);
        if ($distance > 200) return response()->json(['status' => FALSE, 'message' => 'Distance from QR Code is more than 200 meters'], 400);

        $user_tour_badge = UserTourBadge::updateOrCreate([
            'user_id' => $user->id,
            'badge_id' => $tour_badge->id,
        ],[
            'tour_reservation_id' => $tour_reservation->id,
            'status' => 'success'
        ]);

        if($user_tour_badge) {
            return response([
                'status' => TRUE,
                'message' => 'Gotcha! Claim Your Badge Now!',
                'badge' => $tour_badge
            ]);
        } else {
            return response([
                'status' => FALSE,
                'message' => 'Failed to get the badge.',
            ], 500);
        }

    }

    private function calculateDistance($targetLat, $targetLng, $requestLat, $requestLng) {
        $earthRadius = 6371;

        // Convert degrees to radians
        $lat1 = deg2rad($targetLat);
        $lon1 = deg2rad($targetLng);
        $lat2 = deg2rad($requestLat);
        $lon2 = deg2rad($requestLng);
    
        // Haversine formula
        $latDiff = $lat2 - $lat1;
        $lonDiff = $lon2 - $lon1;
    
        $a = sin($latDiff / 2) ** 2 + cos($lat1) * cos($lat2) * sin($lonDiff / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        // Calculate the distance in meters
        $distance = $earthRadius * $c * 1000; // Convert to meters

        return $distance;
    }
}
