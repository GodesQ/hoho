<?php

namespace App\Traits;
use App\Models\Food;
use App\Models\HotelReservation;
use App\Models\Order;
use App\Models\Product;
use App\Models\RestaurantReservation;
use App\Models\Room;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\TravelTaxPassenger;
use App\Models\TravelTaxPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $foods_count = Food::where('merchant_id', $user->merchant_id)->count();
        $restaurant_reservations_count =  RestaurantReservation::where('merchant_id', $user->merchant_id)->get()->count();

        return view('admin-page.dashboard.merchants.restaurant-dashboard', compact('recent_restaurant_reservations', 'type', 'merchantInfo', 'foods_count', 'restaurant_reservations_count'));
    }

    public function storeDashboard($merchantInfo) {
        $user = Auth::guard('admin')->user();
        $type = config('roles.' . $user->role, null);

        $recent_store_orders = Order::whereHas('product', function($q) use ($user) {
            return $q->where('merchant_id', $user->merchant_id);
        })->limit(5)->get();

        $products_count = Product::where('merchant_id', $user->merchant_id)->count();

        $orders_count = Order::whereHas('product', function($q) use ($user) {
            return $q->where('merchant_id', $user->merchant_id);
        })->count();

        return view('admin-page.dashboard.merchants.store-dashboard', compact('recent_store_orders', 'type', 'merchantInfo', 'products_count', 'orders_count'));
    }

    public function tourOperatorDashboard($merchantInfo) {
        $user = Auth::guard('admin')->user();
        $type = config('roles.' . $user->role, null);

        $tours_count = Tour::where('tour_provider_id', $user->merchant->tour_provider_info->id)->count();

        $recent_tour_reservations = TourReservation::with('user', 'tour')
                                            ->where('created_by', $user->id)
                                            ->with('tour', 'user')->latest()
                                            ->limit(5)
                                            ->get();

        return view('admin-page.dashboard.merchants.tour-provider-dashboard', compact('recent_tour_reservations','type','merchantInfo', 'tours_count'));
    }

    public function travelTaxDashboard() {
        $recent_payments = TravelTaxPayment::latest()
                            ->limit(6)
                            ->with('payor')
                            ->get();

        $currentMonth = now()->format('Y-m');

        $total_payments_per_class = TravelTaxPassenger::select("class", DB::raw('max(payment_id) as payment_id'), DB::raw('sum(amount) as total_amount'))
                                        ->with('payment')
                                        ->whereHas('payment', function ($row) {
                                            $row->where('status', 'paid');
                                        })
                                        ->groupBy(['class'])
                                        ->get();

        $recent_passengers = TravelTaxPassenger::where('passenger_type', 'primary')
                                ->limit(5)
                                ->get();
        
        $totalProfit = TravelTaxPayment::where('status', 'paid')
                                    ->where(DB::raw('DATE_FORMAT(payment_time, "%Y-%m")'), $currentMonth)
                                    ->sum('total_amount');

        return view('admin-page.dashboard.travel-tax-dashboard', compact('recent_payments', 'total_payments_per_class', 'totalProfit', 'recent_passengers'));
    }
}