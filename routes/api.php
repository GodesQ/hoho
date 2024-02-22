<?php

use App\Http\Controllers\Api\HotelReservationController;
use App\Http\Controllers\Api\RestaurantReservationController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\TourFeedBackController;
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
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\PromoCodeController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\TicketPassController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\TourBadgeController;

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

Route::get('promocodes', [PromoCodeController::class, 'getPromoCodes']);
Route::get('promocodes/verify/{code}', [PromoCodeController::class, 'checkValidPromoCode']);

Route::post('user/post_forgot_password', [ForgotPasswordController::class, 'post_forgot_password']);

Route::post('user/change_password', [UserController::class, 'changePassword']);

Route::get('announcements/important_announcements', [AnnouncementController::class, 'getImportantAnnouncements']);
Route::get('announcements', [AnnouncementController::class, 'getAnnouncements']);

Route::get('featured_merchants', [MerchantController::class, 'getFeaturedMerchants']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::get('user', [UserController::class, 'getUser']);
    Route::post('user/profile', [UserController::class, 'updateProfile']);
    Route::post('user/update_interest', [UserController::class, 'updateInterest']);
    Route::delete('user/delete', [UserController::class, 'destroyAccount']);

    Route::get('user/reservations', [TourReservationController::class, 'getUserReservations']);
    Route::get('user/future_reservations_dates', [TourReservationController::class, 'getAllUserFutureDateReservations']);
    Route::get('ticket_pass', [TourReservationController::class, 'getDIYTicketPassReservations']);
    Route::get('ticket_pass_reservation/{id}', [TourReservationController::class, 'getDIYTicketPassReservation']);
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
    Route::post('transport/update_current_location/{id}', [TransportController::class, 'updateCurrentLocation']);
    Route::post('transport/update_tracking/{id}', [TransportController::class, 'updateTracking']);

    Route::get('carts/user', [CartController::class, 'getUserCarts']);
    Route::get('carts/user/my_cart/{id}', [CartController::class, 'getUserCart']);
    Route::post('cart/store', [CartController::class, 'storeCart']);
    Route::delete('cart/destroy/{id}', [CartController::class, 'removeCart']);
    Route::delete('cart/destroy_all/user', [CartController::class, 'removeAllUserCart']);

    Route::get('ticket_passes', [TicketPassController::class, 'getTicketPasses']);

    Route::get('interests', [InterestController::class, 'getInterests']);

    Route::get('reservation_codes/verify/{reservation_id}/{code}', [TourReservationController::class, 'verifyReservationCode']);

    Route::get('verify_referral_code/{referral_code}', [ReferralController::class, 'verifyReferralCode']);

    Route::get('tour_badges', [TourBadgeController::class, 'getAllTourBadges']);
    Route::get('tour_badges/user_badges', [TourBadgeController::class,'getUserTourBadges']);
    Route::post('tour_badge/check', [TourBadgeController::class, 'checkBadge']);

    Route::get('rooms/{merchant_id}', [RoomController::class, 'getRooms']);

    Route::post('restaurant-reservations', [RestaurantReservationController::class, 'store']);
    Route::get('restaurant-reservations/{id}', [RestaurantReservationController::class, 'show']);

    Route::post('hotel-reservations', [HotelReservationController::class, 'store']);
    Route::get('hotel-reservations/{id}', [HotelReservationController::class, 'show']);

    Route::get('products', []);

    Route::get('tour-feedbacks', [TourFeedBackController::class, 'index']);
    Route::post('tour-feedbacks', [TourFeedBackController::class, 'store']);
    Route::get('tour-feedbacks/{id}', [TourFeedBackController::class, 'show']);
    Route::put('tour-feedbacks/{id}', [TourFeedBackController::class, 'update']);
    Route::get('tour-feedbacks/tours/{tour_id}', [TourFeedBackController::class, 'getFeedBacksByTour']);
});
