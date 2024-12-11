<?php

use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\Auth\SSOController;
use App\Http\Controllers\Api\HotelReservationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RestaurantReservationController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\TourFeedBackController;
use App\Http\Controllers\Api\TravelTaxController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware(["maintenance_mode"])->group(function () {
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::post('sso/register', [SSOController::class, 'register']);
Route::post('sso/login', [SSOController::class, 'login']);

Route::post('user/post_forgot_password', [ForgotPasswordController::class, 'post_forgot_password']);

Route::post('user/change_password', [UserController::class, 'changePassword']);

Route::get('featured_merchants', [MerchantController::class, 'getFeaturedMerchants']);

Route::get('app-settings/maintenance-mode', [AppSettingController::class, 'checkMaintenanceMode']);
Route::get('promocodes/verify/{code}', action: [PromoCodeController::class, 'checkValidPromoCode']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('announcements/important_announcements', [AnnouncementController::class, 'getImportantAnnouncements']);
    Route::get('announcements', [AnnouncementController::class, 'getAnnouncements']);

    Route::get('promocodes', [PromoCodeController::class, 'getPromoCodes']);

    Route::get('user', [UserController::class, 'getUser']);
    Route::post('user/profile', [UserController::class, 'updateProfile']);
    Route::post('user/update_interest', [UserController::class, 'updateInterest']);
    Route::delete('user/delete', [UserController::class, 'destroyAccount']);

    Route::get('users/{user_id}/future-reservations', [TourReservationController::class, 'getAllUserFutureDateReservations']); // New Version
    Route::get('user/future_reservations_dates', [TourReservationController::class, 'getAllUserFutureDateReservations']); // Older Version

    Route::get('users/{user_id}/reservations', [TourReservationController::class, 'getUserReservations']); // New Version
    Route::get('user/reservations', [TourReservationController::class, 'getUserReservations']); // Older Version

    Route::get('reservation/ticket-passes', [TourReservationController::class, 'getDIYTicketPassReservations']); // New Version
    Route::get('ticket_pass', [TourReservationController::class, 'getDIYTicketPassReservations']); // Older Version

    Route::get('reservation/ticket-passes/{id}', [TourReservationController::class, 'getDIYTicketPassReservation']); // New Version
    Route::get('ticket_pass_reservation/{id}', [TourReservationController::class, 'getDIYTicketPassReservation']); // Older Version

    Route::get('reservation/today', [TourReservationController::class, 'getUserTodayReservation']); // New Version
    Route::get('today_reservation', [TourReservationController::class, 'getUserTodayReservation']); // Older Version

    Route::post('reservation/store/single', [TourReservationController::class, 'storeTourReservation']);
    Route::post('reservation/store', [TourReservationController::class, 'storeMultipleTourReservation']);

    Route::get('organizations', [OrganizationController::class, 'getOrganizations']);
    Route::get('organizations/{id}', [OrganizationController::class, 'getOrganization']);
    Route::get('organization/{id}', [OrganizationController::class, 'getOrganization']);

    Route::get('attractions/{id}', [AttractionController::class, 'getAttraction']);
    Route::get('attraction/{id}', [AttractionController::class, 'getAttraction']);

    Route::get('merchants/{id}', [MerchantController::class, 'getMerchant']);
    Route::get('merchant/{id}', [MerchantController::class, 'getMerchant']);

    Route::group(['prefix' => 'tours'], function () {
        Route::get('/', [TourController::class, 'index']);
        Route::get('guided', [TourController::class, 'getGuidedTours']);
        Route::get('diy', [TourController::class, 'getDIYTours']);
        Route::get('transit', [TourController::class, 'getTransitTours']);
        Route::get('seasonal', [TourController::class, 'getSeasonalTours']);
    });

    Route::get('transports', [TransportController::class, 'getTransports']);

    Route::get('transports/{id}', [TransportController::class, 'getTransport']); // New Version
    Route::get('transport/{id}', [TransportController::class, 'getTransport']); // Older Version

    Route::post('transports/update/location/{id}', [TransportController::class, 'updateLocation']); // New Version
    Route::post('transport/update_location/{id}', [TransportController::class, 'updateLocation']); // Older Version

    Route::post('transports/update/next-location/{id}', [TransportController::class, 'updateNextLocation']); // New Version
    Route::post('transport/update_next_location/{id}', [TransportController::class, 'updateNextLocation']); // Older Version

    Route::post('transports/update/current-location/{id}', [TransportController::class, 'updateCurrentLocation']); // New Version
    Route::post('transport/update_current_location/{id}', [TransportController::class, 'updateCurrentLocation']); // Older Version

    Route::get('carts/user', [CartController::class, 'getUserCarts']);
    Route::get('carts/user/my_cart/{id}', [CartController::class, 'getUserCart']);
    Route::post('cart/store', [CartController::class, 'storeCart']);
    Route::delete('cart/destroy/{id}', [CartController::class, 'removeCart']);
    Route::delete('cart/destroy_all/user', [CartController::class, 'removeAllUserCart']);

    Route::get('ticket_passes', [TicketPassController::class, 'getTicketPasses']);

    Route::get('interests', [InterestController::class, 'getInterests']);

    Route::post('reservation-codes/verify', [TourReservationController::class, 'scanReservationCode']); // New Version
    Route::get('reservation_codes/verify/{reservation_id}/{code}', [TourReservationController::class, 'verifyReservationCode']); // Older Version

    Route::post('referral-codes/verify', [ReferralController::class, 'verifyReferralCode']);
    Route::get('verify_referral_code/{referral_code}', [ReferralController::class, 'verifyReferralCode']);

    Route::get('tour-badges', [TourBadgeController::class, 'getAllTourBadges']); // New Version
    Route::get('tour_badges', [TourBadgeController::class, 'getAllTourBadges']); // Old Version

    Route::get('tour-badges/user-badges', [TourBadgeController::class, 'getUserTourBadges']); // New Version
    Route::get('tour_badges/user_badges', [TourBadgeController::class, 'getUserTourBadges']); // Old Version

    Route::post('tour-badges/verify', [TourBadgeController::class, 'checkBadge']); // New Version
    Route::post('tour_badge/check', [TourBadgeController::class, 'checkBadge']); // Old Version

    Route::get('rooms/merchants/{merchant_id}', [RoomController::class, 'getMerchantRooms']);

    Route::post('restaurant-reservations', [RestaurantReservationController::class, 'store']);
    Route::get('restaurant-reservations/merchants/{merchant_id}', [RestaurantReservationController::class, 'getMerchantRestaurantReservations']);
    Route::get('restaurant-reservations/users/{user_id}', [RestaurantReservationController::class, 'getUserRestaurantReservations']);
    Route::get('restaurant-reservations/{id}', [RestaurantReservationController::class, 'show']);

    Route::post('hotel-reservations', [HotelReservationController::class, 'store']);
    Route::get('hotel-reservations/users/{user_id}', [HotelReservationController::class, 'getUserHotelReservations']);
    Route::get('hotel-reservations/{id}', [HotelReservationController::class, 'show']);

    Route::get('products/merchants/{merchant_id}', [ProductController::class, 'getMerchantProducts']);
    Route::get('products/{id}', [ProductController::class, 'getProduct']);

    Route::post('orders', [OrderController::class, 'store']);
    Route::post('orders/bulk', [OrderController::class, 'bulk_store']);
    Route::get('orders/users/{user_id}', [OrderController::class, 'getUserOrders']);
    Route::get('orders/{order_id}', [OrderController::class, 'show']);

    Route::get('tour-feedbacks', [TourFeedBackController::class, 'index']);
    Route::post('tour-feedbacks', [TourFeedBackController::class, 'store']);
    Route::get('tour-feedbacks/{id}', [TourFeedBackController::class, 'show']);
    Route::put('tour-feedbacks/{id}', [TourFeedBackController::class, 'update']);
    Route::get('tour-feedbacks/tours/{tour_id}', [TourFeedBackController::class, 'getFeedBacksByTour']);

    Route::post('travel-tax', [TravelTaxController::class, 'store']);
    Route::get('travel-tax/users/{user_id}', [TravelTaxController::class, 'getUserTravelTaxPayments']);
});
// });
