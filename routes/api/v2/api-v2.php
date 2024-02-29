<?php

use App\Http\Controllers\Api\v2\AnnouncementController;
use App\Http\Controllers\Api\v2\AttractionController;
use App\Http\Controllers\Api\v2\InterestController;
use App\Http\Controllers\Api\v2\MerchantController;
use App\Http\Controllers\Api\v2\OrganizationController;
use App\Http\Controllers\Api\v2\ProductController;
use App\Http\Controllers\Api\v2\PromocodeController;
use App\Http\Controllers\Api\v2\ReferralController;
use App\Http\Controllers\Api\v2\ReservationCodeController;
use App\Http\Controllers\Api\v2\TicketPassController;
use App\Http\Controllers\Api\v2\TourBadgeController;
use App\Http\Controllers\Api\v2\TourController;
use App\Http\Controllers\Api\v2\TourReservationController;
use App\Http\Controllers\Api\v2\TransportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

    Route::group(['middleware' => ['api_base_authorization']], function () {
        Route::get('announcements', [AnnouncementController::class, 'index']);
        Route::get('announcements/{id}', [AnnouncementController::class, 'show']);

        Route::get('attractions', [AttractionController::class, 'index']);
        Route::get('attractions/{attraction_id}', [AttractionController::class, 'show']);
        Route::get('attractions/organizations/{organization_id}', [AttractionController::class, 'attractionsByOrganization']);

        Route::get('organizations', [OrganizationController::class, 'index']);
        Route::get('organizations/{organization_id}', [OrganizationController::class, 'show']);

        Route::get('merchants', [MerchantController::class, 'index']);
        Route::get('merchants/{merchant_id}', [MerchantController::class, 'show']);

        Route::get('tours', [TourController::class, 'index']);
        Route::get('tours/guided', [TourController::class, 'getGuidedTours']);
        Route::get('tours/diy', [TourController::class, 'getDIYTours']);
        Route::get('tours/{tour_id}', [TourController::class, 'show']);

        Route::get('transports', [TransportController::class, 'index']);
        Route::get('transports/{transport_id}', [TransportController::class, 'show']);

        Route::post('tour-reservations', [TourReservationController::class, 'store']);
        Route::post('tour-reservations/bulk', [TourReservationController::class, 'bulk']);
        Route::post('tour-reservations/guest', [TourReservationController::class, 'storeGuestReservation']);
        Route::get('tour-reservations/users/{user_id}/reservation-dates', [TourReservationController::class, 'userTourReservationDates']);
        Route::get('tour-reservations/users/{user_id}', [TourReservationController::class, 'userTourReservations']);
        Route::get('tour-reservations/{tour_reservation_id}', [TourReservationController::class, 'show']);

        Route::get('promocodes', [PromocodeController::class, 'index']);
        Route::post('promocodes/verify', [PromocodeController::class, 'verify']);

        Route::post('referrals/verify', [ReferralController::class, 'verify']);
        
        Route::get('interests', [InterestController::class, 'index']);
        Route::get('interests/{interest_id}', [InterestController::class, 'show']);

        Route::get('ticket-passes', [TicketPassController::class, 'index']);
        Route::get('ticket-passes/{ticket_pass_id}', [TicketPassController::class, 'show']);

        Route::get('tour-badges', [TourBadgeController::class, 'index']);
        Route::get('tour-badges/{tour_badge_id}', [TourBadgeController::class, 'show']);

        Route::get('reservation-codes/tour-reservations/{reservation_id}', [ReservationCodeController::class, 'getTourReservationCodes']);
        Route::get('reservation-codes/{reservation_code_id}', [ReservationCodeController::class, 'show']);

        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/merchants/{merchant_id}', [ProductController::class, 'merchantProducts']);
        Route::get('products/{product_id}', [ProductController::class, 'show']);
    });