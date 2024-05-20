<?php

namespace App\Traits;
use App\Models\HotelReservation;
use App\Models\Order;
use App\Models\RestaurantReservation;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

trait DashboardTrait {
    public function adminDashboard() {

    }

    public function hotelDashboard($merchantInfo) {
        $user = Auth::guard('admin')->user();
        $type = config('roles.' . $user->role, null);

        $recent_hotel_reservations = HotelReservation::whereHas('room', function($q) use ($user) {
            return $q->where('merchant_id', $user->merchant_id);
        })->limit(5)->get();

        $rooms_count = Room::where('merchant_id', $user->merchant_id)->count();

        $hotel_reservations_count = HotelReservation::whereHas('room', function($q) use ($user) {
            return $q->where('merchant_id', $user->merchant_id);
        })->get()->count();

        return view('admin-page.dashboard.merchants.hotel-dashboard', compact('recent_hotel_reservations', 'type', 'merchantInfo', 'rooms_count', 'hotel_reservations_count'));
    }

    public function restaurantDashboard($merchantInfo) {
        $user = Auth::guard('admin')->user();
        $type = config('roles.' . $user->role, null);

        $recent_restaurant_reservations = RestaurantReservation::where('merchant_id', $user->merchant_id)->limit(5)->get();

        return view('admin-page.dashboard.merchants.restaurant-dashboard', compact('recent_restaurant_reservations', 'type', 'merchantInfo'));
    }

    public function storeDashboard($merchantInfo) {
        $user = Auth::guard('admin')->user();
        $type = config('roles.' . $user->role, null);

        $recent_store_orders = Order::whereHas('product', function($q) use ($user) {
            return $q->where('merchant_id', $user->merchant_id);
        })->limit(5)->get();

        return view('admin-page.dashboard.merchants.store-dashboard', compact('recent_store_orders', 'type', 'merchantInfo'));
    }
}