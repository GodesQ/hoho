<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TourReservationController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttractionController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TransportController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\InterestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user', [UserController::class, 'getUser']);
    Route::post('user/profile', [UserController::class, 'updateProfile']);
    Route::post('user/update_interest', [UserController::class, 'updateInterest']);

    Route::get('ticket_pass', [TourReservationController::class, 'getDIYReservations']);
    Route::get('today_reservation', [TourReservationController::class, 'getUserTodayReservation']);
    Route::post('reservation/store', [TourReservationController::class, 'storeTourReservation']);

    Route::get('organizations', [OrganizationController::class, 'getOrganizations']);
    Route::get('organization/{id}', [OrganizationController::class, 'getOrganization']);

    Route::get('attraction/{id}', [AttractionController::class, 'getAttraction']);
    Route::get('merchant/{id}', [MerchantController::class, 'getMerchant']);

    Route::get('tours/guided', [TourController::class, 'getGuidedTours']);
    Route::get('tours/diy', [TourController::class, 'getDIYTours']);

    Route::get('transports', [TransportController::class, 'getTransports']);
    Route::get('transport/{id}', [TransportController::class, 'getTransport']);
    Route::post('transport/update_location/{id}', [TransportController::class, 'updateLocation']);
    Route::post('transport/update_next_location/{id}', [TransportController::class, 'updateNextLocation']);
    Route::post('transport/update_tracking/{id}', [TransportController::class, 'updateTracking']);

    Route::get('carts/user', [CartController::class, 'getUserCarts']);
    Route::get('carts/user/my_cart/{id}', [CartController::class, 'getUserCart']);
    Route::post('cart/store', [CartController::class, 'storeCart']);
    Route::get('cart/destroy', [CartController::class, 'removeCart']);

    Route::get('interests', [InterestController::class, 'getInterests']);


});
