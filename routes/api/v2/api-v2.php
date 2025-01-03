<?php

use App\Http\Controllers\Api\v2\AnnouncementController;
use App\Http\Controllers\Api\v2\AttractionController;
use App\Http\Controllers\Api\v2\AuthController;
use App\Http\Controllers\Api\v2\HotelReservationController;
use App\Http\Controllers\Api\v2\InterestController;
use App\Http\Controllers\Api\v2\MerchantController;
use App\Http\Controllers\Api\v2\OrderController;
use App\Http\Controllers\Api\v2\OrganizationController;
use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\PromocodeController;
use App\Http\Controllers\Api\v2\ReferralController;
use App\Http\Controllers\Api\v2\ReservationCodeController;
use App\Http\Controllers\Api\v2\RoomController;
use App\Http\Controllers\Api\v2\TicketPassController;
use App\Http\Controllers\Api\v2\TourBadgeController;
use App\Http\Controllers\Api\v2\TourController;
use App\Http\Controllers\Api\v2\TourReservationController;
use App\Http\Controllers\Api\v2\TransactionController;
use App\Http\Controllers\Api\v2\TransportController;
use App\Http\Controllers\Api\v2\TravelTaxController;
use App\Http\Controllers\Api\v2\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['api_base_authorization']], function () {
    // User-related routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('users/profile', [UserController::class, 'updateProfile']);

    // Announcement routes
    Route::get('announcements', [AnnouncementController::class, 'index']);
    Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

    // Attraction routes
    Route::get('attractions', [AttractionController::class, 'index']);
    Route::get('attractions/{attraction_id}', [AttractionController::class, 'show']);
    Route::get('attractions/organizations/{organization_id}', [AttractionController::class, 'attractionsByOrganization']);

    // Organization routes
    Route::get('organizations', [OrganizationController::class, 'index']);
    Route::get('organizations/{organization_id}', [OrganizationController::class, 'show']);

    // Merchant routes
    Route::get('merchants', [MerchantController::class, 'index']);
    Route::get('merchants/{merchant_id}', [MerchantController::class, 'show']);

    // Tour routes
    Route::get('tours', [TourController::class, 'index']);
    Route::get('tours/guided', [TourController::class, 'getGuidedTours']);
    Route::get('tours/diy', [TourController::class, 'getDIYTours']);
    Route::get('tours/transit', [TourController::class, 'getTransitTours']);
    Route::get('tours/{tour_id}', [TourController::class, 'show']);

    // Transport routes
    Route::get('transports', [TransportController::class, 'index']);
    Route::get('transports/{transport_id}', [TransportController::class, 'show']);

    // Tour Reservation routes
    Route::post('tour-reservations', [TourReservationController::class, 'store']);
    Route::post('tour-reservations/single/guest', [TourReservationController::class, 'storeGuestSingleReservation']);
    Route::post('tour-reservations/guest', [TourReservationController::class, 'storeGuestReservations']);
    Route::get('tour-reservations/users/{user_id}/reservation-dates', [TourReservationController::class, 'userTourReservationDates']);
    Route::get('tour-reservations/users/{user_id}', [TourReservationController::class, 'userTourReservations']);
    Route::get('tour-reservations/{tour_reservation_id}', [TourReservationController::class, 'show']);

    // Promocode routes
    Route::get('promocodes', [PromocodeController::class, 'index']);
    Route::post('promocodes/verify', [PromocodeController::class, 'verify']);

    // Referral routes
    Route::post('referrals/verify', [ReferralController::class, 'verify']);

    // Interest routes
    Route::get('interests', [InterestController::class, 'index']);
    Route::get('interests/users/{user_id}', [InterestController::class, 'userInterests']);
    Route::get('interests/{interest_id}', [InterestController::class, 'show']);

    // Ticket Pass routes
    Route::get('ticket-passes', [TicketPassController::class, 'index']);
    Route::get('ticket-passes/{ticket_pass_id}', [TicketPassController::class, 'show']);

    // Tour Badge routes
    Route::get('tour-badges', [TourBadgeController::class, 'index']);
    Route::get('tour-badges/{tour_badge_id}', [TourBadgeController::class, 'show']);

    // Reservation Code routes
    Route::post('reservation-codes/verify', [ReservationCodeController::class, 'verify']);
    Route::get('reservation-codes/tour-reservations/{reservation_id}', [ReservationCodeController::class, 'getTourReservationCodesByReservation']);
    Route::get('reservation-codes/{reservation_code_id}', [ReservationCodeController::class, 'show']);

    // Product routes
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/merchants/{merchant_id}', [ProductController::class, 'merchantProducts']);
    Route::get('products/{product_id}', [ProductController::class, 'show']);

    // Room routes
    Route::get('rooms', [RoomController::class, 'index']);
    Route::get('rooms/merchants/{merchant_id}', [RoomController::class, 'merchantRooms']);
    Route::get('rooms/{room_id}', [RoomController::class, 'show']);

    // Hotel Reservation routes
    Route::get('hotel-reservations/{hotel_reservation_id}', [HotelReservationController::class, 'show']);

    // Travel Tax routes
    Route::get('travel-tax', [TravelTaxController::class, 'index']);
    Route::post('travel-tax', [TravelTaxController::class, 'store']);
    Route::get('travel-tax/{travel_tax_id}/passengers/{passenger_id}', [TravelTaxController::class, 'travelTaxPassenger']);
    Route::get('travel-tax/{travel_tax_id}', [TravelTaxController::class, 'show']);

    // Order routes
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/users/{user_id}', [OrderController::class, 'userOrders']);
    Route::get('orders/{order_id}', [OrderController::class, 'show']);

    // Transaction routes
    Route::get('transactions/{id}', [TransactionController::class, 'getTransaction']);
});
