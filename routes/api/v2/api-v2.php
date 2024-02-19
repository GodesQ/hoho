<?php

use App\Http\Controllers\Api\v2\AnnouncementController;
use App\Http\Controllers\Api\v2\AttractionController;
use App\Http\Controllers\Api\v2\MerchantController;
use App\Http\Controllers\Api\v2\OrganizationController;
use App\Http\Controllers\Api\v2\PromocodeController;
use App\Http\Controllers\Api\v2\TourController;
use App\Http\Controllers\Api\v2\TourReservationController;
use App\Http\Controllers\Api\v2\TransportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
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

    Route::get('tour-reservations/users/{user_id}/reservation-dates', [TourReservationController::class, 'userTourReservationDates']);
    Route::get('tour-reservations/users/{user_id}', [TourReservationController::class, 'userTourReservations']);

    Route::get('promocodes', [PromocodeController::class, 'index']);
    Route::post('promocodes/verify', [PromocodeController::class, 'verify']);
});